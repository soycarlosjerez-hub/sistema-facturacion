<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditoriaInterna extends Model
{
    use HasFactory, Auditable, TenantScope, SoftDeletes;

    protected $table = 'auditorias_internas';

    protected $fillable = [
        'programa_auditoria_id',
        'codigo',
        'area_auditar',
        'responsable_auditoria_id',
        'fecha_programada',
        'fecha_real_inicio',
        'fecha_real_fin',
        'alcance',
        'criterios',
        'estado',
        'cumplimiento_general',
        'tenant_id',
    ];

    protected $casts = [
        'fecha_programada'      => 'date',
        'fecha_real_inicio'     => 'date',
        'fecha_real_fin'        => 'date',
        'cumplimiento_general'  => 'decimal:2',
    ];

    // -- Relaciones --

    public function programa(): BelongsTo
    {
        return $this->belongsTo(ProgramaAuditoria::class, 'programa_auditoria_id');
    }

    public function responsableAuditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_auditoria_id');
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(ChecklistaAuditoria::class, 'auditoria_interna_id');
    }

    public function hallazgos(): HasMany
    {
        return $this->hasMany(HallazgoAuditoria::class, 'auditoria_interna_id');
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

    public function scopeProgramadas($query)
    {
        return $query->where('estado', 'programada');
    }

    public function scopeEnCurso($query)
    {
        return $query->where('estado', 'en_curso');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopeCanceladas($query)
    {
        return $query->where('estado', 'cancelada');
    }

    public function scopePorArea($query, string $area)
    {
        return $query->where('area_auditar', 'like', "%{$area}%");
    }

    public function scopePorResponsable($query, int $responsableId)
    {
        return $query->where('responsable_auditoria_id', $responsableId);
    }

    public function scopeDelPrograma($query, int $programaId)
    {
        return $query->where('programa_auditoria_id', $programaId);
    }

    public function scopeDelAno($query, int $ano)
    {
        return $query->whereYear('fecha_programada', $ano);
    }

    public function scopePorCodigo($query, string $codigo)
    {
        return $query->where('codigo', 'like', "%{$codigo}%");
    }

    public function scopeSinHallazgos($query)
    {
        return $query->whereDoesntHave('hallazgos');
    }

    // -- Accessors --

    public function getEstadoLabelAttribute(): string
    {
        $labels = [
            'programada'  => 'Programada',
            'en_curso'    => 'En Curso',
            'completada'  => 'Completada',
            'cancelada'   => 'Cancelada',
        ];

        return $labels[$this->estado ?? 'programada'] ?? 'Programada';
    }

    public function getColorBadgeEstadoAttribute(): string
    {
        $colors = [
            'programada'  => 'info',
            'en_curso'    => 'warning',
            'completada'  => 'success',
            'cancelada'   => 'secondary',
        ];

        return $colors[$this->estado ?? 'programada'] ?? 'secondary';
    }

    public function getResponsableAuditorLabelAttribute(): string
    {
        return $this->responsableAuditor?->name ?? 'Sin responsable';
    }

    public function getCumplimientoGeneralLabelAttribute(): string
    {
        return number_format($this->cumplimiento_general, 2, '.', '') . '%';
    }

    public function getFechaProgramadaLabelAttribute(): string
    {
        return $this->fecha_programada?->format('d/m/Y') ?? 'Sin programar';
    }

    public function getFechaRealInicioLabelAttribute(): string
    {
        return $this->fecha_real_inicio?->format('d/m/Y') ?? 'Sin iniciar';
    }

    public function getFechaRealFinLabelAttribute(): string
    {
        return $this->fecha_real_fin?->format('d/m/Y') ?? 'Sin finalizar';
    }

    public function getProgramaLabelAttribute(): string
    {
        if (!$this->programa) {
            return 'Sin programa';
        }

        $desc = $this->programa->descripcion ?? '';
        $ano  = $this->programa->ano ?? '';

        if ($desc) {
            return "Programa {$ano}: {$desc}";
        }

        return "Programa {$ano}";
    }

    public function getHallazgoCountAttribute(): int
    {
        return $this->hallazgos()->count();
    }

    public function getNoConformidadCountAttribute(): int
    {
        return $this->hallazgos()->whereNotIn('tipo', ['conforme'])->count();
    }

    public function getConformesCountAttribute(): int
    {
        return $this->hallazgos()->where('tipo', 'conforme')->count();
    }

    public function getObservacionesCountAttribute(): int
    {
        return $this->hallazgos()->where('tipo', 'observacion')->count();
    }

    public function getConformeRatioAttribute(): string
    {
        $total = $this->hallazgos->count();
        if ($total === 0) {
            return '0/0';
        }

        $conformes = $this->conformes_count;
        return "{$conformes}/{$total}";
    }

    public function getHallazgosRatioAttribute(): string
    {
        $total = $this->hallazgos->count();
        if ($total === 0) {
            return '0/0';
        }

        $nods = $this->no_conformidad_count;
        return "{$nods}/{$total}";
    }

    // -- Helpers --

    /**
     * Inicia la auditoría (cambia estado a en_curso).
     */
    public function iniciar(): static
    {
        $this->estado = 'en_curso';
        $this->fecha_real_inicio = now()->toDateString();
        $this->saveQuietly();

        return $this;
    }

    /**
     * Completa la auditoría.
     */
    public function completar(string $cumplimiento): static
    {
        $this->estado = 'completada';
        $this->cumplimiento_general = $cumplimiento;
        $this->fecha_real_fin = now()->toDateString();
        $this->saveQuietly();

        return $this;
    }

    /**
     * Cancela la auditoría.
     */
    public function cancelar(string $motivocancelacion = ''): static
    {
        $this->estado = 'cancelada';
        $this->saveQuietly();

        return $this;
    }

    /**
     * Obtener el porcentaje de conformidad.
     */
    public function getPorcentajeConformeAttribute(): float
    {
        if ($this->checklistItems()->count() === 0) {
            return 0;
        }

        $conformes = $this->checklistItems()->where('cumplimiento', 'conforme')->count();
        $total = $this->checklistItems->count();

        return round(($conformes / $total) * 100, 1);
    }

    public function auditLabel(): string
    {
        return "Auditoría {$this->codigo}: {$this->area_auditar} ({$this->estado_label})";
    }

    /**
     * Opciones para select de estados.
     */
    public static function getBadgesForSelect(): array
    {
        return [
            'programada'  => ['label' => 'Programada', 'color' => 'info', 'value' => 'programada'],
            'en_curso'    => ['label' => 'En Curso', 'color' => 'warning', 'value' => 'en_curso'],
            'completada'  => ['label' => 'Completada', 'color' => 'success', 'value' => 'completada'],
            'cancelada'   => ['label' => 'Cancelada', 'color' => 'secondary', 'value' => 'cancelada'],
        ];
    }
}
