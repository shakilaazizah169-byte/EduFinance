<?php
// File: app/Models/InvoiceItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id', 'description', 'qty', 'unit_cost', 'amount',
    ];

    protected $casts = [
        'qty'       => 'float',
        'unit_cost' => 'float',
        'amount'    => 'float',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}