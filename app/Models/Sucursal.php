<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sucursal extends Model
{
    use SoftDeletes;

    protected $table = 'sucursales';

    protected $fillable = [
        'codigo', 'nombre', 'direccion', 'telefono', 'email', 'rnc', 'activa', 'es_matriz',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'es_matriz' => 'boolean',
    ];

    public function almacenes()
    {
        return $this->hasMany(Almacen::class);
    }

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }
}
