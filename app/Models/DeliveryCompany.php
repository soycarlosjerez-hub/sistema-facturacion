<?php

namespace App\Models;

use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryCompany extends Model
{
    use TenantScope;

    protected $table = 'delivery_companies';

    protected $fillable = [
        'nombre',
        'nombre_corto',
        'comision_porcentaje',
        'activo',
        'tenant_id',
    ];

    protected $casts = [
        'comision_porcentaje' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function getComisionFormateadaAttribute(): string
    {
        return number_format($this->comision_porcentaje, 2) . '%';
    }
}
