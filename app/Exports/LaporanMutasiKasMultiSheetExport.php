<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\KategoriKodeSheet;
use App\Exports\Sheets\MutasiKasSheet;
use App\Exports\Sheets\LabaRugiSheet;
use Carbon\Carbon;

class LaporanMutasiKasMultiSheetExport implements WithMultipleSheets
{
    use Exportable;

    protected $mutasi;
    protected $saldoAwal;
    protected $startDate;
    protected $endDate;
    protected $kategoris;
    
    public function __construct($mutasi, $saldoAwal, $startDate, $endDate, $kategoris)
    {
        $this->mutasi = $mutasi;
        $this->saldoAwal = $saldoAwal;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->kategoris = $kategoris;
    }
    
    public function sheets(): array
    {
        $sheets = [];
        
        // Worksheet 1: Kategori dan Kode Transaksi
        $sheets[] = new KategoriKodeSheet($this->kategoris);
        
        // Worksheet 2: Laporan Mutasi Kas
        $sheets[] = new MutasiKasSheet($this->mutasi, $this->saldoAwal, $this->startDate, $this->endDate);
        
        // Worksheet 3: Laba Rugi per Kode
        $sheets[] = new LabaRugiSheet($this->mutasi, $this->startDate, $this->endDate);
        
        return $sheets;
    }
}