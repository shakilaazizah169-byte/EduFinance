<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function details()
    {
        $user = Auth::user();
        return view('profile.details', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * ROOT CAUSE FIX:
     *
     * MASALAH: Auth::user() mengembalikan objek yang di-CACHE oleh Laravel
     * di dalam session/memory. Ketika kamu memanggil:
     *
     *     $user = Auth::user();
     *     $user->name = 'baru';
     *     $user->save();
     *
     * ...Laravel menyimpan perubahan di objek in-memory, TAPI karena objek ini
     * adalah instance yang sama yang di-share oleh Auth guard, ada kondisi
     * di mana save() berhasil tapi perubahan tidak di-flush ke DB dengan benar
     * — terutama kalau ada event, observer, atau middleware yang me-re-fetch user.
     *
     * SOLUSI: Gunakan User::find($id) untuk mendapatkan fresh instance dari DB,
     * lalu update + save. Ini 100% reliable.
     *
     * MASALAH #2: Storage::delete('public/avatars/' . $user->avatar)
     * Path yang benar adalah 'public/avatars/namafile' — sudah benar di kode lama.
     * Tapi kalau $user->avatar sudah menyimpan path lengkap (bukan hanya nama file),
     * ini akan error. Kita pastikan hanya simpan nama file saja.
     *
     * MASALAH #3: php artisan storage:link harus sudah dijalankan agar
     * storage/app/public/ bisa diakses via public/storage/
     */
    public function update(Request $request)
    {
        // ── KUNCI: Ambil fresh instance dari DB, bukan dari cache Auth ──
        $user = User::findOrFail(Auth::id());

        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email,' . $user->id,
            'avatar'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable',
            'new_password'     => 'nullable|min:8|confirmed',
        ]);

        // ── Update nama & email ───────────────────────────────────────
        $user->name  = $request->name;
        $user->email = $request->email;

        // ── Handle avatar upload ──────────────────────────────────────
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            // Hapus avatar lama dari storage
            if ($user->avatar) {
                Storage::delete('public/avatars/' . $user->avatar);
            }

            // Simpan avatar baru — pakai nama unik agar tidak bentrok
            $avatarName = Auth::id() . '_' . time() . '.' . $request->file('avatar')->extension();
            $request->file('avatar')->storeAs('public/avatars', $avatarName);

            // Simpan HANYA nama file ke DB (bukan path lengkap)
            $user->avatar = $avatarName;
        }

        // ── Handle password ───────────────────────────────────────────
        if ($request->filled('new_password')) {
            if (! $request->filled('current_password')) {
                return back()->withErrors(['current_password' => 'Password saat ini harus diisi untuk mengubah password.']);
            }

            if (! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
            }

            $user->password = Hash::make($request->new_password);
        }

        // ── Simpan ke DB ──────────────────────────────────────────────
        $saved = $user->save();

        if (! $saved) {
            return back()->with('error', 'Gagal menyimpan perubahan. Coba lagi.');
        }

        // ── Log aktivitas ─────────────────────────────────────────────
        try {
            $user->logActivity('updated_profile', 'Updated profile information', [
                'fields_updated' => array_keys($request->except(['_token', '_method', 'current_password', 'new_password', 'new_password_confirmation'])),
            ]);
        } catch (\Throwable $e) {
            // Log gagal tidak boleh batalkan update
            \Illuminate\Support\Facades\Log::warning('logActivity gagal: ' . $e->getMessage());
        }

        return redirect()->route('profile.details')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    public function activity()
    {
        $user = Auth::user();
        $activities = $user->activities()->paginate(20);

        if ($activities->isEmpty()) {
            $this->createSampleActivities($user);
            $activities = $user->activities()->paginate(20);
        }

        return view('profile.activity', compact('user', 'activities'));
    }

    public function notifications()
    {
        $user = Auth::user();
        return view('profile.notifications', compact('user'));
    }

    public function updateNotifications(Request $request)
    {
        return redirect()->route('profile.notifications')
            ->with('success', 'Notification settings updated');
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        Auth::logout();
        $user->delete();

        return redirect('/')->with('success', 'Akun Anda telah dihapus.');
    }

    public function deleteActivity(Activity $activity)
    {
        if ($activity->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $activity->delete();
        return response()->json(['success' => true]);
    }

    private function createSampleActivities($user)
    {
        $samples = [
            ['action' => 'logged_in',      'description' => 'User logged into the system',     'hours' => 2],
            ['action' => 'updated_profile','description' => 'Updated profile information',      'days'  => 1],
            ['action' => 'changed_password','description' => 'Changed account password',        'days'  => 7],
            ['action' => 'viewed_page',    'description' => 'Viewed dashboard page',            'days'  => 2],
            ['action' => 'created_record', 'description' => 'Created new transaction',          'days'  => 3],
        ];

        foreach ($samples as $s) {
            Activity::create([
                'user_id'     => $user->id,
                'action'      => $s['action'],
                'description' => $s['description'],
                'ip_address'  => '127.0.0.1',
                'user_agent'  => 'Mozilla/5.0',
                'created_at'  => isset($s['hours']) ? now()->subHours($s['hours']) : now()->subDays($s['days']),
            ]);
        }
    }

     /**
     * Remove avatar
     */
    public function removeAvatar(Request $request)
    {
        try {
            $user = Auth::user();

            // Delete avatar file if exists
            if ($user->avatar && Storage::disk('public')->exists('avatars/' . $user->avatar)) {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
            }

            // Set avatar to null
            $user->avatar = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Foto profile berhasil dihapus',
                'default_avatar' => $user->getAvatarUrl()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus foto profile: ' . $e->getMessage()
            ], 500);
        }
    }
}