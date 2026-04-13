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
use Carbon\Carbon;

class LabaRugiSheet implements FromArray, WithTitle, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $mutasi;
    protected $startDate;
    protected $endDate;
    
    public function __construct($mutasi, $startDate, $endDate)
    {
        $this->mutasi = $mutasi;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
    
    public function array(): array
    {
        return [
            ['LAPORAN LABA RUGI'],
            ['Periode: ' . Carbon::parse($this->startDate)->format('d F Y') . 
             ' s/d ' . Carbon::parse($this->endDate)->format('d F Y')],
            [],
        ];
    }
    
    private function hitungPendapatan()
    {
        $pendapatan = [
            '101' => ['kode' => '101', 'uraian' => 'Pendapatan Usaha', 'jumlah' => 0],
            '102' => ['kode' => '102', 'uraian' => 'Pendapatan Lain-lain', 'jumlah' => 0],
            '103' => ['kode' => '103', 'uraian' => 'Bagi hasil Bank', 'jumlah' => 0]
        ];
        
        foreach ($this->mutasi as $item) {
            $kode = optional($item->kodeTransaksi)->kode ?? '';
            if (isset($pendapatan[$kode])) {
                $pendapatan[$kode]['jumlah'] += ($item->debit - $item->kredit);
            }
        }
        
        return $pendapatan;
    }
    
    private function hitungBeban()
    {
        $beban = [
            '301' => ['kode' => '301', 'uraian' => 'Biaya Promosi / Marketing', 'jumlah' => 0],
            '302' => ['kode' => '302', 'uraian' => 'Biaya Gaji, Insentif, Komisi Karyawan', 'jumlah' => 0],
            '303' => ['kode' => '303', 'uraian' => 'Biaya Jasa Tenaga Ahli / Vendor', 'jumlah' => 0],
            '304' => ['kode' => '304', 'uraian' => 'Biaya Packing, Angkut dan Kurir', 'jumlah' => 0],
            '305' => ['kode' => '305', 'uraian' => 'Biaya Transportasi dan kendaraan', 'jumlah' => 0],
            '306' => ['kode' => '306', 'uraian' => 'Biaya Konsumsi dan Jamuan', 'jumlah' => 0],
            '307' => ['kode' => '307', 'uraian' => 'Biaya Listrik/Telpon/Internet', 'jumlah' => 0],
            '308' => ['kode' => '308', 'uraian' => 'Biaya sewa alat/penunjang', 'jumlah' => 0],
            '309' => ['kode' => '309', 'uraian' => 'Biaya Perjalanan Dinas', 'jumlah' => 0],
            '310' => ['kode' => '310', 'uraian' => 'Biaya Administrasi, Umum, dll', 'jumlah' => 0]
        ];
        
        foreach ($this->mutasi as $item) {
            $kode = optional($item->kodeTransaksi)->kode ?? '';
            if (isset($beban[$kode])) {
                $beban[$kode]['jumlah'] += ($item->kredit - $item->debit);
            }
        }
        
        return $beban;
    }
    
    public function headings(): array
    {
        return [];
    }
    
    public function title(): string
    {
        return 'Laba Rugi per Kode';
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 15,   // Kolom 1
            'B' => 10,   // Kode
            'C' => 35,   // Uraian
            'D' => 20,   // Jumlah
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        // ===== JUDUL UTAMA =====
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => '2F5597'] // Biru tua profesional
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '2F5597']
                ]
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);
        
        // ===== PERIODE =====
        $sheet->mergeCells('A2:D2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'size' => 11,
                'color' => ['rgb' => '000000']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);
        
        // Show gridlines
        $sheet->setShowGridlines(true);
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                
                // ===== PENDAPATAN =====
                $sheet->mergeCells('A4:D4');
                $sheet->getCell('A4')->setValue('PENDAPATAN');
                $sheet->getStyle('A4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '4472C4'] // Biru
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '2F5597']
                        ]
                    ]
                ]);
                $sheet->getRowDimension(4)->setRowHeight(25);
                
                // Header kolom pendapatan
                $sheet->getCell('B5')->setValue('Kode');
                $sheet->getCell('C5')->setValue('Uraian');
                $sheet->getCell('D5')->setValue('Jumlah (Rp)');
                
                $sheet->getStyle('B5:D5')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '5B9BD5'] // Biru lebih muda
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '2F5597']
                        ]
                    ]
                ]);
                
                // Data pendapatan
                $pendapatanData = $this->hitungPendapatan();
                $totalPendapatan = 0;
                $row = 6;
                
                foreach ($pendapatanData as $kode => $data) {
                    $sheet->getCell('B' . $row)->setValue($kode);
                    $sheet->getCell('C' . $row)->setValue($data['uraian']);
                    
                    if ($data['jumlah'] > 0) {
                        $formattedValue = 'Rp ' . number_format($data['jumlah'], 0, ',', '.');
                        $sheet->getCell('D' . $row)->setValue($formattedValue);
                        $totalPendapatan += $data['jumlah'];
                    } else {
                        $sheet->getCell('D' . $row)->setValue('-');
                    }
                    
                    // Warna bergantian
                    $bgColor = ($row % 2 == 0) ? ['rgb' => 'FFFFFF'] : ['rgb' => 'F8F8F8'];
                    
                    $sheet->getStyle('B' . $row . ':D' . $row)->applyFromArray([
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => $bgColor
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'BFBFBF']
                            ]
                        ]
                    ]);
                    
                    $sheet->getStyle('B' . $row)->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ],
                    ]);
                    
                    $sheet->getStyle('D' . $row)->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        ],
                    ]);
                    
                    $row++;
                }
                
                // Jumlah Pendapatan
                $jumlahPendapatanRow = $row;
                $sheet->mergeCells('B' . $jumlahPendapatanRow . ':C' . $jumlahPendapatanRow);
                $sheet->getCell('B' . $jumlahPendapatanRow)->setValue('Jumlah Pendapatan');
                $sheet->getCell('D' . $jumlahPendapatanRow)->setValue('Rp ' . number_format($totalPendapatan, 0, ',', '.'));
                
                $sheet->getStyle('B' . $jumlahPendapatanRow . ':D' . $jumlahPendapatanRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'E2F0D9'] // Hijau muda
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'BFBFBF']
                        ],
                        'top' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '2F5597']
                        ]
                    ]
                ]);
                
                // ===== BEBAN OPERASIONAL =====
                $row += 2;
                $sheet->mergeCells('A' . $row . ':D' . $row);
                $sheet->getCell('A' . $row)->setValue('BEBAN OPERASIONAL');
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'ED7D31'] // Oranye
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => 'C65911']
                        ]
                    ]
                ]);
                $sheet->getRowDimension($row)->setRowHeight(25);
                $row++;
                
                // Header kolom beban
                $sheet->getCell('B' . $row)->setValue('Kode');
                $sheet->getCell('C' . $row)->setValue('Uraian');
                $sheet->getCell('D' . $row)->setValue('Jumlah (Rp)');
                
                $sheet->getStyle('B' . $row . ':D' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'F4B084'] // Oranye muda
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'C65911']
                        ]
                    ]
                ]);
                $row++;
                
                // Data beban
                $bebanData = $this->hitungBeban();
                $totalBeban = 0;
                
                foreach ($bebanData as $kode => $data) {
                    $sheet->getCell('B' . $row)->setValue($kode);
                    $sheet->getCell('C' . $row)->setValue($data['uraian']);
                    
                    if ($data['jumlah'] > 0) {
                        $formattedValue = 'Rp ' . number_format($data['jumlah'], 0, ',', '.');
                        $sheet->getCell('D' . $row)->setValue($formattedValue);
                        $totalBeban += $data['jumlah'];
                    } else {
                        $sheet->getCell('D' . $row)->setValue('-');
                    }
                    
                    // Warna bergantian
                    $bgColor = ($row % 2 == 0) ? ['rgb' => 'FFFFFF'] : ['rgb' => 'F8F8F8'];
                    
                    $sheet->getStyle('B' . $row . ':D' . $row)->applyFromArray([
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => $bgColor
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'BFBFBF']
                            ]
                        ]
                    ]);
                    
                    $sheet->getStyle('B' . $row)->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ],
                    ]);
                    
                    $sheet->getStyle('D' . $row)->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        ],
                    ]);
                    
                    $row++;
                }
                
                // Jumlah Beban
                $jumlahBebanRow = $row;
                $sheet->mergeCells('B' . $jumlahBebanRow . ':C' . $jumlahBebanRow);
                $sheet->getCell('B' . $jumlahBebanRow)->setValue('Jumlah Beban');
                $sheet->getCell('D' . $jumlahBebanRow)->setValue('Rp ' . number_format($totalBeban, 0, ',', '.'));
                
                $sheet->getStyle('B' . $jumlahBebanRow . ':D' . $jumlahBebanRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FCE4D6'] // Merah muda
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'BFBFBF']
                        ],
                        'top' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => 'C65911']
                        ]
                    ]
                ]);
                
                // ===== LABA/RUGI OPERASIONAL =====
                $row += 2;
                $labaRugi = $totalPendapatan - $totalBeban;

                // Header Laba/Rugi - garis hitam tebal di atas
                $sheet->mergeCells('B' . $row . ':C' . $row);
                $sheet->getCell('B' . $row)->setValue('Laba/Rugi Operasional');
                $sheet->getCell('D' . $row)->setValue('Rp ' . number_format($labaRugi, 0, ',', '.'));

                $sheet->getStyle('B' . $row . ':D' . $row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'C6E0B4'] // Hijau lebih muda
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'BFBFBF']
                        ],
                        'top' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '000000'] // GARIS HITAM TEBAL DI ATAS
                        ]
                    ]
                ]);
                
                // ===== SET PRINT SETTINGS =====
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                
                // Set print area yang menyesuaikan
                $printArea = 'A1:D' . $row;
                $sheet->getPageSetup()->setPrintArea($printArea);
                
                $sheet->getPageMargins()->setTop(0.75);
                $sheet->getPageMargins()->setRight(0.75);
                $sheet->getPageMargins()->setLeft(0.75);
                $sheet->getPageMargins()->setBottom(0.75);
                $sheet->getPageMargins()->setHeader(0.3);
                $sheet->getPageMargins()->setFooter(0.3);
                
                $sheet->getPageSetup()->setHorizontalCentered(true);
                
                // Tampilkan gridlines
                $sheet->setShowGridlines(true);
            },
        ];
    }
}