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
use Carbon\Carbon;

class LabaRugiSheet implements FromArray, WithTitle, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $mutasi;
    protected $startDate;
    protected $endDate;

    public function __construct($mutasi, $startDate, $endDate)
    {
        $this->mutasi    = $mutasi;
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
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

    private function hitungPendapatan(): array
    {
        $pendapatan = [
            '101' => ['kode' => '101', 'uraian' => 'Pendapatan Usaha',    'jumlah' => 0],
            '102' => ['kode' => '102', 'uraian' => 'Pendapatan Lain-lain','jumlah' => 0],
            '103' => ['kode' => '103', 'uraian' => 'Bagi hasil Bank',     'jumlah' => 0],
        ];
        foreach ($this->mutasi as $item) {
            $kode = optional($item->kodeTransaksi)->kode ?? '';
            if (isset($pendapatan[$kode])) {
                $pendapatan[$kode]['jumlah'] += ($item->debit - $item->kredit);
            }
        }
        return $pendapatan;
    }

    private function hitungBeban(): array
    {
        $beban = [
            '301' => ['kode' => '301', 'uraian' => 'Biaya Promosi / Marketing',           'jumlah' => 0],
            '302' => ['kode' => '302', 'uraian' => 'Biaya Gaji, Insentif, Komisi Karyawan','jumlah' => 0],
            '303' => ['kode' => '303', 'uraian' => 'Biaya Jasa Tenaga Ahli / Vendor',      'jumlah' => 0],
            '304' => ['kode' => '304', 'uraian' => 'Biaya Packing, Angkut dan Kurir',      'jumlah' => 0],
            '305' => ['kode' => '305', 'uraian' => 'Biaya Transportasi dan kendaraan',     'jumlah' => 0],
            '306' => ['kode' => '306', 'uraian' => 'Biaya Konsumsi dan Jamuan',            'jumlah' => 0],
            '307' => ['kode' => '307', 'uraian' => 'Biaya Listrik/Telpon/Internet',        'jumlah' => 0],
            '308' => ['kode' => '308', 'uraian' => 'Biaya sewa alat/penunjang',            'jumlah' => 0],
            '309' => ['kode' => '309', 'uraian' => 'Biaya Perjalanan Dinas',               'jumlah' => 0],
            '310' => ['kode' => '310', 'uraian' => 'Biaya Administrasi, Umum, dll',        'jumlah' => 0],
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

    /**
     * Layout kolom:
     *  A  — indent / header section (merged A:D)
     *  B  — Kode
     *  C  — Uraian
     *  D  — Jumlah (Rp)
     *
     * Persis seperti foto: header section melebar A:D, data di B–D,
     * kolom A kosong di baris data (efek indent visual).
     */
    public function columnWidths(): array
    {
        return [
            'A' => 3,    // indent tipis
            'B' => 8,    // Kode
            'C' => 40,   // Uraian
            'D' => 20,   // Jumlah (Rp)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // ── Judul utama ──
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '2F5597']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '2F5597']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // ── Periode ──
        $sheet->mergeCells('A2:D2');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->setShowGridlines(true);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $ss    = $event->sheet->getDelegate();

                // ─────────────────────────────────────────────────
                // Helper: tulis baris data (B=kode, C=uraian, D=jumlah)
                // Kolom A selalu kosong → efek indent seperti foto
                // ─────────────────────────────────────────────────
                $writeDataRow = function (int $r, string $kode, string $uraian, string $jumlah, string $bgColor = 'FFFFFF') use ($sheet, $ss) {
                    $ss->getCell('B' . $r)->setValue($kode);
                    $ss->getCell('C' . $r)->setValue($uraian);
                    $ss->getCell('D' . $r)->setValue($jumlah);

                    // Kolom A — indent kosong
                    $sheet->getStyle('A' . $r)->applyFromArray([
                        'fill'    => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $bgColor]],
                        'borders' => ['left' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFBFBF']]],
                    ]);

                    $sheet->getStyle('B' . $r . ':D' . $r)->applyFromArray([
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $bgColor]],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFBFBF']]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getStyle('B' . $r)->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $sheet->getStyle('D' . $r)->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ]);
                    $sheet->getRowDimension($r)->setRowHeight(20);
                };

                // Helper: baris jumlah (total section) — A:D bold, warna summary
                $writeSummaryRow = function (int $r, string $label, string $jumlah, string $fillColor, string $borderTopColor) use ($sheet, $ss) {
                    $ss->mergeCells('B' . $r . ':C' . $r);
                    $ss->getCell('B' . $r)->setValue($label);
                    $ss->getCell('D' . $r)->setValue($jumlah);

                    // Kolom A — indent kosong dengan warna fill
                    $sheet->getStyle('A' . $r)->applyFromArray([
                        'fill'    => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $fillColor]],
                        'borders' => [
                            'left'   => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFBFBF']],
                            'top'    => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => $borderTopColor]],
                            'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFBFBF']],
                        ],
                    ]);
                    $sheet->getStyle('B' . $r . ':D' . $r)->applyFromArray([
                        'font'      => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $fillColor]],
                        'borders'   => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFBFBF']],
                            'top'        => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => $borderTopColor]],
                        ],
                    ]);
                    $sheet->getRowDimension($r)->setRowHeight(22);
                };

                // Helper: header section (B:D merged, kolom A indent kosong)
                $writeSection = function (int $r, string $label, string $fillColor, string $borderColor) use ($sheet, $ss) {
                    $ss->mergeCells('B' . $r . ':D' . $r);
                    $ss->getCell('B' . $r)->setValue($label);
                    // Kolom A — indent tipis dengan warna sama
                    $sheet->getStyle('A' . $r)->applyFromArray([
                        'fill'    => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $fillColor]],
                        'borders' => ['left' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => $borderColor]]],
                    ]);
                    $sheet->getStyle('B' . $r . ':D' . $r)->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $fillColor]],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => $borderColor]]],
                    ]);
                    $sheet->getRowDimension($r)->setRowHeight(25);
                };

                // Helper: sub-header kolom (B:D)
                $writeSubHeader = function (int $r, string $fillColor, string $borderColor) use ($sheet, $ss) {
                    $ss->getCell('B' . $r)->setValue('Kode');
                    $ss->getCell('C' . $r)->setValue('Uraian');
                    $ss->getCell('D' . $r)->setValue('Jumlah (Rp)');
                    $sheet->getStyle('B' . $r . ':D' . $r)->applyFromArray([
                        'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $fillColor]],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $borderColor]]],
                    ]);
                    // Kolom A pada sub-header — warna sama, kosong
                    $sheet->getStyle('A' . $r)->applyFromArray([
                        'fill'    => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $fillColor]],
                        'borders' => ['left' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $borderColor]]],
                    ]);
                    $sheet->getRowDimension($r)->setRowHeight(22);
                };

                // ══════════════════════════════════════════════════
                // PENDAPATAN
                // ══════════════════════════════════════════════════
                $row = 4;
                $writeSection($row, 'PENDAPATAN', '4472C4', '2F5597');
                $row++;
                $writeSubHeader($row, '5B9BD5', '2F5597');
                $row++;

                $pendapatanData  = $this->hitungPendapatan();
                $totalPendapatan = 0;

                foreach ($pendapatanData as $data) {
                    $jumlah = $data['jumlah'] > 0
                        ? 'Rp ' . number_format($data['jumlah'], 0, ',', '.')
                        : '-';
                    $bgColor = ($row % 2 == 0) ? 'FFFFFF' : 'F2F8FF';
                    $writeDataRow($row, $data['kode'], $data['uraian'], $jumlah, $bgColor);
                    $totalPendapatan += $data['jumlah'];
                    $row++;
                }

                $writeSummaryRow($row, 'Jumlah Pendapatan', 'Rp ' . number_format($totalPendapatan, 0, ',', '.'), 'E2F0D9', '2F5597');
                $row += 2;

                // ══════════════════════════════════════════════════
                // BEBAN OPERASIONAL
                // ══════════════════════════════════════════════════
                $writeSection($row, 'BEBAN OPERASIONAL', 'ED7D31', 'C65911');
                $row++;
                $writeSubHeader($row, 'F4B084', 'C65911');
                $row++;

                $bebanData  = $this->hitungBeban();
                $totalBeban = 0;

                foreach ($bebanData as $data) {
                    $jumlah = $data['jumlah'] > 0
                        ? 'Rp ' . number_format($data['jumlah'], 0, ',', '.')
                        : '-';
                    $bgColor = ($row % 2 == 0) ? 'FFFFFF' : 'FFF9F5';
                    $writeDataRow($row, $data['kode'], $data['uraian'], $jumlah, $bgColor);
                    $totalBeban += $data['jumlah'];
                    $row++;
                }

                $writeSummaryRow($row, 'Jumlah Beban', 'Rp ' . number_format($totalBeban, 0, ',', '.'), 'FCE4D6', 'C65911');
                $row += 2;

                // ══════════════════════════════════════════════════
                // LABA / RUGI OPERASIONAL
                // ══════════════════════════════════════════════════
                $labaRugi = $totalPendapatan - $totalBeban;
                $labaColor = $labaRugi >= 0 ? 'C6E0B4' : 'FFE7E7';
                $labaFontColor = $labaRugi >= 0 ? '375623' : 'C00000';
                $labaBorderColor = $labaRugi >= 0 ? '70AD47' : 'C00000';

                $ss->mergeCells('B' . $row . ':C' . $row);
                $ss->getCell('B' . $row)->setValue('Laba / Rugi Operasional');
                $ss->getCell('D' . $row)->setValue('Rp ' . number_format($labaRugi, 0, ',', '.'));
                // Kolom A — indent kosong dengan warna fill laba
                $sheet->getStyle('A' . $row)->applyFromArray([
                    'fill'    => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $labaColor]],
                    'borders' => [
                        'left'   => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFBFBF']],
                        'top'    => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']],
                        'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => $labaBorderColor]],
                    ],
                ]);
                $sheet->getStyle('B' . $row . ':D' . $row)->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => $labaFontColor]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $labaColor]],
                    'borders'   => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFBFBF']],
                        'top'        => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']],
                        'bottom'     => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => $labaBorderColor]],
                    ],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(25);

                $lastRow = $row;

                // ══════════════════════════════════════════════════
                // Print Settings
                // ══════════════════════════════════════════════════
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getPageSetup()->setPrintArea('A1:D' . $lastRow);
                $sheet->getPageMargins()->setTop(0.75)->setRight(0.75)->setLeft(0.75)->setBottom(0.75);
                $sheet->getPageMargins()->setHeader(0.3)->setFooter(0.3);
                $sheet->getPageSetup()->setHorizontalCentered(true);
                $sheet->setShowGridlines(true);
            },
        ];
    }
}