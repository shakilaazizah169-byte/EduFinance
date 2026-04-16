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
use Carbon\Carbon;

class CashRatioSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    protected $mutasi;
    protected $saldoAwal;
    protected $startDate;
    protected $endDate;

    public function __construct($mutasi, $saldoAwal, $startDate, $endDate)
    {
        $this->mutasi    = $mutasi;
        $this->saldoAwal = $saldoAwal;
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
    }

    // ── Kalkulasi ────────────────────────────────────────────────
    private function hitung(): array
    {
        // Konsisten dengan dashboard: totalDebit = sum semua nilai debit,
        // totalKredit = sum semua nilai kredit (masing-masing dari kolom berbeda)
        $totalDebit  = $this->mutasi->sum('debit');
        $totalKredit = $this->mutasi->sum('kredit');
        $saldoAkhir  = $this->saldoAwal + $totalDebit - $totalKredit;
        $perubahan   = $saldoAkhir - $this->saldoAwal;
        $persentasePerubahan = $this->saldoAwal != 0
            ? ($perubahan / $this->saldoAwal) * 100
            : 0;

        // Cash Ratio = (Total Pemasukan / Total Pengeluaran) × 100
        // Sama persis dengan formula dashboard
        $cashRatio = $totalKredit > 0
            ? ($totalDebit / $totalKredit) * 100
            : ($totalDebit > 0 ? INF : 100);

        // Saving Rate = ((Pemasukan - Pengeluaran) / Pemasukan) × 100
        $savingRate = $totalDebit > 0
            ? (($totalDebit - $totalKredit) / $totalDebit) * 100
            : 0;

        // Net Cash Flow
        $netCashFlow = $totalDebit - $totalKredit;

        // Jumlah transaksi — hitung baris yang punya nilai di kolom tsb
        $jumlahTransaksi = $this->mutasi->count();
        $jumlahDebit     = $this->mutasi->filter(fn($m) => ($m->debit ?? 0) > 0)->count();
        $jumlahKredit    = $this->mutasi->filter(fn($m) => ($m->kredit ?? 0) > 0)->count();

        return compact(
            'totalDebit', 'totalKredit', 'saldoAkhir', 'perubahan',
            'persentasePerubahan', 'cashRatio', 'savingRate',
            'netCashFlow', 'jumlahTransaksi', 'jumlahDebit', 'jumlahKredit'
        );
    }

    public function array(): array
    {
        // Data ditulis via AfterSheet agar lebih fleksibel.
        return [];
    }

    public function title(): string
    {
        return 'Cash Ratio & Kesehatan';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 4,
            'B' => 32,
            'C' => 24,
            'D' => 4,
            'E' => 32,
            'F' => 24,
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
                $ss    = $event->sheet->getDelegate(); // PhpSpreadsheet Worksheet
                $h     = $this->hitung();
                $fmt   = fn($n) => 'Rp ' . number_format($n, 0, ',', '.');

                // ══════════════════════════════════════════════════
                // ROW 1 — Judul Utama
                // ══════════════════════════════════════════════════
                $ss->mergeCells('A1:F1');
                $ss->getCell('A1')->setValue('RINGKASAN KESEHATAN KEUANGAN');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '2F5597']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2F5597']]],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // ROW 2 — Periode
                $ss->mergeCells('A2:F2');
                $ss->getCell('A2')->setValue(
                    'Periode: ' . Carbon::parse($this->startDate)->translatedFormat('d F Y') .
                    ' s/d ' . Carbon::parse($this->endDate)->translatedFormat('d F Y')
                );
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(18);

                // ROW 3 — kosong
                $sheet->getRowDimension(3)->setRowHeight(10);

                // ══════════════════════════════════════════════════
                // SECTION A — SALDO & PERUBAHAN (kiri, col A-C)
                // ══════════════════════════════════════════════════
                $row = 4;

                // Header Section A
                $ss->mergeCells('B' . $row . ':C' . $row);
                $ss->getCell('B' . $row)->setValue('SALDO & PERUBAHAN');
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'fill'    => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4472C4']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2F5597']]],
                ]);
                $sheet->getStyle('B' . $row . ':C' . $row)->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4472C4']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2F5597']]],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(24);

                // Helper untuk tulis baris 2 kolom di kiri
                $writeLeft = function (int &$r, string $label, string $value, string $fillColor = 'FFFFFF', string $fontColor = '000000', bool $bold = false) use ($sheet, $ss) {
                    $ss->getCell('B' . $r)->setValue($label);
                    $ss->getCell('C' . $r)->setValue($value);
                    $style = [
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $fillColor]],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
                    ];
                    // Kolom A — indent kosong dengan warna dan border kiri
                    $sheet->getStyle('A' . $r)->applyFromArray([
                        'fill'    => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $fillColor]],
                        'borders' => [
                            'left'   => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']],
                            'top'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']],
                            'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']],
                        ],
                    ]);
                    $sheet->getStyle('B' . $r)->applyFromArray(array_merge($style, [
                        'font' => ['bold' => $bold, 'color' => ['rgb' => '555555']],
                    ]));
                    $sheet->getStyle('C' . $r)->applyFromArray(array_merge($style, [
                        'font'      => ['bold' => $bold, 'color' => ['rgb' => $fontColor]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]));
                    $sheet->getRowDimension($r)->setRowHeight(20);
                    $r++;
                };

                $row++;
                $writeLeft($row, 'Saldo Awal',        $fmt($this->saldoAwal),    'EEF5E8', '3D6B27');
                $writeLeft($row, 'Total Pemasukan',    $fmt($h['totalDebit']),    'EEF5E8', '3D6B27');
                $writeLeft($row, 'Total Pengeluaran',  $fmt($h['totalKredit']),   'FAF0EB', 'C0392B');
                $writeLeft($row, 'Saldo Akhir',        $fmt($h['saldoAkhir']),    'E2EFDA', '1A6E1A', true);

                // Perubahan Saldo
                $perStr = ($h['perubahan'] >= 0 ? '+' : '') .
                          $fmt(abs($h['perubahan'])) .
                          ' (' . ($h['perubahan'] >= 0 ? '+' : '') .
                          number_format($h['persentasePerubahan'], 1) . '%)';
                $perubahanColor = $h['perubahan'] >= 0 ? '00B050' : 'C00000';
                $writeLeft($row, 'Perubahan Saldo', $perStr, 'F2F2F2', $perubahanColor, true);

                $row++; // spasi

                // ══════════════════════════════════════════════════
                // SECTION B — STATISTIK TRANSAKSI (kiri, lanjut)
                // ══════════════════════════════════════════════════
                $ss->mergeCells('B' . $row . ':C' . $row);
                $ss->getCell('B' . $row)->setValue('STATISTIK TRANSAKSI');
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'fill'    => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'ED7D31']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => 'C65911']]],
                ]);
                $sheet->getStyle('B' . $row . ':C' . $row)->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'ED7D31']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => 'C65911']]],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(24);
                $row++;

                $writeLeft($row, 'Total Transaksi',      $h['jumlahTransaksi'] . ' transaksi', 'FDF6E3');
                $writeLeft($row, 'Transaksi Pemasukan',  $h['jumlahDebit']  . ' transaksi', 'FDF6E3', '3D6B27');
                $writeLeft($row, 'Transaksi Pengeluaran',$h['jumlahKredit'] . ' transaksi', 'FDF6E3', 'C0392B');
                $writeLeft($row, 'Net Cash Flow',
                    ($h['netCashFlow'] >= 0 ? '+' : '') . $fmt($h['netCashFlow']),
                    'F2F2F2',
                    $h['netCashFlow'] >= 0 ? '00B050' : 'C00000',
                    true
                );

                // ══════════════════════════════════════════════════
                // SECTION C — INDIKATOR (kanan, col E-F)
                // ══════════════════════════════════════════════════
                $rightRow = 4;

                // Header Section C
                $ss->mergeCells('E' . $rightRow . ':F' . $rightRow);
                $ss->getCell('E' . $rightRow)->setValue('INDIKATOR KESEHATAN KEUANGAN');
                // Kolom D = gap/spasi antara kiri dan kanan — bersih tanpa border
                $sheet->getStyle('D' . $rightRow)->applyFromArray([
                    'fill'    => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_NONE]],
                ]);
                $sheet->getStyle('E' . $rightRow . ':F' . $rightRow)->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '70AD47']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '4E8A26']]],
                ]);
                $sheet->getRowDimension($rightRow)->setRowHeight(24);

                // Helper tulis baris kanan
                $writeRight = function (int &$r, string $label, string $value, string $fillColor = 'FFFFFF', string $fontColor = '000000', bool $bold = false) use ($sheet, $ss) {
                    $ss->getCell('E' . $r)->setValue($label);
                    $ss->getCell('F' . $r)->setValue($value);
                    $baseStyle = [
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $fillColor]],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
                    ];
                    // Kolom D — gap/spasi antara kiri dan kanan, tidak ada konten
                    $sheet->getStyle('D' . $r)->applyFromArray([
                        'fill'    => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_NONE]],
                    ]);
                    $sheet->getStyle('E' . $r)->applyFromArray(array_merge($baseStyle, [
                        'font' => ['bold' => $bold, 'color' => ['rgb' => '555555']],
                    ]));
                    $sheet->getStyle('F' . $r)->applyFromArray(array_merge($baseStyle, [
                        'font'      => ['bold' => $bold, 'color' => ['rgb' => $fontColor]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]));
                    $sheet->getRowDimension($r)->setRowHeight(20);
                    $r++;
                };

                $rightRow++;

                // Cash Ratio
                $cashRatioDisplay = is_infinite($h['cashRatio']) ? '∞ (Tidak ada pengeluaran)' : number_format($h['cashRatio'], 1) . '%';
                $cashColor = (is_infinite($h['cashRatio']) || $h['cashRatio'] >= 100) ? '00B050' : ($h['cashRatio'] >= 80 ? 'FF9900' : 'C00000');
                $cashFill  = (is_infinite($h['cashRatio']) || $h['cashRatio'] >= 100) ? 'E2EFDA' : ($h['cashRatio'] >= 80 ? 'FFF2CC' : 'FFE7E7');
                $cashLabel = (is_infinite($h['cashRatio']) || $h['cashRatio'] >= 100) ? 'Sehat (≥100%)' : ($h['cashRatio'] >= 80 ? 'Perhatian (80–99%)' : 'Kritis (<80%)');

                $writeRight($rightRow, 'Cash Ratio', $cashRatioDisplay, $cashFill, $cashColor, true);
                $writeRight($rightRow, '→ Status', $cashLabel, $cashFill, $cashColor);
                $writeRight($rightRow, '→ Keterangan', 'Debit / Kredit × 100', 'F8F8F8', '888888');

                $rightRow++; // spasi antar indikator

                // Saving Rate
                $savingDisplay = number_format($h['savingRate'], 1) . '%';
                $savingColor = $h['savingRate'] >= 20 ? '00B050' : ($h['savingRate'] >= 10 ? 'FF9900' : ($h['savingRate'] >= 0 ? 'CC6600' : 'C00000'));
                $savingFill  = $h['savingRate'] >= 20 ? 'E2EFDA' : ($h['savingRate'] >= 10 ? 'FFF2CC' : 'FFE7E7');
                $savingLabel = $h['savingRate'] >= 20 ? 'Baik (≥20%)' : ($h['savingRate'] >= 10 ? 'Cukup (10–19%)' : ($h['savingRate'] >= 0 ? 'Rendah (<10%)' : 'Defisit'));

                $writeRight($rightRow, 'Saving Rate', $savingDisplay, $savingFill, $savingColor, true);
                $writeRight($rightRow, '→ Status', $savingLabel, $savingFill, $savingColor);
                $writeRight($rightRow, '→ Keterangan', '(Debit - Kredit) / Debit × 100', 'F8F8F8', '888888');

                $rightRow++;

                // Efisiensi
                $efisiensi = $h['totalDebit'] > 0 ? ($h['totalKredit'] / $h['totalDebit']) * 100 : 0;
                $efisDisplay = number_format($efisiensi, 1) . '%';
                $efisColor = $efisiensi <= 80 ? '00B050' : ($efisiensi <= 100 ? 'FF9900' : 'C00000');
                $efisFill  = $efisiensi <= 80 ? 'E2EFDA' : ($efisiensi <= 100 ? 'FFF2CC' : 'FFE7E7');
                $efisLabel = $efisiensi <= 80 ? 'Efisien (≤80%)' : ($efisiensi <= 100 ? 'Wajar (81–100%)' : 'Boros (>100%)');

                $writeRight($rightRow, 'Rasio Pengeluaran', $efisDisplay, $efisFill, $efisColor, true);
                $writeRight($rightRow, '→ Status', $efisLabel, $efisFill, $efisColor);
                $writeRight($rightRow, '→ Keterangan', 'Kredit / Debit × 100', 'F8F8F8', '888888');

                // ══════════════════════════════════════════════════
                // SECTION D — Keterangan Indikator (bawah penuh)
                // ══════════════════════════════════════════════════
                $descRow = max($row, $rightRow) + 2;

                $ss->mergeCells('B' . $descRow . ':F' . $descRow);
                $ss->getCell('B' . $descRow)->setValue('PANDUAN INTERPRETASI INDIKATOR');
                $sheet->getStyle('A' . $descRow)->applyFromArray([
                    'fill'    => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '2F5597']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2F5597']]],
                ]);
                $sheet->getStyle('B' . $descRow . ':F' . $descRow)->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '2F5597']],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2F5597']]],
                ]);
                $sheet->getRowDimension($descRow)->setRowHeight(24);
                $descRow++;

                $panduan = [
                    ['Cash Ratio',         '>100% = Sehat: Pemasukan melebihi pengeluaran',    'E2EFDA', '3D6B27'],
                    ['Cash Ratio',         '80–99% = Perhatian: Mendekati batas aman',          'FFF2CC', '7A5C00'],
                    ['Cash Ratio',         '<80%   = Kritis: Pengeluaran melebihi pemasukan',   'FFE7E7', 'C00000'],
                    ['Saving Rate',        '>20%   = Baik: Dana tersimpan cukup banyak',        'E2EFDA', '3D6B27'],
                    ['Saving Rate',        '10–20% = Cukup: Masih bisa ditingkatkan',           'FFF2CC', '7A5C00'],
                    ['Saving Rate',        '<10%   = Rendah: Hampir seluruh dana terpakai',     'FFE7E7', 'C00000'],
                    ['Rasio Pengeluaran',  '≤80%   = Efisien: Pengeluaran terkendali dengan baik','E2EFDA', '3D6B27'],
                    ['Rasio Pengeluaran',  '81–100% = Wajar: Masih dalam batas normal',         'FFF2CC', '7A5C00'],
                    ['Rasio Pengeluaran',  '>100%  = Boros: Pengeluaran melebihi pemasukan',    'FFE7E7', 'C00000'],
                ];

                foreach ($panduan as [$indikator, $keterangan, $fill, $fontColor]) {
                    $ss->getCell('B' . $descRow)->setValue($indikator);
                    $ss->mergeCells('C' . $descRow . ':F' . $descRow);
                    $ss->getCell('C' . $descRow)->setValue($keterangan);

                    $baseStyle = [
                        'fill'    => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $fill]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ];
                    // Kolom A — indent dengan warna fill yang sama
                    $sheet->getStyle('A' . $descRow)->applyFromArray([
                        'fill'    => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $fill]],
                        'borders' => [
                            'left'   => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']],
                            'top'    => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']],
                            'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']],
                        ],
                    ]);
                    $sheet->getStyle('B' . $descRow)->applyFromArray(array_merge($baseStyle, [
                        'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => $fontColor]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]));
                    $sheet->getStyle('C' . $descRow . ':F' . $descRow)->applyFromArray(array_merge($baseStyle, [
                        'font' => ['size' => 9, 'color' => ['rgb' => $fontColor]],
                    ]));
                    $sheet->getRowDimension($descRow)->setRowHeight(18);
                    $descRow++;
                }

                // ══════════════════════════════════════════════════
                // Print settings
                // ══════════════════════════════════════════════════
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getPageMargins()->setTop(0.75)->setBottom(0.75)->setLeft(0.5)->setRight(0.5);
                $sheet->getPageSetup()->setHorizontalCentered(true);
                $sheet->getPageSetup()->setPrintArea('A1:F' . ($descRow - 1));
                $sheet->setShowGridlines(true);
            },
        ];
    }
}