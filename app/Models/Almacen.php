<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Almacen extends Model
{
    use HasFactory;
    use Auditable;

    protected $table = 'almacenes'; // o el nombre que usaste en la base de datos


    protected $fillable = ['nombre', 'ubicacion', 'sucursal_id'];

    public function movimientos()
    {
        return $this->hasMany(AlmacenMovimiento::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function scopeDeSucursal($query, $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }
}
