<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiKas extends Model
{
    use HasFactory;

    protected $table      = 'mutasi_kas';
    protected $primaryKey = 'mutasi_id';

    protected $fillable = [
        'user_id',            // ← TAMBAHAN multi-tenant
        'tanggal',
        'kode_transaksi_id',
        'uraian',
        'debit',
        'kredit',
        'saldo',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kodeTransaksi()
    {
        return $this->belongsTo(KodeTransaksi::class, 'kode_transaksi_id', 'kode_transaksi_id');
    }
}