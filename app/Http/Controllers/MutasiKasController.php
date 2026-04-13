<?php

namespace App\Http\Controllers;

use App\Models\MutasiKas;
use App\Models\KodeTransaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MutasiKasController extends Controller
{
    public function index(Request $request)
    {
        $query = MutasiKas::where('user_id', auth()->id())
            ->with(['kodeTransaksi.kategori'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('mutasi_id', 'desc');

        $filterInfo = null;
        $startDateFilter = null;
        $endDateFilter = null;

        if ($request->has('filter_type')) {

            if ($request->filter_type == 'bulan' && $request->month && $request->year) {

                $startDate = Carbon::create($request->year, $request->month, 1)->startOfMonth();
                $endDate   = Carbon::create($request->year, $request->month, 1)->endOfMonth();

                $query->whereBetween('tanggal', [$startDate, $endDate]);

                $startDateFilter = $startDate;
                $endDateFilter   = $endDate;

                $filterInfo = 'Bulan ' .
                    Carbon::create()->month($request->month)->translatedFormat('F') .
                    ' ' . $request->year;
            }

            elseif ($request->filter_type == 'periode' && $request->start_date) {

                $startDate = Carbon::parse($request->start_date);
                $endDate   = $request->end_date
                    ? Carbon::parse($request->end_date)
                    : $startDate->copy()->endOfMonth();

                $query->whereBetween('tanggal', [$startDate, $endDate]);

                $startDateFilter = $startDate;
                $endDateFilter   = $endDate;

                $filterInfo = 'Periode ' .
                    $startDate->translatedFormat('d F Y') .
                    ' - ' .
                    $endDate->translatedFormat('d F Y');
            }
        }

        $mutasi = $query->paginate(10);

        $statQuery = MutasiKas::where('user_id', auth()->id());

        if ($startDateFilter && $endDateFilter) {
            $statQuery->whereBetween('tanggal', [$startDateFilter, $endDateFilter]);
        }

        $totalDebit  = $statQuery->sum('debit');
        $totalKredit = $statQuery->sum('kredit');
        $saldoAkhir  = $totalDebit - $totalKredit;

        $currentYear = now()->year;
        $years = range($currentYear - 2, $currentYear + 5);

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::create()->month($i)->translatedFormat('F');
        }

        return view('mutasi_kas.index', compact(
            'mutasi',
            'totalDebit',
            'totalKredit',
            'saldoAkhir',
            'filterInfo',
            'months',
            'years'
        ));
    }

    public function create()
    {
        // ✅ FIX: pakai scopeVisibleToUser — tampilkan kode global (null) + milik user
        $kodeTransaksi = KodeTransaksi::visibleToUser()
            ->with('kategori')
            ->orderBy('kode')
            ->get();

        $saldoAkhir = MutasiKas::where('user_id', auth()->id())
            ->orderBy('mutasi_id', 'desc')
            ->value('saldo') ?? 0;

        return view('mutasi_kas.create', compact('kodeTransaksi', 'saldoAkhir'));
    }

    public function store(Request $request)
    {
        // ✅ FIX: strip titik pemisah ribuan sebelum validasi
        // Input "900.000" atau "1.200.000" → jadi "900000" / "1200000"
        // Ini terjadi kalau form pakai format rupiah dengan titik sebagai ribuan
        $request->merge([
            'debit'  => $request->debit  ? (float) str_replace('.', '', $request->debit)  : null,
            'kredit' => $request->kredit ? (float) str_replace('.', '', $request->kredit) : null,
        ]);

        $request->validate([
            'tanggal'           => 'required|date',
            'kode_transaksi_id' => 'required|integer',
            'uraian'            => 'required|string|max:255',
            'debit'             => 'nullable|numeric|min:0',
            'kredit'            => 'nullable|numeric|min:0',
        ]);

        if ($request->debit && $request->kredit) {
            return back()
                ->withErrors(['debit' => 'Debit dan kredit tidak boleh diisi bersamaan'])
                ->withInput();
        }

        if (!$request->debit && !$request->kredit) {
            return back()
                ->withErrors(['debit' => 'Debit atau kredit harus diisi'])
                ->withInput();
        }

        // ✅ FIX: gunakan scopeVisibleToUser — kode global (user_id NULL) juga valid
        $kodeTransaksi = KodeTransaksi::visibleToUser()
            ->where('kode_transaksi_id', $request->kode_transaksi_id)
            ->first();

        if (!$kodeTransaksi) {
            return back()
                ->withErrors(['kode_transaksi_id' => 'Kode transaksi tidak valid atau bukan milik Anda'])
                ->withInput();
        }

        $mutasi = MutasiKas::create([
            'user_id'           => auth()->id(),
            'tanggal'           => $request->tanggal,
            'kode_transaksi_id' => $kodeTransaksi->kode_transaksi_id,
            'uraian'            => $request->uraian,
            'debit'             => $request->debit ?? 0,
            'kredit'            => $request->kredit ?? 0,
            'saldo'             => 0,
        ]);

        $this->recalculateSaldoForUser();

        return redirect()->route('mutasi-kas.index')
            ->with('success', 'Transaksi berhasil disimpan');
    }

    private function recalculateSaldoForUser()
    {
        $transactions = MutasiKas::where('user_id', auth()->id())
            ->orderBy('tanggal', 'asc')
            ->orderBy('mutasi_id', 'asc')
            ->get();

        $saldo = 0;

        foreach ($transactions as $trx) {
            $saldo += $trx->debit - $trx->kredit;
            $trx->update(['saldo' => $saldo]);
        }
    }
}