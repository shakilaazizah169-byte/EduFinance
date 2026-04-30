<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MutationEvidence extends Model
{
    use HasFactory;

    protected $table      = 'mutation_evidences';
    protected $primaryKey = 'id';

    protected $fillable = [
        'mutation_id',
        'evidence_date',
        'evidence_number',
        'evidence_type',
        'evidence_title',
        'evidence_amount',
        'evidence_file',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'evidence_date'   => 'date',
        'evidence_amount' => 'decimal:2',
    ];

    // ─── Label mapping ───────────────────────────────────────
    public static array $typeLabels = [
        'struk'    => 'Struk',
        'kwitansi' => 'Kwitansi',
        'nota'     => 'Nota',
        'faktur'   => 'Faktur',
        'transfer' => 'Transfer',
        'lainnya'  => 'Lainnya',
    ];

    public static array $typeBadgeColors = [
        'struk'    => 'bg-info-subtle text-info',
        'kwitansi' => 'bg-success-subtle text-success',
        'nota'     => 'bg-warning-subtle text-warning',
        'faktur'   => 'bg-primary-subtle text-primary',
        'transfer' => 'bg-purple-subtle text-purple',
        'lainnya'  => 'bg-secondary-subtle text-secondary',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::$typeLabels[$this->evidence_type] ?? ucfirst($this->evidence_type);
    }

    public function getTypeBadgeAttribute(): string
    {
        return self::$typeBadgeColors[$this->evidence_type] ?? 'bg-secondary-subtle text-secondary';
    }

    // ─── Auto-generate evidence number ───────────────────────
    public static function generateNumber(string $date = null): string
    {
        $date   = $date ?? now()->format('Y-m-d');
        $prefix = 'BKT/' . Carbon::parse($date)->format('Ymd') . '/';

        $last = self::where('evidence_number', 'like', $prefix . '%')
            ->orderByDesc('evidence_number')
            ->value('evidence_number');

        $seq = $last ? (int) substr($last, -4) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    // ─── Relationships ────────────────────────────────────────
    public function mutasiKas()
    {
        return $this->belongsTo(MutasiKas::class, 'mutation_id', 'mutasi_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Scopes ───────────────────────────────────────────────
    public function scopeByUser($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }
}
