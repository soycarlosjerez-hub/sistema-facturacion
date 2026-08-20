<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsistenteRevisionDireccion extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $table = 'asistentes_revisiones_direccion';

    protected $fillable = [
        'revision_direccion_id',
        'usuario_id',
        'asistio',
        'notificaciones_previas',
        'creado_por',
        'modificado_por',
        'tenant_id',
    ];

    protected $casts = [
        'asistio' => 'boolean',
    ];

    // -- Relaciones --

    public function revision(): BelongsTo
    {
        return $this->belongsTo(RevisionDireccion::class, 'revision_direccion_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
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

    public function scopeAsistio($query)
    {
        return $query->where('asistio', true);
    }

    public function scopeNoAsistio($query)
    {
        return $query->where('asistio', false);
    }

    public function scopePorRevision($query, int $revisionId)
    {
        return $query->where('revision_direccion_id', $revisionId);
    }

    public function scopePorUsuario($query, int $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    // -- Accessors --

    public function getAsistioLabelAttribute(): string
    {
        return $this->asistio ? 'Sí' : 'No';
    }

    public function getColorBadgeAsistioAttribute(): string
    {
        return $this->asistio ? 'success' : 'danger';
    }

    public function getUsuarioLabelAttribute(): string
    {
        return $this->usuario?->name ?? '—';
    }

    public function auditLabel(): string
    {
        return "Asistente {$this->usuario_id}: {$this->asistio_label}";
    }
}
