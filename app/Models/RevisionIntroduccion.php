<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevisionIntroduccion extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $table = 'revisiones_direccion_entradas';

    protected $fillable = [
        'revision_direccion_id',
        'tipo',
        'contenido',
        'documento_id',
        'creado_por',
        'modificado_por',
        'tenant_id',
    ];

    // -- Relaciones --

    public function revision(): BelongsTo
    {
        return $this->belongsTo(RevisionDireccion::class, 'revision_direccion_id');
    }

    public function documentoSgc(): BelongsTo
    {
        return $this->belongsTo(DocumentoSgc::class, 'documento_id');
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

    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorRevision($query, int $revisionId)
    {
        return $query->where('revision_direccion_id', $revisionId);
    }

    public function scopeConDocumento($query)
    {
        return $query->whereNotNull('documento_id');
    }

    // -- Accessors --

    public function getTipoLabelAttribute(): string
    {
        $labels = [
            'indicadores'            => 'Indicadores',
            'resultados_auditoria'   => 'Resultados Auditoría',
            'estado_riesgos'         => 'Estado Riesgos',
            'satisfaccion_cliente'   => 'Satisfacción Cliente',
            'desempeno_proveedores'  => 'Desempeño Proveedores',
            'objetivos_calidad'      => 'Objetivos Calidad',
            'no_conformidades'       => 'No Conformidades',
            'acciones_pendientes'    => 'Acciones Pendientes',
            'revision_anterior'      => 'Revisión Anterior',
            'cambios'                => 'Cambios',
            'otro'                   => 'Otro',
        ];

        return $labels[$this->tipo ?? 'otro'] ?? 'Otro';
    }

    public function getColorBadgeTipoAttribute(): string
    {
        $colors = [
            'indicadores'            => 'info',
            'resultados_auditoria'   => 'primary',
            'estado_riesgos'         => 'warning',
            'satisfaccion_cliente'   => 'success',
            'desempeno_proveedores'  => 'secondary',
            'objetivos_calidad'      => 'purple',
            'no_conformidades'       => 'danger',
            'acciones_pendientes'    => 'default',
            'revision_anterior'      => 'dark',
            'cambios'                => 'teal',
            'otro'                   => 'muted',
        ];

        return $colors[$this->tipo ?? 'otro'] ?? 'secondary';
    }

    public function getDocumentoLabelAttribute(): ?string
    {
        return $this->documentoSgc?->codigo . ' - ' . $this->documentoSgc?->titulo;
    }

    public function getContenidoTruncadoAttribute(): string
    {
        if (!$this->contenido) {
            return '—';
        }

        return strlen($this->contenido) > 100
            ? substr($this->contenido, 0, 100) . '...'
            : $this->contenido;
    }

    public function auditLabel(): string
    {
        return "Entrada Revisión: {$this->tipo_label}";
    }

    /**
     * Opciones para select de tipos.
     */
    public static function getTiposOpciones(): array
    {
        return [
            'indicadores'            => 'Indicadores',
            'resultados_auditoria'   => 'Resultados Auditoría',
            'estado_riesgos'         => 'Estado Riesgos',
            'satisfaccion_cliente'   => 'Satisfacción Cliente',
            'desempeno_proveedores'  => 'Desempeño Proveedores',
            'objetivos_calidad'      => 'Objetivos Calidad',
            'no_conformidades'       => 'No Conformidades',
            'acciones_pendientes'    => 'Acciones Pendientes',
            'revision_anterior'      => 'Revisión Anterior',
            'cambios'                => 'Cambios',
            'otro'                   => 'Otro',
        ];
    }
}
