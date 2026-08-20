<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificacionAccion extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $table = 'verificaciones_accion';

    protected $fillable = [
        'accion_correctiva_id',
        'fecha_verificacion',
        'resultado',
        'evidencias',
        'accion_pendiente',
        'verificado_por',
        'creado_por',
        'modificado_por',
        'tenant_id',
    ];

    protected $casts = [
        'fecha_verificacion' => 'date',
    ];

    // -- Relaciones --

    public function accionCorrectiva(): BelongsTo
    {
        return $this->belongsTo(AccionCorrectiva::class);
    }

    public function verificadoPorMod(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verificado_por');
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

    public function scopePorResultado($query, string $resultado)
    {
        return $query->where('resultado', $resultado);
    }

    public function scopeEficaces($query)
    {
        return $query->where('resultado', 'eficaz');
    }

    public function scopeParciales($query)
    {
        return $query->where('resultado', 'parcial');
    }

    public function scopeIneficaces($query)
    {
        return $query->where('resultado', 'ineficaz');
    }

    public function scopePorVerificador($query, int $verificadorId)
    {
        return $query->where('verificado_por', $verificadorId);
    }

    public function scopePorAccion($query, int $accionId)
    {
        return $query->where('accion_correctiva_id', $accionId);
    }

    public function scopeConAccionPendiente($query)
    {
        return $query->whereNotNull('accion_pendiente')
            ->where('accion_pendiente', '!=', '');
    }

    // -- Accessors --

    public function getResultadoLabelAttribute(): string
    {
        $labels = [
            'eficaz'  => 'Eficaz',
            'parcial' => 'Parcial',
            'ineficaz'=> 'Ineficaz',
            'na'      => 'N/A',
        ];

        return $labels[$this->resultado ?? 'na'] ?? 'N/A';
    }

    public function getColorBadgeResultadoAttribute(): string
    {
        $colors = [
            'eficaz'  => 'success',
            'parcial' => 'warning',
            'ineficaz'=> 'danger',
            'na'      => 'secondary',
        ];

        return $colors[$this->resultado ?? 'na'] ?? 'secondary';
    }

    public function getFechaVerificacionLabelAttribute(): string
    {
        return $this->fecha_verificacion?->format('d/m/Y') ?? '—';
    }

    public function getVerificadoPorLabelAttribute(): string
    {
        return $this->verificadoPorMod?->name ?? '—';
    }

    public function getEvidenciasTruncadaAttribute(): string
    {
        if (!$this->evidencias) {
            return '—';
        }

        return strlen($this->evidencias) > 100
            ? substr($this->evidencias, 0, 100) . '...'
            : $this->evidencias;
    }

    public function getAccionPendienteLabelAttribute(): string
    {
        return $this->accion_pendiente ?: 'No aplica';
    }

    // -- Helpers --

    public function auditLabel(): string
    {
        return "Verificación Acción #{$this->id}: {$this->resultado_label}";
    }

    /**
     * Opciones para select de resultados.
     */
    public static function getResultadoOptions(): array
    {
        return [
            'eficaz'  => 'Eficaz',
            'parcial' => 'Parcial',
            'ineficaz'=> 'Ineficaz',
            'na'      => 'N/A',
        ];
    }
}
