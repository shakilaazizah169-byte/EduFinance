<?php

namespace App\Http\Controllers;

use App\Models\Perencanaan;
use App\Models\PerencanaanDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PerencanaanController extends Controller
{
    /* =====================================================
     * INDEX
     * ===================================================== */
    public function index(Request $request) // Tambah Request $request
    {
        $query = Perencanaan::where('user_id', auth()->id())
            ->with('details');
        
        // Filter berdasarkan bulan
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        
        // Filter berdasarkan tahun
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        
        // Search berdasarkan judul
        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }
        
        $perencanaan = $query->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->paginate(5)
            ->withQueryString(); // Penting: menyimpan parameter filter di pagination

        $months = $this->getMonths();
        $years  = range(now()->year - 2, now()->year + 1);
        
        // Hitung statistik untuk cards
        $totalRencana = $query->get()->sum(function($p) {
            return $p->details->count();
        });
        
        $totalDetail = PerencanaanDetail::whereHas('perencanaan', function($q) {
            $q->where('user_id', auth()->id());
        })->count();
        
        $thisYearCount = Perencanaan::where('user_id', auth()->id())
            ->where('tahun', now()->year)
            ->count();

        return view('perencanaan.index', compact('perencanaan', 'months', 'years', 'totalRencana', 'totalDetail', 'thisYearCount'));
    }

    /* =====================================================
     * CREATE
     * ===================================================== */
    public function create()
    {
        $months = $this->getMonths();
        $years  = range(now()->year, now()->year + 1);

        return view('perencanaan.create', compact('months', 'years'));
    }

    /* =====================================================
     * STORE
     * ===================================================== */
    public function store(Request $request)
    {
        $request->validate($this->validationRules(), $this->validationMessages());

        // Cek duplikat hanya untuk user ini
        $existing = Perencanaan::where('user_id', auth()->id())
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->exists();

        if ($existing) {
            $this->saveInputToSession($request);

            return back()
                ->withErrors(['bulan' => 'Sudah ada perencanaan untuk bulan dan tahun tersebut'])
                ->withInput();
        }

        $perencanaan = Perencanaan::create([
            'user_id' => auth()->id(),
            'judul'   => $request->judul,
            'bulan'   => $request->bulan,
            'tahun'   => $request->tahun,
        ]);

        foreach ($request->perencanaan as $index => $rencana) {
            $perencanaan->details()->create([
                'user_id'     => auth()->id(),
                'perencanaan' => $rencana,
                'target'      => $request->target[$index],
                'deskripsi'   => $request->deskripsi[$index] ?? null,
                'pelaksanaan' => $request->pelaksanaan[$index] ?? null,
            ]);
        }

        session()->forget('old_details');

        return redirect()->route('perencanaan.index')
            ->with('success', 'Perencanaan berhasil disimpan');
    }

    /* =====================================================
     * SHOW
     * ===================================================== */
    public function show(Perencanaan $perencanaan)
    {
        $this->authorize403($perencanaan);

        $perencanaan->load('details');
        $monthName = Carbon::create()->month($perencanaan->bulan)->translatedFormat('F');

        return view('perencanaan.show', compact('perencanaan', 'monthName'));
    }

    /* =====================================================
     * EDIT
     * ===================================================== */
    public function edit(Perencanaan $perencanaan)
    {
        $this->authorize403($perencanaan);

        $perencanaan->load('details');

        $months = $this->getMonths();
        $years  = range(now()->year, now()->year + 1);

        return view('perencanaan.edit', compact('perencanaan', 'months', 'years'));
    }

    /* =====================================================
     * UPDATE
     * ===================================================== */
    public function update(Request $request, Perencanaan $perencanaan)
    {
        $this->authorize403($perencanaan);

        $request->validate($this->validationRules());

        // Update data utama perencanaan
        $perencanaan->update([
            'judul' => $request->judul,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ]);

        // Ambil semua ID detail yang ada di database
        $existingDetailIds = $perencanaan->details->pluck('detail_id')->toArray();
        
        // Ambil ID detail yang dikirim dari form (yang masih ada)
        $submittedDetailIds = $request->detail_ids ?? [];
        
        // Hapus detail yang tidak ada di form (yang dihapus user)
        $idsToDelete = array_diff($existingDetailIds, $submittedDetailIds);
        if (!empty($idsToDelete)) {
            $perencanaan->details()->whereIn('detail_id', $idsToDelete)->delete();
        }

        // Update atau tambah detail
        foreach ($request->perencanaan as $index => $item) {
            $detailId = $request->detail_ids[$index] ?? null;
            
            $data = [
                'perencanaan' => $item,
                'target'      => $request->target[$index],
                'deskripsi'   => $request->deskripsi[$index] ?? null,
                'pelaksanaan' => $request->pelaksanaan[$index] ?? null,
            ];

            if ($detailId && in_array($detailId, $existingDetailIds)) {
                // Update detail yang sudah ada (dan tidak dihapus)
                $perencanaan->details()->where('detail_id', $detailId)->update($data);
            } else {
                // Tambah detail baru
                $data['user_id'] = auth()->id(); // ← TAMBAHKAN INI
                $perencanaan->details()->create($data);
            }
        }

        return redirect()->route('perencanaan.index')
            ->with('success', 'Perencanaan berhasil diperbarui!');
    }

    /* =====================================================
     * DESTROY
     * ===================================================== */
    public function destroy(Perencanaan $perencanaan)
    {
        $this->authorize403($perencanaan);

        $perencanaan->delete();

        return redirect()->route('perencanaan.index')
            ->with('success', 'Perencanaan berhasil dihapus');
    }

    /* =====================================================
     * PRIVATE HELPERS
     * ===================================================== */

    private function authorize403(Perencanaan $perencanaan): void
    {
        if ($perencanaan->user_id !== auth()->id()) {
            abort(403, 'Anda tidak berhak mengakses data ini.');
        }
    }

    private function getMonths(): array
    {
        $months = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::create()->month($i)->translatedFormat('F');
        }

        return $months;
    }

    private function validationRules(): array
    {
        return [
            'judul'         => 'required|string|max:255',
            'bulan'         => 'required|integer|between:1,12',
            'tahun'         => 'required|integer|min:2020|max:' . (date('Y') + 5),
            'perencanaan'   => 'required|array|min:1',
            'perencanaan.*' => 'required|string|max:255',
            'target'        => 'required|array|min:1',
            'target.*'      => 'required|string|max:255',
            'deskripsi'     => 'nullable|array',
            'deskripsi.*'   => 'nullable|string',
            'pelaksanaan'   => 'nullable|array',
            'pelaksanaan.*' => 'nullable|string',
        ];
    }

    private function validationMessages(): array
    {
        return [
            'perencanaan.*.required' => 'Kolom perencanaan wajib diisi',
            'target.*.required'      => 'Kolom target wajib diisi',
            'perencanaan.min'        => 'Minimal harus ada 1 detail perencanaan',
        ];
    }

    private function saveInputToSession(Request $request): void
    {
        $details = [];

        foreach ($request->perencanaan as $index => $item) {
            $details[] = [
                'perencanaan' => $item,
                'target'      => $request->target[$index] ?? '',
                'deskripsi'   => $request->deskripsi[$index] ?? '',
                'pelaksanaan' => $request->pelaksanaan[$index] ?? '',
            ];
        }

        session(['old_details' => $details]);
    }
}