<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PreguntaEncuesta extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $table = 'preguntas_encuestas';

    protected $fillable = [
        'encuesta_satisfaccion_id',
        'texto',
        'tipo',
        'orden',
        'obligatoria',
        'tenant_id',
    ];

    protected $casts = [
        'orden'       => 'integer',
        'obligatoria' => 'boolean',
    ];

    // -- Relaciones --

    public function encuesta(): BelongsTo
    {
        return $this->belongsTo(EncuestaSatisfaccion::class, 'encuesta_satisfaccion_id');
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

    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeObligatorias($query)
    {
        return $query->where('obligatoria', true);
    }

    public function scopeOpcionales($query)
    {
        return $query->where('obligatoria', false);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden');
    }

    public function scopeDeLaEncuesta($query, int $encuestaId)
    {
        return $query->where('encuesta_satisfaccion_id', $encuestaId);
    }

    // -- Accessors --

    public function getTipoLabelAttribute(): string
    {
        $labels = [
            'escala_5'  => 'Escala 1-5',
            'escala_10' => 'Escala 1-10',
            'texto'     => 'Texto libre',
            'si_no'     => 'Sí / No',
        ];

        return $labels[$this->tipo ?? 'escala_5'] ?? 'Escala 1-5';
    }

    public function getColorBadgeTipoAttribute(): string
    {
        $colors = [
            'escala_5'  => 'primary',
            'escala_10' => 'info',
            'texto'     => 'warning',
            'si_no'     => 'success',
        ];

        return $colors[$this->tipo ?? 'escala_5'] ?? 'secondary';
    }

    public function getObligatoriaBadgeAttribute(): string
    {
        return $this->obligatoria ? 'Sí ✓' : 'No';
    }

    public function getRespuestasCountAttribute(): int
    {
        return $this->respuestas()->count();
    }

    public function getPromedioScoreAttribute(): ?float
    {
        if (!str_starts_with($this->tipo, 'escala_')) {
            return null;
        }

        return $this->respuestas()->whereNotNull('valor')->avg('valor');
    }

    // -- Helpers --

    public function getOptionsForSelect(): array
    {
        return match ($this->tipo) {
            'escala_5'  => range(1, 5),
            'escala_10' => range(1, 10),
            'si_no'     => [
                'si'  => 'Sí',
                'no'  => 'No',
                'ne'  => 'No aplica',
            ],
            'texto'     => [],
            default     => [],
        };
    }

    public function auditLabel(): string
    {
        return "Pregunta #{$this->orden}: {$this->texto}";
    }
}
