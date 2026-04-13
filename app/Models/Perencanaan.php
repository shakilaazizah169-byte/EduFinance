<?php

namespace App\Models;

use App\Models\PerencanaanDetail;
use Illuminate\Database\Eloquent\Model;

class Perencanaan extends Model
{
    // ✅ FIX: nama tabel yang benar sesuai migration
    protected $table = 'perencanaans';

    // ✅ FIX: primary key yang benar
    protected $primaryKey = 'perencanaan_id';

    protected $fillable = [
        'user_id',
        'judul',
        'bulan',
        'tahun',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(PerencanaanDetail::class, 'perencanaan_id', 'perencanaan_id')
                    ->where('user_id', auth()->id()); // ← FILTER OTOMATIS
    }

    public function realisasi()
    {
        return $this->hasMany(Realisasi::class, 'perencanaan_id', 'perencanaan_id');
    }

    /**
     * Scope untuk mengambil data milik user yang login.
     */
    public function scopeForCurrentUser($query)
    {
        return $query->where('user_id', auth()->id());
    }
}