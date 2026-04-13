<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Realisasi extends Model
{
    use HasFactory;

    protected $table = 'realisasi';

    protected $fillable = [
        'perencanaan_id',
        'detail_perencanaan_id',
        'user_id',
        'judul',
        'tanggal_realisasi',
        'deskripsi',
        'status_target',
        'persentase',
        'keterangan_target',
        'catatan_tambahan',
    ];

    protected $casts = [
        'tanggal_realisasi' => 'date',
        'persentase' => 'decimal:2',
    ];

    public function perencanaan()
    {
        return $this->belongsTo(Perencanaan::class, 'perencanaan_id');
    }

    public function detailPerencanaan()
    {
        return $this->belongsTo(PerencanaanDetail::class, 'detail_perencanaan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lampiran()
    {
        return $this->hasMany(RealisasiLampiran::class, 'realisasi_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status_target) {
            'sesuai'   => 'Sesuai Target',
            'tidak'    => 'Tidak Sesuai',
            'sebagian' => 'Tercapai Sebagian',
            default    => '-',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status_target) {
            'sesuai'   => 'success',
            'tidak'    => 'danger',
            'sebagian' => 'warning',
            default    => 'secondary',
        };
    }
}