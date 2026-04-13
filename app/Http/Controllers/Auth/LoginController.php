<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Override logout — pastikan session benar-benar dihapus & tidak nyangkut
     */
    public function logout(Request $request)
    {
        $user = auth()->user();

        // Bersihkan session_id dari lisensi supaya device lock tidak nyangkut
        if ($user) {
            License::where('user_id', $user->id)
                ->where('session_id', session()->getId())
                ->update(['session_id' => null]);
        }

        $this->guard()->logout();

        // INI yang penting — default Laravel trait TIDAK selalu panggil ini
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Override: setelah login berhasil, catat session baru & hapus sesi lama
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->isSuperAdmin()) return;

        $license = License::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->latest()
            ->first();

        if (!$license) return;

        $currentSessionId = session()->getId();

        if ($license->session_id && $license->session_id !== $currentSessionId) {
            DB::table('sessions')->where('id', $license->session_id)->delete();
        }

        $license->update([
            'session_id'    => $currentSessionId,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);
    }
}