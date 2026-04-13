<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table      = 'kategori';
    protected $primaryKey = 'kategori_id';

    protected $fillable = [
        'user_id',        // nullable = global; berisi id = milik user
        'nama_kategori',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kodeTransaksi()
    {
        return $this->hasMany(KodeTransaksi::class, 'kategori_id', 'kategori_id');
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

    /**
     * Apakah kategori ini adalah data global (seed awal)?
     */
    public function isGlobal(): bool
    {
        return is_null($this->user_id);
    }

    /**
     * Apakah kategori ini milik user yang sedang login?
     */
    public function isOwnedByCurrentUser(): bool
    {
        return $this->user_id === auth()->id();
    }
}