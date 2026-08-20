<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EncuestaSatisfaccion extends Model
{
    use HasFactory, Auditable, TenantScope, SoftDeletes;

    protected $table = 'encuestas_satisfaccion';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'instrucciones',
        'creado_por',
        'modificado_por',
        'tenant_id',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    // -- Relaciones --

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function modificador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modificado_por');
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(PreguntaEncuesta::class)->orderBy('orden');
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(RespuestaEncuesta::class);
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

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa')
            ->whereDate('fecha_inicio', '<=', now()->toDateString())
            ->where(function ($q) {
                $q->whereNull('fecha_fin')
                  ->orWhereDate('fecha_fin', '>=', now()->toDateString());
            });
    }

    public function scopeCerradas($query)
    {
        return $query->where('estado', 'cerrada');
    }

    public function scopeBorrador($query)
    {
        return $query->where('estado', 'borrador');
    }

    public function scopePorFecha($query, string $fecha)
    {
        return $query->whereDate('fecha_inicio', $fecha);
    }

    public function scopeDelPeriodo($query, string $fechaInicio = null, string $fechaFin = null)
    {
        if ($fechaInicio) {
            $query->where('fecha_fin', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->where('fecha_inicio', '<=', $fechaFin);
        }

        return $query;
    }

    public function scopeConRespuestas($query)
    {
        return $query->has('respuestas');
    }

    // -- Accessors --

    public function getEstadoLabelAttribute(): string
    {
        $labels = [
            'borrador' => 'Borrador',
            'activa'   => 'Activa',
            'cerrada'  => 'Cerrada',
        ];

        return $labels[$this->estado ?? 'borrador'] ?? 'Borrador';
    }

    public function getColorBadgeEstadoAttribute(): string
    {
        $colors = [
            'borrador' => 'secondary',
            'activa'   => 'success',
            'cerrada'  => 'info',
        ];

        return $colors[$this->estado ?? 'borrador'] ?? 'secondary';
    }

    public function getFechaFinLabelAttribute(): string
    {
        return $this->fecha_fin?->format('d/m/Y') ?? 'Indefinido';
    }

    public function getFechaInicioLabelAttribute(): string
    {
        return $this->fecha_inicio?->format('d/m/Y') ?? '—';
    }

    public function getEstadoLabelBadgeAttribute(): string
    {
        if ($this->estado === 'borrador') {
            return 'secondary';
        }

        if ($this->estado !== 'activa') {
            return 'info';
        }

        if (!$this->estaActiva()) {
            return 'warning';
        }

        return 'success';
    }

    public function getPorcentajeRespuestasAttribute(): float
    {
        $preguntasActivas = $this->preguntas()->where('obligatoria', true)->count();
        if ($preguntasActivas === 0) {
            return 0;
        }

        // Count responses per question, average completion ratio
        $totalPreguntas = $preguntasActivas * $this->respuestas()->distinct('cliente_id')->count();

        if ($totalPreguntas === 0) {
            return 0;
        }

        $respuestasTotal = (float) $this->respuestas()->count();
        return round(($respuestasTotal / $totalPreguntas) * 100, 1);
    }

    public function getRespuestasClientesCountAttribute(): int
    {
        return (int) $this->respuestas()->distinct('cliente_id')->count();
    }

    public function getPreguntaCountAttribute(): int
    {
        return $this->preguntas()->count();
    }

    // -- Helpers --

    public function estaActiva(): bool
    {
        if ($this->estado !== 'activa') {
            return false;
        }

        if ($this->fecha_inicio > now()->toDateString()) {
            return false;
        }

        if ($this->fecha_fin && $this->fecha_fin < now()->toDateString()) {
            return false;
        }

        return true;
    }

    public function estaCerrada(): bool
    {
        return $this->estado === 'cerrada'
            || ($this->fecha_fin && $this->fecha_fin < now()->toDateString());
    }

    public function getPromedioPuntuacionAttribute(): ?float
    {
        return $this->respuestas()->where('tipo', 'escala_5')
            ->orWhere('tipo', 'escala_10')
            ->whereNotNull('valor')
            ->avg('valor');
    }

    public function auditLabel(): string
    {
        return "Encuesta: {$this->titulo} ({$this->estado_label})";
    }
}
