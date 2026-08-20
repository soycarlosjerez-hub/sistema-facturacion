<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class HallazgoAuditoria extends Model
{
    use HasFactory, Auditable, TenantScope, SoftDeletes;

    protected $table = 'hallazgos_auditoria';

    protected $fillable = [
        'auditoria_interna_id',
        'numero',
        'tipo',
        'descripcion',
        'requisito_iso',
        'evidencia',
        'causa',
        'clase_nc',
        'accion_inmediata',
        'nc_id',
        'creado_por',
        'modificado_por',
        'tenant_id',
    ];

    // -- Relaciones --

    public function auditoria(): BelongsTo
    {
        return $this->belongsTo(AuditoriaInterna::class, 'auditoria_interna_id');
    }

    public function noConformidad(): BelongsTo
    {
        return $this->belongsTo(NoConformidad::class, 'nc_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function modificador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modificado_por');
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

    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeConforme($query)
    {
        return $query->where('tipo', 'conforme');
    }

    public function scopeNoConformeMayor($query)
    {
        return $query->where('tipo', 'no_conforme_mayor');
    }

    public function scopeNoConformeMenor($query)
    {
        return $query->where('tipo', 'no_conforme_menor');
    }

    public function scopeObservacion($query)
    {
        return $query->where('tipo', 'observacion');
    }

    public function scopePorAuditoria($query, int $auditoriaId)
    {
        return $query->where('auditoria_interna_id', $auditoriaId);
    }

    public function scopeSinNoConformidad($query)
    {
        return $query->whereNull('nc_id');
    }

    public function scopeConNoConformidad($query)
    {
        return $query->whereNotNull('nc_id');
    }

    public function scopePorClase($query, string $clase)
    {
        return $query->where('clase_nc', $clase);
    }

    public function scopeDelAno($query, int $ano)
    {
        return $query->whereYear('created_at', $ano);
    }

    // -- Accessors --

    public function getTipoLabelAttribute(): string
    {
        $labels = [
            'conforme'           => 'Conforme',
            'no_conforme_mayor'  => 'NC Mayor',
            'no_conforme_menor'  => 'NC Menor',
            'observacion'        => 'Observación',
        ];

        return $labels[$this->tipo ?? 'observacion'] ?? 'Observación';
    }

    public function getColorBadgeTipoAttribute(): string
    {
        $colors = [
            'conforme'           => 'success',
            'no_conforme_mayor'  => 'danger',
            'no_conforme_menor'  => 'warning',
            'observacion'        => 'info',
        ];

        return $colors[$this->tipo ?? 'observacion'] ?? 'secondary';
    }

    public function getClaseNcLabelAttribute(): string
    {
        if (!$this->tipo || $this->tipo === 'conforme' || $this->tipo === 'observacion') {
            return '—';
        }

        $labels = [
            'mayor' => 'Mayor',
            'menor' => 'Menor',
        ];

        return $labels[$this->clase_nc ?? 'menor'] ?? 'Menor';
    }

    public function getColorBadgeClaseNcAttribute(): string
    {
        if (!$this->clase_nc) {
            return 'secondary';
        }

        $colors = [
            'mayor' => 'danger',
            'menor' => 'warning',
        ];

        return $colors[$this->clase_nc ?? 'menor'] ?? 'secondary';
    }

    public function getAuditoriaLabelAttribute(): string
    {
        return $this->auditoria?->codigo ?? '—';
    }

    public function getNoConformidadLabelAttribute(): string
    {
        return $this->noConformidad?->numero ?? '—';
    }

    public function getNumeroLabelAttribute(): string
    {
        return $this->numero ?? sprintf('#%d', $this->id);
    }

    // -- Helpers --

    /**
     * Crea una NoConformidad asociada a este hallazgo.
     */
    public function crearNoConformidad(array $datosNC): NoConformidad
    {
        $nc = NoConformidad::create(array_merge($datosNC, [
            'auditoria_id'      => $this->auditoria_interna_id,
            'origen'            => 'auditoria',
            'descripcion'       => strlen($datosNC['descripcion'] ?? '') > 0
                ? $datosNC['descripcion']
                : "NC derivada del hallazgo #{$this->numero}: {$this->descripcion}",
        ]));

        $this->nc_id = $nc->id;
        $this->saveQuietly();

        return $nc;
    }

    public function auditLabel(): string
    {
        return "Hallazgo {$this->numero}: {$this->tipo_label} ({$this->clase_nc_label})";
    }

    /**
     * Opciones para select de tipos de hallazgo.
     */
    public static function getTiposOpciones(): array
    {
        return [
            'conforme'           => 'Conforme',
            'no_conforme_mayor'  => 'NC Mayor',
            'no_conforme_menor'  => 'NC Menor',
            'observacion'        => 'Observación',
        ];
    }

    /**
     * Opciones para select de clases de NC.
     */
    public static function getClaseNcOptions(): array
    {
        return [
            'mayor' => 'Mayor',
            'menor' => 'Menor',
        ];
    }
}
