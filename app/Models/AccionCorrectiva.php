<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AccionCorrectiva extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $table = 'acciones_correctivas';

    protected $fillable = [
        'no_conformidad_id',
        'descripcion',
        'responsable_id',
        'fecha_limite',
        'fecha_inicio',
        'fecha_fin',
        'costo_estimado',
        'estado',
        'evidencias',
        'reporte_final',
        'creado_por',
        'modificado_por',
        'tenant_id',
    ];

    protected $casts = [
        'fecha_limite'      => 'date',
        'fecha_inicio'      => 'date',
        'fecha_fin'         => 'date',
        'costo_estimado'    => 'decimal:2',
    ];

    // -- Relaciones --

    public function noConformidad(): BelongsTo
    {
        return $this->belongsTo(NoConformidad::class);
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

    public function verificacion()
    {
        return $this->hasOne(VerificacionAccion::class);
    }

    public function documentos(): MorphMany
    {
        return $this->morphMany(DocumentoSgc::class, 'auditable');
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

    public function scopeNoAplicable($query)
    {
        return $query->where('estado', 'no_aplicable');
    }

    public function scopePorResponsable($query, int $responsableId)
    {
        return $query->where('responsable_id', $responsableId);
    }

    public function scopePorNC($query, int $noConformidadId)
    {
        return $query->where('no_conformidad_id', $noConformidadId);
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
            ->whereNotIn('estado', ['completada', 'no_aplicable']);
    }

    public function scopeConCosto($query)
    {
        return $query->whereNotNull('costo_estimado')
            ->where('costo_estimado', '>', 0);
    }

    // -- Accessors --

    public function getEstadoLabelAttribute(): string
    {
        $labels = [
            'pendiente'     => 'Pendiente',
            'en_curso'      => 'En Curso',
            'completada'    => 'Completada',
            'no_aplicable'  => 'No Aplicable',
        ];

        return $labels[$this->estado ?? 'pendiente'] ?? 'Pendiente';
    }

    public function getColorBadgeEstadoAttribute(): string
    {
        $colors = [
            'pendiente'     => 'danger',
            'en_curso'      => 'warning',
            'completada'    => 'success',
            'no_aplicable'  => 'secondary',
        ];

        return $colors[$this->estado ?? 'pendiente'] ?? 'secondary';
    }

    public function getResponsableLabelAttribute(): string
    {
        return $this->responsable?->name ?? 'Sin asignar';
    }

    public function getFechaLimiteLabelAttribute(): string
    {
        return $this->fecha_limite?->format('d/m/Y') ?? 'Sin límite';
    }

    public function getFechaInicioLabelAttribute(): string
    {
        return $this->fecha_inicio?->format('d/m/Y') ?? '—';
    }

    public function getFechaFinLabelAttribute(): string
    {
        return $this->fecha_fin?->format('d/m/Y') ?? '—';
    }

    public function getCostoEstimadoLabelAttribute(): string
    {
        if (!$this->costo_estimado) {
            return '—';
        }

        return 'RD$ ' . number_format($this->costo_estimado, 2);
    }

    public function getDescripcionTruncadaAttribute(): string
    {
        if (!$this->descripcion) {
            return '—';
        }

        return strlen($this->descripcion) > 100
            ? substr($this->descripcion, 0, 100) . '...'
            : $this->descripcion;
    }

    public function getEsVencidaAttribute(): bool
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

    public function getEsVerificadaAttribute(): bool
    {
        return (bool) $this->verificacion;
    }

    // -- Helpers --

    /**
     * Inicia la acción correctiva.
     */
    public function iniciar(): static
    {
        $this->estado = 'en_curso';
        $this->fecha_inicio = now()->toDateString();
        $this->saveQuietly();

        return $this;
    }

    /**
     * Marca la acción como completada.
     */
    public function completar(string $reporteFinal = '', string $evidencias = ''): static
    {
        $this->estado = 'completada';
        $this->fecha_fin = now()->toDateString();

        if ($reporteFinal) {
            $this->reporte_final = $reporteFinal;
        }
        if ($evidencias) {
            $this->evidencias = $evidencias;
        }

        $this->saveQuietly();

        return $this;
    }

    /**
     * Marca la acción como no aplicable.
     */
    public function noAplicable(): static
    {
        $this->estado = 'no_aplicable';
        $this->saveQuietly();

        return $this;
    }

    public function auditLabel(): string
    {
        return "Acción Correctiva #{$this->id}: {$this->estado_label} ({$this->responsable_label})";
    }

    /**
     * Opciones para select de estados.
     */
    public static function getBadgesForSelect(): array
    {
        return [
            'pendiente'     => ['label' => 'Pendiente', 'color' => 'danger', 'value' => 'pendiente'],
            'en_curso'      => ['label' => 'En Curso', 'color' => 'warning', 'value' => 'en_curso'],
            'completada'    => ['label' => 'Completada', 'color' => 'success', 'value' => 'completada'],
            'no_aplicable'  => ['label' => 'No Aplicable', 'color' => 'secondary', 'value' => 'no_aplicable'],
        ];
    }
}
