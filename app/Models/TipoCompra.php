<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCompra extends Model
{
    use HasFactory;

    // Nombre de la tabla (opcional si sigue la convención plural)
    protected $table = 'tipos_compras';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'nombre',
    ];
}

