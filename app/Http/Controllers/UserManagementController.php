<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\License;
use App\Models\Payment;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserManagementController extends Controller
{
    /**
     * Display a listing of all users with search & filter.
     */
    public function index(Request $request)
    {
        $query = User::query()
            ->withCount('licenses')
            ->with(['licenses' => fn($q) => $q->latest()->limit(1)]);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('name',        'like', "%{$s}%")
                ->orWhere('email',       'like', "%{$s}%")
                ->orWhere('school_name', 'like', "%{$s}%")
            );
        }

        if ($request->filled('status')) {
            match($request->status) {
                'active'    => $query->where('is_suspended', false),
                'suspended' => $query->where('is_suspended', true),
                default     => null,
            };
        }

        if ($request->filled('license')) {
            match($request->license) {
                'licensed' => $query->whereHas('licenses',
                    fn($q) => $q->where('end_date', '>=', now())),
                'expired'  => $query->whereDoesntHave('licenses',
                    fn($q) => $q->where('end_date', '>=', now())),
                default    => null,
            };
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total'     => User::count(),
            'active'    => User::where('is_suspended', false)->count(),
            'suspended' => User::where('is_suspended', true)->count(),
            'licensed'  => User::whereHas('licenses',
                fn($q) => $q->where('end_date', '>=', now()))->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show detail of a single user.
     */
    public function show($id)
    {
        $user = User::with([
            'licenses' => fn($q) => $q->latest(),
            'payments' => fn($q) => $q->latest()->limit(10),
        ])->findOrFail($id);

        $activeLicense = $user->licenses()
            ->where('end_date', '>=', now())
            ->latest('end_date')
            ->first();

        $totalSpent = $user->payments()
            ->whereIn('status', ['settlement', 'capture'])
            ->sum('amount');

        return view('admin.users.show', compact('user', 'activeLicense', 'totalSpent'));
    }

    /**
     * Suspend a user account.
     */
    public function suspend(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->is_suspended) {
            return back()->with('warning', "User {$user->name} sudah dalam status suspended.");
        }

        $user->update([
            'is_suspended'     => true,
            'suspended_at'     => now(),
            'suspended_reason' => $request->reason ?? 'Disuspend oleh admin',
        ]);

        return back()->with('success', "User {$user->name} berhasil disuspend.");
    }

    /**
     * Activate (unsuspend) a user account.
     */
    public function activate($id)
    {
        $user = User::findOrFail($id);

        if (! $user->is_suspended) {
            return back()->with('warning', "User {$user->name} sudah aktif.");
        }

        $user->update([
            'is_suspended'     => false,
            'suspended_at'     => null,
            'suspended_reason' => null,
        ]);

        return back()->with('success', "User {$user->name} berhasil diaktifkan kembali.");
    }

    /**
     * Render printable invoice for a license.
     * Route: GET /admin/users/{userId}/invoice/{licenseId}
     */
    public function invoice($userId, $licenseId)
    {
        $user    = User::findOrFail($userId);
        $license = License::where('id', $licenseId)
                        ->where(function ($q) use ($user) {
                            $q->where('user_id', $user->id)
                                ->orWhere('buyer_email', $user->email);
                        })
                        ->firstOrFail();

        $payment = Payment::where('buyer_email', $license->buyer_email)
                        ->where('package_type', $license->package_type)
                        ->whereIn('status', ['settlement', 'capture'])
                        ->latest()
                        ->first();

        // AMBIL SETTING UNTUK SUPER ADMIN (user_id = 2)
        $setting = SchoolSetting::where('user_id', 2)->first();
        
        // Jika tidak ada, coba ambil yang pertama
        if (!$setting) {
            $setting = SchoolSetting::first();
        }

        // Debug: lihat path file
        if ($setting && $setting->ttd_kepala) {
            $fullPath = storage_path('app/public/' . $setting->ttd_kepala);
            \Log::info('TTD Path: ' . $fullPath);
            \Log::info('File exists: ' . (file_exists($fullPath) ? 'Yes' : 'No'));
        }

        $packageLabel = match($license->package_type ?? 'monthly') {
            'monthly'   => 'Bulanan (30 Hari)',
            'quarterly' => 'Triwulan (90 Hari)',
            'yearly'    => 'Tahunan (365 Hari)',
            'lifetime'  => 'Seumur Hidup',
            default     => ucfirst($license->package_type ?? 'Bulanan'),
        };

        $admin_name  = auth()->user()->name;
        $admin_email = auth()->user()->email;

        return view('admin.users.invoice', compact(
            'user', 'license', 'payment', 'setting',
            'packageLabel', 'admin_name', 'admin_email'
        ));
    }
}