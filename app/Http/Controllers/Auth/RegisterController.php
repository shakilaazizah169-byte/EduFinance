<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * RegisterController — Alur lengkap registrasi dengan lisensi:
 *
 * 1. User buka /register (bisa dari link di halaman finish payment)
 * 2. Form meminta: name, email, phone, school_name, password, license_key
 * 3. Saat submit:
 *    a. Validasi license_key: harus ada, status active, belum diklaim
 *    b. Email harus cocok dengan buyer_email di lisensi (atau boleh berbeda — lihat config)
 *    c. Buat user baru
 *    d. Update licenses.user_id = user baru
 *    e. Isi school_name dari lisensi jika kosong
 *    f. Buat SchoolSetting awal dengan nama sekolah
 *    g. Login otomatis
 *    h. Redirect ke dashboard
 */
class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('checkLicense');
    }

    /**
     * Tampilkan form registrasi.
     * Jika ada ?license_key=xxx di URL (dari halaman finish),
     * pre-fill field license_key dan data buyer.
     */
    public function showRegistrationForm(Request $request)
    {
        $licenseKey = $request->query('license_key');
        $prefill    = [];

        // Coba ambil data dari lisensi untuk pre-fill form
        if ($licenseKey) {
            $license = License::where('license_key', $licenseKey)
                ->whereNull('user_id')
                ->where('status', 'active')
                ->first();

            if ($license) {
                $prefill = [
                    'name'        => $license->buyer_name,
                    'email'       => $license->buyer_email,
                    'phone'       => $license->buyer_phone,
                    'school_name' => $license->buyer_name
                        ? $this->extractSchoolName($license->license_key)
                        : null,
                ];
            }
        }

        return view('auth.register', compact('licenseKey', 'prefill'));
    }

    /**
     * Proses registrasi
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users',
            'phone'        => 'required|string|max:20',
            'school_name'  => 'required|string|max:255',
            'password'     => 'required|string|min:8|confirmed',
            'license_key'  => 'required|string',
        ], [
            'name.required'        => 'Nama lengkap wajib diisi.',
            'email.required'       => 'Email wajib diisi.',
            'email.unique'         => 'Email ini sudah terdaftar. Silakan login.',
            'phone.required'       => 'Nomor WhatsApp wajib diisi.',
            'school_name.required' => 'Nama sekolah wajib diisi.',
            'password.min'         => 'Password minimal 8 karakter.',
            'password.confirmed'   => 'Konfirmasi password tidak cocok.',
            'license_key.required' => 'Kode lisensi wajib diisi.',
        ]);

        // ── Validasi Lisensi ────────────────────────────────────────
        $license = License::where('license_key', $request->license_key)->first();

        if (! $license) {
            return back()
                ->withInput()
                ->withErrors(['license_key' => 'Kode lisensi tidak ditemukan.']);
        }

        if ($license->user_id !== null) {
            return back()
                ->withInput()
                ->withErrors(['license_key' => 'Kode lisensi ini sudah digunakan oleh akun lain.']);
        }

        if ($license->status !== 'active') {
            $statusLabel = match ($license->status) {
                'expired' => 'sudah kadaluarsa',
                default   => 'tidak aktif (' . $license->status . ')',
            };
            return back()
                ->withInput()
                ->withErrors(['license_key' => "Kode lisensi {$statusLabel}."]);
        }

        if ($license->end_date->isPast()) {
            return back()
                ->withInput()
                ->withErrors(['license_key' => 'Kode lisensi sudah melewati masa berlaku (' . $license->end_date->format('d/m/Y') . ').']);
        }

        // Opsional: cek email harus sama dengan buyer_email di lisensi
        // Aktifkan jika ingin strict, nonaktifkan jika user boleh pakai email lain
        // if ($license->buyer_email && strtolower($license->buyer_email) !== strtolower($request->email)) {
        //     return back()->withInput()->withErrors([
        //         'email' => 'Email tidak cocok dengan email yang digunakan saat pembelian (' . $license->buyer_email . ').',
        //     ]);
        // }

        // ── Buat User ───────────────────────────────────────────────
        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'school_name' => $request->school_name,
            'password'    => Hash::make($request->password),
            'role'        => 'admin',
        ]);

        Log::info('✅ User baru terdaftar', [
            'user_id'     => $user->id,
            'email'       => $user->email,
            'license_key' => $request->license_key,
        ]);

        // ── Klaim lisensi ke user ini ───────────────────────────────
        $license->update(['user_id' => $user->id]);

        Log::info('🔑 Lisensi diklaim', [
            'license_key' => $license->license_key,
            'user_id'     => $user->id,
        ]);

        // ── Buat SchoolSetting awal ─────────────────────────────────
        // Sehingga user langsung bisa lihat nama sekolahnya di sidebar
        SchoolSetting::create([
            'user_id'     => $user->id,
            'nama_sekolah'=> $request->school_name,
            'telepon'     => $request->phone,
            'email'       => $request->email,
        ]);

        // ── Login otomatis ──────────────────────────────────────────
        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Selamat datang, ' . $user->name . '! Akun Anda berhasil dibuat.');
    }

    /**
     * Endpoint AJAX untuk validasi license_key secara realtime
     * (dipanggil dari JS saat user selesai mengetik kode lisensi)
     *
     * GET /register/check-license?key=XXXX-YYYY-ZZZZ
     */
    public function checkLicense(Request $request)
    {
        $key     = $request->query('key');
        $license = License::where('license_key', $key)->first();

        if (! $license) {
            return response()->json(['valid' => false, 'message' => 'Kode lisensi tidak ditemukan.']);
        }
        if ($license->user_id !== null) {
            return response()->json(['valid' => false, 'message' => 'Kode lisensi sudah digunakan.']);
        }
        if ($license->status !== 'active' || $license->end_date->isPast()) {
            return response()->json(['valid' => false, 'message' => 'Kode lisensi tidak aktif atau kadaluarsa.']);
        }

        return response()->json([
            'valid'   => true,
            'message' => 'Kode lisensi valid ✓',
            'data'    => [
                'package'    => match ($license->package_type) {
                    'monthly'  => 'Paket Bulanan',
                    'yearly'   => 'Paket Tahunan',
                    'lifetime' => 'Paket Lifetime',
                    default    => $license->package_type,
                },
                'valid_until' => $license->end_date->format('d F Y'),
                'buyer_name'  => $license->buyer_name,
                'school_name' => $license->buyer_name
                    ? $this->extractSchoolName($license->license_key)
                    : null,
            ],
        ]);
    }

    /**
     * Ekstrak nama sekolah dari prefix license_key.
     * Ex: "SMKN-ABCD-EFGH-IJKL" → prefix "SMKN" adalah kode sekolah
     */
    private function extractSchoolName(string $licenseKey): ?string
    {
        // License key format: SCHL-XXXX-XXXX-XXXX
        // Prefix 4 huruf pertama = kode sekolah (dari school_name saat beli)
        // Tidak bisa dikembalikan ke nama asli, tapi bisa jadi placeholder
        return null;
    }
}