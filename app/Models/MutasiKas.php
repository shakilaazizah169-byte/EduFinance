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
        'user_id',
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

    // ─── Existing Relationships ───────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kodeTransaksi()
    {
        return $this->belongsTo(KodeTransaksi::class, 'kode_transaksi_id', 'kode_transaksi_id');
    }

    // ─── Evidence Relationships (BARU) ───────────────────────
    /**
     * Satu mutasi bisa memiliki banyak bukti.
     */
    public function evidences()
    {
        return $this->hasMany(MutationEvidence::class, 'mutation_id', 'mutasi_id');
    }

    /**
     * Accessor: total nominal semua bukti pada mutasi ini.
     */
    public function getTotalEvidencesAmountAttribute(): float
    {
        return $this->evidences()->sum('evidence_amount');
    }

    /**
     * Accessor: jumlah bukti yang dimiliki mutasi ini.
     */
    public function getEvidenceCountAttribute(): int
    {
        return $this->evidences()->count();
    }
}
