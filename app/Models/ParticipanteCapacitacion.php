<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParticipanteCapacitacion extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $table = 'participantes_capacitaciones';

    protected $fillable = [
        'capacitacion_id',
        'usuario_id',
        'puntuacion',
        'estado',
        'fecha_evaluacion',
        'comentarios',
        'archivo_certificado',
        'tenant_id',
    ];

    protected $casts = [
        'puntuacion'        => 'integer',
        'fecha_evaluacion'  => 'date',
    ];

    // -- Relaciones --

    public function capacitacion(): BelongsTo
    {
        return $this->belongsTo(Capacitacion::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
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

    public function scopeInscritos($query)
    {
        return $query->where('estado', 'inscritos');
    }

    public function scopeAsistio($query)
    {
        return $query->where('estado', 'asistio');
    }

    public function scopeNoAsistio($query)
    {
        return $query->where('estado', 'no_asistio');
    }

    public function scopeConCertificado($query)
    {
        return $query->where('estado', 'certificado');
    }

    public function scopeConPuntuacion($query)
    {
        return $query->whereNotNull('puntuacion');
    }

    public function scopeSobrePuntuacionMinima($query, int $min)
    {
        return $query->where('puntuacion', '>=', $min);
    }

    public function scopePorUsuario($query, int $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    // -- Accessors --

    public function getEstadoLabelAttribute(): string
    {
        $labels = [
            'inscritos'   => 'Inscrito',
            'asistio'     => 'Asistió',
            'no_asistio'  => 'No Asistió',
            'certificado' => 'Certificado',
        ];

        return $labels[$this->estado ?? 'inscritos'] ?? 'Inscrito';
    }

    public function getColorBadgeEstadoAttribute(): string
    {
        $colors = [
            'inscritos'   => 'info',
            'asistio'     => 'success',
            'no_asistio'  => 'danger',
            'certificado' => 'warning',
        ];

        return $colors[$this->estado ?? 'inscritos'] ?? 'secondary';
    }

    public function getPuntuacionLabelAttribute(): string
    {
        return $this->puntuacion !== null
            ? "{$this->puntuacion}/100"
            : 'Sin evaluar';
    }

    public function getAproboAttribute(): bool
    {
        return $this->puntuacion !== null && $this->puntuacion >= 70;
    }

    public function getEstadoBadgeColorAttribute(): string
    {
        return $this->color_badge_estado;
    }

    // -- Helpers --

    /**
     * Otorga certificado al participante si aprobó.
     */
    public function otorgarCertificado(): void
    {
        if ($this->estado === 'asistio' && $this->aprobado) {
            $this->estado = 'certificado';
            $this->saveQuietly();
        }
    }

    public function auditLabel(): string
    {
        return "Participante {$this->usuario_id}: {$this->estado_label} ({$this->puntuacion_label})";
    }
}
