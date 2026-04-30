<?php

namespace App\Http\Controllers;

use App\Models\MutasiKas;
use App\Models\MutationEvidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class MutationEvidenceController extends Controller
{
    // ─────────────────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = MutationEvidence::byUser(auth()->id())
            ->with(['mutasiKas.kodeTransaksi'])
            ->orderByDesc('evidence_date')
            ->orderByDesc('id');

        // Search / Filter
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('evidence_number', 'like', "%$s%")
                    ->orWhere('evidence_title',  'like', "%$s%");
            });
        }

        if ($request->filled('evidence_type')) {
            $query->where('evidence_type', $request->evidence_type);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('evidence_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('evidence_date', '<=', $request->end_date);
        }

        $evidences   = $query->paginate(15)->withQueryString();
        $totalAmount = $query->sum('evidence_amount');
        $typeLabels  = MutationEvidence::$typeLabels;

        return view('mutation_evidence.index', compact('evidences', 'totalAmount', 'typeLabels'));
    }

    // ─────────────────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────────────────
    public function create(Request $request)
    {
        $selectedMutasiId = $request->mutation_id ?? session('_old_input.mutation_id');

        $mutasiList = MutasiKas::where('user_id', auth()->id())
            ->with('kodeTransaksi')
            ->orderByDesc('tanggal')
            ->orderByDesc('mutasi_id')
            ->get();

        $selectedMutasi = $selectedMutasiId
            ? MutasiKas::where('user_id', auth()->id())->find($selectedMutasiId)
            : null;

        $autoNumber = MutationEvidence::generateNumber();
        $typeLabels = MutationEvidence::$typeLabels;

        return view('mutation_evidence.create', compact(
            'mutasiList',
            'selectedMutasi',
            'autoNumber',
            'typeLabels'
        ));
    }

    // ─────────────────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->merge([
            'evidence_amount' => $request->evidence_amount
                ? (float) str_replace('.', '', $request->evidence_amount)
                : null,
        ]);

        $request->validate([
            'mutation_id'     => 'required|integer|exists:mutasi_kas,mutasi_id',
            'evidence_date'   => 'nullable|date',
            'evidence_number' => 'required|string|max:50|unique:mutation_evidences,evidence_number',
            'evidence_type'   => 'required|in:struk,kwitansi,nota,faktur,transfer,lainnya',
            'evidence_title'  => 'required|string|max:255',
            'evidence_amount' => 'nullable|numeric|min:0',
            'evidence_file'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notes'           => 'nullable|string',
        ]);

        // Verify the mutasi belongs to this user
        $mutasi = MutasiKas::where('user_id', auth()->id())
            ->where('mutasi_id', $request->mutation_id)
            ->firstOrFail();

        $filePath = null;
        if ($request->hasFile('evidence_file')) {
            $filePath = $request->file('evidence_file')
                ->store('mutation_evidences/' . auth()->id(), 'public');
        }

        MutationEvidence::create([
            'mutation_id'     => $mutasi->mutasi_id,
            'evidence_date'   => $request->evidence_date,
            'evidence_number' => $request->evidence_number,
            'evidence_type'   => $request->evidence_type,
            'evidence_title'  => $request->evidence_title,
            'evidence_amount' => $request->evidence_amount ?? 0,
            'evidence_file'   => $filePath,
            'notes'           => $request->notes,
            'created_by'      => auth()->id(),
        ]);

        if ($request->action === 'save_add') {
            return redirect()
                ->route('mutation-evidence.create', ['mutation_id' => $mutasi->mutasi_id])
                ->with('success', 'Bukti berhasil disimpan. Silakan tambah bukti lagi.');
        }

        return redirect()
            ->route('mutation-evidence.index')
            ->with('success', 'Bukti mutasi berhasil disimpan.');
    }

    // ─────────────────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────────────────
    public function edit(MutationEvidence $mutationEvidence)
    {
        abort_if($mutationEvidence->created_by !== auth()->id(), 403);

        $mutasiList = MutasiKas::where('user_id', auth()->id())
            ->with('kodeTransaksi')
            ->orderByDesc('tanggal')
            ->get();

        $typeLabels = MutationEvidence::$typeLabels;

        return view('mutation_evidence.edit', compact('mutationEvidence', 'mutasiList', 'typeLabels'));
    }

    // ─────────────────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────────────────
    public function update(Request $request, MutationEvidence $mutationEvidence)
    {
        abort_if($mutationEvidence->created_by !== auth()->id(), 403);

        $request->merge([
            'evidence_amount' => $request->evidence_amount
                ? (float) str_replace('.', '', $request->evidence_amount)
                : null,
        ]);

        $request->validate([
            'mutation_id'     => 'required|integer|exists:mutasi_kas,mutasi_id',
            'evidence_date'   => 'nullable|date',
            'evidence_number' => 'required|string|max:50|unique:mutation_evidences,evidence_number,' . $mutationEvidence->id,
            'evidence_type'   => 'required|in:struk,kwitansi,nota,faktur,transfer,lainnya',
            'evidence_title'  => 'required|string|max:255',
            'evidence_amount' => 'nullable|numeric|min:0',
            'evidence_file'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notes'           => 'nullable|string',
        ]);

        $filePath = $mutationEvidence->evidence_file;

        if ($request->hasFile('evidence_file')) {
            // Delete old file if exists
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('evidence_file')
                ->store('mutation_evidences/' . auth()->id(), 'public');
        }

        $mutationEvidence->update([
            'mutation_id'     => $request->mutation_id,
            'evidence_date'   => $request->evidence_date,
            'evidence_number' => $request->evidence_number,
            'evidence_type'   => $request->evidence_type,
            'evidence_title'  => $request->evidence_title,
            'evidence_amount' => $request->evidence_amount ?? 0,
            'evidence_file'   => $filePath,
            'notes'           => $request->notes,
        ]);

        return redirect()
            ->route('mutation-evidence.index')
            ->with('success', 'Bukti mutasi berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────────────────
    public function destroy(MutationEvidence $mutationEvidence)
    {
        abort_if($mutationEvidence->created_by !== auth()->id(), 403);

        if ($mutationEvidence->evidence_file) {
            Storage::disk('public')->delete($mutationEvidence->evidence_file);
        }

        $mutationEvidence->delete();

        return redirect()
            ->route('mutation-evidence.index')
            ->with('success', 'Bukti mutasi berhasil dihapus.');
    }

    // ─────────────────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────────────────
    public function show(MutationEvidence $mutationEvidence)
    {
        abort_if($mutationEvidence->created_by !== auth()->id(), 403);
        $mutationEvidence->load('mutasiKas.kodeTransaksi');
        return view('mutation_evidence.show', compact('mutationEvidence'));
    }

    // ─────────────────────────────────────────────────────────
    // PRINT / CETAK
    // ─────────────────────────────────────────────────────────
    public function print(Request $request)
    {
        $query = MutationEvidence::byUser(auth()->id())
            ->with(['mutasiKas.kodeTransaksi'])
            ->orderBy('evidence_date')
            ->orderBy('id');

        if ($request->filled('start_date')) {
            $query->whereDate('evidence_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('evidence_date', '<=', $request->end_date);
        }

        if ($request->filled('evidence_type')) {
            $query->where('evidence_type', $request->evidence_type);
        }

        if ($request->filled('mutation_id')) {
            $query->where('mutation_id', $request->mutation_id);
        }

        $evidences   = $query->get();
        $totalAmount = $evidences->sum('evidence_amount');

        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        $setting = \App\Models\Setting::first(); // adjust to your app's Setting model

        return view('mutation_evidence.print', compact(
            'evidences',
            'totalAmount',
            'startDate',
            'endDate',
            'setting'
        ));
    }

    // ─────────────────────────────────────────────────────────
    // GENERATE NUMBER (AJAX)
    // ─────────────────────────────────────────────────────────
    public function generateNumber(Request $request)
    {
        $date   = $request->date ?? now()->format('Y-m-d');
        $number = MutationEvidence::generateNumber($date);
        return response()->json(['number' => $number]);
    }
}
