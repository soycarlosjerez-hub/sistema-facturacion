<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TenantScope;

class ListaPrecio extends Model
{
    use TenantScope, SoftDeletes;

    protected $table = 'lista_precios';

    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'vigencia_desde', 'vigencia_hasta', 'activa', 'tenant_id',
    ];

    protected $casts = [
        'vigencia_desde' => 'date',
        'vigencia_hasta' => 'date',
        'activa' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ListaPrecioItem::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ListaPrecioLog::class);
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeVigentes($query)
    {
        return $query->where('activa', true)
            ->where(fn($q) => $q->whereNull('vigencia_desde')->orWhere('vigencia_desde', '<=', now()))
            ->where(fn($q) => $q->whereNull('vigencia_hasta')->orWhere('vigencia_hasta', '>=', now()));
    }

    public function scopePorExpirar($query)
    {
        return $query->whereNotNull('vigencia_hasta')
            ->where('vigencia_hasta', '>=', now()->startOfDay())
            ->where('vigencia_hasta', '<=', now()->addDays(7)->endOfDay());
    }

    public function scopeExpiradas($query)
    {
        return $query->whereNotNull('vigencia_hasta')
            ->where('vigencia_hasta', '<', now()->startOfDay());
    }

    public function scopeNoIniciadas($query)
    {
        return $query->whereNotNull('vigencia_desde')
            ->where('vigencia_desde', '>', now()->endOfDay());
    }

    public function getStatusAttribute(): array
    {
        $today = now()->startOfDay();
        $sevenDays = now()->addDays(7)->endOfDay();

        if ($this->vigencia_desde && $this->vigencia_desde->gt($today)) {
            return ['class' => 'info', 'label' => 'No iniciada', 'icon' => 'bi-clock'];
        }

        if ($this->vigencia_hasta && $this->vigencia_hasta->lt($today)) {
            return ['class' => 'danger', 'label' => 'Expirada', 'icon' => 'bi-x-circle'];
        }

        if ($this->vigencia_hasta && $this->vigencia_hasta->le($sevenDays)) {
            return ['class' => 'warning', 'label' => 'Por expirar', 'icon' => 'bi-exclamation-triangle'];
        }

        return ['class' => 'success', 'label' => 'Vigente', 'icon' => 'bi-check-circle'];
    }

    public function getPrecioProducto(Producto $producto): ?float
    {
        $item = $this->items()->where('producto_id', $producto->id)->first();
        return $item ? (float) $item->precio : null;
    }
}
