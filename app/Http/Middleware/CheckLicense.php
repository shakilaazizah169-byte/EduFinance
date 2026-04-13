<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckLicense
{
    public function handle(Request $request, Closure $next, $required = false)
    {
        $user = Auth::user();

        // Jika tidak login, redirect ke login
        if (!$user) {
            return redirect()->route('login');
        }

        // Super admin bypass
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // Jika lisensi required (write operations) dan user tidak punya lisensi aktif
        if ($required && !$user->hasActiveLicense()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lisensi Anda tidak aktif. Silakan perpanjang lisensi.',
                    'redirect' => route('pricing')
                ], 403);
            }

            return redirect()->route('pricing')
                ->with('error', 'Lisensi Anda tidak aktif. Silakan perpanjang lisensi untuk melanjutkan.');
        }

        // Jika user memiliki lisensi expired, mereka tetap bisa lihat halaman (read-only)
        // TAPI hanya untuk route yang tidak mewajibkan lisensi aktif ($required = false)
        if ($user->isLicenseExpired() && $user->lisensi_status !== 'never') {
            if (!$required) {
                // Untuk read-only pages, tambahkan warning
                view()->share('licenseExpiredWarning', true);
            }
        }

        return $next($request);
    }
}