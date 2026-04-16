<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\Perencanaan;
use App\Models\Realisasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PerencanaanRealisasiSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    protected $startDate;
    protected $endDate;
    protected $userId;

    // Data yang sudah dihitung
    protected $perencanaanData;
    protected $realisasiData;

    public function __construct($startDate, $endDate, $userId = null)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
        $this->userId    = $userId ?? Auth::id();
        $this->loadData();
    }

    private function loadData(): void
    {
        $start = Carbon::parse($this->startDate);
        $end   = Carbon::parse($this->endDate);

        // Filter perencanaan berdasarkan bulan & tahun (bukan created_at / tanggal apapun)
        // Pakai perbandingan numerik agar aman lintas tahun
        $startNum = (int) $start->format('Ym'); // misal 202604
        $endNum   = (int) $end->format('Ym');   // misal 202604

        $this->perencanaanData = Perencanaan::with('details')
            ->where('user_id', $this->userId)
            ->whereRaw('(tahun * 100 + bulan) >= ?', [$startNum])
            ->whereRaw('(tahun * 100 + bulan) <= ?', [$endNum])
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

        // Kumpulkan semua detail_id dari perencanaan yang masuk periode
        $detailIds = $this->perencanaanData
            ->flatMap(fn($p) => $p->details->pluck('detail_id'))
            ->filter()
            ->unique()
            ->values();

        // Realisasi diambil berdasarkan keterhubungan ke perencanaan (via detail_id),
        // BUKAN dari tanggal_realisasi — sehingga realisasi yang dilakukan di luar
        // periode tetap muncul selama perencanaannya masuk periode yang dipilih
        $this->realisasiData = Realisasi::with(['perencanaan', 'detailPerencanaan'])
            ->where('user_id', $this->userId)
            ->whereIn('detail_perencanaan_id', $detailIds)
            ->orderBy('tanggal_realisasi')
            ->get();
    }

    public function array(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Perencanaan & Realisasi';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 12,
            'C' => 30,
            'D' => 25,
            'E' => 18,
            'F' => 12,
            'G' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setShowGridlines(true);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $ss    = $event->sheet->getDelegate();

                $row = 1;

                // ══════════════════════════════════════════════════
                // JUDUL
                // ══════════════════════════════════════════════════
                $ss->mergeCells('A1:G1');
                $ss->getCell('A1')->setValue('LAPORAN PERENCANAAN & REALISASI');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '2F5597']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2F5597']]],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // Periode
                $row = 2;
                $ss->mergeCells('A2:G2');
                $ss->getCell('A2')->setValue(
                    'Periode: ' . Carbon::parse($this->startDate)->translatedFormat('d F Y') .
                    ' s/d ' . Carbon::parse($this->endDate)->translatedFormat('d F Y')
                );
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(18);

                $row = 3;
                $sheet->getRowDimension($row)->setRowHeight(8);
                $row = 4;

                // ══════════════════════════════════════════════════
                // SECTION 1 — PERENCANAAN
                // ══════════════════════════════════════════════════
                $ss->mergeCells('A' . $row . ':G' . $row);
                $ss->getCell('A' . $row)->setValue('  DAFTAR PERENCANAAN');
                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4472C4']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2F5597']]],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(25);
                $row++;

                // Header Perencanaan
                $headerP = ['No', 'Bulan/Tahun', 'Kegiatan', 'Target', 'Pelaksanaan', 'Keterangan', 'Status Realisasi'];
                foreach ($headerP as $ci => $head) {
                    $col = chr(65 + $ci); // A, B, C ...
                    $ss->getCell($col . $row)->setValue($head);
                }
                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '5B9BD5']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '2F5597']]],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(22);
                $row++;

                // Buat map detail_id → status realisasi
                $realisasiMap = $this->realisasiData->keyBy('detail_perencanaan_id');

                $no = 1;
                foreach ($this->perencanaanData as $perencanaan) {
                    $bulanTahun = Carbon::create($perencanaan->tahun, $perencanaan->bulan, 1)->translatedFormat('F Y');
                    foreach ($perencanaan->details as $detail) {
                        $bgColor = ($no % 2 == 0) ? 'F2F8FF' : 'FFFFFF';

                        $realisasi = $realisasiMap->get($detail->detail_id);
                        if ($realisasi) {
                            $statusLabel = match($realisasi->status_target) {
                                'sesuai'   => '✓ Sesuai Target',
                                'sebagian' => '~ Sebagian',
                                'tidak'    => '✗ Tidak Sesuai',
                                default    => '-',
                            };
                            $statusColor = match($realisasi->status_target) {
                                'sesuai'   => '00B050',
                                'sebagian' => 'FF9900',
                                'tidak'    => 'C00000',
                                default    => '555555',
                            };
                            $statusFill = match($realisasi->status_target) {
                                'sesuai'   => 'E2EFDA',
                                'sebagian' => 'FFF2CC',
                                'tidak'    => 'FFE7E7',
                                default    => $bgColor,
                            };
                        } else {
                            $statusLabel = '○ Belum Realisasi';
                            $statusColor = 'FF9900';
                            $statusFill  = 'FFF2CC';
                        }

                        $values = [
                            'A' => $no,
                            'B' => $bulanTahun,
                            'C' => $detail->perencanaan,
                            'D' => $detail->target,
                            'E' => $detail->pelaksanaan ?? '-',
                            'F' => $detail->deskripsi ?? '-',
                            'G' => $statusLabel,
                        ];

                        foreach ($values as $col => $val) {
                            $ss->getCell($col . $row)->setValue($val);
                        }

                        $baseStyle = [
                            'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $bgColor]],
                            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
                            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        ];

                        $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($baseStyle);
                        $sheet->getStyle('A' . $row)->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
                        $sheet->getStyle('B' . $row)->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

                        // Status kolom G warna khusus
                        $sheet->getStyle('G' . $row)->applyFromArray([
                            'font'      => ['bold' => true, 'color' => ['rgb' => $statusColor]],
                            'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $statusFill]],
                            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);

                        $sheet->getRowDimension($row)->setRowHeight(20);
                        $row++;
                        $no++;
                    }
                }

                if ($no === 1) {
                    // Tidak ada data
                    $ss->mergeCells('A' . $row . ':G' . $row);
                    $ss->getCell('A' . $row)->setValue('Tidak ada data perencanaan dalam periode ini');
                    $sheet->getStyle('A' . $row)->applyFromArray([
                        'font'      => ['italic' => true, 'color' => ['rgb' => '888888']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(20);
                    $row++;
                }

                // Ringkasan Perencanaan
                $totalPerencanaan = $this->perencanaanData->sum(fn($p) => $p->details->count());
                $sudahRealisasi   = $this->perencanaanData->sum(fn($p) => $p->details->filter(fn($d) => $realisasiMap->has($d->detail_id))->count());
                $belumRealisasi   = $totalPerencanaan - $sudahRealisasi;

                $row++; // spasi

                // ══════════════════════════════════════════════════
                // SECTION 2 — REALISASI
                // ══════════════════════════════════════════════════
                $ss->mergeCells('A' . $row . ':G' . $row);
                $ss->getCell('A' . $row)->setValue('  DAFTAR REALISASI');
                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'ED7D31']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => 'C65911']]],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(25);
                $row++;

                // Header Realisasi (7 kolom berbeda konten)
                $headerR = ['No', 'Tanggal', 'Judul Realisasi', 'Perencanaan', 'Deskripsi', '%', 'Status'];
                foreach ($headerR as $ci => $head) {
                    $col = chr(65 + $ci);
                    $ss->getCell($col . $row)->setValue($head);
                }
                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F4B084']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C65911']]],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(22);
                $row++;

                $no = 1;
                foreach ($this->realisasiData as $real) {
                    $bgColor = ($no % 2 == 0) ? 'FFF9F5' : 'FFFFFF';
                    $statusLabel = match($real->status_target) {
                        'sesuai'   => '✓ Sesuai',
                        'sebagian' => '~ Sebagian',
                        'tidak'    => '✗ Tidak',
                        default    => '-',
                    };
                    $statusColor = match($real->status_target) {
                        'sesuai'   => '00B050',
                        'sebagian' => 'FF9900',
                        'tidak'    => 'C00000',
                        default    => '555555',
                    };
                    $statusFill = match($real->status_target) {
                        'sesuai'   => 'E2EFDA',
                        'sebagian' => 'FFF2CC',
                        'tidak'    => 'FFE7E7',
                        default    => $bgColor,
                    };

                    $perencanaanJudul = optional($real->perencanaan)->judul ?? '-';
                    if ($real->detailPerencanaan) {
                        $perencanaanJudul .= ' → ' . $real->detailPerencanaan->perencanaan;
                    }

                    $vals = [
                        'A' => $no,
                        'B' => Carbon::parse($real->tanggal_realisasi)->format('d/m/Y'),
                        'C' => $real->judul,
                        'D' => $perencanaanJudul,
                        'E' => $real->deskripsi,
                        'F' => $real->persentase . '%',
                        'G' => $statusLabel,
                    ];

                    foreach ($vals as $col => $val) {
                        $ss->getCell($col . $row)->setValue($val);
                    }

                    $baseStyle = [
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $bgColor]],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ];
                    $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($baseStyle);
                    $sheet->getStyle('A' . $row)->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
                    $sheet->getStyle('B' . $row)->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
                    $sheet->getStyle('F' . $row)->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
                    $sheet->getStyle('G' . $row)->applyFromArray([
                        'font'      => ['bold' => true, 'color' => ['rgb' => $statusColor]],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $statusFill]],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(20);
                    $row++;
                    $no++;
                }

                if ($no === 1) {
                    $ss->mergeCells('A' . $row . ':G' . $row);
                    $ss->getCell('A' . $row)->setValue('Tidak ada data realisasi dalam periode ini');
                    $sheet->getStyle('A' . $row)->applyFromArray([
                        'font'      => ['italic' => true, 'color' => ['rgb' => '888888']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(20);
                    $row++;
                }

                $row++; // spasi

                // ══════════════════════════════════════════════════
                // SECTION 3 — RINGKASAN
                // ══════════════════════════════════════════════════
                $ss->mergeCells('A' . $row . ':C' . $row);
                $ss->getCell('A' . $row)->setValue('RINGKASAN');
                $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '70AD47']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '4E8A26']]],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(25);
                $row++;

                $sesuai   = $this->realisasiData->where('status_target', 'sesuai')->count();
                $sebagian = $this->realisasiData->where('status_target', 'sebagian')->count();
                $tidak    = $this->realisasiData->where('status_target', 'tidak')->count();
                $avgPersentase = $this->realisasiData->count() > 0
                    ? $this->realisasiData->avg('persentase')
                    : 0;

                $ringkasanRows = [
                    ['Total Perencanaan (detail)',     $totalPerencanaan . ' item'],
                    ['Sudah Direalisasi',              $sudahRealisasi . ' item'],
                    ['Belum Direalisasi',              $belumRealisasi . ' item'],
                    ['Total Realisasi Dicatat',        $this->realisasiData->count() . ' realisasi'],
                    ['Status Sesuai Target',           $sesuai . ' realisasi'],
                    ['Status Sebagian',                $sebagian . ' realisasi'],
                    ['Status Tidak Sesuai',            $tidak . ' realisasi'],
                    ['Rata-rata Persentase Capaian',   number_format($avgPersentase, 1) . '%'],
                ];

                foreach ($ringkasanRows as $i => [$label, $value]) {
                    $bgColor = ($i % 2 == 0) ? 'F0FFF0' : 'FFFFFF';
                    $ss->getCell('B' . $row)->setValue($label);
                    $ss->getCell('C' . $row)->setValue($value);
                    // Kolom A — indent kosong dengan warna fill yang sama
                    $sheet->getStyle('A' . $row)->applyFromArray([
                        'fill'    => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $bgColor]],
                        'borders' => [
                            'left'   => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']],
                            'top'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']],
                            'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']],
                        ],
                    ]);
                    $sheet->getStyle('B' . $row . ':C' . $row)->applyFromArray([
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $bgColor]],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getStyle('C' . $row)->applyFromArray([
                        'font'      => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(20);
                    $row++;
                }

                $lastRow = $row - 1;

                // ══════════════════════════════════════════════════
                // Print Settings
                // ══════════════════════════════════════════════════
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getPageMargins()->setTop(0.75)->setBottom(0.75)->setLeft(0.5)->setRight(0.5);
                $sheet->getPageSetup()->setHorizontalCentered(true);
                $sheet->getPageSetup()->setPrintArea('A1:G' . $lastRow);
                $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 2);
                $sheet->setShowGridlines(true);
            },
        ];
    }
}