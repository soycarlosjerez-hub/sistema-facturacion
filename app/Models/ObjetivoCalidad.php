<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObjetivoCalidad extends Model
{
    use HasFactory, Auditable, TenantScope, SoftDeletes;

    protected $table = 'objetivos_calidad';

    protected $fillable = [
        'codigo',
        'titulo',
        'descripcion',
        'indicador',
        'meta',
        'valor_actual',
        'unidad',
        'periodo_inicio',
        'periodo_fin',
        'responsable_id',
        'estado',
        'cumplimiento',
        'evidencias',
        'acciones_mejora',
        'kpi_asociado_id',
        'creado_por',
        'modificado_por',
        'tenant_id',
    ];

    protected $casts = [
        'meta'            => 'decimal:2',
        'valor_actual'    => 'decimal:2',
        'cumplimiento'    => 'decimal:2',
        'periodo_inicio'  => 'date',
        'periodo_fin'     => 'date',
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

    public function mediciones(): HasMany
    {
        return $this->hasMany(MedicionObjetivo::class);
    }

    public function kpiAsociado(): BelongsTo
    {
        return $this->belongsTo(MejoraContinua::class, 'kpi_asociado_id');
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

    public function scopeEnCurso($query)
    {
        return $query->where('estado', 'en_curso');
    }

    public function scopeCumplidos($query)
    {
        return $query->where('estado', 'cumplido');
    }

    public function scopeAtrasados($query)
    {
        return $query->where('estado', 'atrasado');
    }

    public function scopePorResponsable($query, int $responsableId)
    {
        return $query->where('responsable_id', $responsableId);
    }

    public function scopeDelPeriodo($query, string $fechaInicio = null, string $fechaFin = null)
    {
        if ($fechaInicio) {
            $query->where('periodo_fin', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->where('periodo_inicio', '<=', $fechaFin);
        }

        return $query;
    }

    public function scopePorIndicador($query, string $indicador)
    {
        return $query->where('indicador', 'like', "%{$indicador}%");
    }

    // -- Accessors --

    public function getEstadoLabelAttribute(): string
    {
        $labels = [
            'en_curso'    => 'En Curso',
            'cumplido'    => 'Cumplido',
            'no_cumplido' => 'No Cumplido',
            'atrasado'    => 'Atrasado',
        ];

        return $labels[$this->estado ?? 'en_curso'] ?? 'En Curso';
    }

    public function getColorBadgeEstadoAttribute(): string
    {
        $colors = [
            'en_curso'    => 'info',
            'cumplido'    => 'success',
            'no_cumplido' => 'danger',
            'atrasado'    => 'warning',
        ];

        return $colors[$this->estado ?? 'en_curso'] ?? 'secondary';
    }

    public function getCumplimientoBarAttribute(): string
    {
        return $this->cumplimiento >= 100
            ? 'bg-success'
            : ($this->cumplimiento >= 75 ? 'bg-warning' : 'bg-danger');
    }

    public function getValorDiferenciaAttribute(): float
    {
        return round($this->valor_actual - $this->meta, 2);
    }

    public function estaCumplido(): bool
    {
        if ($this->meta <= 0) {
            return $this->valor_actual > 0;
        }

        return $this->valor_actual >= $this->meta;
    }

    public function getPeriodoLabelAttribute(): string
    {
        return $this->periodo_inicio?->format('d/m/Y') . ' → ' . $this->periodo_fin?->format('d/m/Y');
    }

    public function getUnidadesLabelAttribute(): string
    {
        if ($this->unidad === '%') {
            return 'Porcentaje';
        }

        return "{$this->unidad} (" . ucfirst($this->unidad) . ")";
    }

    // -- Helpers --

    /**
     * Registra una medición del objetivo y actualiza el valor actual.
     */
    public function registrarMedicion(float $valor, string $observaciones = '', ?int $registradoPor = null): MedicionObjetivo
    {
        $cumplimiento = $this->meta > 0
            ? round(($valor / $this->meta) * 100, 2)
            : 0;

        $medicion = $this->mediciones()->create([
            'valor'         => round($valor, 2),
            'cumplimiento'  => round($cumplimiento, 2),
            'observaciones' => $observaciones,
            'registrado_por'=> $registradoPor ?? auth()->id(),
        ]);

        $this->valor_actual = round($valor, 2);
        $this->cumplimiento = round(min($cumplimiento, 100), 2);

        if ($cumplimiento >= 100) {
            $this->estado = 'cumplido';
        } elseif ($cumplimiento < 50 && $this->estado === 'en_curso') {
            $this->estado = 'no_cumplido';
        }

        $this->saveQuietly();

        return $medicion;
    }

    public function auditLabel(): string
    {
        return "Objetivo {$this->codigo}: {$this->titulo}";
    }
}
