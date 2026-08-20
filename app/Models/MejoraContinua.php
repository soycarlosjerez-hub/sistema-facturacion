<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MejoraContinua extends Model
{
    use HasFactory, Auditable, TenantScope, SoftDeletes;

    protected $table = 'mejoras_continuas';

    protected $fillable = [
        'numero',
        'titulo',
        'descripcion',
        'origen',
        'prioridad',
        'impacto',
        'fase',
        'responsable_id',
        'fecha_propuesta',
        'fecha_limite',
        'fecha_completar',
        'beneficios_esperados',
        'beneficios_logrados',
        'ahorro_estimado',
        'costo_estimado',
        'creado_por',
        'modificado_por',
        'tenant_id',
        'riesgo_id',
        'nc_id',
        'auditoria_id',
    ];

    protected $casts = [
        'fecha_propuesta'    => 'date',
        'fecha_limite'       => 'date',
        'fecha_completar'    => 'date',
        'ahorro_estimado'    => 'decimal:2',
        'costo_estimado'     => 'decimal:2',
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

    public function riesgo(): BelongsTo
    {
        return $this->belongsTo(Riesgo::class, 'riesgo_id');
    }

    public function nc(): BelongsTo
    {
        return $this->belongsTo(NoConformidad::class, 'nc_id');
    }

    public function auditoria(): BelongsTo
    {
        return $this->belongsTo(AuditoriaInterna::class, 'auditoria_id');
    }

    public function propuestas(): HasMany
    {
        return $this->hasMany(PropuestaMejora::class);
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

    public function scopePorFase($query, string $fase)
    {
        return $query->where('fase', $fase);
    }

    public function scopePropuestas($query)
    {
        return $query->where('fase', 'propuesta');
    }

    public function scopeEvaluando($query)
    {
        return $query->where('fase', 'evaluando');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('fase', 'aprobada');
    }

    public function scopeEnCurso($query)
    {
        return $query->where('fase', 'en_curso');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('fase', 'completada');
    }

    public function scopeVerificadas($query)
    {
        return $query->where('fase', 'verificada');
    }

    public function scopeCerradas($query)
    {
        return $query->where('fase', 'cerrada');
    }

    public function scopePorPrioridad($query, string $prioridad)
    {
        return $query->where('prioridad', $prioridad);
    }

    public function scopeUrgentes($query)
    {
        return $query->where('prioridad', 'urgente');
    }

    public function scopePorImpacto($query, string $impacto)
    {
        return $query->where('impacto', $impacto);
    }

    public function scopeDeAltoImpacto($query)
    {
        return $query->where('impacto', 'alto');
    }

    public function scopePorResponsable($query, int $responsableId)
    {
        return $query->where('responsable_id', $responsableId);
    }

    public function scopePorOrigen($query, string $origen)
    {
        return $query->where('origen', $origen);
    }

    public function scopeDelPeriodo($query, string $fechaInicio, string $fechaFin)
    {
        return $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
    }

    public function scopeConAhorro($query)
    {
        return $query->whereNotNull('ahorro_estimado')
            ->where('ahorro_estimado', '>', 0);
    }

    // -- Accessors --

    public function getFaseLabelAttribute(): string
    {
        $labels = [
            'propuesta'     => 'Propuesta',
            'evaluando'     => 'Evaluando',
            'aprobada'      => 'Aprobada',
            'en_curso'      => 'En Curso',
            'completada'    => 'Completada',
            'verificada'    => 'Verificada',
            'cerrada'       => 'Cerrada',
        ];

        return $labels[$this->fase ?? 'propuesta'] ?? 'Propuesta';
    }

    public function getColorBadgeFaseAttribute(): string
    {
        $colors = [
            'propuesta'     => 'secondary',
            'evaluando'     => 'info',
            'aprobada'      => 'primary',
            'en_curso'      => 'warning',
            'completada'    => 'success',
            'verificada'    => 'success',
            'cerrada'       => 'dark',
        ];

        return $colors[$this->fase ?? 'propuesta'] ?? 'secondary';
    }

    public function getPrioridadLabelAttribute(): string
    {
        $labels = [
            'baja'    => 'Baja',
            'media'   => 'Media',
            'alta'    => 'Alta',
            'urgente' => 'Urgente',
        ];

        return $labels[$this->prioridad ?? 'media'] ?? 'Media';
    }

    public function getColorBadgePrioridadAttribute(): string
    {
        $colors = [
            'baja'    => 'info',
            'media'   => 'warning',
            'alta'    => 'danger',
            'urgente' => 'dark',
        ];

        return $colors[$this->prioridad ?? 'media'] ?? 'secondary';
    }

    public function getImpactoLabelAttribute(): string
    {
        $labels = [
            'bajo'  => 'Bajo',
            'medio' => 'Medio',
            'alto'  => 'Alto',
        ];

        return $labels[$this->impacto ?? 'medio'] ?? 'Medio';
    }

    public function getColorBadgeImpactoAttribute(): string
    {
        $colors = [
            'bajo'  => 'info',
            'medio' => 'warning',
            'alto'  => 'danger',
        ];

        return $colors[$this->impacto ?? 'medio'] ?? 'secondary';
    }

    public function getResponsableLabelAttribute(): string
    {
        return $this->responsable?->name ?? 'Sin asignar';
    }

    public function getFechaPropuestaLabelAttribute(): string
    {
        return $this->fecha_propuesta?->format('d/m/Y') ?? '—';
    }

    public function getFechaLimiteLabelAttribute(): string
    {
        return $this->fecha_limite?->format('d/m/Y') ?? 'Sin límite';
    }

    public function getFechaCompletarLabelAttribute(): string
    {
        return $this->fecha_completar?->format('d/m/Y') ?? '—';
    }

    public function getNumeroLabelAttribute(): string
    {
        return $this->numero ?? sprintf('MC-#%d', $this->id);
    }

    public function getAhorroEstimadoLabelAttribute(): string
    {
        if (!$this->ahorro_estimado) {
            return '—';
        }

        return 'RD$ ' . number_format($this->ahorro_estimado, 2);
    }

    public function getCostoEstimadoLabelAttribute(): string
    {
        if (!$this->costo_estimado) {
            return '—';
        }

        return 'RD$ ' . number_format($this->costo_estimado, 2);
    }

    public function getTiqueAhorroAttribute(): ?float
    {
        if (!$this->ahorro_estimado || !$this->costo_estimado) {
            return null;
        }

        if ($this->costo_estimado <= 0) {
            return 0;
        }

        return round((($this->ahorro_estimado - $this->costo_estimado) / $this->costo_estimado) * 100, 2);
    }

    public function getTituloTruncadoAttribute(): string
    {
        return strlen($this->titulo) > 60
            ? substr($this->titulo, 0, 60) . '...'
            : $this->titulo;
    }

    // -- Helpers --

    /**
     * Avanza a la siguiente fase.
     */
    public function avanzarFase(string $nuevaFase): static
    {
        $this->fase = $nuevaFase;

        if ($nuevaFase === 'en_curso' && !$this->fecha_inicio) {
            // fecha_inicio is not a field, so we skip
        }

        if ($nuevaFase === 'completada') {
            $this->fecha_completar = now()->toDateString();
        }

        $this->saveQuietly();

        return $this;
    }

    /**
     * Establece que el beneficio esperado ya fue logrado.
     */
    public function registrarBeneficioLogrado(string $beneficioLogrado): static
    {
        $this->beneficios_logrados = $beneficioLogrado;
        $this->fase = 'verificada';
        $this->fecha_completar = now()->toDateString();
        $this->saveQuietly();

        return $this;
    }

    public function auditLabel(): string
    {
        return "Mejora #{$this->numero}: {$this->titulo} ({$this->fase_label})";
    }

    /**
     * Opciones para select de fases.
     */
    public static function getBadgesForSelect(): array
    {
        return [
            'propuesta'     => ['label' => 'Propuesta', 'color' => 'secondary', 'value' => 'propuesta'],
            'evaluando'     => ['label' => 'Evaluando', 'color' => 'info', 'value' => 'evaluando'],
            'aprobada'      => ['label' => 'Aprobada', 'color' => 'primary', 'value' => 'aprobada'],
            'en_curso'      => ['label' => 'En Curso', 'color' => 'warning', 'value' => 'en_curso'],
            'completada'    => ['label' => 'Completada', 'color' => 'success', 'value' => 'completada'],
            'verificada'    => ['label' => 'Verificada', 'color' => 'success', 'value' => 'verificada'],
            'cerrada'       => ['label' => 'Cerrada', 'color' => 'dark', 'value' => 'cerrada'],
        ];
    }

    /**
     * Opciones para select de prioridades.
     */
    public static function getPrioridadOptions(): array
    {
        return [
            'baja'    => 'Baja',
            'media'   => 'Media',
            'alta'    => 'Alta',
            'urgente' => 'Urgente',
        ];
    }

    /**
     * Opciones para select de impacto.
     */
    public static function getImpactoOptions(): array
    {
        return [
            'bajo'  => 'Bajo',
            'medio' => 'Medio',
            'alto'  => 'Alto',
        ];
    }

    /**
     * Opciones para select de orígenes.
     */
    public static function getOrigenOptions(): array
    {
        return [
            'sugerencia'        => 'Sugerencia',
            'auditoria'         => 'Auditoría',
            'nc'                => 'No Conformidad',
            'direccion'         => 'Dirección',
            'indicador'         => 'Indicador',
            'otro'              => 'Otro',
        ];
    }
}
