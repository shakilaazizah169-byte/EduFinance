<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Payment;
use App\Models\SchoolSetting;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'super_admin') {
                abort(403, 'Akses ditolak — hanya Super Admin.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = User::with(['licenses' => fn($q) => $q->latest()])
            ->where('role', '!=', 'super_admin')
            ->orderByDesc('created_at');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('school_name', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10)->withQueryString();

        $stats = [
            'total'     => User::where('role', '!=', 'super_admin')->count(),
            'active'    => \App\Models\License::where('status', 'active')->where('end_date', '>=', now())->distinct('user_id')->count(),
            'expired'   => \App\Models\License::where('status', 'expired')->distinct('user_id')->count(),
            'suspended' => \App\Models\License::where('status', 'suspended')->distinct('user_id')->count(),
            'revenue'   => \App\Models\Payment::whereIn('status', ['settlement', 'capture'])->sum('amount'),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function show(int $id)
    {
        $user = User::with([
            'licenses' => fn($q) => $q->orderByDesc('created_at'),
            'payments' => fn($q) => $q->orderByDesc('created_at'),
        ])->findOrFail($id);

        $latestLicense = $user->licenses->first();
        $latestPayment = $user->payments
            ->whereIn('status', ['settlement', 'capture'])
            ->first();

        return view('admin.users.show', compact('user', 'latestLicense', 'latestPayment'));
    }

    public function suspend(int $id)
    {
        $user    = User::findOrFail($id);
        $updated = License::where('user_id', $id)
            ->whereIn('status', ['active'])
            ->update(['status' => 'suspended']);

        Log::info("Super Admin suspend user #{$id} ({$user->name}), {$updated} lisensi di-suspend");

        return redirect()->back()->with('success', "User {$user->name} berhasil di-suspend.");
    }

    public function activate(int $id)
    {
        $user = User::findOrFail($id);

        $updated = License::where('user_id', $id)
            ->where('status', 'suspended')
            ->where('end_date', '>=', now())
            ->update(['status' => 'active']);

        License::where('user_id', $id)
            ->where('status', 'suspended')
            ->where('end_date', '<', now())
            ->update(['status' => 'expired']);

        $message = $updated > 0
            ? "User {$user->name} berhasil diaktifkan kembali."
            : "Lisensi user {$user->name} sudah expired — perlu beli paket baru.";

        return redirect()->back()->with($updated > 0 ? 'success' : 'warning', $message);
    }

    /**
     * Cetak invoice PDF untuk satu transaksi.
     *
     * Setting kop invoice diambil dari user yang sedang LOGIN (auth()->id()),
     * bukan hardcode cari role='super_admin'.
     * Dengan begitu, siapapun super admin yang login dan punya data di
     * school_settings, datanya akan tampil di invoice.
     */
    public function invoice(int $userId, int $licenseId)
    {
        $user    = User::findOrFail($userId);
        $license = License::where('id', $licenseId)
            ->where(function ($q) use ($userId, $user) {
                $q->where('user_id', $userId)
                  ->orWhere('buyer_email', $user->email);
            })->firstOrFail();

        $payment = Payment::where('buyer_email', $license->buyer_email ?? $user->email)
            ->where('package_type', $license->package_type)
            ->whereIn('status', ['settlement', 'capture'])
            ->orderByDesc('created_at')
            ->first();

        $packageLabel = match ($license->package_type) {
            'monthly'  => 'Paket Bulanan (30 Hari)',
            'yearly'   => 'Paket Tahunan (365 Hari)',
            'lifetime' => 'Paket Lifetime (Seumur Hidup)',
            default    => ucfirst($license->package_type),
        };

        // FIX: ambil setting dari user yang sedang LOGIN (auth()->id())
        // bukan dari User::where('role','super_admin')->first() yang
        // return user_id=1 tapi datanya kosong di school_settings.
        $setting = SchoolSetting::where('user_id', auth()->id())->first()
                   ?? new SchoolSetting();

        $pdf = Pdf::loadView('admin.users.invoice', compact(
            'user', 'license', 'payment', 'packageLabel', 'setting'
        ))->setPaper('a4', 'portrait');

        $filename = 'Invoice-' . ($payment->order_id ?? $license->license_key) . '.pdf';

        return $pdf->download($filename);
    }
}