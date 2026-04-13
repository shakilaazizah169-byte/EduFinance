<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

class MutasiKasSheet implements FromArray, WithTitle, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $mutasi;
    protected $saldoAwal;
    protected $startDate;
    protected $endDate;

    public function __construct($mutasi, $saldoAwal, $startDate, $endDate)
    {
        $this->mutasi = $mutasi;
        $this->saldoAwal = $saldoAwal;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        $saldo = $this->saldoAwal;
        $data = [];
        $totalDebit = 0;
        $totalKredit = 0;

        // Baris Saldo Awal
        $data[] = [
            '',
            Carbon::parse($this->startDate)->format('d-m-Y'),
            'Saldo Awal Periode',
            '',
            '',
            '',
            $this->saldoAwal
        ];

        // Data Transaksi
        foreach ($this->mutasi as $index => $item) {
            $saldo += $item->debit - $item->kredit;
            $totalDebit += $item->debit;
            $totalKredit += $item->kredit;

            $data[] = [
                $index + 1,
                Carbon::parse($item->tanggal)->format('d-m-Y'),
                $item->uraian,
                $item->kodeTransaksi->kode ?? '-',
                $item->debit > 0 ? $item->debit : '',
                $item->kredit > 0 ? $item->kredit : '',
                $saldo
            ];
        }

        // Baris Total
        $data[] = [
            '',
            '',
            '',
            'TOTAL',
            $totalDebit,
            $totalKredit,
            ''
        ];

        // Baris Saldo Akhir — simpan nilai numerik, diformat di AfterSheet
        $data[] = [
            '',
            '',
            '',
            '',
            '',
            'SALDO AKHIR',
            $saldo
        ];

        return $data;
    }

    public function headings(): array
    {
        return [
            ['LAPORAN MUTASI KAS'],
            ['Periode: ' . Carbon::parse($this->startDate)->format('d F Y') .
             ' s/d ' . Carbon::parse($this->endDate)->format('d F Y')],
            [],
            ['No', 'Tanggal', 'Uraian', 'Kode Transaksi', 'Masuk', 'Keluar', 'Saldo']
        ];
    }

    public function title(): string
    {
        return 'Laporan Mutasi Kas';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 12,
            'C' => 40,
            'D' => 12,
            'E' => 15,
            'F' => 15,
            'G' => 18,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $totalRows = 4 + count($this->mutasi) + 2;
        $startDataRow = 5;

        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '000000']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['size' => 11, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle('A4:G4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '366092']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ]);

        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(4)->setRowHeight(25);

        $sheet->getStyle('A' . $startDataRow . ':G' . $totalRows)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFBFBF']]]
        ]);

        $sheet->getStyle('A' . $startDataRow . ':A' . $totalRows)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('B' . $startDataRow . ':B' . $totalRows)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('D' . $startDataRow . ':D' . $totalRows)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('E' . $startDataRow . ':G' . $totalRows)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('C' . $startDataRow . ':C' . $totalRows)->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        for ($i = $startDataRow; $i <= $totalRows; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(20);
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet       = $event->sheet;
                $spreadsheet = $event->sheet->getDelegate();
                $totalRows   = 4 + count($this->mutasi) + 2;
                $startDataRow = 5;
                $dataEndRow  = $startDataRow + count($this->mutasi);
                $totalRow    = $totalRows - 1;
                $saldoRow    = $totalRows;

                // ===== FORMAT ANGKA semua baris kecuali saldo akhir =====
                // Format: Rp 900.000 (titik = pemisah ribuan, format Indonesia)
                for ($row = $startDataRow; $row <= $totalRow; $row++) {
                    foreach (['E', 'F', 'G'] as $col) {
                        $value = $spreadsheet->getCell($col . $row)->getValue();
                        if (is_numeric($value) && $value !== '') {
                            $spreadsheet->getCell($col . $row)
                                ->setValue('Rp ' . number_format((float)$value, 0, ',', '.'));
                        }
                    }
                }

                // ===== FORMAT SALDO AKHIR =====
                // Tampil: "Rp 900.000" — titik sebagai pemisah ribuan (format Indonesia)
                $saldoValue = $spreadsheet->getCell('G' . $saldoRow)->getValue();

                if (is_numeric($saldoValue) && $saldoValue !== '') {
                    $formatted = 'Rp ' . number_format((float)$saldoValue, 0, ',', '.');
                    $spreadsheet->getCell('G' . $saldoRow)->setValue($formatted);
                } elseif (is_string($saldoValue) && !str_starts_with($saldoValue, 'Rp')) {
                    $numericOnly = preg_replace('/[^0-9]/', '', $saldoValue);
                    if ($numericOnly !== '') {
                        $spreadsheet->getCell('G' . $saldoRow)
                            ->setValue('Rp ' . number_format((float)$numericOnly, 0, ',', '.'));
                    }
                }
                // Jika sudah 'Rp ...' → tidak perlu diubah

                // ===== ZEBRA STRIPING =====
                for ($row = $startDataRow + 1; $row <= $dataEndRow; $row++) {
                    $bgColor = ($row - $startDataRow) % 2 == 0 ? 'FFFFFF' : 'F2F2F2';
                    $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $bgColor]]
                    ]);
                }

                // ===== WARNA Masuk / Keluar =====
                for ($row = $startDataRow + 1; $row <= $dataEndRow; $row++) {
                    $masukValue  = $spreadsheet->getCell('E' . $row)->getValue();
                    $keluarValue = $spreadsheet->getCell('F' . $row)->getValue();

                    if ($masukValue && $masukValue != '') {
                        $sheet->getStyle('E' . $row)->applyFromArray([
                            'font' => ['color' => ['rgb' => '00B050'], 'bold' => true]
                        ]);
                    }
                    if ($keluarValue && $keluarValue != '') {
                        $sheet->getStyle('F' . $row)->applyFromArray([
                            'font' => ['color' => ['rgb' => 'FF0000'], 'bold' => true]
                        ]);
                    }
                }

                // ===== BARIS TOTAL =====
                $sheet->getStyle('A' . $totalRow . ':G' . $totalRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E7E6E6']],
                    'borders' => [
                        'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]
                    ]
                ]);

                // ===== BARIS SALDO AKHIR =====
                $sheet->getStyle('A' . $saldoRow . ':G' . $saldoRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFFFF']],
                    'borders' => [
                        'top'    => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '366092']],
                        'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '366092']],
                    ]
                ]);
                $sheet->getStyle('F' . $saldoRow)->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
                ]);
                $sheet->getStyle('G' . $saldoRow)->applyFromArray([
                    'font'      => ['color' => ['rgb' => '366092']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
                ]);

                // ===== BARIS SALDO AWAL =====
                $sheet->getStyle('A' . $startDataRow . ':G' . $startDataRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E2EFDA']]
                ]);

                // ===== PRINT SETTINGS =====
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getPageMargins()->setTop(0.75);
                $sheet->getPageMargins()->setRight(0.5);
                $sheet->getPageMargins()->setLeft(0.5);
                $sheet->getPageMargins()->setBottom(0.75);
                $sheet->getPageMargins()->setHeader(0.3);
                $sheet->getPageMargins()->setFooter(0.3);
                $sheet->getPageSetup()->setHorizontalCentered(true);
                $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 4);
                $sheet->setShowGridlines(true);
                $sheet->getPageSetup()->setPrintArea('A1:G' . $totalRows);
            },
        ];
    }
}
