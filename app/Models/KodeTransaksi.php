<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeTransaksi extends Model
{
    use HasFactory;

    protected $table      = 'kode_transaksi';
    protected $primaryKey = 'kode_transaksi_id';

    protected $fillable = [
        'user_id',        // nullable = global; berisi id = milik user
        'kode',
        'keterangan',
        'kategori_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'kategori_id');
    }

    public function mutasiKas()
    {
        return $this->hasMany(MutasiKas::class, 'kode_transaksi_id', 'kode_transaksi_id');
    }

    /**
     * Scope: data global (user_id = null) ATAU milik user yang login.
     */
    public function scopeVisibleToUser($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('user_id')
              ->orWhere('user_id', auth()->id());
        });
    }

    public function isGlobal(): bool
    {
        return is_null($this->user_id);
    }

    public function isOwnedByCurrentUser(): bool
    {
        return $this->user_id === auth()->id();
    }
}