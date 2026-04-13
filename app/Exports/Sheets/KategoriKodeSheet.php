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

class KategoriKodeSheet implements FromArray, WithTitle, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $kategoris;

    public function __construct($kategoris)
    {
        $this->kategoris = $kategoris;
    }

    public function array(): array
    {
        return [
            ['DAFTAR KATEGORI DAN KODE TRANSAKSI'],
            [],
        ];
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Kategori & Kode';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 3,
            'D' => 5,
            'E' => 8,
            'F' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // ===== JUDUL UTAMA — disamain dengan LabaRugiSheet =====
        $sheet->mergeCells('A1:F1');
        $sheet->getCell('A1')->setValue('DAFTAR KATEGORI DAN KODE TRANSAKSI');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 14,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color'    => ['rgb' => '2F5597'] // ← sama dengan LabaRugiSheet
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color'       => ['rgb' => '2F5597']
                ]
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30); // ← sama dengan LabaRugiSheet

        $sheet->setShowGridlines(true);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet       = $event->sheet;
                $spreadsheet = $event->sheet->getDelegate();

                // ===== HEADER KATEGORI — disamain dengan LabaRugiSheet section header =====
                $sheet->mergeCells('A3:B3');
                $spreadsheet->getCell('A3')->setValue('DAFTAR KATEGORI');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 12,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color'    => ['rgb' => '4472C4'] // ← sama dengan LabaRugiSheet
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color'       => ['rgb' => '2F5597']
                        ]
                    ]
                ]);
                $sheet->getRowDimension(3)->setRowHeight(25);

                // ===== HEADER KODE TRANSAKSI =====
                $sheet->mergeCells('D3:F3');
                $spreadsheet->getCell('D3')->setValue('DAFTAR KODE TRANSAKSI');
                $sheet->getStyle('D3')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 12,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color'    => ['rgb' => '4472C4']
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color'       => ['rgb' => '2F5597']
                        ]
                    ]
                ]);

                // ===== SUB HEADER KODE — disamain dengan LabaRugiSheet sub-header =====
                $spreadsheet->getCell('D4')->setValue('No');
                $spreadsheet->getCell('E4')->setValue('Kode');
                $spreadsheet->getCell('F4')->setValue('Keterangan');

                $sheet->getStyle('D4:F4')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 10,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color'    => ['rgb' => '5B9BD5'] // ← sama dengan LabaRugiSheet
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => '2F5597']
                        ]
                    ]
                ]);
                $sheet->getRowDimension(4)->setRowHeight(18);

                // ===== SUB HEADER KATEGORI =====
                $spreadsheet->getCell('A4')->setValue('No');
                $spreadsheet->getCell('B4')->setValue('Kategori Transaksi');

                $sheet->getStyle('A4:B4')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 10,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color'    => ['rgb' => '5B9BD5']
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => '2F5597']
                        ]
                    ]
                ]);

                // ===== WARNA UNTUK SETIAP KATEGORI =====
                $kategoriColors = [
                    'Penerimaan Pendapatan'     => ['fill' => ['rgb' => 'EEF5E8'], 'font' => ['rgb' => '3D6B27'], 'border' => ['rgb' => 'BDD9A0']],
                    'Penerimaan non Pendapatan' => ['fill' => ['rgb' => 'FDF6E3'], 'font' => ['rgb' => '7A5C00'], 'border' => ['rgb' => 'F0D080']],
                    'Pengeluaran Biaya'         => ['fill' => ['rgb' => 'FAF0EB'], 'font' => ['rgb' => '8B4513'], 'border' => ['rgb' => 'E8B89A']],
                    'Pengeluaran non Biaya'     => ['fill' => ['rgb' => 'EEF4FB'], 'font' => ['rgb' => '2E5090'], 'border' => ['rgb' => 'ACC8E5']],
                ];

                // ===== ISI DATA KATEGORI =====
                $startRow = 5;
                $kategoriNo = 1;
                $maxKategoriLength = 0;
                $kategoriData = [];

                foreach ($this->kategoris as $kategori) {
                    $kategoriName = $kategori->nama_kategori;
                    $kategoriData[] = [
                        'no'    => $kategoriNo,
                        'name'  => $kategoriName,
                        'color' => $kategoriColors[$kategoriName] ?? $kategoriColors['Penerimaan Pendapatan']
                    ];
                    $length = mb_strlen($kategoriName, 'UTF-8');
                    if ($length > $maxKategoriLength) $maxKategoriLength = $length;
                    $kategoriNo++;
                }

                foreach ($kategoriData as $data) {
                    $spreadsheet->getCell('A' . $startRow)->setValue($data['no']);
                    $sheet->getStyle('A' . $startRow)->applyFromArray([
                        'font'      => ['size' => 9, 'color' => $data['color']['font']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => $data['color']['fill']],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => $data['color']['border']]]
                    ]);
                    $spreadsheet->getCell('B' . $startRow)->setValue($data['name']);
                    $sheet->getStyle('B' . $startRow)->applyFromArray([
                        'font'      => ['size' => 9, 'color' => $data['color']['font']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => $data['color']['fill']],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => $data['color']['border']]]
                    ]);
                    $startRow++;
                }

                // ===== ISI DATA KODE =====
                $kodeStartRow = 5;
                $kodeNo = 1;
                $maxKodeLength = 0;
                $maxKetLength = 0;
                $kodeData = [];

                foreach ($this->kategoris as $kategori) {
                    $kategoriName = $kategori->nama_kategori;
                    $colorConfig  = $kategoriColors[$kategoriName] ?? $kategoriColors['Penerimaan Pendapatan'];
                    foreach ($kategori->kodeTransaksi as $kode) {
                        $kodeData[] = [
                            'no'         => $kodeNo,
                            'kode'       => $kode->kode,
                            'keterangan' => $kode->keterangan,
                            'color'      => $colorConfig
                        ];
                        $kodeLength = mb_strlen($kode->kode, 'UTF-8');
                        if ($kodeLength > $maxKodeLength) $maxKodeLength = $kodeLength;
                        $ketLength = mb_strlen($kode->keterangan, 'UTF-8');
                        if ($ketLength > $maxKetLength) $maxKetLength = $ketLength;
                        $kodeNo++;
                    }
                }

                foreach ($kodeData as $data) {
                    $spreadsheet->getCell('D' . $kodeStartRow)->setValue($data['no']);
                    $sheet->getStyle('D' . $kodeStartRow)->applyFromArray([
                        'font'      => ['size' => 9, 'color' => $data['color']['font']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => $data['color']['fill']],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => $data['color']['border']]]
                    ]);
                    $spreadsheet->getCell('E' . $kodeStartRow)->setValue($data['kode']);
                    $sheet->getStyle('E' . $kodeStartRow)->applyFromArray([
                        'font'      => ['size' => 9, 'color' => $data['color']['font']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => $data['color']['fill']],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => $data['color']['border']]]
                    ]);
                    $spreadsheet->getCell('F' . $kodeStartRow)->setValue($data['keterangan']);
                    $sheet->getStyle('F' . $kodeStartRow)->applyFromArray([
                        'font'      => ['size' => 9, 'color' => $data['color']['font']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => $data['color']['fill']],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => $data['color']['border']]]
                    ]);
                    $kodeStartRow++;
                }

                // ===== HITUNG BARIS & AUTO WIDTH =====
                $kategoriEndRow = 5 + count($this->kategoris) - 1;
                $kodeEndRow     = $kodeStartRow - 1;
                $totalRows      = max($kategoriEndRow, $kodeEndRow);

                for ($i = 5; $i <= $totalRows; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(20);
                }

                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('D')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(min(($maxKategoriLength * 1.1) + 2, 35));
                $sheet->getColumnDimension('E')->setWidth(min(($maxKodeLength * 1.1) + 2, 15));
                $sheet->getColumnDimension('F')->setWidth(min(($maxKetLength * 1.1) + 2, 50));
                $sheet->getColumnDimension('C')->setWidth(3);

                // ===== BORDER LUAR =====
                if ($kategoriEndRow >= 5) {
                    $sheet->getStyle('A4:B' . $kategoriEndRow)->applyFromArray([
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]]
                    ]);
                }
                if ($kodeEndRow >= 5) {
                    $sheet->getStyle('D4:F' . $kodeEndRow)->applyFromArray([
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]]
                    ]);
                }

                // ===== PRINT SETTINGS =====
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $maxRow = max($kategoriEndRow, $kodeEndRow, 5);
                $sheet->getPageSetup()->setPrintArea('A1:F' . $maxRow);
                $sheet->getPageMargins()->setTop(0.75);
                $sheet->getPageMargins()->setRight(0.5);
                $sheet->getPageMargins()->setLeft(0.5);
                $sheet->getPageMargins()->setBottom(0.75);
                $sheet->getPageSetup()->setHorizontalCentered(true);
                $sheet->setShowGridlines(true);

                // ===== OPTIMASI TAMPILAN =====
                $sheet->getStyle('A1:F' . $maxRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('B5:B' . $maxRow)->getAlignment()->setWrapText(true);
                $sheet->getStyle('F5:F' . $maxRow)->getAlignment()->setWrapText(true);

                $kategoriWidth = min(($maxKategoriLength * 1.1) + 2, 35);
                $ketWidth      = min(($maxKetLength * 1.1) + 2, 50);
                for ($i = 5; $i <= $maxRow; $i++) {
                    $cellB = $spreadsheet->getCell('B' . $i)->getValue();
                    $cellF = $spreadsheet->getCell('F' . $i)->getValue();
                    if ($cellB && mb_strlen($cellB, 'UTF-8') > ($kategoriWidth * 0.8)) {
                        $sheet->getRowDimension($i)->setRowHeight(-1);
                    }
                    if ($cellF && mb_strlen($cellF, 'UTF-8') > ($ketWidth * 0.8)) {
                        $sheet->getRowDimension($i)->setRowHeight(-1);
                    }
                }
            },
        ];
    }
}
