<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use Auditable, TenantScope;
    protected $fillable = [
        'categoria_id',
        'nombre',
        'codigo_barras',
        'descripcion',
        'precio',
        'precio_compra',
        'unidad_medida',
        'itbis_porcentaje',
        'stock',
        'stock_minimo',
        'activo',
        'imagen',
        'tenant_id',
    ];

    protected $casts = [
        'precio'           => 'decimal:2',
        'precio_compra'    => 'decimal:2',
        'itbis_porcentaje' => 'decimal:2',
        'stock'            => 'integer',
        'stock_minimo'     => 'integer',
        'activo'            => 'boolean',
    ];

    protected $appends = ['ganancia', 'margen_porcentaje', 'estado_stock', 'imagen_url', 'tiene_imagen'];
    protected $attributes = [
        'precio_compra' => 0,
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function detallesCompras(): HasMany
    {
        return $this->hasMany(DetalleCompra::class, 'producto_id');
    }

    public function ventaDetalles(): HasMany
    {
        return $this->hasMany(VentaDetalle::class, 'producto_id');
    }

    public function movimientosAlmacen(): HasMany
    {
        return $this->hasMany(AlmacenMovimiento::class, 'producto_id');
    }

    public function getGananciaAttribute(): float
    {
        return (float) ($this->precio - ($this->precio_compra ?? 0));
    }

    public function getMargenPorcentajeAttribute(): float
    {
        $compra = (float) ($this->precio_compra ?? 0);
        if ($compra <= 0) {
            return 0.0;
        }
        return round((($this->precio - $compra) / $compra) * 100, 2);
    }

    public function getEstadoStockAttribute(): string
    {
        $stock = (int) $this->stock;
        if ($stock <= 5) {
            return 'critical';
        }
        if ($stock <= 15) {
            return 'low';
        }
        return 'ok';
    }

    public function getActivoLabelAttribute(): string
    {
        return $this->activo ? 'Activo' : 'Inactivo';
    }

    public function getColorBadgeActivoAttribute(): string
    {
        return $this->activo ? 'success' : 'secondary';
    }

    public function getTieneImagenAttribute(): bool
    {
        return ! empty($this->imagen) && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->imagen);
    }

    public function getImagenUrlAttribute(): string
    {
        if (! empty($this->imagen) && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->imagen)) {
            return asset('storage/' . $this->imagen);
        }
        return asset('img/producto-placeholder.svg');
    }

    public function scopeStockCritico(Builder $query): Builder
    {
        return $query->where('stock', '<=', 5);
    }

    public function scopeStockBajo(Builder $query): Builder
    {
        return $query->whereBetween('stock', [6, 15]);
    }

    public function ingredientes(): BelongsToMany
    {
        return $this->belongsToMany(Ingrediente::class, 'producto_ingrediente')
            ->withPivot('cantidad');
    }}
