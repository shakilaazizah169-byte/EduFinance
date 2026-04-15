<?php

namespace App\Http\Controllers;

use App\Models\MutasiKas;
use App\Models\SchoolSetting;
use App\Models\Kategori;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanMutasiKasMultiSheetExport;
use Illuminate\Support\Facades\DB;

class LaporanKasController extends Controller
{
    public function index(Request $request)
    {
        [$startDate, $endDate, $filterInfo] = $this->resolveFilter($request);

        // Set default values if no filter is applied
        if (!$startDate || !$endDate) {
            $startDate = null;
            $endDate = null;
            $filterInfo = null;
            
            $mutasi = collect([]);
            $saldoAwal = 0;
            $totalDebit = 0;
            $totalKredit = 0;
            $saldoAkhir = 0;
        } else {
            $mutasi = $this->getMutasi($startDate, $endDate);
            $saldoAwal = $this->getSaldoAwal($startDate);

            $saldo = $saldoAwal;
            foreach ($mutasi as $item) {
                $saldo += $item->debit - $item->kredit;
                $item->saldo_perhitungan = $saldo;
            }

            $totalDebit  = $mutasi->sum('debit');
            $totalKredit = $mutasi->sum('kredit');
            $saldoAkhir  = $saldo;
        }

        return view('laporan.mutasi', compact(
            'mutasi',
            'saldoAwal',
            'totalDebit',
            'totalKredit',
            'saldoAkhir',
            'startDate',
            'endDate',
            'filterInfo'
        ));
    }

    /**
     * 🔥 LAPORAN MENGGUNAKAN STORED PROCEDURE
     * Untuk memenuhi persyaratan uji kompetensi
     */
    public function laporanWithStoredProcedure(Request $request)
    {
        $user_id = auth()->id();
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan', 0);
        
        // Ambil nama bulan untuk ditampilkan
        $namaBulan = $bulan > 0 ? Carbon::create()->month($bulan)->translatedFormat('F') : 'Semua Bulan';
        
        try {
            // 🔥 PANGGIL STORED PROCEDURE
            $results = DB::select('CALL GetLaporanMutasiKas(?, ?, ?)', [
                $user_id, 
                $tahun, 
                $bulan
            ]);
            
            // Hitung total dari hasil stored procedure
            $totalDebit = collect($results)->sum('debit');
            $totalKredit = collect($results)->sum('kredit');
            $saldoAkhir = !empty($results) ? end($results)->saldo : 0;
            
            return view('laporan.mutasi_sp', compact(
                'results',
                'tahun',
                'bulan',
                'namaBulan',
                'totalDebit',
                'totalKredit',
                'saldoAkhir'
            ));
            
        } catch (\Exception $e) {
            // Jika stored procedure belum dibuat, tampilkan error
            return back()->with('error', 'Stored procedure belum tersedia. Jalankan migration terlebih dahulu. Error: ' . $e->getMessage());
        }
    }

    /**
     * 🔥 RINGKASAN KEUANGAN DENGAN STORED PROCEDURE
     */
    public function ringkasanWithStoredProcedure(Request $request)
    {
        $user_id = auth()->id();
        $tahun = $request->get('tahun', date('Y'));
        
        try {
            // 🔥 PANGGIL STORED PROCEDURE RINGKASAN
            $ringkasan = DB::select('CALL GetRingkasanKeuangan(?, ?)', [
                $user_id, 
                $tahun
            ]);
            
            // Format data untuk chart
            $bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $pemasukanData = array_fill(0, 12, 0);
            $pengeluaranData = array_fill(0, 12, 0);
            
            foreach ($ringkasan as $item) {
                $index = $item->bulan - 1;
                $pemasukanData[$index] = (float) $item->total_pemasukan;
                $pengeluaranData[$index] = (float) $item->total_pengeluaran;
            }
            
            return view('laporan.ringkasan_sp', compact(
                'ringkasan',
                'tahun',
                'bulanLabels',
                'pemasukanData',
                'pengeluaranData'
            ));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Stored procedure belum tersedia. Error: ' . $e->getMessage());
        }
    }

    /**
     * 🔥 CEK STATUS LISENSI DENGAN STORED PROCEDURE
     */
    public function cekLisensiWithStoredProcedure()
    {
        $user_id = auth()->id();
        
        try {
            // 🔥 PANGGIL STORED PROCEDURE CEK LISENSI
            $result = DB::select('CALL GetStatusLisensi(?)', [$user_id]);
            
            $lisensi = !empty($result) ? $result[0] : null;
            
            return view('laporan.lisensi_sp', compact('lisensi'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Stored procedure belum tersedia. Error: ' . $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        [$startDate, $endDate, $filterInfo] = $this->resolveFilter($request);

        // If no filter, get all data
        if (!$startDate || !$endDate) {
            $startDate = MutasiKas::where('user_id', auth()->id())->min('tanggal') ?? Carbon::now()->startOfMonth();
            $endDate = MutasiKas::where('user_id', auth()->id())->max('tanggal') ?? Carbon::now()->endOfMonth();
            $filterInfo = 'Semua Data';
        }

        $mutasi = $this->getMutasi($startDate, $endDate);
        $saldoAwal = $this->getSaldoAwal($startDate);

        $saldo = $saldoAwal;
        foreach ($mutasi as $item) {
            $saldo += $item->debit - $item->kredit;
            $item->saldo_perhitungan = $saldo;
        }

        $totalDebit  = $mutasi->sum('debit');
        $totalKredit = $mutasi->sum('kredit');
        $saldoAkhir  = $saldo;

        // ✅ FIX: pakai where()->first() bukan forUser() supaya benar-benar
        // fetch dari DB. forUser() pakai firstOrNew yang bisa return kosong
        // kalau ada mismatch tipe data (bigint vs int di user_id).
        $setting = SchoolSetting::where('user_id', auth()->id())->first()
                ?? new SchoolSetting();
                

        $fileName = 'Laporan-Mutasi-Kas';
        if ($filterInfo != 'Semua Data') {
            $fileName .= '-' . Carbon::parse($startDate)->format('Ymd') . '-sd-' . Carbon::parse($endDate)->format('Ymd');
        } else {
            $fileName .= '-Semua-Data';
        }
        $fileName .= '.pdf';

        $pdf = Pdf::loadView('export.mutasi_pdf', compact(
            'mutasi',
            'saldoAwal',
            'totalDebit',
            'totalKredit',
            'saldoAkhir',
            'setting',
            'startDate',
            'endDate',
            'filterInfo'
        ))
        ->setPaper('A4', 'landscape');

        return $pdf->download($fileName);
    }

    public function exportExcel(Request $request)
    {
        [$startDate, $endDate, $filterInfo] = $this->resolveFilter($request);

        // If no filter, get all data
        if (!$startDate || !$endDate) {
            $startDate = MutasiKas::where('user_id', auth()->id())->min('tanggal') ?? Carbon::now()->startOfMonth();
            $endDate = MutasiKas::where('user_id', auth()->id())->max('tanggal') ?? Carbon::now()->endOfMonth();
            $filterInfo = 'Semua Data';
        }

        $mutasi = $this->getMutasi($startDate, $endDate);
        $saldoAwal = $this->getSaldoAwal($startDate);

        $userId = auth()->id();

        // Tabel "kategori" TIDAK punya user_id — kategori bersifat global/master
        // kategori & kode_transaksi = master data global, tidak difilter per user
        $kategoris = \DB::table('kategori')
            ->orderBy('kategori_id')
            ->get()
            ->map(function($kat) {
                $kat->kodeTransaksi = \DB::table('kode_transaksi')
                    ->where('kategori_id', $kat->kategori_id)
                    ->orderBy('kode')
                    ->get();
                return $kat;
            });

        $fileName = 'Laporan-Mutasi-Kas';
        if ($filterInfo != 'Semua Data') {
            $fileName .= '-' . Carbon::parse($startDate)->format('Ymd') . '-sd-' . Carbon::parse($endDate)->format('Ymd');
        } else {
            $fileName .= '-Semua-Data';
        }
        $fileName .= '.xlsx';

        return Excel::download(
            new LaporanMutasiKasMultiSheetExport(
                $mutasi,
                $saldoAwal,
                $startDate,
                $endDate,
                $kategoris
            ),
            $fileName
        );
    }

    // ================== PRIVATE HELPERS ==================

    private function getMutasi($startDate, $endDate)
    {
        return MutasiKas::where('user_id', auth()->id())
            ->with(['kodeTransaksi.kategori'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->orderBy('mutasi_id', 'asc')
            ->get();
    }

    private function getSaldoAwal($startDate)
    {
        if (!$startDate) {
            return 0;
        }

        return MutasiKas::where('user_id', auth()->id())
            ->where('tanggal', '<', $startDate)
            ->orderByDesc('tanggal')
            ->orderByDesc('mutasi_id')
            ->value('saldo') ?? 0;
    }

    private function resolveFilter(Request $request): array
    {
        $startDate = null;
        $endDate   = null;
        $filterInfo = null;

        // Filter Bulanan
        if ($request->filter_type === 'bulan' && $request->month && $request->year) {
            $startDate = Carbon::create($request->year, $request->month, 1)->startOfMonth();
            $endDate   = Carbon::create($request->year, $request->month, 1)->endOfMonth();
            $filterInfo = 'Bulan ' . Carbon::create()->month($request->month)->translatedFormat('F') . ' ' . $request->year;
        }

        // Filter Periode
        elseif ($request->filter_type === 'periode' && $request->start_date) {
            $startDate = Carbon::parse($request->start_date);
            $endDate   = $request->end_date ? Carbon::parse($request->end_date) : $startDate->copy()->endOfMonth();
            $filterInfo = 'Periode ' . $startDate->translatedFormat('d F Y') . ' - ' . $endDate->translatedFormat('d F Y');
        }

        // Format tanggal_mulai - tanggal_akhir
        elseif ($request->tanggal_mulai && $request->tanggal_akhir) {
            $startDate = Carbon::parse($request->tanggal_mulai);
            $endDate   = Carbon::parse($request->tanggal_akhir);
            $filterInfo = 'Periode ' . $startDate->translatedFormat('d F Y') . ' - ' . $endDate->translatedFormat('d F Y');
        }

        return [$startDate, $endDate, $filterInfo];
    }
}