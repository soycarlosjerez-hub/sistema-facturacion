<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluacionProveedor extends Model
{
    use HasFactory, Auditable, TenantScope, SoftDeletes;

    protected $table = 'evaluaciones_proveedores';

    protected $fillable = [
        'fecha',
        'proveedor_id',
        'evaluado_por',
        'documentos_cumplen',
        'documentacion_completa',
        'criterios',
        'total_puntuacion',
        'observaciones',
        'estado',
        'tenant_id',
    ];

    protected $casts = [
        'fecha'               => 'date',
        'documentos_cumplen'  => 'boolean',
        'documentacion_completa' => 'boolean',
        'criterios'           => 'array',
        'total_puntuacion'    => 'decimal:2',
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

    public function documentos()
    {
        return $this->hasMany(EvaluacionProveedorDocumento::class);
    }

    public function incumplimientos()
    {
        return $this->hasMany(IncumplimientoProveedor::class, 'evaluacion_proveedor_id');
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

    public function scopeCalificados($query)
    {
        return $query->where('estado', 'calificado');
    }

    public function scopeNoCalificados($query)
    {
        return $query->where('estado', 'no_calificado');
    }

    public function scopeProvisionales($query)
    {
        return $query->where('estado', 'provisional');
    }

    public function scopePorProveedor($query, int $proveedorId)
    {
        return $query->where('proveedor_id', $proveedorId);
    }

    public function scopeDelPeriodo($query, string $fechaInicio = null, string $fechaFin = null)
    {
        if ($fechaInicio) {
            $query->where('fecha', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->where('fecha', '<=', $fechaFin);
        }

        return $query;
    }

    public function scopeConDocumentacionCompleta($query)
    {
        return $query->where('documentacion_completa', true);
    }

    // -- Accessors --

    public function getEstadoLabelAttribute(): string
    {
        $labels = [
            'calificado'      => 'Calificado',
            'no_calificado'   => 'No Calificado',
            'provisional'     => 'Provisional',
        ];

        return $labels[$this->estado ?? 'provisional'] ?? 'Provisional';
    }

    public function getColorBadgeEstadoAttribute(): string
    {
        $colors = [
            'calificado'      => 'success',
            'no_calificado'   => 'danger',
            'provisional'     => 'warning',
        ];

        return $colors[$this->estado ?? 'provisional'] ?? 'secondary';
    }

    public function getTotalPuntuacionLabelAttribute(): string
    {
        return number_format($this->total_puntuacion, 2) . ' / 25';
    }

    public function getCriteriosLabelAttribute(): string
    {
        if (!$this->criterios) {
            return '—';
        }

        $criterios = $this->criterios;
        $parts = [];

        if (isset($criterios['calidad'])) {
            $parts[] = "Calidad: {$criterios['calidad']}/5";
        }
        if (isset($criterios['precio'])) {
            $parts[] = "Precio: {$criterios['precio']}/5";
        }
        if (isset($criterios['entrega_puntualidad'])) {
            $parts[] = "Entrega: {$criterios['entrega_puntualidad']}/5";
        }
        if (isset($criterios['servicio_soporte'])) {
            $parts[] = "Soporte: {$criterios['servicio_soporte']}/5";
        }
        if (isset($criterios['cumplimiento_normas'])) {
            $parts[] = "Normas: {$criterios['cumplimiento_normas']}/5";
        }

        return implode(' | ', $parts);
    }

    public function getEvaluadoPorLabelAttribute(): string
    {
        return $this->evaluadoPor?->name ?? '—';
    }

    public function getProveedorNombreAttribute(): string
    {
        return $this->proveedor?->nombre ?? '—';
    }

    public function getDocumentosCumplenLabelAttribute(): string
    {
        return $this->documentos_cumplen ? 'Sí' : 'No';
    }

    public function getDocumentacionCompletaLabelAttribute(): string
    {
        return $this->documentacion_completa ? 'Sí' : 'No';
    }

    public function getFechaLabelAttribute(): string
    {
        return $this->fecha?->format('d/m/Y') ?? '—';
    }

    // -- Helpers --

    /**
     * Calcula la puntuación total a partir de los criterios.
     */
    public function calcularTotalPuntuacion(): float
    {
        if (!$this->criterios) {
            return 0;
        }

        $criterios = $this->criterios;
        $total = 0;
        $count = 0;

        $keys = ['calidad', 'precio', 'entrega_puntualidad', 'servicio_soporte', 'cumplimiento_normas'];

        foreach ($keys as $key) {
            if (isset($criterios[$key])) {
                $valor = (int) $criterios[$key];
                $valor = max(1, min(5, $valor));
                $total += $valor;
                $count++;
            }
        }

        if ($count === 0) {
            return 0;
        }

        return round($total / $count, 2);
    }

    /**
     * Actualiza la puntuación total basada en criterios.
     */
    public function actualizarPuntuacion(): static
    {
        if (!$this->criterios) {
            $this->total_puntuacion = 0;
            $this->saveQuietly();
            return $this;
        }

        switch ($this->estado) {
            case 'calificado':
                $maximo = 4.5;
                $accion = 'calificado';
                $color = 'success';
                break;
            case 'provisional':
                $maximo = 3.5;
                $accion = 'provisional';
                $color = 'warning';
                break;
            case 'no_calificado':
            default:
                $maximo = 0;
                $accion = 'no_calificado';
                $color = 'danger';
                break;
        }

        $total = $this->total_puntuacion ?? 0; // Puntuación promedio ponderada

        if ($total >= $maximo && !$this->documentos_cumplen) {
            $this->documentos_cumplen = true;
            $this->saveQuietly();
        }

        return $this;
    }

    public function auditLabel(): string
    {
        return "Evaluación Proveedor #{$this->id}: {$this->proveedor_nombre} ({$this->estado_label})";
    }

    /**
     * Opciones para select de estados.
     */
    public static function getBadgesForSelect(): array
    {
        return [
            'calificado'      => ['label' => 'Calificado', 'color' => 'success', 'value' => 'calificado'],
            'no_calificado'   => ['label' => 'No Calificado', 'color' => 'danger', 'value' => 'no_calificado'],
            'provisional'     => ['label' => 'Provisional', 'color' => 'warning', 'value' => 'provisional'],
        ];
    }

    /**
     * Opciones para select de criterios.
     */
    public static function getCriteriosOptions(): array
    {
        return [
            'calidad'           => 'Calidad',
            'precio'            => 'Precio',
            'entrega_puntualidad' => 'Entrega Puntualidad',
            'servicio_soporte'  => 'Servicio y Soporte',
            'cumplimiento_normas' => 'Cumplimiento Normas',
        ];
    }
}
