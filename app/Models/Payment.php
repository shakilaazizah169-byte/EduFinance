<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'buyer_name',
        'buyer_email',
        'buyer_phone',
        'school_name',
        'package_type',
        'amount',
        'status',
        'payment_type',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔥 TAMBAHKAN RELASI INI
    public function license()
    {
        return $this->hasOne(License::class, 'buyer_email', 'buyer_email')
            ->where('package_type', $this->package_type);
    }

    public function getRecipientNameAttribute(): string
    {
        return $this->user?->name ?? $this->buyer_name ?? 'Pelanggan';
    }

    public function getRecipientEmailAttribute(): ?string
    {
        return $this->buyer_email ?? $this->user?->email;
    }

    public function getRecipientPhoneAttribute(): ?string
    {
        return $this->buyer_phone ?? $this->user?->phone;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'                 => 'Menunggu Pembayaran',
            'settlement', 'capture'  => 'Sukses',
            'expired'                => 'Kadaluarsa',
            'cancel'                 => 'Dibatalkan',
            default                  => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'                => 'warning',
            'settlement', 'capture' => 'success',
            'expired'               => 'secondary',
            'cancel'                => 'danger',
            default                 => 'info',
        };
    }
}