<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicionObjetivo extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $table = 'mediciones_objetivo';

    protected $fillable = [
        'objetivo_calidad_id',
        'fecha',
        'valor',
        'cumplimiento',
        'observaciones',
        'registrado_por',
        'tenant_id',
    ];

    protected $casts = [
        'fecha'        => 'date',
        'valor'        => 'decimal:2',
        'cumplimiento' => 'decimal:2',
    ];

    // -- Relaciones --

    public function objetivo(): BelongsTo
    {
        return $this->belongsTo(ObjetivoCalidad::class, 'objetivo_calidad_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'model');
    }

    // -- Scopes --

    public function scopeDeLaFecha($query, string|array $fecha)
    {
        if (is_array($fecha)) {
            return $query->whereBetween('fecha', $fecha);
        }

        return $query->whereDate('fecha', $fecha);
    }

    public function scopeDeLaFechaDesde($query, string $fecha)
    {
        return $query->where('fecha', '>=', $fecha);
    }

    public function scopeDeLaFechaHasta($query, string $fecha)
    {
        return $query->where('fecha', '<=', $fecha);
    }

    public function scopeConCumplimientoMayor($query, float $min)
    {
        return $query->where('cumplimiento', '>=', $min);
    }

    public function scopeConCumplimientoMenor($query, float $max)
    {
        return $query->where('cumplimiento', '<=', $max);
    }

    public function scopePorResponsable($query, int $usuarioId)
    {
        return $query->where('registrado_por', $usuarioId);
    }

    // -- Accessors --

    public function getCumplimientoBarAttribute(): string
    {
        return $this->cumplimiento >= 100
            ? 'bg-success'
            : ($this->cumplimiento >= 75 ? 'bg-warning' : 'bg-danger');
    }

    public function getMesLabelAttribute(): string
    {
        return $this->fecha?->locale('es_ES')->translatedFormat('F Y')
            ?? $this->fecha?->format('F Y')
            ?? '';
    }

    public function auditLabel(): string
    {
        return "Medición {$this->fecha->format('d/m/Y')}: {$this->valor} ({$this->cumplimiento}%)";
    }
}
