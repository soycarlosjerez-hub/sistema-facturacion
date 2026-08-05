<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class Encargo extends Model
{
    use HasFactory, Auditable, TenantScope, SoftDeletes;

    protected $table = 'encargos';

    protected $fillable = [
        'cliente_id',
        'user_id',
        'titulo',
        'descripcion',
        'boceto_path',
        'sketch_approved',
        'precio_total',
        'deposito',
        'saldo',
        'avance_porcentaje',
        'estimated_completion',
        'actual_completion',
        'status',
        'notas',
        'progress_photos',
        'tenant_id',
    ];

    protected $casts = [
        'sketch_approved' => 'boolean',
        'precio_total' => 'decimal:2',
        'deposito' => 'decimal:2',
        'saldo' => 'decimal:2',
        'avance_porcentaje' => 'integer',
        'estimated_completion' => 'date',
        'actual_completion' => 'date',
        'progress_photos' => 'array',
    ];

    public const STATUSES = [
        'solicitado' => ['label' => 'Solicitado', 'color' => 'secondary', 'icon' => 'mail'],
        'aprobado' => ['label' => 'Aprobado', 'color' => 'info', 'icon' => 'check'],
        'deposito' => ['label' => 'Deposito', 'color' => 'warning', 'icon' => 'cash'],
        'creacion' => ['label' => 'Creacion', 'color' => 'primary', 'icon' => 'hammer'],
        'progreso' => ['label' => 'En Progreso', 'color' => 'primary', 'icon' => 'gear'],
        'aprobado_final' => ['label' => 'Aprobado Final', 'color' => 'success', 'icon' => 'star'],
        'listo_entrega' => ['label' => 'Listo para Entrega', 'color' => 'success', 'icon' => 'gift'],
        'completado' => ['label' => 'Completado', 'color' => 'success', 'icon' => 'check-circle'],
        'cancelado' => ['label' => 'Cancelado', 'color' => 'danger', 'icon' => 'x-circle'],
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status && isset(self::STATUSES[$this->status])
            ? self::STATUSES[$this->status]['label']
            : 'Desconocido';
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUSES[$this->status]['color'] ?? 'secondary';
    }

    public function getStatusIconAttribute(): string
    {
        return self::STATUSES[$this->status]['icon'] ?? 'circle';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->estimated_completion
            && $this->estimated_completion->isPast()
            && !in_array($this->status, ['completado', 'cancelado']);
    }

    public function getProgressPercentageBar(): string
    {
        $pct = (int) $this->avance_porcentaje;
        if ($pct >= 100) return 'success';
        if ($pct >= 60) return 'primary';
        if ($pct >= 30) return 'warning';
        return 'secondary';
    }

    public function formatProgressPhotos(): array
    {
        $photos = $this->progress_photos ?? [];
        if (!is_array($photos)) {
            return [];
        }
        return array_map(function ($p) {
            if (str_starts_with($p, 'http')) return $p;
            return asset('storage/' . $p);
        }, $photos);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completado', 'cancelado']);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('titulo', 'like', "%{$search}%");
        })->orWhereHas('cliente', function ($q) use ($search) {
            $q->where('nombre', 'like', "%{$search}%");
        });
    }

    public function scopeOrderByCompletion($query, $order = 'asc')
    {
        return $query->orderBy('estimated_completion', $order);
    }
}
