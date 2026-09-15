<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoProveedor extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $table = 'documentos_proveedores';

    protected $fillable = [
        'documento_sgc_id',
        'proveedor_id',
        'descripcionDocumento',
        'fechaCarga',
        'fechaVencimiento',
        'estado',
        'archivo_path',
        'archivo_original_name',
        'archivo_mime_type',
        'archivo_size_bytes',
        'tenant_id',
        'subido_por',
    ];

    protected $casts = [
        'fechaCarga' => 'date',
        'fechaVencimiento' => 'date',
        'archivo_size_bytes' => 'integer',
    ];

    public function documentoSgc(): BelongsTo
    {
        return $this->belongsTo(DocumentoSgc::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subido_por');
    }

    public function getArchivoUrlAttribute(): ?string
    {
        if (!$this->archivo_path) return null;
        return route('sgc.documentos-proveedor.archivo.show', $this);
    }

    public function auditLabel(): string
    {
        return "Doc Proveedor: {$this->proveedor->nombre ?? '#'.$this->proveedor_id} - {$this->documentoSgc->codigo ?? '#'.$this->documento_sgc_id}";
    }
}
