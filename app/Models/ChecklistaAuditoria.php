<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistaAuditoria extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $table = 'checklist_auditorias';

    protected $fillable = [
        'auditoria_interna_id',
        'criterio_auditado',
        'evidencia',
        'cumplimiento',
        'observaciones',
        'hallazgo_id',
        'creado_por',
        'modificado_por',
        'tenant_id',
    ];

    // -- Relaciones --

    public function auditoria(): BelongsTo
    {
        return $this->belongsTo(AuditoriaInterna::class, 'auditoria_interna_id');
    }

    public function hallazgo(): BelongsTo
    {
        return $this->belongsTo(HallazgoAuditoria::class, 'hallazgo_id');
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

    public function scopeConforme($query)
    {
        return $query->where('cumplimiento', 'conforme');
    }

    public function scopeNoConforme($query)
    {
        return $query->where('cumplimiento', 'no_conforme');
    }

    public function scopePorAuditoria($query, int $auditoriaId)
    {
        return $query->where('auditoria_interna_id', $auditoriaId);
    }

    public function scopeConObservaciones($query)
    {
        return $query->whereNotNull('observaciones')
            ->where('observaciones', '!=', '');
    }

    public function scopeConHallazgos($query)
    {
        return $query->whereNotNull('hallazgo_id');
    }

    // -- Accessors --

    public function getCumplimientoLabelAttribute(): string
    {
        $labels = [
            'conforme'    => 'Conforme',
            'no_conforme' => 'No Conforme',
        ];

        return $labels[$this->cumplimiento ?? 'no_conforme'] ?? 'No Conforme';
    }

    public function getColorBadgeCumplimientoAttribute(): string
    {
        $colors = [
            'conforme'    => 'success',
            'no_conforme' => 'danger',
        ];

        return $colors[$this->cumplimiento ?? 'no_conforme'] ?? 'secondary';
    }

    public function getCriterioAuditadoLabelAttribute(): string
    {
        return strlen($this->criterio_auditado) > 80
            ? substr($this->criterio_auditado, 0, 80) . '...'
            : $this->criterio_auditado;
    }

    // -- Helpers --

    public function auditLabel(): string
    {
        return "Checklist #{$this->id}: {$this->criterio_auditado} ({$this->cumplimiento_label})";
    }

    /**
     * Opciones para select de cumplimiento.
     */
    public static function getCumplimientoOptions(): array
    {
        return [
            'conforme'    => 'Conforme',
            'no_conforme' => 'No Conforme',
        ];
    }
}
