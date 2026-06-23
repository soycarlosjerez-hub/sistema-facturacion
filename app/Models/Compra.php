<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Compra extends Model
{
    use Auditable, TenantScope;
    protected $fillable = [
        'tenant_id',
        'proveedor_id',
        'sucursal_id',
        'almacen_id',
        'tipo_compra_id',
        'user_id',
        'total',
        'subtotal',
        'itbis_total',
        'fecha',
        'observaciones',
        'aplica_retencion_isr',
        'aplica_retencion_itbis',
        'retencion_isr',
        'retencion_itbis',
        'total_neto',
        'ecf_documento_id',
    ];

    protected $casts = [
        'total'       => 'decimal:2',
        'subtotal'    => 'decimal:2',
        'itbis_total' => 'decimal:2',
        'aplica_retencion_isr' => 'boolean',
        'aplica_retencion_itbis' => 'boolean',
        'retencion_isr' => 'decimal:2',
        'retencion_itbis' => 'decimal:2',
        'total_neto' => 'decimal:2',
        'fecha'       => 'date',
    ];

    public function getTotalRetencionesAttribute(): float
    {
        return (float) $this->retencion_isr + (float) $this->retencion_itbis;
    }

    public function getTotalPagarAttribute(): float
    {
        return (float) $this->total - $this->total_retenciones;
    }

    public function ecfDocumento(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\EcfDocumento::class, 'ecf_documento_id');
    }

    public function getPuedeGenerarEcfAttribute(): bool
    {
        return !$this->ecf_documento_id && $this->proveedor && $this->proveedor->rnc;
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleCompra::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function almacen(): BelongsTo
    {
        return $this->belongsTo(Almacen::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tipoCompra(): BelongsTo
    {
        return $this->belongsTo(TipoCompra::class, 'tipo_compra_id');
    }

    public function getFolioAttribute(): string
    {
        return 'C-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }
}
