<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use Auditable;

    protected $table = 'proveedores';

    protected $fillable = ['nombre', 'email', 'telefono', 'direccion', 'rnc', 'tipo_persona', 'sujeto_retencion_isr', 'sujeto_retencion_itbis'];

    protected $casts = [
        'sujeto_retencion_isr' => 'boolean',
        'sujeto_retencion_itbis' => 'boolean',
    ];

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }
}
