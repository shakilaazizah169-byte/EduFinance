<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SchoolSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // FIX: forUser() sudah diperbaiki — gunakan langsung
        $setting = SchoolSetting::forUser(auth()->id());
        return view('school_settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_sekolah'        => 'nullable|string|max:255',
            'alamat'              => 'nullable|string|max:1000',
            'kota'                => 'nullable|string|max:100',
            'telepon'             => 'nullable|string|max:30',
            'npsn'                => 'nullable|string|max:20',
            'email'               => 'nullable|email|max:255',
            'website'             => 'nullable|url|max:255',
            'nama_kepala_sekolah' => 'nullable|string|max:255',
            'nip_kepala_sekolah'  => 'nullable|string|max:30',
            'nama_bendahara'      => 'nullable|string|max:255',
            'nip_bendahara'       => 'nullable|string|max:30',
            'logo_sekolah'        => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'logo_yayasan'        => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'ttd_kepala'          => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
            'ttd_bendahara'       => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
        ]);

        $userId = auth()->id();

        // FIX: Pisahkan pencarian record dari pembuatan instance baru.
        // JANGAN gunakan firstOrNew() di sini karena sama bermasalahnya
        // — gunakan firstOrCreate() yang dijamin atomic, atau where()->first() + new.
        $setting = SchoolSetting::where('user_id', $userId)->first();

        if (! $setting) {
            $setting          = new SchoolSetting();
            $setting->user_id = $userId;
        }

        // Ambil data teks dari request
        $data = $request->only([
            'nama_sekolah', 'alamat', 'kota', 'telepon', 'npsn',
            'email', 'website',
            'nama_kepala_sekolah', 'nip_kepala_sekolah',
            'nama_bendahara', 'nip_bendahara',
        ]);

        // Handle upload file
        // FIX: Gunakan getRawOriginal() untuk cek path lama — bukan $setting->$field
        // karena jika $setting baru di-load fresh dari DB, kedua cara hasilnya sama.
        // Tapi jika ada edge case di mana accessor override, getRawOriginal() lebih aman.
        $fileFields = ['logo_sekolah', 'logo_yayasan', 'ttd_kepala', 'ttd_bendahara'];
        $dir        = 'school/' . $userId;

        foreach ($fileFields as $field) {
            if ($request->hasFile($field) && $request->file($field)->isValid()) {
                // Hapus file lama
                $oldPath = $setting->getRawOriginal($field);
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
                $data[$field] = $request->file($field)->store($dir, 'public');
            }
        }

        // Fill dan save
        $setting->fill($data);
        $saved = $setting->save();

        Log::info('SchoolSetting save', [
            'user_id'      => $userId,
            'setting_id'   => $setting->id,
            'saved'        => $saved,
            'nama_sekolah' => $setting->getRawOriginal('nama_sekolah'),
        ]);

        if (! $saved) {
            return redirect()->route('school.settings')
                ->with('error', 'Gagal menyimpan setting. Silakan coba lagi.');
        }

        return redirect()->route('school.settings')
            ->with('success', 'Setting sekolah berhasil disimpan.');
    }

    /**
     * Hapus file spesifik (logo/ttd) via AJAX
     */
    public function deleteFile(Request $request)
    {
        $request->validate([
            'field' => 'required|in:logo_sekolah,logo_yayasan,ttd_kepala,ttd_bendahara',
        ]);

        $setting = SchoolSetting::where('user_id', auth()->id())->first();

        if (! $setting) {
            return response()->json(['success' => false, 'message' => 'Setting tidak ditemukan.'], 404);
        }

        $field   = $request->field;
        $oldPath = $setting->getRawOriginal($field);

        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        // Gunakan update() langsung pada kolom — paling aman
        $setting->update([$field => null]);

        return response()->json(['success' => true]);
    }
}