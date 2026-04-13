<?php

namespace App\Http\Controllers;

use App\Models\Perencanaan;
use App\Models\Realisasi;
use App\Models\RealisasiLampiran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RealisasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Realisasi::with(['perencanaan', 'lampiran'])
            ->where('user_id', Auth::id());

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_realisasi', $request->tahun);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_realisasi', $request->bulan);
        }
        if ($request->filled('status')) {
            $query->where('status_target', $request->status);
        }

        $realisasi = $query->orderByDesc('tanggal_realisasi')->paginate(5)->withQueryString();

        $baseQuery    = Realisasi::where('user_id', Auth::id());
        $statSesuai   = (clone $baseQuery)->where('status_target', 'sesuai')->count();
        $statTidak    = (clone $baseQuery)->where('status_target', 'tidak')->count();
        $statSebagian = (clone $baseQuery)->where('status_target', 'sebagian')->count();

        $tahunList = range(date('Y') - 5, date('Y') + 1);
        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return view('realisasi.index', compact(
            'realisasi', 'tahunList', 'bulanList',
            'statSesuai', 'statTidak', 'statSebagian'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $perencanaanList = Perencanaan::where('user_id', Auth::id())
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        return view('realisasi.create', compact('perencanaanList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'perencanaan_id'        => 'required|exists:perencanaans,perencanaan_id',
            'detail_perencanaan_id' => 'nullable|exists:perencanaan_details,detail_id',
            'judul'                 => 'required|string|max:255',
            'tanggal_realisasi'     => 'required|date',
            'deskripsi'             => 'required|string',
            'status_target'         => 'required|in:sesuai,tidak,sebagian',
            'persentase'            => 'required|numeric|min:0|max:100',
            'keterangan_target'     => 'nullable|string',
            'catatan_tambahan'      => 'nullable|string',
            'lampiran.*'            => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
        ]);

        // Pastikan perencanaan milik user yang login
        Perencanaan::where('perencanaan_id', $validated['perencanaan_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // ✅ FIX: Cek apakah detail perencanaan ini sudah punya realisasi
        if (!empty($validated['detail_perencanaan_id'])) {
            $sudahAda = Realisasi::where('detail_perencanaan_id', $validated['detail_perencanaan_id'])
                ->exists();

            if ($sudahAda) {
                return back()
                    ->withInput()
                    ->withErrors(['detail_perencanaan_id' => 'Detail perencanaan ini sudah memiliki realisasi. Silakan pilih detail lain atau edit realisasi yang sudah ada.']);
            }
        }

        $realisasi = Realisasi::create([
            'perencanaan_id'        => $validated['perencanaan_id'],
            'detail_perencanaan_id' => $validated['detail_perencanaan_id'] ?? null,
            'user_id'               => Auth::id(),
            'judul'                 => $validated['judul'],
            'tanggal_realisasi'     => $validated['tanggal_realisasi'],
            'deskripsi'             => $validated['deskripsi'],
            'status_target'         => $validated['status_target'],
            'persentase'            => $validated['persentase'],
            'keterangan_target'     => $validated['keterangan_target'] ?? null,
            'catatan_tambahan'      => $validated['catatan_tambahan'] ?? null,
        ]);

        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $this->simpanLampiran($realisasi->id, $file);
            }
        }

        return redirect()->route('realisasi.show', $realisasi)
            ->with('success', 'Realisasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Realisasi $realisasi)
    {
        $this->authorizeRealisasi($realisasi);
        $realisasi->load(['perencanaan', 'detailPerencanaan', 'lampiran', 'user']);

        return view('realisasi.show', compact('realisasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Realisasi $realisasi)
    {
        $this->authorizeRealisasi($realisasi);
        $realisasi->load(['lampiran']);

        $perencanaanList = Perencanaan::where('user_id', Auth::id())
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        return view('realisasi.edit', compact('realisasi', 'perencanaanList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Realisasi $realisasi)
    {
        $this->authorizeRealisasi($realisasi);

        $validated = $request->validate([
            'perencanaan_id'        => 'required|exists:perencanaans,perencanaan_id',
            'detail_perencanaan_id' => 'nullable|exists:perencanaan_details,detail_id',
            'judul'                 => 'required|string|max:255',
            'tanggal_realisasi'     => 'required|date',
            'deskripsi'             => 'required|string',
            'status_target'         => 'required|in:sesuai,tidak,sebagian',
            'persentase'            => 'required|numeric|min:0|max:100',
            'keterangan_target'     => 'nullable|string',
            'catatan_tambahan'      => 'nullable|string',
            'lampiran.*'            => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
            'hapus_lampiran'        => 'nullable|array',
            'hapus_lampiran.*'      => 'exists:realisasi_lampiran,id',
        ]);

        // ✅ FIX: Cek duplikat saat update — kecualikan realisasi yang sedang diedit
        if (!empty($validated['detail_perencanaan_id'])) {
            $sudahAda = Realisasi::where('detail_perencanaan_id', $validated['detail_perencanaan_id'])
                ->where('id', '!=', $realisasi->id) // ← kecualikan diri sendiri
                ->exists();

            if ($sudahAda) {
                return back()
                    ->withInput()
                    ->withErrors(['detail_perencanaan_id' => 'Detail perencanaan ini sudah memiliki realisasi lain. Silakan pilih detail yang berbeda.']);
            }
        }

        $realisasi->update([
            'perencanaan_id'        => $validated['perencanaan_id'],
            'detail_perencanaan_id' => $validated['detail_perencanaan_id'] ?? null,
            'judul'                 => $validated['judul'],
            'tanggal_realisasi'     => $validated['tanggal_realisasi'],
            'deskripsi'             => $validated['deskripsi'],
            'status_target'         => $validated['status_target'],
            'persentase'            => $validated['persentase'],
            'keterangan_target'     => $validated['keterangan_target'] ?? null,
            'catatan_tambahan'      => $validated['catatan_tambahan'] ?? null,
        ]);

        // Hapus lampiran yang dipilih
        if ($request->filled('hapus_lampiran')) {
            $lampiranHapus = RealisasiLampiran::whereIn('id', $request->hapus_lampiran)
                ->where('realisasi_id', $realisasi->id)->get();
            foreach ($lampiranHapus as $lmp) {
                Storage::disk('public')->delete($lmp->path_file);
                $lmp->delete();
            }
        }

        // Upload lampiran baru
        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $this->simpanLampiran($realisasi->id, $file);
            }
        }

        return redirect()->route('realisasi.show', $realisasi)
            ->with('success', 'Realisasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Realisasi $realisasi)
    {
        $this->authorizeRealisasi($realisasi);

        foreach ($realisasi->lampiran as $lmp) {
            Storage::disk('public')->delete($lmp->path_file);
        }

        $realisasi->delete();

        return redirect()->route('realisasi.index')
            ->with('success', 'Realisasi berhasil dihapus.');
    }

    /**
     * Download lampiran file.
     */
    public function downloadLampiran(RealisasiLampiran $lampiran)
    {
        $this->authorizeRealisasi($lampiran->realisasi);

        return Storage::disk('public')->download($lampiran->path_file, $lampiran->nama_file);
    }

    /**
     * AJAX: Get detail perencanaan berdasarkan perencanaan_id.
     * Menandai detail yang sudah punya realisasi agar bisa di-disable di form.
     */
    public function getDetailPerencanaan(Request $request)
    {
        $request->validate(['perencanaan_id' => 'required|exists:perencanaans,perencanaan_id']);

        $perencanaan = Perencanaan::with('details')
            ->where('perencanaan_id', $request->perencanaan_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Ambil semua detail_id yang sudah punya realisasi
        $sudahAdaRealisasi = Realisasi::whereIn(
            'detail_perencanaan_id',
            $perencanaan->details->pluck('detail_id')
        )->pluck('detail_perencanaan_id')->toArray();

        // Tambahkan nomor urut + flag sudah_direalisasi ke setiap detail
        $details = $perencanaan->details->values()->map(function ($d, $index) use ($sudahAdaRealisasi) {
            return [
                'detail_id'         => $d->detail_id,
                'nomor'             => $index + 1,
                'perencanaan'       => $d->perencanaan,
                'target'            => $d->target,
                'deskripsi'         => $d->deskripsi,
                'pelaksanaan'       => $d->pelaksanaan,
                'sudah_direalisasi' => in_array($d->detail_id, $sudahAdaRealisasi), // ✅ flag baru
            ];
        });

        return response()->json([
            'details'     => $details,
            'perencanaan' => [
                'perencanaan_id' => $perencanaan->perencanaan_id,
                'judul'          => $perencanaan->judul,
                'bulan'          => $perencanaan->bulan,
                'tahun'          => $perencanaan->tahun,
            ],
        ]);
    }

    // ─── Private helpers ────────────────────────────────────────────────────────

    private function authorizeRealisasi(Realisasi $realisasi): void
    {
        if ($realisasi->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }

    private function simpanLampiran(int $realisasiId, $file): void
    {
        RealisasiLampiran::create([
            'realisasi_id' => $realisasiId,
            'nama_file'    => $file->getClientOriginalName(),
            'path_file'    => $file->store('realisasi/lampiran', 'public'),
            'tipe_file'    => strtolower($file->getClientOriginalExtension()),
            'ukuran_file'  => $file->getSize(),
        ]);
    }
}