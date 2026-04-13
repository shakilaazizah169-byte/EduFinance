<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SchoolSetting extends Model
{
    protected $fillable = [
        'user_id',
        'nama_sekolah',
        'alamat',
        'telepon',
        'email',
        'website',
        'npsn',
        'kota',
        'nama_kepala_sekolah',
        'nip_kepala_sekolah',
        'nama_bendahara',
        'nip_bendahara',
        'logo_sekolah',
        'logo_yayasan',
        'ttd_kepala',
        'ttd_bendahara',
    ];

    // ═══════════════════════════════════════════════════════════════
    // RELASI
    // ═══════════════════════════════════════════════════════════════

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ═══════════════════════════════════════════════════════════════
    // ACCESSOR
    // ═══════════════════════════════════════════════════════════════

    /**
     * Alamat lengkap untuk kop surat.
     * Menggunakan getRawOriginal() agar tidak bergantung pada
     * state $attributes yang bisa tidak ter-load dengan benar.
     */
    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->getRawOriginal('alamat'),
            $this->getRawOriginal('kota'),
        ]));
    }

    // ═══════════════════════════════════════════════════════════════
    // URL HELPERS — untuk Blade web (bukan PDF)
    // ═══════════════════════════════════════════════════════════════

    public function logoSekolahUrl(): ?string
    {
        $path = $this->getRawOriginal('logo_sekolah');
        return $path ? Storage::url($path) : null;
    }

    public function logoYayasanUrl(): ?string
    {
        $path = $this->getRawOriginal('logo_yayasan');
        return $path ? Storage::url($path) : null;
    }

    public function ttdKepalaUrl(): ?string
    {
        $path = $this->getRawOriginal('ttd_kepala');
        return $path ? Storage::url($path) : null;
    }

    public function ttdBendaharaUrl(): ?string
    {
        $path = $this->getRawOriginal('ttd_bendahara');
        return $path ? Storage::url($path) : null;
    }

    // ═══════════════════════════════════════════════════════════════
    // BASE64 HELPERS — untuk embed di PDF (DomPDF tidak bisa akses URL)
    // ═══════════════════════════════════════════════════════════════

    public function logoSekolahBase64(): ?string
    {
        return $this->toBase64($this->getRawOriginal('logo_sekolah'));
    }

    public function logoYayasanBase64(): ?string
    {
        return $this->toBase64($this->getRawOriginal('logo_yayasan'));
    }

    public function ttdKepalaBase64(): ?string
    {
        return $this->toBase64($this->getRawOriginal('ttd_kepala'));
    }

    public function ttdBendaharaBase64(): ?string
    {
        return $this->toBase64($this->getRawOriginal('ttd_bendahara'));
    }

    private function toBase64(?string $path): ?string
    {
        if (! $path) return null;

        $fullPath = storage_path('app/public/' . $path);
        if (! file_exists($fullPath)) return null;

        $ext  = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'png'          => 'image/png',
            'jpg', 'jpeg'  => 'image/jpeg',
            'gif'          => 'image/gif',
            default        => 'image/png',
        };

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($fullPath));
    }

    // ═══════════════════════════════════════════════════════════════
    // STATIC FACTORY
    // ═══════════════════════════════════════════════════════════════

    /**
     * FIX: Gunakan where()->first() bukan firstOrNew().
     *
     * KENAPA firstOrNew() BERMASALAH:
     * firstOrNew(['user_id' => $uid]) memang melakukan query ke DB,
     * tapi ia menginisialisasi model dengan $attributes = ['user_id' => $uid].
     * Pada beberapa versi Laravel/konfigurasi, ini bisa menyebabkan
     * kolom lain tidak ter-load dengan benar ke dalam $attributes array
     * karena Eloquent memprioritaskan $attributes yang di-pass ke constructor.
     *
     * where()->first() selalu load SEMUA kolom dari DB tanpa ambiguitas.
     * Ini adalah pola yang paling aman dan eksplisit.
     */
    public static function forUser(?int $userId = null): self
    {
        $uid = $userId ?? auth()->id();

        // Selalu query fresh dari DB — tidak ada ambiguitas
        $existing = static::where('user_id', $uid)->first();

        if ($existing) {
            return $existing;
        }

        // Belum ada record: kembalikan instance baru (BELUM disimpan)
        $new           = new static();
        $new->user_id  = $uid;
        return $new;
    }
}