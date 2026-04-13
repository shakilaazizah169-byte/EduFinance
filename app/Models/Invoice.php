<?php
// File: app/Models/Invoice.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'user_id', 'invoice_number', 'invoice_date',
        'bill_to_nama', 'bill_to_alamat', 'bill_to_telepon', 'bill_to_email',
        'subtotal', 'tax_rate', 'sales_tax', 'other', 'total',
        'terbilang', 'catatan_bank', 'pesan_penutup', 'catatan_tambahan',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'subtotal'     => 'float',
        'tax_rate'     => 'float',
        'sales_tax'    => 'float',
        'other'        => 'float',
        'total'        => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}