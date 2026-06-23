<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;

class LavaderoServicio extends Model
{
    use Auditable;
    use TenantScope;

    protected $fillable = [
        'nombre', 'descripcion', 'precio', 'precio_compra',
        'duracion_minutos', 'categoria', 'activo', 'orden', 'tenant_id',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'precio_compra' => 'decimal:2',
        'activo' => 'boolean',
        'orden' => 'integer',
        'duracion_minutos' => 'integer',
    ];

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
