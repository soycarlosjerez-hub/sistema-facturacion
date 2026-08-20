<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropuestaMejora extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $table = 'propuestas_mejora';

    protected $fillable = [
        'mejora_continua_id',
        'titulo',
        'descripcion',
        'autor_id',
        'fecha',
        'estado',
        'creado_por',
        'modificado_por',
        'tenant_id',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    // -- Relaciones --

    public function mejoraContinua(): BelongsTo
    {
        return $this->belongsTo(MejoraContinua::class, 'mejora_continua_id');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
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

    public function scopePendiente($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobada($query)
    {
        return $query->where('estado', 'aprobada');
    }

    public function scopeRechazada($query)
    {
        return $query->where('estado', 'rechazada');
    }

    public function scopePorAutor($query, int $autorId)
    {
        return $query->where('autor_id', $autorId);
    }

    public function scopePorMejora($query, int $mejoraId)
    {
        return $query->where('mejora_continua_id', $mejoraId);
    }

    public function scopeDeLaFecha($query, string $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    public function scopeSinMejoraAsignada($query)
    {
        return $query->whereNull('mejora_continua_id');
    }

    public function scopeConMejoraAsignada($query)
    {
        return $query->whereNotNull('mejora_continua_id');
    }

    public function scopeDelMes($query, ?string $mes = null)
    {
        if (!$mes) {
            $mes = now()->format('Y-m');
        }

        return $query->whereYear('fecha', (int) substr($mes, 0, 4))
            ->whereMonth('fecha', (int) substr($mes, 5, 2));
    }

    // -- Accessors --

    public function getEstadoLabelAttribute(): string
    {
        $labels = [
            'pendiente'  => 'Pendiente',
            'aprobada'   => 'Aprobada',
            'rechazada'  => 'Rechazada',
        ];

        return $labels[$this->estado ?? 'pendiente'] ?? 'Pendiente';
    }

    public function getColorBadgeEstadoAttribute(): string
    {
        $colors = [
            'pendiente'  => 'warning',
            'aprobada'   => 'success',
            'rechazada'  => 'danger',
        ];

        return $colors[$this->estado ?? 'pendiente'] ?? 'secondary';
    }

    public function getAutorLabelAttribute(): string
    {
        return $this->autor?->name ?? '—';
    }

    public function getFechaLabelAttribute(): string
    {
        return $this->fecha?->format('d/m/Y') ?? '—';
    }

    public function getMejoraLabelAttribute(): ?string
    {
        return $this->mejoraContinua?->numero . ' - ' . $this->mejoraContinua?->titulo;
    }

    public function getTituloTruncadoAttribute(): string
    {
        return strlen($this->titulo) > 80
            ? substr($this->titulo, 0, 80) . '...'
            : $this->titulo;
    }

    public function getDescripcionTruncadaAttribute(): string
    {
        if (!$this->descripcion) {
            return '—';
        }

        return strlen($this->descripcion) > 120
            ? substr($this->descripcion, 0, 120) . '...'
            : $this->descripcion;
    }

    // -- Helpers --

    /**
     * Acepta la propuesta como aprobada.
     */
    public function aprobar(): static
    {
        $this->estado = 'aprobada';
        $this->saveQuietly();

        return $this;
    }

    /**
     * Rechaza la propuesta.
     */
    public function rechazar(): static
    {
        $this->estado = 'rechazada';
        $this->saveQuietly();

        return $this;
    }

    public function auditLabel(): string
    {
        return "Propuesta #{$this->id}: {$this->titulo} ({$this->estado_label})";
    }

    /**
     * Opciones para select de estados.
     */
    public static function getBadgesForSelect(): array
    {
        return [
            'pendiente'  => ['label' => 'Pendiente', 'color' => 'warning', 'value' => 'pendiente'],
            'aprobada'   => ['label' => 'Aprobada', 'color' => 'success', 'value' => 'aprobada'],
            'rechazada'  => ['label' => 'Rechazada', 'color' => 'danger', 'value' => 'rechazada'],
        ];
    }
}
