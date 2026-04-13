<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerencanaanDetail extends Model
{
    protected $primaryKey = 'detail_id';

    protected $fillable = [
        'perencanaan_id',
        'user_id',  
        'perencanaan',
        'target',
        'deskripsi',
        'pelaksanaan'
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function perencanaan()
    {
        return $this->belongsTo(Perencanaan::class, 'perencanaan_id');
    }

    /**
     * Relasi ke Realisasi — dipakai dashboard untuk cek status tiap detail
     */
    public function realisasi()
    {
        return $this->hasMany(\App\Models\Realisasi::class, 'detail_perencanaan_id', 'detail_id');
    }
}