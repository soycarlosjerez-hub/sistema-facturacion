<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramaAuditoria extends Model
{
    use HasFactory, Auditable, TenantScope, SoftDeletes;

    protected $table = 'programas_auditoria';

    protected $fillable = [
        'ano',
        'descripcion',
        'fecha_programacion',
        'fecha_inicio',
        'fecha_fin',
        'auditor_jefe_id',
        'estado',
        'alcance',
        'criterios',
        'creado_por',
        'modificado_por',
        'tenant_id',
    ];

    protected $casts = [
        'ano'                => 'integer',
        'fecha_programacion' => 'date',
        'fecha_inicio'       => 'date',
        'fecha_fin'          => 'date',
    ];

    // -- Relaciones --

    public function auditorJefe(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_jefe_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function auditorias()
    {
        return $this->hasMany(AuditoriaInterna::class, 'programa_auditoria_id');
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

    public function scopeProgramados($query)
    {
        return $query->where('estado', 'programada');
    }

    public function scopeEnCurso($query)
    {
        return $query->where('estado', 'en_curso');
    }

    public function scopeCompletados($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopeDelAno($query, int $ano)
    {
        return $query->where('ano', $ano);
    }

    public function scopePorAuditorJefe($query, int $auditorJefeId)
    {
        return $query->where('auditor_jefe_id', $auditorJefeId);
    }

    public function scopeEnPeriodo($query, string $fechaInicio, string $fechaFin)
    {
        return $query->where('fecha_inicio', '<=', $fechaFin)
            ->where('fecha_fin', '>=', $fechaInicio);
    }

    public function scopeConAuditorias($query)
    {
        return $query->has('auditorias');
    }

    // -- Accessors --

    public function getEstadoLabelAttribute(): string
    {
        $labels = [
            'programada'  => 'Programada',
            'en_curso'    => 'En Curso',
            'completada'  => 'Completada',
        ];

        return $labels[$this->estado ?? 'programada'] ?? 'Programada';
    }

    public function getColorBadgeEstadoAttribute(): string
    {
        $colors = [
            'programada'  => 'info',
            'en_curso'    => 'warning',
            'completada'  => 'success',
        ];

        return $colors[$this->estado ?? 'programada'] ?? 'secondary';
    }

    public function getAuditorJefeLabelAttribute(): string
    {
        return $this->auditorJefe?->name ?? 'Sin jefe';
    }

    public function getPeriodoLabelAttribute(): string
    {
        $inicio = $this->fecha_inicio?->format('d/m/Y') ?? 'Sin inicio';
        $fin = $this->fecha_fin?->format('d/m/Y') ?? 'Sin fin';

        return "{$inicio} → {$fin}";
    }

    public function getAuditoriasCountAttribute(): int
    {
        return $this->auditorias()->count();
    }

    public function getCompletadasCountAttribute(): int
    {
        return $this->auditorias()->where('estado', 'completada')->count();
    }

    public function getEnCursoCountAttribute(): int
    {
        return $this->auditorias()->where('estado', 'en_curso')->count();
    }

    public function getDescriptioLabelAttribute(): string
    {
        if (!$this->descripcion) {
            return '';
        }

        return $this->descripcion;
    }

    public function getDescripcionTruncadaAttribute(): string
    {
        if (!$this->descripcion) {
            return '';
        }

        return strlen($this->descripcion) > 100
            ? substr($this->descripcion, 0, 100) . '...'
            : $this->descripcion;
    }

    public function getAnoLabelAttribute(): string
    {
        return (string) $this->ano;
    }

    // -- Helpers --

    /**
     * Inicia el programa de auditoría.
     */
    public function iniciar(): static
    {
        $this->estado = 'en_curso';
        $this->fecha_programacion = now()->toDateString();
        $this->saveQuietly();

        return $this;
    }

    /**
     * Completa el programa.
     */
    public function completar(): static
    {
        $this->estado = 'completada';
        $this->saveQuietly();

        return $this;
    }

    public function auditLabel(): string
    {
        $desc = $this->descripcion ?? '';
        $label = $desc ? "{$desc}" : "Sin descripción";

        return "Programa Auditoría {$this->ano}: {$label} ({$this->estado_label})";
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
        ];
    }
}
