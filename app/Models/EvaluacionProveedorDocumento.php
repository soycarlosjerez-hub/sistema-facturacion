<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluacionProveedorDocumento extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $table = 'evaluaciones_proveedores_documentos';

    protected $fillable = [
        'evaluacion_proveedor_id',
        'tipo',
        'fecha',
        'archivo_path',
        'archivo_original_name',
        'archivo_mime_type',
        'archivo_size_bytes',
        'aprobado',
        'observaciones',
        'creado_por',
        'modificado_por',
        'tenant_id',
    ];

    protected $casts = [
        'fecha'               => 'date',
        'aprobado'            => 'boolean',
        'archivo_size_bytes'  => 'integer',
    ];

    // -- Relaciones --

    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(EvaluacionProveedor::class, 'evaluacion_proveedor_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'model');
    }

    // -- Scopes --

    public function scopeAprobados($query)
    {
        return $query->where('aprobado', true);
    }

    public function scopeRechazados($query)
    {
        return $query->where('aprobado', false);
    }

    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeConArchivo($query)
    {
        return $query->whereNotNull('archivo_path');
    }

    // -- Accessors --

    public function getTipoLabelAttribute(): string
    {
        $labels = [
            'rnc_vigente'       => 'RNC Vigente',
            'registro_dgii'     => 'Registro DGII',
            'licencias'         => 'Licencias',
            'certificaciones'   => 'Certificaciones',
            'otros'             => 'Otros',
        ];

        return $labels[$this->tipo ?? 'otros'] ?? 'Otros';
    }

    public function getColorBadgeTipoAttribute(): string
    {
        $colors = [
            'rnc_vigente'       => 'info',
            'registro_dgii'     => 'primary',
            'licencias'         => 'warning',
            'certificaciones'   => 'success',
            'otros'             => 'secondary',
        ];

        return $colors[$this->tipo ?? 'otros'] ?? 'secondary';
    }

    public function getAprobadoLabelAttribute(): string
    {
        return $this->aprobado ? 'Aprobado' : 'Pendiente';
    }

    public function getColorBadgeAprobadoAttribute(): string
    {
        return $this->aprobado ? 'success' : 'warning';
    }

    public function getArchivoUrlAttribute(): ?string
    {
        if (!$this->archivo_path) {
            return null;
        }

        return asset('storage/' . $this->archivo_path);
    }

    public function auditLabel(): string
    {
        return "Documento Evaluación Proveedor #{$this->id}: {$this->tipo_label} ({$this->aprobado_label})";
    }

    /**
     * Opciones para select de tipos de documento.
     */
    public static function getTiposOpciones(): array
    {
        return [
            'rnc_vigente'       => 'RNC Vigente',
            'registro_dgii'     => 'Registro DGII',
            'licencias'         => 'Licencias',
            'certificaciones'   => 'Certificaciones',
            'otros'             => 'Otros',
        ];
    }
}
