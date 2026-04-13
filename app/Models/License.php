<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = [
        'license_key',
        'user_id',
        'buyer_email',
        'buyer_phone',
        'buyer_name',
        'session_id',
        'last_login_at',
        'last_login_ip',
        'status',
        'start_date',
        'end_date',
        'package_type',
        'price',
    ];

    protected $casts = [
        'start_date'    => 'date',
        'end_date'      => 'date',
        'last_login_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && Carbon::now()->lte($this->end_date);
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || Carbon::now()->gt($this->end_date);
    }

    /** Belum diklaim user (baru beli, belum register) */
    public function isClaimed(): bool
    {
        return !is_null($this->user_id);
    }

    public function daysLeft(): int
    {
        if ($this->isExpired()) return 0;
        return (int) Carbon::now()->diffInDays($this->end_date, false);
    }

    public function getValidityPeriod(): string
    {
        return $this->start_date->format('d/m/Y') . ' – ' . $this->end_date->format('d/m/Y');
    }

    public function getPackageLabelAttribute(): string
    {
        return match ($this->package_type) {
            'monthly'  => 'Paket Bulanan',
            'yearly'   => 'Paket Tahunan',
            'lifetime' => 'Paket Lifetime',
            default    => ucfirst($this->package_type),
        };
    }
}