<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class Equipo extends Model
{
    use HasFactory;
    use Auditable;
    use TenantScope;

    protected $table = 'equipos';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'producto_id',
        'serial_imei',
        'serial_esn',
        'marca',
        'modelo',
        'almacenamiento_gb',
        'color',
        'estado',
        'precio_compra',
        'precio_venta',
        'comprado_a_proveedor_id',
        'fecha_compra',
        'factura_compra',
        'garantia_desde',
        'garantia_hasta',
        'garantia_tipo',
        'bloqueado_icloud',
        'bloqueado_fr',
        'observaciones',
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'fecha_compra' => 'date',
        'garantia_desde' => 'date',
        'garantia_hasta' => 'date',
        'bloqueado_icloud' => 'boolean',
        'bloqueado_fr' => 'boolean',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'comprado_a_proveedor_id');
    }

    public function ordenesReparacion(): HasMany
    {
        return $this->hasMany(OrdenReparacion::class);
    }

    public function garantias(): HasMany
    {
        return $this->hasMany(Garantia::class);
    }

    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'disponible');
    }

    public function scopeEnReparacion($query)
    {
        return $query->where('estado', 'en_reparacion');
    }

    public function scopePorMarca($query, $marca)
    {
        return $query->where('marca', $marca);
    }

    public function getEstadoLabelAttribute(): ?string
    {
        return match ($this->estado) {
            'disponible' => 'Disponible',
            'vendido' => 'Vendido',
            'en_reparacion' => 'En Reparación',
            'dañado' => 'Dañado',
            'reservado' => 'Reservado',
            'mantenimiento' => 'Mantenimiento',
            default => null,
        };
    }

    public function getGarantiaActivaAttribute(): bool
    {
        return $this->garantia_hasta && $this->garantia_hasta->gte(today());
    }
}
