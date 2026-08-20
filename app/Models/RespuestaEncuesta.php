<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RespuestaEncuesta extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $table = 'respuestas_encuestas';

    protected $fillable = [
        'encuesta_satisfaccion_id',
        'pregunta_encuesta_id',
        'valor',
        'comentario',
        'respondido_por',
        'tenant_id',
    ];

    protected $casts = [
        'valor'        => 'string',
    ];

    // -- Relaciones --

    public function encuesta(): BelongsTo
    {
        return $this->belongsTo(EncuestaSatisfaccion::class, 'encuesta_satisfaccion_id');
    }

    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(PreguntaEncuesta::class, 'pregunta_encuesta_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function respondioPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'respondido_por');
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'model');
    }

    // -- Scopes --

    public function scopeDeLaEncuesta($query, int $encuestaId)
    {
        return $query->where('encuesta_satisfaccion_id', $encuestaId);
    }

    public function scopeDeLaPregunta($query, int $preguntaId)
    {
        return $query->where('pregunta_encuesta_id', $preguntaId);
    }

    public function scopeConValorNumericos($query)
    {
        return $query->whereNotNull('valor')
            ->where(function ($q) {
                $q->where('valor', 'like', '%[0-9]%')
                  ->orWhere('valor', 'like', '%[0-9][0-9]%', '%');
            });
    }

    public function scopePorCliente($query, int $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    public function scopeConComentario($query)
    {
        return $query->whereNotNull('comentario');
    }

    public function scopeDeFecha($query, string $fecha)
    {
        return $query->whereDate('created_at', $fecha);
    }

    // -- Accessors --

    public function getValorEscalarAttribute(): ?int
    {
        $valorInt = (int) filter_var($this->valor, FILTER_VALIDATE_INT);

        if ($valorInt !== false && $valorInt >= 0) {
            return $valorInt;
        }

        return null;
    }

    public function esSiNo(): bool
    {
        return in_array(strtolower($this->valor ?? ''), ['si', 'no', 'sí', 'no']);
    }

    public function getColorBadgeSatisfaccionAttribute(): string
    {
        $valor = $this->valor_escalar;

        if ($valor === null) {
            return 'secondary';
        }

        // Para escala 5 y 10:
        $maximo = 5;
        if ($this->pregunta->tipo === 'escala_10') {
            $maximo = 10;
        }

        $ratio = $valor / $maximo;

        if ($ratio >= 0.8) {
            return 'success';
        }

        if ($ratio >= 0.5) {
            return 'warning';
        }

        return 'danger';
    }

    public function getComentarioTruncadoAttribute(): string
    {
        if (!$this->comentario) {
            return '';
        }

        return mb_substr($this->comentario, 0, 60) . (mb_strlen($this->comentario) > 60 ? '...' : '');
    }

    // -- Helpers --

    public function auditLabel(): string
    {
        $preguntaTexto = $this->pregunta?->texto ?? '?';
        return "Respuesta de encuesta: {$this->valor} ({$preguntaTexto})";
    }
}
