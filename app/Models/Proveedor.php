<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use Auditable;
    use TenantScope;

    protected $table = 'proveedores';

    protected $fillable = ['nombre', 'email', 'telefono', 'direccion', 'rnc', 'tipo_persona', 'sujeto_retencion_isr', 'sujeto_retencion_itbis', 'tenant_id', 'activo'];

    protected $casts = [
        'sujeto_retencion_isr' => 'boolean',
        'sujeto_retencion_itbis' => 'boolean',
        'activo' => 'boolean',
    ];

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function scopeInactivo($query)
    {
        return $query->where('activo', false);
    }

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }
}
