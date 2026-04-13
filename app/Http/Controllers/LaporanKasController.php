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