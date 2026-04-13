<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealisasiLampiran extends Model
{
    use HasFactory;

    protected $table = 'realisasi_lampiran';

    protected $fillable = [
        'realisasi_id',
        'nama_file',
        'path_file',
        'tipe_file',
        'ukuran_file',
    ];

    public function realisasi()
    {
        return $this->belongsTo(Realisasi::class, 'realisasi_id');
    }

    public function isImage(): bool
    {
        return in_array(strtolower($this->tipe_file), ['jpg', 'jpeg', 'png']);
    }

    public function getIconAttribute(): string
    {
        return match(strtolower($this->tipe_file)) {
            'pdf'             => 'bi-file-earmark-pdf text-danger',
            'doc', 'docx'     => 'bi-file-earmark-word text-primary',
            'xls', 'xlsx'     => 'bi-file-earmark-excel text-success',
            'jpg', 'jpeg', 'png' => 'bi-file-earmark-image text-info',
            default           => 'bi-file-earmark text-secondary',
        };
    }

    public function getUkuranFormatAttribute(): string
    {
        $bytes = $this->ukuran_file;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }
}