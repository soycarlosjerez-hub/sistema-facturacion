<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Capacitacion extends Model
{
    use HasFactory, Auditable, TenantScope, SoftDeletes;

    protected $table = 'capacitaciones';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'lugar',
        'modalidad',
        'instructor_id',
        'instructor_nombre',
        'duracion_horas',
        'temas',
        'estado',
        'archivo_evidencia',
        'archivo_certificado',
        'evaluacion_calificacion',
        'creado_por',
        'modificado_por',
        'tenant_id',
        'documento_sgc_id',
    ];

    protected $casts = [
        'fecha'                   => 'date',
        'hora_inicio'             => 'datetime:H:i',
        'hora_fin'                => 'datetime:H:i',
        'duracion_horas'          => 'integer',
        'evaluacion_calificacion' => 'integer',
    ];

    // -- Relaciones --

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function instructorMod(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function participantes(): HasMany
    {
        return $this->hasMany(ParticipanteCapacitacion::class);
    }

    public function documentoSgc(): BelongsTo
    {
        return $this->belongsTo(DocumentoSgc::class, 'documento_sgc_id');
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

    public function scopeCancelar($query)
    {
        return $query->where('estado', 'cancelada');
    }

    public function scopeDeLaFecha($query, string $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    public function scopeDePrimerPlazo($query, int $dias = 7)
    {
        return $query->where('estado', 'programada')
            ->where('fecha', '>=', now()->toDateString())
            ->where('fecha', '<=', now()->addDays($dias)->toDateString());
    }

    public function scopeConInstructor($query, int $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    public function scopePorModalidad($query, string $modalidad)
    {
        return $query->where('modalidad', $modalidad);
    }

    public function scopePendientesEvidencia($query)
    {
        return $query->where('estado', 'completada')
            ->whereNull('archivo_evidencia');
    }

    // -- Accessors --

    public function getModalidadLabelAttribute(): string
    {
        $labels = [
            'presencial' => 'Presencial',
            'virtual'    => 'Virtual',
            'hibrido'    => 'Híbrido',
        ];

        return $labels[$this->modalidad ?? 'presencial'] ?? 'Presencial';
    }

    public function getColorBadgeModalidadAttribute(): string
    {
        $colors = [
            'presencial' => 'primary',
            'virtual'    => 'info',
            'hibrido'    => 'warning',
        ];

        return $colors[$this->modalidad ?? 'presencial'] ?? 'secondary';
    }

    public function getEstadoLabelAttribute(): string
    {
        $labels = [
            'programada'   => 'Programada',
            'en_curso'     => 'En Curso',
            'completada'   => 'Completada',
            'cancelada'    => 'Cancelada',
        ];

        return $labels[$this->estado ?? 'programada'] ?? 'Programada';
    }

    public function getColorBadgeEstadoAttribute(): string
    {
        $colors = [
            'programada'   => 'info',
            'en_curso'     => 'warning',
            'completada'   => 'success',
            'cancelada'    => 'secondary',
        ];

        return $colors[$this->estado ?? 'programada'] ?? 'secondary';
    }

    public function getInstructorLabelAttribute(): string
    {
        if ($this->instructorNombre) {
            return $this->instructor_nombre;
        }

        return $this->instructorMod?->name ?? '—';
    }

    public function getDuracionLabelAttribute(): string
    {
        if ($this->duracion_horas >= 1) {
            return $this->duracion_horas . ' hora' . ($this->duracion_horas > 1 ? 's' : '');
        }

        return round($this->duracion_horas * 60) . ' minutos';
    }

    public function getHorarioLabelAttribute(): string
    {
        $inicio = $this->hora_inicio ? date('H:i', strtotime($this->hora_inicio)) : '--:--';
        $fin = $this->hora_fin ? date('H:i', strtotime($this->hora_fin)) : '--:--';

        return "{$inicio} - {$fin}";
    }

    public function getAsistenciaCountAttribute(): int
    {
        return $this->participantes()->where('estado', 'asistio')->count();
    }

    public function getAsistenciaPercentageAttribute(): float
    {
        $total = $this->participantes->count();
        if ($total === 0) {
            return 0;
        }

        return round(($this->asistencia_count / $total) * 100, 1);
    }

    public function getPromedioCalificacionAttribute(): ?float
    {
        return $this->participantes()->whereNotNull('puntuacion')->avg('puntuacion');
    }

    public function getArchivoEvidenciaUrlAttribute(): ?string
    {
        if (!$this->archivo_evidencia) {
            return null;
        }

        return asset('storage/' . $this->archivo_evidencia);
    }

    public function getArchivoCertificadoUrlAttribute(): ?string
    {
        if (!$this->archivo_certificado) {
            return null;
        }

        return asset('storage/' . $this->archivo_certificado);
    }

    // -- Helpers --

    /**
     * Agrega un participante a la capacitación.
     */
    public function agregarParticipante(int $usuarioId, array $datosExtra = []): ParticipanteCapacitacion
    {
        return $this->participantes()->firstOrCreate(
            ['usuario_id' => $usuarioId],
            array_merge(['estado' => 'inscritos'], $datosExtra)
        );
    }

    /**
     * Marca asistencia del participante.
     */
    public function registrarAsistencia(int $usuarioId, int $puntuacion = null): ?ParticipanteCapacitacion
    {
        $participante = $this->participantes()->where('usuario_id', $usuarioId)->first();

        if (!$participante) {
            return null;
        }

        $participante->estado = 'asistio';
        $participante->puntuacion = $puntuacion;
        $participante->fecha_evaluacion = now()->toDateString();
        $participante->saveQuietly();

        return $participante;
    }

    public function auditLabel(): string
    {
        return "Capacitación: {$this->titulo} ({$this->estado_label})";
    }
}
