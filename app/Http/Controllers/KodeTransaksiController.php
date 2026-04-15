<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\KodeTransaksi;
use App\Models\MutasiKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class KodeTransaksiController extends Controller
{
    /**
     * Tampilkan data global + data milik user yang login.
     */
    public function index(Request $request)
    {
        $query = KodeTransaksi::with(['kategori'])
            ->visibleToUser()
            ->with(['mutasiKas' => function($q) {
                // Hanya ambil mutasi kas milik user yang login
                $q->where('user_id', Auth::id());
            }]);
        
        // Filter berdasarkan kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }
        
        // Filter berdasarkan kode
        if ($request->filled('kode')) {
            $query->where('kode', 'like', '%' . $request->kode . '%');
        }
        
        // Search berdasarkan kode atau keterangan
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('kode', 'like', '%' . $request->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $request->search . '%');
            });
        }
        
        // PENTING: Gunakan paginate() dan simpan hasilnya ke variabel
        $kodeTransaksi = $query->orderByRaw('user_id IS NULL DESC')
            ->orderBy('kode')
            ->paginate(10)
            ->withQueryString(); // Mempertahankan filter di pagination
        
        // =====================================================
        // HITUNG STATISTIK UNTUK CARDS (HANYA UNTUK USER YANG LOGIN)
        // =====================================================
        
        // Total kode transaksi (global + milik sendiri)
        $totalKode = KodeTransaksi::visibleToUser()->count();
        
        // Total kode milik user sendiri (bukan global)
        $kodeSendiri = KodeTransaksi::where('user_id', Auth::id())->count();
        
        // Total kode global yang tersedia
        $kodeGlobal = KodeTransaksi::whereNull('user_id')->count();
        
        // Total transaksi milik user yang login
        $totalTransaksi = MutasiKas::where('user_id', Auth::id())->count();
        
        // Kategori untuk filter
        $kategoris = Kategori::visibleToUser()->orderBy('nama_kategori')->get();
        
        // =====================================================
        // HITUNG TOTAL TRANSAKSI PER KODE (HANYA UNTUK USER YANG LOGIN)
        // =====================================================
        // Kita perlu menghitung total transaksi untuk setiap kode
        // Ini dilakukan dengan mengakses collection items dari paginator
        
        $items = $kodeTransaksi->getCollection(); // Ambil collection dari paginator
        
        $itemsWithStats = $items->map(function($kt) {
            // Hitung total transaksi untuk kode ini (hanya milik user yang login)
            $totalTransaksiKode = MutasiKas::where('user_id', Auth::id())
                ->where('kode_transaksi_id', $kt->kode_transaksi_id)
                ->count();
            
            // Tambahkan atribut ke model
            $kt->total_transaksi_user = $totalTransaksiKode;
            
            return $kt;
        });
        
        // Set kembali collection yang sudah dimodifikasi ke paginator
        $kodeTransaksi->setCollection($itemsWithStats);

        return view('kode_transaksi.index', compact(
            'kodeTransaksi',
            'kategoris',
            'totalKode',
            'kodeSendiri',
            'kodeGlobal',
            'totalTransaksi'
        ));
    }

    /**
     * Form create — semua user bisa buat kode transaksi sendiri.
     */
    public function create()
    {
        $kategori = Kategori::visibleToUser()->orderBy('nama_kategori')->get();

        return view('kode_transaksi.create', compact('kategori'));
    }

    /**
     * Store — simpan dengan user_id = Auth::id().
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode'        => ['required', 'string', 'max:10',
                              Rule::unique('kode_transaksi', 'kode')->where('user_id', Auth::id())],
            'keterangan'  => 'required|string|max:255',
            'kategori_id' => 'required|integer|exists:kategori,kategori_id',
        ]);

        KodeTransaksi::create([
            'user_id'     => Auth::id(),
            'kode'        => $request->kode,
            'keterangan'  => $request->keterangan,
            'kategori_id' => $request->kategori_id,
        ]);

        return redirect()->route('kode-transaksi.index')
            ->with('success', 'Kode transaksi berhasil ditambahkan.');
    }

    /**
     * Show detail.
     */
    public function show($id)
    {
        $kodeTransaksi = KodeTransaksi::visibleToUser()
            ->with(['kategori', 'mutasiKas' => function($q) {
                $q->where('user_id', Auth::id());
            }])
            ->where('kode_transaksi_id', $id)
            ->firstOrFail();

        return view('kode_transaksi.show', compact('kodeTransaksi'));
    }

    /**
     * Form edit — hanya milik sendiri atau super admin.
     */
    public function edit($id)
    {
        $kodeTransaksi = KodeTransaksi::where('kode_transaksi_id', $id)->firstOrFail();

        if (! Auth::user()->isSuperAdmin() && $kodeTransaksi->user_id !== Auth::id()) {
            if ($kodeTransaksi->isGlobal()) {
                return redirect()->route('kode-transaksi.index')
                    ->with('info', 'Data global tidak bisa diedit. Silakan buat kode transaksi baru.');
            }
            abort(403);
        }

        $kategori = Kategori::visibleToUser()->orderBy('nama_kategori')->get();

        return view('kode_transaksi.edit', compact('kodeTransaksi', 'kategori'));
    }

    /**
     * Update.
     */
    public function update(Request $request, $id)
    {
        $kodeTransaksi = KodeTransaksi::where('kode_transaksi_id', $id)->firstOrFail();

        if (! Auth::user()->isSuperAdmin() && $kodeTransaksi->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'kode' => ['required', 'string', 'max:10',
                       Rule::unique('kode_transaksi', 'kode')
                           ->where('user_id', $kodeTransaksi->user_id)
                           ->ignore($id, 'kode_transaksi_id')],
            'keterangan'  => 'required|string|max:255',
            'kategori_id' => 'required|integer|exists:kategori,kategori_id',
        ]);

        $kodeTransaksi->update([
            'kode'        => $request->kode,
            'keterangan'  => $request->keterangan,
            'kategori_id' => $request->kategori_id,
        ]);

        return redirect()->route('kode-transaksi.index')
            ->with('success', 'Kode transaksi berhasil diperbarui.');
    }

    /**
     * Destroy — hanya milik sendiri atau super admin untuk data global.
     */
    public function destroy($id)
    {
        $kodeTransaksi = KodeTransaksi::where('kode_transaksi_id', $id)->firstOrFail();

        if (! Auth::user()->isSuperAdmin() && $kodeTransaksi->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak menghapus kode transaksi ini.');
        }

        // Cek apakah kode transaksi masih digunakan di mutasi kas milik user ini
        $transaksiTerpakai = MutasiKas::where('user_id', Auth::id())
            ->where('kode_transaksi_id', $id)
            ->count();
            
        if ($transaksiTerpakai > 0) {
            return redirect()->back()->with('error', 'Kode transaksi tidak dapat dihapus karena masih digunakan dalam transaksi.');
        }

        $kodeTransaksi->delete();

        return redirect()->route('kode-transaksi.index')
            ->with('success', 'Kode transaksi berhasil dihapus.');
    }
}