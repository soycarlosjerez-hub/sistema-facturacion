<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluacionPeriodicaProveedor extends Model
{
    use HasFactory, Auditable, TenantScope, SoftDeletes;

    protected $table = 'evaluaciones_periodicas_proveedores';

    protected $fillable = [
        'proveedor_id',
        'periodo',
        'evaluacion_general',
        'cumplimiento_ncf',
        'cumplimiento_calidad',
        'tiempo_entrega',
        'comunicacion',
        'observaciones',
        'estado',
        'evaluado_por',
        'tenant_id',
    ];

    protected $casts = [
        'periodo'               => 'integer',
        'evaluacion_general'    => 'integer',
        'cumplimiento_ncf'      => 'integer',
        'cumplimiento_calidad'  => 'integer',
        'tiempo_entrega'        => 'integer',
        'comunicacion'          => 'integer',
    ];

    // -- Relaciones --

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function evaluadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluado_por');
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'model');
    }

    // -- Scopes --

    public function scopePorEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeAprobados($query)
    {
        return $query->where('estado', 'aprobado');
    }

    public function scopeConObservaciones($query)
    {
        return $query->where('estado', 'observaciones');
    }

    public function scopeDesaprobados($query)
    {
        return $query->where('estado', 'desaprobado');
    }

    public function scopePorProveedor($query, int $proveedorId)
    {
        return $query->where('proveedor_id', $proveedorId);
    }

    public function scopeDelAno($query, int $ano)
    {
        return $query->where('periodo', $ano);
    }

    public function scopePorEvaluador($query, int $evaluadorId)
    {
        return $query->where('evaluado_por', $evaluadorId);
    }

    public function scopeConEvalucionGeneral($query, int $minimo)
    {
        return $query->where('evaluacion_general', '>=', $minimo);
    }

    // -- Accessors --

    public function getEstadoLabelAttribute(): string
    {
        $labels = [
            'aprobado'     => 'Aprobado',
            'observaciones' => 'Observaciones',
            'desaprobado'  => 'Desaprobado',
        ];

        return $labels[$this->estado ?? 'observaciones'] ?? 'Observaciones';
    }

    public function getColorBadgeEstadoAttribute(): string
    {
        $colors = [
            'aprobado'      => 'success',
            'observaciones' => 'warning',
            'desaprobado'   => 'danger',
        ];

        return $colors[$this->estado ?? 'observaciones'] ?? 'secondary';
    }

    public function getPeriodoLabelAttribute(): string
    {
        return (string) $this->periodo;
    }

    public function getCalificacionLabelAttribute(): string
    {
        return "{$this->evaluacion_general}/5";
    }

    public function getEvaluacionGeneralLabelAttribute(): string
    {
        $labels = [
            1 => '1 - Muy Bajo',
            2 => '2 - Bajo',
            3 => '3 - Regular',
            4 => '4 - Bueno',
            5 => '5 - Excelente',
        ];

        return $labels[$this->evaluacion_general ?? 3] ?? '—';
    }

    public function getColorBadgeEvaluacionGeneralAttribute(): string
    {
        $valor = (int) $this->evaluacion_general;

        return match ($valor) {
            5 => 'success',
            4 => 'info',
            3 => 'warning',
            default => 'danger',
        };
    }

    public function getEvaluadoPorLabelAttribute(): string
    {
        return $this->evaluadoPor?->name ?? '—';
    }

    public function getProveedorNameAttribute(): string
    {
        return $this->proveedor?->nombre ?? '—';
    }

    // -- Helpers --

    /**
     * Calcula el promedio general de todas las categorías evaluadas.
     */
    public function calcularPromedioGeneral(): float
    {
        $valores = array_filter([
            $this->evaluacion_general,
            $this->cumplimiento_ncf,
            $this->cumplimiento_calidad,
            $this->tiempo_entrega,
            $this->comunicacion,
        ], fn ($v) => !empty($v));

        if (empty($valores)) {
            return 0;
        }

        return round(array_sum($valores) / count($valores), 1);
    }

    /**
     * Determina el estado basado en la evaluación general.
     */
    public function determinarEstado(): static
    {
        if (!$this->evaluacion_general) {
            return $this;
        }

        if ($this->evaluacion_general >= 4) {
            $this->estado = 'aprobado';
        } elseif ($this->evaluacion_general >= 3) {
            $this->estado = 'observaciones';
        } else {
            $this->estado = 'desaprobado';
        }

        $this->saveQuietly();

        return $this;
    }

    public function auditLabel(): string
    {
        return "Evaluación Per. Proveedor #{$this->id}: {$this->proveedor_name} ({$this->periodo_label}) - {$this->estado_label}";
    }

    /**
     * Opciones para select de estados.
     */
    public static function getBadgesForSelect(): array
    {
        return [
            'aprobado'      => ['label' => 'Aprobado', 'color' => 'success', 'value' => 'aprobado'],
            'observaciones' => ['label' => 'Observaciones', 'color' => 'warning', 'value' => 'observaciones'],
            'desaprobado'   => ['label' => 'Desaprobado', 'color' => 'danger', 'value' => 'desaprobado'],
        ];
    }
}
