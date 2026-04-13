<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckLicenseActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super admin bypass
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // Cek lisensi aktif
        if (!$user->hasActiveLicense()) {
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

        return $next($request);
    }
}