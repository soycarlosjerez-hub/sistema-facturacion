<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetenciaEmpleado extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $table = 'competencias_empleados';

    protected $fillable = [
        'usuario_id',
        'puesto',
        'competencia',
        'nivel',
        'fecha_evaluacion',
        'evidencia_tipo',
        'archivo_evidencia',
        'evaluado_por',
        'tenant_id',
    ];

    protected $casts = [
        'nivel'           => 'integer',
        'fecha_evaluacion' => 'date',
    ];

    // -- Relaciones --

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
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

    public function scopePorPuesto($query, string $puesto)
    {
        return $query->where('puesto', 'like', "%{$puesto}%");
    }

    public function scopePorCompetencia($query, string $competencia)
    {
        return $query->where('competencia', 'like', "%{$competencia}%");
    }

    public function scopePorNivel($query, int $nivel)
    {
        return $query->where('nivel', $nivel);
    }

    public function scopeNivelMinimo($query, int $min)
    {
        return $query->where('nivel', '>=', $min);
    }

    public function scopeEvaluedadoUltimosDias($query, int $dias = 90)
    {
        return $query->whereNotNull('fecha_evaluacion')
            ->where('fecha_evaluacion', '>=', now()->subDays($dias)->toDateString());
    }

    public function scopePorUsuario($query, int $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopeConEvidencia($query)
    {
        return $query->whereNotNull('archivo_evidencia');
    }

    public function scopePorEvaluador($query, int $evaluadorId)
    {
        return $query->where('evaluado_por', $evaluadorId);
    }

    // -- Accessors --

    public function getNivelLabelAttribute(): string
    {
        $labels = [
            1 => '1 - Conocimiento básico',
            2 => '2 - Conocimiento intermedio',
            3 => '3 - Competente',
            4 => '4 - Avanzado',
            5 => '5 - Experto',
        ];

        return $labels[$this->nivel ?? 1] ?? '1 - Conocimiento básico';
    }

    public function getNivelNombreAttribute(): string
    {
        $nombres = [
            1 => 'Principiante',
            2 => 'Intermedio',
            3 => 'Competente',
            4 => 'Avanzado',
            5 => 'Experto',
        ];

        return $nombres[$this->nivel ?? 1] ?? 'Principiante';
    }

    public function getColorBadgeNivelAttribute(): string
    {
        $colors = [
            1 => 'secondary',
            2 => 'info',
            3 => 'primary',
            4 => 'warning',
            5 => 'success',
        ];

        return $colors[$this->nivel ?? 1] ?? 'secondary';
    }

    public function getEvidenciaTipoLabelAttribute(): string
    {
        $labels = [
            'titulo'     => 'Título / Diploma',
            'certificado'=> 'Certificado',
            'curso'      => 'Curso completado',
            'experiencia'=> 'Experiencia laboral',
            'evaluacion' => 'Evaluación interna',
            'referencia' => 'Referencia profesional',
        ];

        return $labels[$this->evidencia_tipo ?? 'titulo'] ?? 'Título / Diploma';
    }

    public function getArchivoEvidenciaUrlAttribute(): ?string
    {
        if (!$this->archivo_evidencia) {
            return null;
        }

        return asset('storage/' . $this->archivo_evidencia);
    }

    public function getFechaEvaluacionHaceAttribute(): string
    {
        if (!$this->fecha_evaluacion) {
            return 'Sin evaluar';
        }

        return $this->fecha_evaluacion->diffForHumans();
    }

    public function getEvaluadorLabelAttribute(): string
    {
        return $this->evaluadoPor?->name ?? (auth()->user()?->name ?? '—');
    }

    // -- Helpers --

    /**
     * Actualiza el nivel de competencia con una nueva evaluación.
     */
    public function actualizarNivel(int $nuevoNivel, ?string $nuevaEvidencia = null): static
    {
        $this->nivel = max(1, min(5, $nuevoNivel));

        if ($nuevaEvidencia) {
            $this->archivo_evidencia = $nuevaEvidencia;
        }

        $this->fecha_evaluacion = now()->toDateString();
        $this->saveQuietly();

        return $this;
    }

    public function auditLabel(): string
    {
        return "Competencia {$this->usuario_id}: {$this->competencia} (Nivel {$this->nivel})";
    }
}
