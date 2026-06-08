<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NcfSequence extends Model
{
    protected $fillable = [
        'nombre',
        'prefijo',
        'desde',
        'hasta',
        'actual',
        'fecha_vencimiento',
        'activo',
    ];
}
