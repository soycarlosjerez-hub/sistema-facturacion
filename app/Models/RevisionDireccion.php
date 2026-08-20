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

class RevisionDireccion extends Model
{
    use HasFactory, Auditable, TenantScope, SoftDeletes;

    protected $table = 'revisiones_direccion';

    protected $fillable = [
        'numero',
        'fecha',
        'tipo',
        'estado',
        'tipo_negocio_id',
        'duracion_horas',
        'resumen',
        'resumen_resoluciones',
        'creado_por',
        'modificado_por',
        'tenant_id',
    ];

    protected $casts = [
        'fecha'           => 'date',
        'duracion_horas'  => 'decimal:1',
    ];

    // -- Relaciones --

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function asistenteMod(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modificado_por');
    }

    public function asistentes()
    {
        return $this->hasMany(AsistenteRevisionDireccion::class, 'revision_direccion_id');
    }

    public function entradas()
    {
        return $this->hasMany(RevisionIntroduccion::class, 'revision_direccion_id');
    }

    public function salidas()
    {
        return $this->hasMany(RevisionSalida::class, 'revision_direccion_id');
    }

    public function tipoNegocio(): BelongsTo
    {
        return $this->belongsTo(BusinessType::class, 'tipo_negocio_id');
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

    public function scopeEnEjecucion($query)
    {
        return $query->where('estado', 'en_ejecucion');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeExtraordinarias($query)
    {
        return $query->where('tipo', 'extraordinaria');
    }

    public function scopeDelPeriodo($query, string $fechaInicio, string $fechaFin)
    {
        return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    }

    public function scopeDeLaFecha($query, string $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    public function scopePorCreador($query, int $usuarioId)
    {
        return $query->where('creado_por', $usuarioId);
    }

    public function scopeConAsistentes($query)
    {
        return $query->has('asistentes');
    }

    public function scopeConSalidas($query, string $estado = null)
    {
        $query->has('salidas');

        if ($estado) {
            $query->whereHas('salidas', fn ($q) => $q->where('estado', $estado));
        }

        return $query;
    }

    // -- Accessors --

    public function getEstadoLabelAttribute(): string
    {
        $labels = [
            'programada'     => 'Programada',
            'en_ejecucion'   => 'En Ejecución',
            'completada'     => 'Completada',
        ];

        return $labels[$this->estado ?? 'programada'] ?? 'Programada';
    }

    public function getColorBadgeEstadoAttribute(): string
    {
        $colors = [
            'programada'     => 'info',
            'en_ejecucion'   => 'warning',
            'completada'     => 'success',
        ];

        return $colors[$this->estado ?? 'programada'] ?? 'secondary';
    }

    public function getTipoLabelAttribute(): string
    {
        $labels = [
            'programada'    => 'Programada',
            'extraordinaria'=> 'Extraordinaria',
        ];

        return $labels[$this->tipo ?? 'programada'] ?? 'Programada';
    }

    public function getColorBadgeTipoAttribute(): string
    {
        $colors = [
            'programada'    => 'info',
            'extraordinaria'=> 'danger',
        ];

        return $colors[$this->tipo ?? 'programada'] ?? 'secondary';
    }

    public function getCreadorLabelAttribute(): string
    {
        return $this->creador?->name ?? '—';
    }

    public function getFechaLabelAttribute(): string
    {
        return $this->fecha?->format('d/m/Y') ?? '—';
    }

    public function getDuracionLabelAttribute(): string
    {
        if (!$this->duracion_horas) {
            return '—';
        }

        $horas = floor($this->duracion_horas);
        $minutos = round(($this->duracion_horas - $horas) * 60);

        if ($minutos === 0) {
            return "{$horas} hora" . ($horas > 1 ? 's' : '');
        }

        return "{$horas}h {$minutos}m";
    }

    public function getAsistentesCountAttribute(): int
    {
        return $this->asistentes()->count();
    }

    public function getAsistentesPresentesCountAttribute(): int
    {
        return $this->asistentes()->where('asistio', true)->count();
    }

    public function getSalidasCountAttribute(): int
    {
        return $this->salidas()->count();
    }

    public function getSalidasPendientesAttribute(): int
    {
        return $this->salidas()->where('estado', 'pendiente')->count();
    }

    public function getResumenTruncadoAttribute(): string
    {
        if (!$this->resumen) {
            return '';
        }

        return strlen($this->resumen) > 150
            ? substr($this->resumen, 0, 150) . '...'
            : $this->resumen;
    }

    // -- Helpers --

    /**
     * Inicia la revisión.
     */
    public function iniciar(): static
    {
        $this->estado = 'en_ejecucion';
        $this->saveQuietly();

        return $this;
    }

    /**
     * Completa la revisión.
     */
    public function completar(): static
    {
        $this->estado = 'completada';
        $this->saveQuietly();

        return $this;
    }

    public function auditLabel(): string
    {
        return "Revisión Dirección #{$this->numero}: {$this->tipo_label} ({$this->estado_label})";
    }

    /**
     * Opciones para select de estados.
     */
    public static function getBadgesForSelect(): array
    {
        return [
            'programada'     => ['label' => 'Programada', 'color' => 'info', 'value' => 'programada'],
            'en_ejecucion'   => ['label' => 'En Ejecución', 'color' => 'warning', 'value' => 'en_ejecucion'],
            'completada'     => ['label' => 'Completada', 'color' => 'success', 'value' => 'completada'],
        ];
    }

    /**
     * Opciones para select de tipos.
     */
    public static function getTiposOpciones(): array
    {
        return [
            'programada'    => 'Programada',
            'extraordinaria'=> 'Extraordinaria',
        ];
    }
}
