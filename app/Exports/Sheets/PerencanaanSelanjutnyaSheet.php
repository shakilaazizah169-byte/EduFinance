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
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PerencanaanSelanjutnyaSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    protected $endDate;
    protected $userId;

    // Bulan & tahun berikutnya (dihitung dari endDate)
    protected $bulanSelanjutnya;
    protected $tahunSelanjutnya;

    // Data yang sudah di-load
    protected $perencanaanData;

    public function __construct($endDate, $userId = null)
    {
        $this->endDate = $endDate;
        $this->userId  = $userId ?? Auth::id();
        $this->computeNextPeriod();
        $this->loadData();
    }

    // ─────────────────────────────────────────────────
    // Hitung 1 bulan setelah akhir periode filter
    // ─────────────────────────────────────────────────
    private function computeNextPeriod(): void
    {
        $next = Carbon::parse($this->endDate)->addMonth()->startOfMonth();

        $this->bulanSelanjutnya = (int) $next->format('m');
        $this->tahunSelanjutnya = (int) $next->format('Y');
    }

    // ─────────────────────────────────────────────────
    // Ambil perencanaan bulan berikutnya (tanpa realisasi)
    // ─────────────────────────────────────────────────
    private function loadData(): void
    {
        $this->perencanaanData = Perencanaan::with('details')
            ->where('user_id', $this->userId)
            ->where('bulan', $this->bulanSelanjutnya)
            ->where('tahun', $this->tahunSelanjutnya)
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();
    }

    // ─────────────────────────────────────────────────
    // Wajib ada karena FromArray, konten asli via AfterSheet
    // ─────────────────────────────────────────────────
    public function array(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Perencanaan Selanjutnya';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 12,
            'C' => 32,
            'D' => 28,
            'E' => 22,
            'F' => 22,
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

                $bulanLabel = Carbon::create($this->tahunSelanjutnya, $this->bulanSelanjutnya, 1)
                    ->translatedFormat('F Y');

                $row = 1;

                // ══════════════════════════════════════════════════
                // JUDUL UTAMA
                // ══════════════════════════════════════════════════
                $ss->mergeCells('A1:F1');
                $ss->getCell('A1')->setValue('PERENCANAAN BULAN SELANJUTNYA');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '2F5597']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2F5597']]],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // Sub-judul periode
                $row = 2;
                $ss->mergeCells('A2:F2');
                $ss->getCell('A2')->setValue('Periode Perencanaan: ' . $bulanLabel);
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(18);

                // Spacer
                $row = 3;
                $sheet->getRowDimension($row)->setRowHeight(8);
                $row = 4;

                // ══════════════════════════════════════════════════
                // SECTION HEADER — DAFTAR PERENCANAAN
                // ══════════════════════════════════════════════════
                $ss->mergeCells('A' . $row . ':F' . $row);
                $ss->getCell('A' . $row)->setValue('  DAFTAR PERENCANAAN — ' . strtoupper($bulanLabel));
                $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4472C4']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2F5597']]],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(25);
                $row++;

                // ══════════════════════════════════════════════════
                // HEADER KOLOM
                // ══════════════════════════════════════════════════
                $headers = ['No', 'Bulan/Tahun', 'Kegiatan', 'Target', 'Pelaksanaan', 'Keterangan'];
                foreach ($headers as $ci => $head) {
                    $col = chr(65 + $ci);
                    $ss->getCell($col . $row)->setValue($head);
                }
                $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '5B9BD5']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '2F5597']]],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(22);
                $row++;

                // ══════════════════════════════════════════════════
                // DATA BARIS
                // ══════════════════════════════════════════════════
                $no = 1;
                foreach ($this->perencanaanData as $perencanaan) {
                    $bulanTahun = Carbon::create($perencanaan->tahun, $perencanaan->bulan, 1)
                        ->translatedFormat('F Y');

                    foreach ($perencanaan->details as $detail) {
                        $bgColor = ($no % 2 == 0) ? 'F2F8FF' : 'FFFFFF';

                        $values = [
                            'A' => $no,
                            'B' => $bulanTahun,
                            'C' => $detail->perencanaan,
                            'D' => $detail->target,
                            'E' => $detail->pelaksanaan ?? '-',
                            'F' => $detail->deskripsi ?? '-',
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
                        $sheet->getStyle('A' . $row)->applyFromArray([
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
                        $sheet->getStyle('B' . $row)->applyFromArray([
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);

                        $sheet->getRowDimension($row)->setRowHeight(20);
                        $row++;
                        $no++;
                    }
                }

                // Jika tidak ada data
                if ($no === 1) {
                    $ss->mergeCells('A' . $row . ':F' . $row);
                    $ss->getCell('A' . $row)->setValue(
                        'Belum ada perencanaan untuk bulan ' . $bulanLabel
                    );
                    $sheet->getStyle('A' . $row)->applyFromArray([
                        'font'      => ['italic' => true, 'color' => ['rgb' => '888888']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFDE7']],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(20);
                    $row++;
                }

                $row++; // spasi

                // ══════════════════════════════════════════════════
                // SECTION RINGKASAN
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

                $totalKegiatan  = $this->perencanaanData->sum(fn($p) => $p->details->count());
                $totalJudul     = $this->perencanaanData->count();

                $ringkasanRows = [
                    ['Periode Perencanaan', $bulanLabel],
                    ['Jumlah Judul Perencanaan', $totalJudul . ' judul'],
                    ['Total Kegiatan / Detail', $totalKegiatan . ' item'],
                    ['Status Realisasi', 'Belum direalisasi'],
                ];

                foreach ($ringkasanRows as $i => [$label, $value]) {
                    $bgColor = ($i % 2 == 0) ? 'F0FFF0' : 'FFFFFF';

                    $ss->getCell('B' . $row)->setValue($label);
                    $ss->getCell('C' . $row)->setValue($value);

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

                // Catatan kaki
                $row++;
                $ss->mergeCells('A' . $row . ':F' . $row);
                $ss->getCell('A' . $row)->setValue(
                    '⚠  Data ini menampilkan perencanaan bulan ' . $bulanLabel . ' yang belum memiliki realisasi.'
                );
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '7F7F7F']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(18);

                $lastRow = $row;

                // ══════════════════════════════════════════════════
                // Print Settings
                // ══════════════════════════════════════════════════
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getPageMargins()->setTop(0.75)->setBottom(0.75)->setLeft(0.5)->setRight(0.5);
                $sheet->getPageSetup()->setHorizontalCentered(true);
                $sheet->getPageSetup()->setPrintArea('A1:F' . $lastRow);
                $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 2);
                $sheet->setShowGridlines(true);
            },
        ];
    }
}