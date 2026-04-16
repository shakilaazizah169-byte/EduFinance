<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SchoolSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getSetting(): SchoolSetting
    {
        return SchoolSetting::where('user_id', auth()->id())->first()
               ?? new SchoolSetting();
    }

    public function index(Request $request)
    {
        $query = Invoice::where('user_id', auth()->id());

        // Filter tanggal dari
        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        // Filter tanggal sampai
        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        // Filter pencarian (no invoice atau nama customer)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('bill_to_nama', 'like', "%{$search}%")
                  ->orWhere('bill_to_email', 'like', "%{$search}%");
            });
        }

        $invoices = $query->orderByDesc('invoice_date')
                          ->orderByDesc('id')
                          ->paginate(10)
                          ->withQueryString(); // Menjaga filter saat pagination

        // Data untuk statistik cards
        $totalInvoice = Invoice::where('user_id', auth()->id())->count();
        $totalNilaiInvoice = Invoice::where('user_id', auth()->id())->sum('total');
        
        // Invoice bulan ini
        $totalBulanIni = Invoice::where('user_id', auth()->id())
            ->whereMonth('invoice_date', now()->month)
            ->whereYear('invoice_date', now()->year)
            ->count();
        
        // Total customer unik
        $totalCustomer = Invoice::where('user_id', auth()->id())
            ->distinct('bill_to_nama')
            ->count('bill_to_nama');

        return view('invoice.index', compact(
            'invoices', 
            'totalInvoice', 
            'totalNilaiInvoice', 
            'totalBulanIni', 
            'totalCustomer'
        ));
    }

    public function create()
    {
        $setting = $this->getSetting();
        return view('invoice.create', compact('setting'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_number'      => 'required|string|max:100|unique:invoices,invoice_number,NULL,id,user_id,' . auth()->id(),
            'invoice_date'        => 'required|date',
            'bill_to_nama'        => 'required|string|max:255',
            'bill_to_alamat'      => 'nullable|string|max:1000',
            'bill_to_telepon'     => 'nullable|string|max:50',
            'bill_to_email'       => 'nullable|email|max:255',
            'items'               => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.qty'         => 'required|numeric|min:0',
            'items.*.unit_cost'   => 'required|numeric|min:0',
            'subtotal'            => 'nullable|numeric|min:0',
            'tax_rate'            => 'nullable|numeric|min:0|max:100',
            'sales_tax'           => 'nullable|numeric|min:0',
            'other'               => 'nullable|numeric|min:0',
            'total'               => 'nullable|numeric|min:0',
            'terbilang'           => 'nullable|string|max:1000',
            'catatan_bank'        => 'nullable|string|max:1000',
            'pesan_penutup'       => 'nullable|string|max:255',
            'catatan_tambahan'    => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'user_id'          => auth()->id(),
                'invoice_number'   => $request->invoice_number,
                'invoice_date'     => $request->invoice_date,
                'bill_to_nama'     => $request->bill_to_nama,
                'bill_to_alamat'   => $request->bill_to_alamat,
                'bill_to_telepon'  => $request->bill_to_telepon,
                'bill_to_email'    => $request->bill_to_email,
                'subtotal'         => $request->subtotal,
                'tax_rate'         => $request->tax_rate  ?? 0,
                'sales_tax'        => $request->sales_tax ?? 0,
                'other'            => $request->other     ?? 0,
                'total'            => $request->total,
                'terbilang'        => $request->terbilang,
                'catatan_bank'     => $request->catatan_bank,
                'pesan_penutup'    => $request->pesan_penutup,
                'catatan_tambahan' => $request->catatan_tambahan,
            ]);

            foreach ($request->items as $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'qty'         => $item['qty'],
                    'unit_cost'   => $item['unit_cost'],
                    'amount'      => $item['qty'] * $item['unit_cost'],
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }

        if ($request->action === 'save_print') {
            return redirect()->route('invoice.print', $invoice->id);
        }

        return redirect()->route('invoice.index')
            ->with('success', 'Invoice #' . $invoice->invoice_number . ' berhasil dibuat.');
    }

    public function show(int $id)
    {
        $invoice = Invoice::with('items')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $setting = $this->getSetting();
        return view('invoice.show', compact('invoice', 'setting'));
    }

    public function edit(int $id)
    {
        $invoice = Invoice::with('items')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $setting = $this->getSetting();
        return view('invoice.edit', compact('invoice', 'setting'));
    }

    public function update(Request $request, int $id)
    {
        $invoice = Invoice::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'invoice_number'      => 'required|string|max:100|unique:invoices,invoice_number,' . $id . ',id,user_id,' . auth()->id(),
            'invoice_date'        => 'required|date',
            'bill_to_nama'        => 'required|string|max:255',
            'bill_to_alamat'      => 'nullable|string|max:1000',
            'bill_to_telepon'     => 'nullable|string|max:50',
            'bill_to_email'       => 'nullable|email|max:255',
            'items'               => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.qty'         => 'required|numeric|min:0',
            'items.*.unit_cost'   => 'required|numeric|min:0',
            'subtotal'            => 'nullable|numeric|min:0',
            'tax_rate'            => 'nullable|numeric|min:0|max:100',
            'sales_tax'           => 'nullable|numeric|min:0',
            'other'               => 'nullable|numeric|min:0',
            'total'               => 'nullable|numeric|min:0',
            'terbilang'           => 'nullable|string|max:1000',
            'catatan_bank'        => 'nullable|string|max:1000',
            'pesan_penutup'       => 'nullable|string|max:255',
            'catatan_tambahan'    => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $invoice->update([
                'invoice_number'   => $request->invoice_number,
                'invoice_date'     => $request->invoice_date,
                'bill_to_nama'     => $request->bill_to_nama,
                'bill_to_alamat'   => $request->bill_to_alamat,
                'bill_to_telepon'  => $request->bill_to_telepon,
                'bill_to_email'    => $request->bill_to_email,
                'subtotal'         => $request->subtotal,
                'tax_rate'         => $request->tax_rate  ?? 0,
                'sales_tax'        => $request->sales_tax ?? 0,
                'other'            => $request->other     ?? 0,
                'total'            => $request->total,
                'terbilang'        => $request->terbilang,
                'catatan_bank'     => $request->catatan_bank,
                'pesan_penutup'    => $request->pesan_penutup,
                'catatan_tambahan' => $request->catatan_tambahan,
            ]);

            $invoice->items()->delete();
            foreach ($request->items as $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'qty'         => $item['qty'],
                    'unit_cost'   => $item['unit_cost'],
                    'amount'      => $item['qty'] * $item['unit_cost'],
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage())->withInput();
        }

        if ($request->action === 'save_print') {
            return redirect()->route('invoice.print', $invoice->id);
        }

        return redirect()->route('invoice.index')
            ->with('success', 'Invoice #' . $invoice->invoice_number . ' berhasil diupdate.');
    }

    public function destroy(int $id)
    {
        $invoice = Invoice::where('user_id', auth()->id())->findOrFail($id);
        $invoice->items()->delete();
        $invoice->delete();

        return redirect()->route('invoice.index')
            ->with('success', 'Invoice berhasil dihapus.');
    }

    /**
     * Cetak PDF.
     * Kota, nama TTD, jabatan TTD diambil LANGSUNG dari school_settings
     * saat print — bukan disimpan di tabel invoices.
     * Artinya jika setting diubah, PDF berikutnya otomatis ikut berubah.
     */
    public function print(int $id)
    {
        $invoice = Invoice::with('items')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        // Selalu ambil fresh dari DB — sinkron dengan setting terkini
        $setting = $this->getSetting();

        $pdf = Pdf::loadView('invoice.pdf', compact('invoice', 'setting'))
            ->setPaper('A4', 'portrait');

        // Sanitize filename - replace invalid characters
        $safeInvoiceNumber = str_replace(['/', '\\'], '-', $invoice->invoice_number);
        $filename = 'Invoice-' . $safeInvoiceNumber . '.pdf';

        return $pdf->download($filename);
    }
}