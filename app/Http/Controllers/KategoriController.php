<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\KodeTransaksi;
use App\Models\MutasiKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KategoriController extends Controller
{
    /**
     * Tampilkan data global + data milik user yang login.
     */
    public function index(Request $request)
    {
        $query = Kategori::visibleToUser()
            ->with(['kodeTransaksi' => function($q) {
                $q->where('user_id', Auth::id());
            }]);
        
        // Filter berdasarkan tipe
        if ($request->filled('tipe')) {
            if ($request->tipe == 'penerimaan') {
                $query->where('nama_kategori', 'like', '%penerimaan%');
            } elseif ($request->tipe == 'pengeluaran') {
                $query->where('nama_kategori', 'like', '%pengeluaran%');
            }
        }
        
        if ($request->filled('search')) {
            $query->where('nama_kategori', 'like', '%' . $request->search . '%');
        }
        
        $kategori = $query->orderByRaw('user_id IS NULL DESC')
            ->orderBy('nama_kategori')
            ->paginate(10)
            ->withQueryString();
        
        // Hitung statistik
        $totalKategori = Kategori::visibleToUser()->count();
        $totalPenerimaan = Kategori::visibleToUser()
            ->where('nama_kategori', 'like', '%penerimaan%')
            ->count();
        $totalPengeluaran = Kategori::visibleToUser()
            ->where('nama_kategori', 'like', '%pengeluaran%')
            ->count();
        $totalKode = KodeTransaksi::where('user_id', Auth::id())->count();
        $totalTransaksiSemua = MutasiKas::where('user_id', Auth::id())->count();
        
        // Hitung total transaksi per kategori
        $items = $kategori->getCollection();
        $itemsWithStats = $items->map(function($kat) {
            $kodeTransaksiUser = $kat->kodeTransaksi()
                ->where('user_id', Auth::id())
                ->get();
            
            $totalTransaksi = 0;
            foreach ($kodeTransaksiUser as $kode) {
                $totalTransaksi += MutasiKas::where('user_id', Auth::id())
                    ->where('kode_transaksi_id', $kode->kode_transaksi_id)
                    ->count();
            }
            
            $kat->total_transaksi_user = $totalTransaksi;
            $kat->total_kode_user = $kodeTransaksiUser->count();
            
            return $kat;
        });
        
        $kategori->setCollection($itemsWithStats);

        return view('kategori.index', compact(
            'kategori', 
            'totalKategori', 
            'totalPenerimaan', 
            'totalPengeluaran', 
            'totalKode',
            'totalTransaksiSemua'
        ));
    }

    /**
     * Form tambah kategori — semua user bisa buat kategori milik sendiri.
     */
    public function create()
    {
        return view('kategori.create');
    }

    /**
     * Simpan kategori baru dengan user_id = user yang login.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => [
                'required', 'string', 'max:100',
                \Illuminate\Validation\Rule::unique('kategori', 'nama_kategori')
                    ->where('user_id', Auth::id()),
            ],
        ]);

        Kategori::create([
            'user_id'       => Auth::id(),
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Form edit — hanya bisa edit data milik sendiri (bukan global).
     */
    public function edit(int $id)
    {
        $kategori = Kategori::findOrFail($id);

        if (! Auth::user()->isSuperAdmin() && $kategori->user_id !== Auth::id()) {
            if ($kategori->isGlobal()) {
                return redirect()->route('kategori.index')
                    ->with('info', 'Data global tidak bisa diedit langsung. Buat kategori baru dengan nama yang sama untuk mengkustomisasi.');
            }
            abort(403, 'Anda tidak berhak mengedit kategori ini.');
        }

        return view('kategori.edit', compact('kategori'));
    }

    /**
     * Update — hanya bisa update data milik sendiri.
     */
    public function update(Request $request, int $id)
    {
        $kategori = Kategori::findOrFail($id);

        if (! Auth::user()->isSuperAdmin() && $kategori->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'nama_kategori' => [
                'required', 'string', 'max:100',
                \Illuminate\Validation\Rule::unique('kategori', 'nama_kategori')
                    ->where('user_id', $kategori->user_id)
                    ->ignore($id, 'kategori_id'),
            ],
        ]);

        $kategori->update(['nama_kategori' => $request->nama_kategori]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Hapus — hanya bisa hapus data milik sendiri.
     * Super admin bisa hapus data global.
     */
    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);

        if (! Auth::user()->isSuperAdmin() && $kategori->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak menghapus kategori ini.');
        }

        if ($kategori->kodeTransaksi()->count() > 0) {
            return redirect()->back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh kode transaksi.');
        }

        $kategori->delete();

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }

    /**
     * BULK DELETE - Hapus banyak kategori sekaligus dari checkbox
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = explode(',', $request->ids);
        $ids = array_filter($ids, function($id) {
            return is_numeric($id) && $id > 0;
        });

        if (empty($ids)) {
            return redirect()->route('kategori.index')->with('error', 'Tidak ada kategori yang dipilih.');
        }

        $validIds = [];
        $blockedNames = [];
        $blockedCount = 0;

        foreach ($ids as $id) {
            $kategori = Kategori::find($id);
            
            if (!$kategori) continue;

            if (!Auth::user()->isSuperAdmin() && $kategori->user_id !== Auth::id()) {
                $blockedNames[] = $kategori->nama_kategori;
                continue;
            }

            if ($kategori->kodeTransaksi()->count() > 0) {
                $blockedNames[] = $kategori->nama_kategori . ' (sedang digunakan)';
                $blockedCount++;
                continue;
            }

            $validIds[] = $id;
        }

        if (count($validIds) > 0) {
            Kategori::whereIn('kategori_id', $validIds)->delete();
            
            $message = count($validIds) . ' kategori berhasil dihapus.';
            
            if ($blockedCount > 0) {
                $message .= ' ' . $blockedCount . ' kategori tidak dapat dihapus karena masih digunakan atau bukan milik Anda.';
            }
            
            return redirect()->route('kategori.index')->with('success', $message);
        } else {
            if ($blockedCount > 0) {
                $errorMsg = 'Tidak ada kategori yang dapat dihapus. ' . 
                    implode(', ', array_slice($blockedNames, 0, 3)) . 
                    (count($blockedNames) > 3 ? ' dan ' . (count($blockedNames)-3) . ' lainnya' : '');
                
                return redirect()->route('kategori.index')->with('error', $errorMsg);
            } else {
                return redirect()->route('kategori.index')->with('error', 'Tidak ada kategori yang valid untuk dihapus.');
            }
        }
    }
}