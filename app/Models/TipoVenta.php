<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoVenta extends Model
{
    use HasFactory;

    protected $table = 'tipos_ventas';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    // Relación inversa (opcional)
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'tipo_venta_id');
    }
}
