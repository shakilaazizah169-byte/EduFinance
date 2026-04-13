<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuperAdminMutasi extends Model
{
    protected $table = 'super_admin_mutasi';

    protected $fillable = [
        'payment_id',
        'order_id',
        'buyer_name',
        'buyer_email',
        'school_name',
        'package_type',
        'package_label',
        'tanggal',
        'uraian',
        'debit',
        'kredit',
        'saldo',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'debit'   => 'decimal:2',
        'kredit'  => 'decimal:2',
        'saldo'   => 'decimal:2',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Generate & simpan mutasi untuk Super Admin dari sebuah Payment.
     * Cegah duplikat lewat payment_id.
     */
    public static function generateDariPayment(Payment $payment): ?self
    {
        // Cegah duplikat
        if (static::where('payment_id', $payment->id)->exists()) {
            return null;
        }

        $packageLabel = match ($payment->package_type) {
            'monthly'  => 'Paket Bulanan',
            'yearly'   => 'Paket Tahunan',
            'lifetime' => 'Paket Lifetime',
            default    => ucfirst($payment->package_type),
        };

        // Hitung saldo kumulatif
        $lastSaldo = static::orderByDesc('id')->value('saldo') ?? 0;
        $newSaldo  = $lastSaldo + $payment->amount;

        $uraian = "Pemasukan dari pembelian {$packageLabel}"
                . " oleh {$payment->buyer_name}"
                . " ({$payment->school_name})"
                . " — Order ID: {$payment->order_id}";

        return static::create([
            'payment_id'   => $payment->id,
            'order_id'     => $payment->order_id,
            'buyer_name'   => $payment->buyer_name,
            'buyer_email'  => $payment->buyer_email,
            'school_name'  => $payment->school_name,
            'package_type' => $payment->package_type,
            'package_label'=> $packageLabel,
            'tanggal'      => $payment->created_at->toDateString(),
            'uraian'       => $uraian,
            'debit'        => $payment->amount,
            'kredit'       => 0,
            'saldo'        => $newSaldo,
        ]);
    }
}