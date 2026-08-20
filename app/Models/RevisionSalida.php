<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevisionSalida extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $table = 'revisiones_direccion_salidas';

    protected $fillable = [
        'revision_direccion_id',
        'descripcion',
        'tipo_accion',
        'responsable_id',
        'fecha_limite',
        'estado',
        'evidencia_complet',
        'creado_por',
        'modificado_por',
        'tenant_id',
    ];

    protected $casts = [
        'fecha_limite'    => 'date',
    ];

    // -- Relaciones --

    public function revision(): BelongsTo
    {
        return $this->belongsTo(RevisionDireccion::class, 'revision_direccion_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
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

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeEnCurso($query)
    {
        return $query->where('estado', 'en_curso');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo_accion', $tipo);
    }

    public function scopePorRevision($query, int $revisionId)
    {
        return $query->where('revision_direccion_id', $revisionId);
    }

    public function scopePorResponsable($query, int $responsableId)
    {
        return $query->where('responsable_id', $responsableId);
    }

    public function scopeVencidas($query)
    {
        return $query->whereNotNull('fecha_limite')
            ->where('fecha_limite', '<', now()->toDateString())
            ->whereNotIn('estado', ['completada', 'no_aplicable']);
    }

    public function scopeProximasAVencer($query, int $dias = 7)
    {
        return $query->whereNotNull('fecha_limite')
            ->where('fecha_limite', '>=', now()->toDateString())
            ->where('fecha_limite', '<=', now()->addDays($dias)->toDateString())
            ->whereNotIn('estado', ['completada']);
    }

    // -- Accessors --

    public function getEstadoLabelAttribute(): string
    {
        $labels = [
            'pendiente'   => 'Pendiente',
            'en_curso'    => 'En Curso',
            'completada'  => 'Completada',
        ];

        return $labels[$this->estado ?? 'pendiente'] ?? 'Pendiente';
    }

    public function getColorBadgeEstadoAttribute(): string
    {
        $colors = [
            'pendiente'   => 'danger',
            'en_curso'    => 'warning',
            'completada'  => 'success',
        ];

        return $colors[$this->estado ?? 'pendiente'] ?? 'secondary';
    }

    public function getTipoAccionLabelAttribute(): string
    {
        $labels = [
            'mejora_cambia'     => 'Mejora / Cambio',
            'recurso_necesario'      => 'Recurso Necesario',
            'decision_urgente' => 'Decisión Urgente',
            'plan_seguimiento'   => 'Plan Seguimiento',
        ];

        return $labels[$this->tipo_accion ?? 'mejora_cambia'] ?? 'Mejora / Cambio';
    }

    public function getColorBadgeTipoAccionAttribute(): string
    {
        $colors = [
            'mejora_cambia'     => 'info',
            'recurso_necesario'      => 'primary',
            'decision_urgente' => 'danger',
            'plan_seguimiento'   => 'warning',
        ];

        return $colors[$this->tipo_accion ?? 'mejora_cambia'] ?? 'secondary';
    }

    public function getResponsableLabelAttribute(): string
    {
        return $this->responsable?->name ?? '—';
    }

    public function getFechaLimiteLabelAttribute(): string
    {
        return $this->fecha_limite?->format('d/m/Y') ?? 'Sin límite';
    }

    public function getEstaVencidaAttribute(): bool
    {
        if (!$this->fecha_limite || $this->estado === 'completada') {
            return false;
        }

        return $this->fecha_limite->isPast();
    }

    public function getDiasRestantesAttribute(): int
    {
        if (!$this->fecha_limite) {
            return 0;
        }

        return now()->diffInDays($this->fecha_limite, false);
    }

    public function getDescripcionTruncadaAttribute(): string
    {
        if (!$this->descripcion) {
            return '—';
        }

        return strlen($this->descripcion) > 80
            ? substr($this->descripcion, 0, 80) . '...'
            : $this->descripcion;
    }

    public function getEvidenciaCompletLabelAttribute(): string
    {
        return $this->evidencia_complet ? 'Con evidencia' : 'Sin evidencia';
    }

    // -- Helpers --

    /**
     * Marca la salida como completada.
     */
    public function completar(string $evidencia = ''): static
    {
        $this->estado = 'completada';
        if ($evidencia) {
            $this->evidencia_complet = $evidencia;
        }
        $this->saveQuietly();

        return $this;
    }

    public function auditLabel(): string
    {
        return "Salida Revisión #{$this->id}: {$this->tipo_accion_label} ({$this->estado_label})";
    }

    /**
     * Opciones para select de tipos de acción.
     */
    public static function getTiposAccionOptions(): array
    {
        return [
            'mejora_cambia'     => 'Mejora / Cambio',
            'recurso_necesario'      => 'Recurso Necesario',
            'decision_urgente' => 'Decisión Urgente',
            'plan_seguimiento'   => 'Plan Seguimiento',
        ];
    }
}
