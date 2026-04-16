<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\KategoriKodeSheet;
use App\Exports\Sheets\MutasiKasSheet;
use App\Exports\Sheets\LabaRugiSheet;
use App\Exports\Sheets\CashRatioSheet;
use App\Exports\Sheets\PerencanaanRealisasiSheet;
use App\Exports\Sheets\PerencanaanSelanjutnyaSheet;

class LaporanMutasiKasMultiSheetExport implements WithMultipleSheets
{
    use Exportable;

    protected $mutasi;
    protected $saldoAwal;
    protected $startDate;
    protected $endDate;
    protected $kategoris;
    protected $userId;

    public function __construct($mutasi, $saldoAwal, $startDate, $endDate, $kategoris, $userId = null)
    {
        $this->mutasi    = $mutasi;
        $this->saldoAwal = $saldoAwal;
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
        $this->kategoris = $kategoris;
        $this->userId    = $userId;
    }

    public function sheets(): array
    {
        return [
            // Sheet 1: Kategori & Kode Transaksi
            new KategoriKodeSheet($this->kategoris),

            // Sheet 2: Laporan Mutasi Kas
            new MutasiKasSheet($this->mutasi, $this->saldoAwal, $this->startDate, $this->endDate),

            // Sheet 3: Laba Rugi per Kode
            new LabaRugiSheet($this->mutasi, $this->startDate, $this->endDate),

            // Sheet 4: Cash Ratio & Kesehatan Keuangan
            new CashRatioSheet($this->mutasi, $this->saldoAwal, $this->startDate, $this->endDate),

            // Sheet 5: Perencanaan & Realisasi (sesuai periode filter)
            new PerencanaanRealisasiSheet($this->startDate, $this->endDate, $this->userId),

            // Sheet 6: Perencanaan Selanjutnya (1 bulan setelah endDate, tanpa realisasi)
            new PerencanaanSelanjutnyaSheet($this->endDate, $this->userId),
        ];
    }
}