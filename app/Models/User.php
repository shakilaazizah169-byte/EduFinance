<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'avatar', 'password',
        'school_name', 'phone', 'role',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['email_verified_at' => 'datetime'];

    // ── Multi-tenant relations ────────────────────────────────

    public function details()
    {
        return $this->hasMany(PerencanaanDetail::class);
    }

    public function kategori()
    {
        return $this->hasMany(Kategori::class);
    }

    public function kodeTransaksi()
    {
        return $this->hasMany(KodeTransaksi::class);
    }

    public function mutasiKas()
    {
        return $this->hasMany(MutasiKas::class);
    }

    /**
     * Relasi ke tabel licenses (satu user punya satu lisensi aktif)
     * 
     * Perhatikan: Ini hasOne, bukan hasMany, karena kita hanya butuh 1 lisensi aktif
     */
    public function license()
    {
        return $this->hasOne(License::class)->where('status', 'active')->latest();
    }

    /**
     * Ambil semua lisensi user (riwayat)
     */
    public function Licenses()
    {
        return $this->hasMany(License::class)->orderBy('created_at', 'desc');
    }

    public function activeLicense()
    {
        return $this->hasOne(License::class)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->latest();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // ── Helper methods ────────────────────────────────────────

    public function getAvatarUrl(): string
    {
        return $this->avatar
            ? asset('storage/avatars/' . $this->avatar)
            : asset('assets/images/avatar/1.jpg');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    /**
     * Cek apakah user memiliki lisensi aktif
     */

    /**
     * Get the user's active license from licenses table
     */
    public function getActiveLicense()
    {
        return $this->license()->where('status', 'active')
            ->where('end_date', '>=', now())
            ->first();
    }

    /**
     * Cek apakah user memiliki lisensi aktif (dari tabel licenses)
     */
    public function hasActiveLicense(): bool
    {
        // Super admin selalu dianggap punya akses
        if ($this->role === 'super_admin') {
            return true;
        }
        
        // 🔥 AMBIL DARI RELASI LICENSE, BUKAN DARI KOLOM lisensi_status
        $license = $this->license()
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->first();
        
        return $license !== null;
    }

    /**
     * Cek apakah lisensi user sudah expired
     */
    public function isLicenseExpired(): bool
    {
        // Super admin tidak pernah expired
        if ($this->role === 'super_admin') {
            return false;
        }
        
        // 🔥 CEK DARI TABEL LICENSES
        $license = $this->license()
            ->where('status', 'active')
            ->first();
        
        if (!$license) {
            return true;
        }
        
        return now()->gt($license->end_date);
    }

    /**
     * Get remaining days of license (dari tabel licenses)
     */
    public function getLicenseDaysLeft(): int
    {
        $license = $this->getActiveLicense();
        
        if (!$license) {
            return 0;
        }
        
        return (int) now()->diffInDays($license->end_date, false);
    }

    /**
     * Get license end date
     */
    public function getLicenseEndDate()
    {
        $license = $this->getActiveLicense();
        return $license ? $license->end_date : null;
    }

}