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

    // ── License & Payment ─────────────────────────────────────

    public function license()
    {
        return $this->hasOne(License::class)->where('status', 'active');
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
     * Cek apakah lisensi user sudah expired
     */
    public function isLicenseExpired(): bool
    {
        // Jika status never atau tidak punya expired_at
        if ($this->lisensi_status === 'never' || !$this->lisensi_expired_at) {
            return true;
        }
        
        // Jika status expired dari sistem
        if ($this->lisensi_status === 'expired') {
            return true;
        }
        
        // Cek berdasarkan tanggal
        return now()->gte($this->lisensi_expired_at);
    }

    /**
     * Cek apakah user memiliki lisensi aktif
     */
    public function hasActiveLicense(): bool
    {
        // Super admin selalu dianggap punya akses
        if ($this->role === 'super_admin') {
            return true;
        }
        
        // Cek status dan tanggal
        return $this->lisensi_status === 'active' 
            && $this->lisensi_expired_at 
            && now()->lt($this->lisensi_expired_at);
    }

    /**
     * Get remaining days of license
     */
    public function getLicenseDaysLeft(): int
    {
        if (!$this->hasActiveLicense() || !$this->lisensi_expired_at) {
            return 0;
        }
        
        return (int) now()->diffInDays($this->lisensi_expired_at, false);
    }

}