<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Riesgo extends Model
{
    use HasFactory, Auditable, TenantScope, SoftDeletes;

    protected $table = 'riesgos';

    protected $fillable = [
        'codigo',
        'area',
        'descripcion',
        'causa',
        'consecuencia',
        'probabilidad',
        'impacto',
        'nivel',
        'clasificacion',
        'controles_existentes',
        'plan_accion',
        'fecha_limite',
        'responsable_id',
        'estado',
        'plan_mitigacion',
        'probabilidad_residual',
        'impacto_residual',
        'nivel_residual',
        'proceso_afectado_id',
        'observaciones',
        'creado_por',
        'modificado_por',
        'tenant_id',
        'documento_sgc_id',
        'auditoria_id',
        'mejora_id',
    ];

    protected $casts = [
        'probabilidad'            => 'integer',
        'impacto'                 => 'integer',
        'nivel'                   => 'integer',
        'probabilidad_residual'   => 'integer',
        'impacto_residual'        => 'integer',
        'nivel_residual'          => 'integer',
        'fecha_limite'            => 'date',
    ];

    // -- Relaciones --

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function procesoAfectado(): BelongsTo
    {
        return $this->belongsTo(Proceso::class, 'proceso_afectado_id');
    }

    public function documentoSgc(): BelongsTo
    {
        return $this->belongsTo(DocumentoSgc::class, 'documento_sgc_id');
    }

    public function auditoria(): BelongsTo
    {
        return $this->belongsTo(AuditoriaInterna::class, 'auditoria_id');
    }

    public function mejora(): BelongsTo
    {
        return $this->belongsTo(MejoraContinua::class, 'mejora_id');
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

    public function scopePorClasificacion($query, string $clasificacion)
    {
        return $query->where('clasificacion', $clasificacion);
    }

    public function scopeNivel($query, int $nivel)
    {
        return $query->where('nivel', $nivel);
    }

    public function scopePorArea($query, string $area)
    {
        return $query->where('area', 'like', "%{$area}%");
    }

    public function scopeEnTratamiento($query)
    {
        return $query->where('estado', 'en_tratamiento');
    }

    public function scopeCriticos($query)
    {
        return $query->where('clasificacion', 'critico');
    }

    public function scopeVencidos($query)
    {
        return $query->whereNotNull('fecha_limite')
            ->where('fecha_limite', '<', now()->toDateString())
            ->where('estado', '!=', 'cerrado');
    }

    public function scopeConResponsable($query)
    {
        return $query->whereNotNull('responsable_id');
    }

    // -- Accessors --

    public function getClasificacionLabelAttribute(): string
    {
        $labels = [
            'bajo'   => 'Bajo',
            'medio'  => 'Medio',
            'alto'   => 'Alto',
            'critico'=> 'Crítico',
        ];

        return $labels[$this->clasificacion ?? 'medio'] ?? 'Medio';
    }

    public function getColorBadgeClasificacionAttribute(): string
    {
        $colors = [
            'bajo'   => 'success',
            'medio'  => 'info',
            'alto'   => 'warning',
            'critico'=> 'danger',
        ];

        return $colors[$this->clasificacion ?? 'medio'] ?? 'secondary';
    }

    public function getEstadoLabelAttribute(): string
    {
        $labels = [
            'identificado'     => 'Identificado',
            'en_tratamiento'   => 'En Tratamiento',
            'cerrado'          => 'Cerrado',
        ];

        return $labels[$this->estado ?? 'identificado'] ?? 'Identificado';
    }

    public function getColorBadgeEstadoAttribute(): string
    {
        $colors = [
            'identificado'     => 'info',
            'en_tratamiento'   => 'warning',
            'cerrado'          => 'success',
        ];

        return $colors[$this->estado ?? 'identificado'] ?? 'secondary';
    }

    public function getNivelLabelAttribute(): string
    {
        return "{$this->probabilidad} × {$this->impacto} = {$this->nivel}";
    }

    public function getNivelResidualLabelAttribute(): string
    {
        if ($this->nivel_residual === 0) {
            return 'Cálculo pendiente';
        }
        return "Residual: {$this->probabilidad_residual} × {$this->impacto_residual} = {$this->nivel_residual}";
    }

    public function estaVencido(): bool
    {
        if (!$this->fecha_limite || $this->estado === 'cerrado') {
            return false;
        }

        return $this->fecha_limite->isPast();
    }

    // -- Helpers --

    /**
     * Calcula el nivel de riesgo basado en probabilidad × impacto.
     */
    public function calcularNivel(int $probabilidad, int $impacto): int
    {
        return $probabilidad * $impacto;
    }

    /**
     * Determina la clasificación según el nivel calculado.
     */
    public function getClasificacionPorNivel(int $nivel): string
    {
        if ($nivel <= 4) {
            return 'bajo';
        }

        if ($nivel <= 9) {
            return 'medio';
        }

        if ($nivel <= 15) {
            return 'alto';
        }

        return 'critico';
    }

    public function auditLabel(): string
    {
        return "Riesgo {$this->codigo}: {$this->area} ({$this->clasificacion_label})";
    }
}
