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

    protected $fillable = ['nombre', 'email', 'telefono', 'direccion', 'rnc', 'tipo_persona', 'sujeto_retencion_isr', 'sujeto_retencion_itbis', 'tenant_id'];

    protected $casts = [
        'sujeto_retencion_isr' => 'boolean',
        'sujeto_retencion_itbis' => 'boolean',
    ];

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }
}
