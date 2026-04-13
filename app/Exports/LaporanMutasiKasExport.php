<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

class LaporanMutasiKasExport implements FromArray, WithHeadings, WithStyles, WithEvents
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
            '-',
            Carbon::parse($this->startDate)->format('d-m-Y'),
            'Saldo Awal Periode',
            '-',
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
                $item->debit > 0 ? $item->debit : null,
                $item->kredit > 0 ? $item->kredit : null,
                $saldo
            ];
        }
        
        // Baris Kosong
        $data[] = [];
        
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
        
        // Baris Saldo Akhir
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
        $totalDebit = $this->mutasi->sum('debit');
        $totalKredit = $this->mutasi->sum('kredit');
        $saldoAkhir = $this->saldoAwal + $totalDebit - $totalKredit;
        $perubahan = $saldoAkhir - $this->saldoAwal;
        
        return [
            ['LAPORAN MUTASI KAS'],
            ['Periode: ' . Carbon::parse($this->startDate)->format('d F Y') . 
            ' s/d ' . Carbon::parse($this->endDate)->format('d F Y')],
            ['Dicetak pada: ' . Carbon::now()->format('d F Y H:i:s')],
            [],
            ['INFORMASI PERIODE'],
            ['Saldo Awal', ': Rp ' . number_format($this->saldoAwal, 0, ',', '.')],
            ['Jumlah Transaksi', ': ' . $this->mutasi->count() . ' transaksi'],
            ['Total Debit', ': Rp ' . number_format($totalDebit, 0, ',', '.')],
            ['Total Kredit', ': Rp ' . number_format($totalKredit, 0, ',', '.')],
            ['Perubahan Saldo', ': ' . ($perubahan >= 0 ? '▲' : '▼') . ' Rp ' . 
                number_format(abs($perubahan), 0, ',', '.') . 
                ($this->saldoAwal != 0 ? ' (' . ($perubahan >= 0 ? '+' : '') . 
                number_format(($perubahan / $this->saldoAwal) * 100, 2) . '%)' : '')],
            [], // Baris kosong untuk jarak
            [], // Baris kosong tambahan
            ['DETAIL TRANSAKSI'],
            ['No', 'Tanggal', 'Uraian', 'Kode', 'Debit', 'Kredit', 'Saldo']
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        $lastRow = 14 + $this->mutasi->count() + 3; // Header + jarak + data + total + saldo akhir
        $startDataRow = 15; // Mulai dari row 15 (setelah header detail transaksi)
        $lastDataRow = $startDataRow + $this->mutasi->count(); // Row terakhir data transaksi
        
        // Merge cells untuk judul
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->mergeCells('A3:G3');
        $sheet->mergeCells('A5:G5');
        $sheet->mergeCells('A13:G13'); // DETAIL TRANSAKSI
        
        // Set tinggi baris
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(5)->setRowHeight(25);
        $sheet->getRowDimension(13)->setRowHeight(25); // Header detail transaksi
        $sheet->getRowDimension(14)->setRowHeight(25); // Header tabel
        $sheet->getRowDimension(11)->setRowHeight(10); // Baris kosong untuk jarak
        
        return [
            // Judul utama
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => '2C3E50']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => 'ECF0F1']
                ]
            ],
            // Periode
            2 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => '7B8A8B']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ]
            ],
            // Tanggal cetak
            3 => [
                'font' => [
                    'size' => 10,
                    'color' => ['rgb' => '95A5A6']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ]
            ],
            // Informasi Periode header
            5 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => '3498DB']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '3498DB']
                    ]
                ]
            ],
            // Informasi data (row 6-10)
            'A6:B10' => [
                'font' => [
                    'bold' => true,
                ]
            ],
            // Baris 11-12: Baris kosong untuk jarak
            11 => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ],
            12 => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ],
            // DETAIL TRANSAKSI header
            13 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => '3498DB']
                ]
            ],
            // Header tabel
            14 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '2C3E50']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => 'F8F9FA']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DEE2E6']
                    ]
                ]
            ],
            // Baris Saldo Awal
            $startDataRow => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => 'E8F4FD']
                ],
                'font' => [
                    'bold' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD']
                    ]
                ]
            ],
            // Data transaksi
            'A' . ($startDataRow + 1) . ':G' . $lastDataRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD']
                    ]
                ]
            ],
            // Baris Total
            ($lastRow - 1) => [
                'font' => [
                    'bold' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD']
                    ],
                    'top' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ],
            // Baris Saldo Akhir
            $lastRow => [
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => 'FFF3CD']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD']
                    ],
                    'top' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ],
            // Alignment untuk semua kolom
            'A' . $startDataRow . ':A' . $lastRow => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]
            ],
            'B' . $startDataRow . ':B' . $lastRow => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]
            ],
            'C' . $startDataRow . ':C' . $lastRow => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ]
            ],
            'D' . $startDataRow . ':D' . $lastRow => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]
            ],
            'E' . $startDataRow . ':G' . $lastRow => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]
            ],
        ];
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $totalRows = 14 + $this->mutasi->count() + 3;
                $startDataRow = 15;
                $lastDataRow = $startDataRow + $this->mutasi->count();
                
                // ===== ATUR LEBAR KOLOM YANG TETAP =====
                // Set lebar kolom yang konsisten (TIDAK auto-size)
                $sheet->getColumnDimension('A')->setWidth(8);    // No
                $sheet->getColumnDimension('B')->setWidth(15);   // Tanggal
                $sheet->getColumnDimension('C')->setWidth(45);   // Uraian
                $sheet->getColumnDimension('D')->setWidth(12);   // Kode
                $sheet->getColumnDimension('E')->setWidth(20);   // Debit
                $sheet->getColumnDimension('F')->setWidth(20);   // Kredit
                $sheet->getColumnDimension('G')->setWidth(20);   // Saldo
                
                // Format number untuk kolom angka (E, F, G)
                $sheet->getStyle('E' . $startDataRow . ':G' . $totalRows)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');
                
                // Tambahkan simbol Rp secara manual
                for ($row = $startDataRow; $row <= $totalRows; $row++) {
                    $cells = ['E', 'F', 'G'];
                    foreach ($cells as $col) {
                        $cellValue = $sheet->getCell($col . $row)->getValue();
                        if (is_numeric($cellValue) && $cellValue != '') {
                            $formattedValue = 'Rp ' . number_format($cellValue, 0, ',', '.');
                            $sheet->setCellValue($col . $row, $formattedValue);
                        }
                    }
                }
                
                // Warna untuk Debit (hijau) dan Kredit (merah) pada data transaksi
                for ($row = $startDataRow + 1; $row <= $lastDataRow; $row++) {
                    // Debit hijau jika ada nilai
                    $debitCell = 'E' . $row;
                    $debitValue = $sheet->getCell($debitCell)->getValue();
                    if ($debitValue && $debitValue != '') {
                        $sheet->getStyle($debitCell)->applyFromArray([
                            'font' => [
                                'color' => ['rgb' => '27AE60'], // Hijau
                                'bold' => true
                            ]
                        ]);
                    }
                    
                    // Kredit merah jika ada nilai
                    $kreditCell = 'F' . $row;
                    $kreditValue = $sheet->getCell($kreditCell)->getValue();
                    if ($kreditValue && $kreditValue != '') {
                        $sheet->getStyle($kreditCell)->applyFromArray([
                            'font' => [
                                'color' => ['rgb' => 'E74C3C'], // Merah
                                'bold' => true
                            ]
                        ]);
                    }
                }
                
                // Zebra striping untuk data transaksi
                for ($row = $startDataRow + 1; $row <= $lastDataRow; $row++) {
                    $bgColor = ($row - $startDataRow) % 2 == 0 ? 'F9F9F9' : 'FFFFFF';
                    $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['rgb' => $bgColor]
                        ]
                    ]);
                }
                
                // Format untuk baris Total dan Saldo Akhir
                $totalRow = $totalRows - 1;
                $saldoAkhirRow = $totalRows;
                
                $sheet->getStyle('E' . $totalRow . ':F' . $totalRow)->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => '2C3E50'],
                        'bold' => true
                    ]
                ]);
                
                $sheet->getStyle('G' . $saldoAkhirRow)->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => '2980B9'],
                        'bold' => true,
                        'size' => 12
                    ]
                ]);
                
                // Format untuk header informasi
                $sheet->getStyle('B6:B10')->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => '2C3E50']
                    ]
                ]);
                
                // Warna untuk perubahan saldo (row 10)
                $perubahanCell = 'B10';
                $perubahanValue = $this->mutasi->sum('debit') - $this->mutasi->sum('kredit');
                if ($perubahanValue >= 0) {
                    $sheet->getStyle($perubahanCell)->applyFromArray([
                        'font' => [
                            'color' => ['rgb' => '27AE60'], // Hijau
                            'bold' => true
                        ]
                    ]);
                } else {
                    $sheet->getStyle($perubahanCell)->applyFromArray([
                        'font' => [
                            'color' => ['rgb' => 'E74C3C'], // Merah
                            'bold' => true
                        ]
                    ]);
                }
                
                // Set wrap text untuk kolom uraian
                $sheet->getStyle('C' . $startDataRow . ':C' . $lastDataRow)
                    ->getAlignment()
                    ->setWrapText(true);
                
                // Border untuk DETAIL TRANSAKSI header
                $sheet->getStyle('A13:G13')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '3498DB']
                        ]
                    ]
                ]);
                
                // ===== SET PRINT SETTINGS =====
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);

                // Margin untuk print
                $sheet->getPageMargins()->setTop(0.75);
                $sheet->getPageMargins()->setRight(0.5);
                $sheet->getPageMargins()->setLeft(0.5);
                $sheet->getPageMargins()->setBottom(0.75);
                $sheet->getPageMargins()->setHeader(0.3);
                $sheet->getPageMargins()->setFooter(0.3);

                // Center on page horizontally
                $sheet->getPageSetup()->setHorizontalCentered(true);

                // Repeat header row on each page
                $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 4);

                // Print gridlines
                $sheet->setShowGridlines(true);

                // Set print area
                $sheet->getPageSetup()->setPrintArea('A1:G' . $totalRows);
            },
        ];
    }
}