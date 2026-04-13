<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolSetting;
use App\Models\SuperAdminMutasi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class SuperAdminMutasiController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'super_admin') {
                abort(403, 'Akses ditolak — hanya Super Admin.');
            }
            return $next($request);
        });
    }

    /**
     * Daftar mutasi pemasukan Super Admin
     */
    public function index(Request $request)
    {
        $query = SuperAdminMutasi::with('payment')->orderByDesc('tanggal')->orderByDesc('id');

        // Filter tahun
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        // Filter bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        // Filter package type
        if ($request->filled('paket')) {
            $query->where('package_type', $request->paket);
        }

        // Filter pencarian nama sekolah / order_id
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('school_name', 'like', "%{$search}%")
                  ->orWhere('order_id', 'like', "%{$search}%")
                  ->orWhere('buyer_name', 'like', "%{$search}%");
            });
        }

        $mutasiList = $query->paginate(20)->withQueryString();

        // Stats
        $baseQuery   = SuperAdminMutasi::query();
        $totalDebit  = (clone $baseQuery)->sum('debit');
        $totalTrx    = (clone $baseQuery)->count();
        $thisMonth   = (clone $baseQuery)->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year)->sum('debit');
        $saldoTerkini = (clone $baseQuery)->orderByDesc('id')->value('saldo') ?? 0;

        // Dropdown helpers
        $tahunList = SuperAdminMutasi::selectRaw('YEAR(tanggal) as tahun')
            ->distinct()->orderByDesc('tahun')->pluck('tahun')->toArray();
        if (empty($tahunList)) $tahunList = [date('Y')];

        $bulanList = [
            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
            5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
            9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember',
        ];

        return view('admin.mutasi.index', compact(
            'mutasiList', 'totalDebit', 'totalTrx', 'thisMonth', 'saldoTerkini',
            'tahunList', 'bulanList'
        ));
    }

    /**
     * Export PDF
     */
    public function exportPdf(Request $request)
    {
        $query = SuperAdminMutasi::orderBy('tanggal')->orderBy('id');

        if ($request->filled('tahun')) $query->whereYear('tanggal', $request->tahun);
        if ($request->filled('bulan')) $query->whereMonth('tanggal', $request->bulan);
        if ($request->filled('paket')) $query->where('package_type', $request->paket);

        $mutasiList   = $query->get();
        $totalDebit   = $mutasiList->sum('debit');
        $saldoTerkini = SuperAdminMutasi::orderByDesc('id')->value('saldo') ?? 0;
        $filterLabel  = $this->buildFilterLabel($request);

        // Ambil setting dari user yang sedang login untuk kop laporan
        $setting = SchoolSetting::where('user_id', auth()->id())->first()
                   ?? new SchoolSetting();

        $pdf = Pdf::loadView('admin.mutasi.pdf', compact(
            'mutasiList', 'totalDebit', 'saldoTerkini', 'filterLabel', 'setting'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('laporan-pemasukan-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Excel — menggunakan PhpSpreadsheet langsung (.xlsx proper dengan formatting)
     */
    public function exportExcel(Request $request)
    {
        $query = SuperAdminMutasi::orderBy('tanggal')->orderBy('id');

        if ($request->filled('tahun'))  $query->whereYear('tanggal', $request->tahun);
        if ($request->filled('bulan'))  $query->whereMonth('tanggal', $request->bulan);
        if ($request->filled('paket'))  $query->where('package_type', $request->paket);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('school_name', 'like', "%{$s}%")
                ->orWhere('order_id',   'like', "%{$s}%")
                ->orWhere('buyer_name', 'like', "%{$s}%")
            );
        }

        $mutasiList   = $query->get();
        $totalDebit   = (float) $mutasiList->sum('debit');
        $saldoTerkini = (float) ($mutasiList->last()?->saldo ?? 0);
        $filterLabel  = $this->buildFilterLabel($request);
        $filename     = 'Laporan-Pemasukan-' . date('Y-m-d') . '.xlsx';

        $sp = $this->buildSpreadsheet($mutasiList, $totalDebit, $saldoTerkini, $filterLabel);

        return response()->streamDownload(function () use ($sp) {
            (new XlsxWriter($sp))->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    // ════════════════════════════════════════════════════════════════
    // BUILD SPREADSHEET (PhpSpreadsheet)
    // ════════════════════════════════════════════════════════════════

    private function buildSpreadsheet($data, float $totalDebit, float $saldoTerkini, string $filterLabel): Spreadsheet
    {
        $sp = new Spreadsheet();
        $sp->getProperties()
            ->setCreator('EduFinance')
            ->setTitle('Laporan Mutasi Pemasukan Super Admin')
            ->setSubject('Laporan Keuangan');

        $this->sheetUtama($sp->getActiveSheet(), $data, $totalDebit, $saldoTerkini, $filterLabel);
        $this->sheetRingkasan($sp->createSheet()->setTitle('Ringkasan Per Paket'), $data, $totalDebit);
        $this->sheetBulanan($sp->createSheet()->setTitle('Rekap Bulanan'), $data);

        $sp->setActiveSheetIndex(0);
        return $sp;
    }

    // ── Palette ──────────────────────────────────────────────────────
    private const C = [
        'navy'     => '3498DB', 'navy2'    => '2C3E50',
        'navy_lt'  => 'F8F9FA', 'navy_bd'  => '3498DB',
        'green'    => '27AE60', 'green_lt' => 'F8F9FA',
        'blue_lt'  => 'F8F9FA', 'blue_d'   => '2980B9',
        'purp_d'   => '2C3E50', 'purp_lt'  => 'F8F9FA',
        'ambr_d'   => 'E74C3C', 'ambr_lt'  => 'FFF3CD',
        'gray_f'   => 'F9F9F9', 'gray_tx'  => '7B8A8B',
        'white'    => 'FFFFFF',
    ];

    private const PAKET_COLOR = [
        'monthly'  => ['F8F9FA', '3498DB'],
        'yearly'   => ['F8F9FA', '3498DB'],
        'lifetime' => ['F8F9FA', '3498DB'],
    ];

    // ── Cell helper ──────────────────────────────────────────────────
    private function c(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws,
        string $coord, mixed $value,
        bool $bold = false, float $size = 9,
        string $fg = '1A1A2E', ?string $bg = null,
        string $hAlign = 'left', bool $wrap = false,
        bool $italic = false, ?array $borders = null,
        ?string $numFmt = null,
    ): \PhpOffice\PhpSpreadsheet\Cell\Cell {
        $cell = $ws->getCell($coord);
        $cell->setValue($value);
        $cell->getStyle()->getFont()->setName('Calibri')->setSize($size)
             ->setBold($bold)->setItalic($italic)
             ->getColor()->setARGB('FF' . $fg);
        $cell->getStyle()->getAlignment()
             ->setHorizontal($hAlign)->setVertical('center')->setWrapText($wrap);
        if ($bg !== null) {
            $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)
                 ->getStartColor()->setARGB('FF' . $bg);
        }
        if ($borders !== null) {
            $cell->getStyle()->getBorders()->applyFromArray($borders);
        }
        if ($numFmt !== null) {
            $cell->getStyle()->getNumberFormat()->setFormatCode($numFmt);
        }
        return $cell;
    }

    private function medBorder(string $hex = ''): array
    {
        $hex = $hex ?: self::C['navy'];
        return ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF' . $hex]];
    }

    private function thinBorder(string $hex = 'DEE2E6'): array
    {
        return ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF' . $hex]];
    }

    // ════════════════════════════════════════════════════════════════
    // SHEET 1 — LAPORAN UTAMA
    // ════════════════════════════════════════════════════════════════
    private function sheetUtama(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws,
        $data, float $totalDebit, float $saldoTerkini, string $filterLabel
    ): void {
        $ws->setTitle('Laporan Pemasukan');
        $ws->setShowGridlines(true);

        // Kolom
        foreach (['A'=>5,'B'=>20,'C'=>14,'D'=>30,'E'=>25,'F'=>15,'G'=>18,'H'=>18,'I'=>18,'J'=>15] as $col => $w) {
            $ws->getColumnDimension($col)->setWidth($w);
        }

        // Page setup
        $ws->getPageSetup()->setPaperSize(9)->setOrientation('landscape')
            ->setFitToPage(true)->setFitToWidth(1)->setFitToHeight(0);
        $ws->getPageMargins()->setTop(0.7)->setBottom(0.7)->setLeft(0.5)->setRight(0.5)
                              ->setHeader(0.3)->setFooter(0.3);
        $ws->getHeaderFooter()
            ->setOddHeader('&C&"Calibri,Bold"&14KAS SEKOLAH — LAPORAN MUTASI PEMASUKAN SUPER ADMIN')
            ->setOddFooter('&L&"Calibri,Regular"&8Kas Instansi © ' . date('Y') .
                           '&C&"Calibri,Regular"&8Halaman &P dari &N  |  Dicetak: &D  |  RAHASIA' .
                           '&R&"Calibri,Regular"&8kassekolah.com');
        $ws->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 11);

        // Header Title
        $ws->mergeCells('A1:J1');
        $this->c($ws, 'A1', 'LAPORAN MUTASI PEMASUKAN SUPER ADMIN', bold: true, size: 16, fg: self::C['navy2'], hAlign: 'center', bg: self::C['navy_lt']);
        
        $ws->mergeCells('A2:J2');
        $this->c($ws, 'A2', 'Periode: ' . $filterLabel, bold: true, size: 12, fg: self::C['gray_tx'], hAlign: 'center');

        $ws->mergeCells('A3:J3');
        $this->c($ws, 'A3', 'Dicetak pada: ' . now()->format('d F Y, H:i:s'), size: 10, fg: self::C['gray_tx'], hAlign: 'center');

        // Row 4 Spacing
        $ws->getRowDimension(4)->setRowHeight(10);
        
        // Row 5 Info Header
        $ws->mergeCells('A5:J5');
        $this->c($ws, 'A5', 'INFORMASI PERIODE', bold: true, size: 12, fg: self::C['white'], bg: self::C['navy'], hAlign: 'center', borders: ['allBorders' => $this->thinBorder('3498DB')]);

        // Info List
        $rata2 = $data->count() > 0 ? $totalDebit / $data->count() : 0;
        $this->c($ws, 'A6', 'Jumlah Transaksi', bold: true, size: 10);
        $this->c($ws, 'B6', ': ' . $data->count() . ' transaksi', bold: true, size: 10);
        
        $this->c($ws, 'A7', 'Total Pemasukan', bold: true, size: 10);
        $this->c($ws, 'B7', ': Rp ' . number_format($totalDebit, 0, ',', '.'), bold: true, size: 10);
        
        $this->c($ws, 'A8', 'Rata-Rata / Trx', bold: true, size: 10);
        $this->c($ws, 'B8', ': Rp ' . number_format($rata2, 0, ',', '.'), bold: true, size: 10);
        
        $this->c($ws, 'A9', 'Saldo Terkini', bold: true, size: 10);
        $this->c($ws, 'B9', ': Rp ' . number_format($saldoTerkini, 0, ',', '.'), bold: true, size: 10);

        // Row 10 Spacing
        $ws->getRowDimension(10)->setRowHeight(10);

        // ── ROW 11: Table header
        $ws->getRowDimension(11)->setRowHeight(22);
        $headers = [
            'A' => ['No',             'center'],
            'B' => ['Order ID',       'left'],
            'C' => ['Tanggal',        'center'],
            'D' => ['Nama Instansi',   'left'],
            'E' => ['Pembeli',        'left'],
            'F' => ['Paket',          'center'],
            'G' => ['Debit (Rp)',     'right'],
            'H' => ['Kredit (Rp)',    'right'],
            'I' => ['Saldo Kum.(Rp)', 'right'],
            'J' => ['Status',         'center'],
        ];
        foreach ($headers as $col => [$label, $hAlign]) {
            $isEdge = $col === 'A' || $col === 'J';
            $this->c($ws, $col . '11', $label, bold: true, size: 9,
                fg: self::C['navy2'], bg: self::C['navy_lt'], hAlign: $hAlign,
                borders: [
                    'allBorders' => $this->thinBorder('DEE2E6'),
                ]);
        }

        // ── DATA ROWS
        $dataStart = 12;
        foreach ($data as $i => $m) {
            $row = $dataStart + $i;
            $ws->getRowDimension($row)->setRowHeight(18);

            $rowBg = $i % 2 === 0 ? self::C['gray_f'] : self::C['white'];
            [$pkBg, $pkFg] = self::PAKET_COLOR[$m->package_type] ?? ['F1F5F9', '374151'];

            $bdr = fn($col) => [
                'allBorders' => $this->thinBorder('DDDDDD'),
            ];

            // A: No
            $this->c($ws, 'A'.$row, $i+1, size: 9, fg: '94A3B8', bg: $rowBg, hAlign: 'center', borders: $bdr('A'));

            // B: Order ID (monospace)
            $cell = $ws->getCell('B'.$row);
            $cell->setValue($m->order_id);
            $cell->getStyle()->getFont()->setName('Consolas')->setSize(8.5)->getColor()->setARGB('FF334155');
            $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF'.$rowBg);
            $cell->getStyle()->getAlignment()->setHorizontal('left')->setVertical('center');
            $cell->getStyle()->getBorders()->applyFromArray($bdr('B'));

            // C: Tanggal (date value, not text string)
            $cell = $ws->getCell('C'.$row);
            $cell->setValue(ExcelDate::PHPToExcel($m->tanggal->timestamp));
            $cell->getStyle()->getNumberFormat()->setFormatCode('DD/MM/YYYY');
            $cell->getStyle()->getFont()->setName('Calibri')->setSize(9)->getColor()->setARGB('FF475569');
            $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF'.$rowBg);
            $cell->getStyle()->getAlignment()->setHorizontal('center')->setVertical('center');
            $cell->getStyle()->getBorders()->applyFromArray($bdr('C'));

            // D: Instansi
            $this->c($ws, 'D'.$row, $m->school_name ?? '—', bold: true, size: 9,
                fg: '1E293B', bg: $rowBg, hAlign: 'left', borders: $bdr('D'));

            // E: Pembeli
            $this->c($ws, 'E'.$row, $m->buyer_name ?? '—', size: 9,
                fg: '374151', bg: $rowBg, hAlign: 'left', borders: $bdr('E'));

            // F: Paket badge
            $this->c($ws, 'F'.$row, $m->package_label, bold: true, size: 8.5,
                fg: $pkFg, bg: $pkBg, hAlign: 'center', borders: $bdr('F'));

            // G: Debit — angka murni, format ribuan
            $cell = $ws->getCell('G'.$row);
            $cell->setValue((float) $m->debit);
            $cell->getStyle()->getNumberFormat()->setFormatCode('#,##0');
            $cell->getStyle()->getFont()->setName('Calibri')->setSize(9)->setBold(true)->getColor()->setARGB('FF' . self::C['green']);
            $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF' . self::C['green_lt']);
            $cell->getStyle()->getAlignment()->setHorizontal('right')->setVertical('center');
            $cell->getStyle()->getBorders()->applyFromArray($bdr('G'));

            // H: Kredit
            $cell = $ws->getCell('H'.$row);
            $cell->setValue(0);
            $cell->getStyle()->getNumberFormat()->setFormatCode('#,##0');
            $cell->getStyle()->getFont()->setName('Calibri')->setSize(9)->getColor()->setARGB('FF94A3B8');
            $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF'.$rowBg);
            $cell->getStyle()->getAlignment()->setHorizontal('right')->setVertical('center');
            $cell->getStyle()->getBorders()->applyFromArray($bdr('H'));

            // I: Saldo — angka murni
            $cell = $ws->getCell('I'.$row);
            $cell->setValue((float) $m->saldo);
            $cell->getStyle()->getNumberFormat()->setFormatCode('#,##0');
            $cell->getStyle()->getFont()->setName('Calibri')->setSize(9)->setBold(true)->getColor()->setARGB('FF' . self::C['blue_d']);
            $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF' . self::C['blue_lt']);
            $cell->getStyle()->getAlignment()->setHorizontal('right')->setVertical('center');
            $cell->getStyle()->getBorders()->applyFromArray($bdr('I'));

            // J: Status
            $this->c($ws, 'J'.$row, 'LUNAS', bold: true, size: 8,
                fg: '27AE60', bg: 'E8F4FD', hAlign: 'center', borders: $bdr('J'));
        }

        // ── TOTAL ROW
        $totalRow = $dataStart + $data->count();
        $ws->getRowDimension($totalRow)->setRowHeight(22);
        $ws->mergeCells('A'.$totalRow.':F'.$totalRow);

        $totalBd = ['allBorders' => $this->thinBorder('DDDDDD')];
        $this->c($ws, 'A'.$totalRow, 'TOTAL KESELURUHAN', bold: true, size: 10,
            fg: self::C['navy2'], bg: self::C['navy_lt'], hAlign: 'right', borders: $totalBd);

        // Formula SUM untuk debit
        $cell = $ws->getCell('G'.$totalRow);
        $cell->setValue("=SUM(G{$dataStart}:G" . ($totalRow-1) . ")");
        $cell->getStyle()->getNumberFormat()->setFormatCode('#,##0');
        $cell->getStyle()->getFont()->setName('Calibri')->setSize(10)->setBold(true)->getColor()->setARGB('FF' . self::C['navy2']);
        $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF' . self::C['navy_lt']);
        $cell->getStyle()->getAlignment()->setHorizontal('right')->setVertical('center');
        $cell->getStyle()->getBorders()->applyFromArray($totalBd);

        foreach (['H' => '—', 'I' => $saldoTerkini, 'J' => ''] as $col => $val) {
            $cell = $ws->getCell($col.$totalRow);
            $cell->setValue($val);
            if (is_float($val)) $cell->getStyle()->getNumberFormat()->setFormatCode('#,##0');
            $cell->getStyle()->getFont()->setName('Calibri')->setSize(10)->setBold(true)->getColor()->setARGB('FF' . self::C['navy2']);
            $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF' . self::C['navy_lt']);
            $cell->getStyle()->getAlignment()->setHorizontal($col === 'H' ? 'center' : 'right')->setVertical('center');
            $cell->getStyle()->getBorders()->applyFromArray($totalBd);
        }

        // ── CATATAN
        $noteRow = $totalRow + 2;
        $ws->getRowDimension($noteRow)->setRowHeight(13);
        $ws->mergeCells('A'.$noteRow.':J'.$noteRow);
        $this->c($ws, 'A'.$noteRow,
            '* Dokumen RAHASIA — hanya Super Admin. Angka Debit/Saldo dalam Rupiah (Rp). Dilarang didistribusikan.',
            italic: true, size: 8, fg: self::C['gray_tx'], hAlign: 'left');

        // ── Freeze + Filter
        $ws->freezePane('A'.$dataStart);
        $ws->setAutoFilter('A11:J'.($totalRow-1));
    }

    // ════════════════════════════════════════════════════════════════
    // SHEET 2 — RINGKASAN PER PAKET
    // ════════════════════════════════════════════════════════════════
    private function sheetRingkasan(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws,
        $data, float $totalDebit
    ): void {
        $ws->setShowGridlines(true);
        $ws->getPageSetup()->setPaperSize(9)->setOrientation('portrait');

        foreach (['A'=>5,'B'=>28,'C'=>18,'D'=>22,'E'=>22,'F'=>16] as $col => $w) {
            $ws->getColumnDimension($col)->setWidth($w);
        }

        $ws->getRowDimension(1)->setRowHeight(30);
        $ws->mergeCells('A1:F1');
        $this->c($ws,'A1','LAPORAN RINGKASAN PER PAKET', bold:true, size:14, fg:self::C['navy2'], bg:self::C['navy_lt'], hAlign:'center');

        $ws->getRowDimension(2)->setRowHeight(20);
        $ws->mergeCells('A2:F2');
        $this->c($ws,'A2','Dicetak pada: ' . now()->format('d F Y, H:i:s'), size:10, fg:self::C['gray_tx'], hAlign:'center');

        $ws->getRowDimension(3)->setRowHeight(10);

        // Header tabel
        $ws->getRowDimension(4)->setRowHeight(20);
        $h2 = [['No','center'],['Paket','left'],['Jml Transaksi','center'],
               ['Total Debit (Rp)','right'],['Rata-rata (Rp)','right'],['% dari Total','center']];
        foreach ($h2 as $ci => [$label, $ha]) {
            $col = chr(65 + $ci);
            $this->c($ws, $col.'4', $label, bold:true, size:9, fg:self::C['white'], bg:self::C['navy'], hAlign:$ha,
                borders:['allBorders' => $this->medBorder('FFFFFF')]);
        }

        // Group by paket
        $paketSum = [];
        foreach ($data as $m) {
            $k = $m->package_type;
            $paketSum[$k] ??= ['label' => $m->package_label, 'count' => 0, 'total' => 0];
            $paketSum[$k]['count']++;
            $paketSum[$k]['total'] += $m->debit;
        }
        $grand = array_sum(array_column($paketSum, 'total'));

        $ri = 0;
        foreach ($paketSum as $ptype => $pd) {
            $row = 5 + $ri++;
            $ws->getRowDimension($row)->setRowHeight(18);
            [$pkBg, $pkFg] = self::PAKET_COLOR[$ptype] ?? ['F1F5F9','374151'];
            $avg = $pd['count'] > 0 ? $pd['total'] / $pd['count'] : 0;
            $pct = $grand > 0 ? $pd['total'] / $grand : 0;

            $rowBg = $ri % 2 === 0 ? self::C['gray_f'] : self::C['white'];
            $cols = [
                'A' => [$ri,             null,   'center', false, $rowBg, '374151'],
                'B' => [$pd['label'],    null,   'left',   true,  $pkBg,  $pkFg],
                'C' => [$pd['count'],    '#,##0','center', false, $rowBg, '374151'],
                'D' => [(float)$pd['total'],'#,##0','right',true, self::C['green_lt'], self::C['green']],
                'E' => [(float)$avg,     '#,##0','right',  false, $rowBg, '374151'],
                'F' => [$pct,            '0.0%', 'center', false, $rowBg, '374151'],
            ];
            foreach ($cols as $col => [$val,$fmt,$ha,$bo,$bg,$fg]) {
                $cell = $ws->getCell($col.$row);
                $cell->setValue($val);
                $cell->getStyle()->getFont()->setName('Calibri')->setSize(9)->setBold($bo)->getColor()->setARGB('FF'.$fg);
                $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF'.$bg);
                $cell->getStyle()->getAlignment()->setHorizontal($ha)->setVertical('center');
                $cell->getStyle()->getBorders()->applyFromArray(['allBorders' => $this->thinBorder()]);
                if ($fmt) $cell->getStyle()->getNumberFormat()->setFormatCode($fmt);
            }
        }

        // Total row
        $tr2 = 5 + count($paketSum);
        $ws->getRowDimension($tr2)->setRowHeight(20);
        $ws->mergeCells('A'.$tr2.':B'.$tr2);
        $totalBd2 = ['allBorders' => $this->medBorder('FFFFFF')];

        foreach (['A','B','C','D','E','F'] as $col) {
            $this->c($ws, $col.$tr2, '', bold:true, size:10, fg:self::C['white'], bg:self::C['navy'], borders:$totalBd2);
        }
        $ws->getCell('A'.$tr2)->setValue('TOTAL');
        $ws->getCell('A'.$tr2)->getStyle()->getAlignment()->setHorizontal('right');

        $ws->getCell('C'.$tr2)->setValue($data->count());
        $ws->getCell('C'.$tr2)->getStyle()->getAlignment()->setHorizontal('center');

        $cell = $ws->getCell('D'.$tr2);
        $cell->setValue((float)$grand);
        $cell->getStyle()->getNumberFormat()->setFormatCode('#,##0');
        $cell->getStyle()->getAlignment()->setHorizontal('right');

        $cell = $ws->getCell('E'.$tr2);
        $cell->setValue($data->count() > 0 ? round($grand / $data->count()) : 0);
        $cell->getStyle()->getNumberFormat()->setFormatCode('#,##0');
        $cell->getStyle()->getAlignment()->setHorizontal('right');

        $cell = $ws->getCell('F'.$tr2);
        $cell->setValue(1.0);
        $cell->getStyle()->getNumberFormat()->setFormatCode('0.0%');
        $cell->getStyle()->getAlignment()->setHorizontal('center');
    }

    // ════════════════════════════════════════════════════════════════
    // SHEET 3 — REKAP BULANAN
    // ════════════════════════════════════════════════════════════════
    private function sheetBulanan(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws,
        $data
    ): void {
        $ws->setShowGridlines(true);
        $ws->getPageSetup()->setPaperSize(9)->setOrientation('portrait');

        foreach (['A'=>5,'B'=>22,'C'=>18,'D'=>22,'E'=>18,'F'=>22] as $col => $w) {
            $ws->getColumnDimension($col)->setWidth($w);
        }

        $ws->getRowDimension(1)->setRowHeight(30);
        $ws->mergeCells('A1:F1');
        $this->c($ws,'A1','LAPORAN REKAP BULANAN', bold:true, size:14, fg:self::C['navy2'], bg:self::C['navy_lt'], hAlign:'center');

        $ws->getRowDimension(2)->setRowHeight(20);
        $ws->mergeCells('A2:F2');
        $this->c($ws,'A2','Dicetak pada: ' . now()->format('d F Y, H:i:s'), size:10, fg:self::C['gray_tx'], hAlign:'center');

        $ws->getRowDimension(3)->setRowHeight(10);

        $ws->getRowDimension(4)->setRowHeight(20);
        $h3 = [['No','center'],['Bulan','left'],['Jml Transaksi','center'],
               ['Total Debit (Rp)','right'],['Kredit (Rp)','right'],['Saldo Kum. (Rp)','right']];
        foreach ($h3 as $ci => [$label, $ha]) {
            $col = chr(65 + $ci);
            $this->c($ws, $col.'4', $label, bold:true, size:9, fg:self::C['white'], bg:self::C['navy'], hAlign:$ha,
                borders:['allBorders' => $this->medBorder('FFFFFF')]);
        }

        // Group by month
        $monthly = [];
        foreach ($data as $m) {
            $key   = $m->tanggal->format('Y-m');
            $label = $m->tanggal->translatedFormat('F Y');
            $monthly[$key] ??= ['label' => $label, 'count' => 0, 'total' => 0];
            $monthly[$key]['count']++;
            $monthly[$key]['total'] += $m->debit;
        }
        ksort($monthly);

        $cum = 0;
        $ri3 = 0;
        $blnBg = [self::C['navy_lt'], self::C['white']];

        foreach ($monthly as $mdata) {
            $row3 = 5 + $ri3;
            $ws->getRowDimension($row3)->setRowHeight(18);
            $cum += $mdata['total'];
            $bg3  = $blnBg[$ri3 % 2];

            $cols3 = [
                'A' => [$ri3+1, null,   'center', false, $bg3,                 '374151'],
                'B' => [$mdata['label'], null, 'left', true, $bg3,             '1E293B'],
                'C' => [$mdata['count'], '#,##0','center',false,$bg3,          '374151'],
                'D' => [(float)$mdata['total'],'#,##0','right',true,self::C['green_lt'],self::C['green']],
                'E' => [0,               '#,##0','right', false,$bg3,          '94A3B8'],
                'F' => [(float)$cum,     '#,##0','right', true, self::C['blue_lt'],self::C['blue_d']],
            ];

            foreach ($cols3 as $col => [$val,$fmt,$ha,$bo,$bg,$fg]) {
                $cell = $ws->getCell($col.$row3);
                $cell->setValue($val);
                $cell->getStyle()->getFont()->setName('Calibri')->setSize(9)->setBold($bo)->getColor()->setARGB('FF'.$fg);
                $cell->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF'.$bg);
                $cell->getStyle()->getAlignment()->setHorizontal($ha)->setVertical('center');
                $cell->getStyle()->getBorders()->applyFromArray(['allBorders' => $this->thinBorder()]);
                if ($fmt) $cell->getStyle()->getNumberFormat()->setFormatCode($fmt);
            }
            $ri3++;
        }

        // Total row
        $tr3 = 5 + count($monthly);
        $ws->getRowDimension($tr3)->setRowHeight(20);
        $ws->mergeCells('A'.$tr3.':C'.$tr3);
        $totalBd3 = ['allBorders' => $this->medBorder('FFFFFF')];

        foreach (['A','B','C','D','E','F'] as $col) {
            $this->c($ws,$col.$tr3,'',bold:true,size:10,fg:self::C['white'],bg:self::C['navy'],borders:$totalBd3);
        }
        $ws->getCell('A'.$tr3)->setValue('TOTAL');
        $ws->getCell('A'.$tr3)->getStyle()->getAlignment()->setHorizontal('right');

        $grandBln = array_sum(array_column($monthly, 'total'));
        $ws->getCell('D'.$tr3)->setValue((float)$grandBln);
        $ws->getCell('D'.$tr3)->getStyle()->getNumberFormat()->setFormatCode('#,##0');
        $ws->getCell('D'.$tr3)->getStyle()->getAlignment()->setHorizontal('right');

        $ws->getCell('E'.$tr3)->setValue(0);
        $ws->getCell('E'.$tr3)->getStyle()->getNumberFormat()->setFormatCode('#,##0');
        $ws->getCell('E'.$tr3)->getStyle()->getAlignment()->setHorizontal('right');

        $ws->getCell('F'.$tr3)->setValue((float)$cum);
        $ws->getCell('F'.$tr3)->getStyle()->getNumberFormat()->setFormatCode('#,##0');
        $ws->getCell('F'.$tr3)->getStyle()->getAlignment()->setHorizontal('right');
    }

    private function buildFilterLabel(Request $request): string
    {
        $parts = [];
        if ($request->filled('tahun')) $parts[] = 'Tahun ' . $request->tahun;
        if ($request->filled('bulan')) {
            $bulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                      7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
            $parts[] = $bulan[$request->bulan] ?? '';
        }
        if ($request->filled('paket')) $parts[] = ucfirst($request->paket);
        return empty($parts) ? 'Semua Periode' : implode(' · ', $parts);
    }
}
