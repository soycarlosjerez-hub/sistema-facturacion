<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncumplimientoProveedor extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $table = 'incumplimientos_proveedores';

    protected $fillable = [
        'evaluacion_proveedor_id',
        'fecha_ocurrencia',
        'descripcion',
        'tipo',
        'gravedad',
        'accion_inmediata',
        'estado',
        'creado_por',
        'modificado_por',
        'tenant_id',
    ];

    protected $casts = [
        'fecha_ocurrencia'    => 'date',
    ];

    // -- Relaciones --

    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(EvaluacionProveedor::class, 'evaluacion_proveedor_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function modificador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modificado_por');
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

    public function scopeAbiertos($query)
    {
        return $query->where('estado', 'abierta');
    }

    public function scopeCerrados($query)
    {
        return $query->where('estado', 'cerrada');
    }

    public function scopeEnSeguimiento($query)
    {
        return $query->where('estado', 'en_seguimiento');
    }

    public function scopePorGravedad($query, string $gravedad)
    {
        return $query->where('gravedad', $gravedad);
    }

    public function scopeGraves($query)
    {
        return $query->where('gravedad', 'grave');
    }

    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorEvaluacion($query, int $evaluacionId)
    {
        return $query->where('evaluacion_proveedor_id', $evaluacionId);
    }

    // -- Accessors --

    public function getEstadoLabelAttribute(): string
    {
        $labels = [
            'abierta'    => 'Abierta',
            'en_seguimiento' => 'En Seguimiento',
            'cerrada'    => 'Cerrada',
        ];

        return $labels[$this->estado ?? 'abierta'] ?? 'Abierta';
    }

    public function getColorBadgeEstadoAttribute(): string
    {
        $colors = [
            'abierta'       => 'danger',
            'en_seguimiento' => 'warning',
            'cerrada'       => 'success',
        ];

        return $colors[$this->estado ?? 'abierta'] ?? 'secondary';
    }

    public function getTipoLabelAttribute(): string
    {
        $labels = [
            'documento_out'      => 'Documento Out',
            'entrega_tarde'      => 'Entrega Tarde',
            'calidad_mala'       => 'Calidad Mala',
            'servicio_poor'      => 'Servicio Deficiente',
            'otro'               => 'Otro',
        ];

        return $labels[$this->tipo ?? 'otro'] ?? 'Otro';
    }

    public function getColorBadgeTipoAttribute(): string
    {
        $colors = [
            'documento_out'      => 'info',
            'entrega_tarde'      => 'warning',
            'calidad_mala'       => 'danger',
            'servicio_poor'      => 'danger',
            'otro'               => 'secondary',
        ];

        return $colors[$this->tipo ?? 'otro'] ?? 'secondary';
    }

    public function getGravedadLabelAttribute(): string
    {
        $labels = [
            'leve'     => 'Leve',
            'moderada' => 'Moderada',
            'grave'    => 'Grave',
        ];

        return $labels[$this->gravedad ?? 'leve'] ?? 'Leve';
    }

    public function getColorBadgeGravedadAttribute(): string
    {
        $colors = [
            'leve'     => 'info',
            'moderada' => 'warning',
            'grave'    => 'danger',
        ];

        return $colors[$this->gravedad ?? 'leve'] ?? 'secondary';
    }

    public function getFechaOcurrenciaLabelAttribute(): string
    {
        return $this->fecha_ocurrencia?->format('d/m/Y') ?? '—';
    }

    // -- Helpers --

    /**
     * Marca el incumplimiento como cerrado con observaciones.
     */
    public function cerrar(string $observaciones = ''): static
    {
        $this->estado = 'cerrada';
        if ($observaciones) {
            $this->accion_inmediata = $observaciones;
        }
        $this->saveQuietly();

        return $this->auditAction('closed', 'Cerró incumplimiento', [], ['estado' => 'cerrada']);
    }

    public function auditLabel(): string
    {
        return "Incumplimiento #{$this->id}: {$this->tipo_label} - {$this->gravedad_label} ({$this->estado_label})";
    }

    /**
     * Opciones para select de tipos.
     */
    public static function getTiposOpciones(): array
    {
        return [
            'documento_out'      => 'Documento Out',
            'entrega_tarde'      => 'Entrega Tarde',
            'calidad_mala'       => 'Calidad Mala',
            'servicio_poor'      => 'Servicio Deficiente',
            'otro'               => 'Otro',
        ];
    }

    /**
     * Opciones para select de gravedad.
     */
    public static function getGravedadOpciones(): array
    {
        return [
            'leve'     => 'Leve',
            'moderada' => 'Moderada',
            'grave'    => 'Grave',
        ];
    }

    /**
     * Opciones para select de estados.
     */
    public static function getBadgesForSelect(): array
    {
        return [
            'abierta'        => ['label' => 'Abierta', 'color' => 'danger', 'value' => 'abierta'],
            'en_seguimiento' => ['label' => 'En Seguimiento', 'color' => 'warning', 'value' => 'en_seguimiento'],
            'cerrada'        => ['label' => 'Cerrada', 'color' => 'success', 'value' => 'cerrada'],
        ];
    }
}
