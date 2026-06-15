<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Mesa extends Model
{
    use Auditable;
    protected $table = 'mesas';

    protected $fillable = [
        'sucursal_id', 'numero', 'nombre', 'capacidad',
        'ubicacion', 'estado', 'activa', 'categoria_id', 'pos_x', 'pos_y',
    ];

    protected $casts = [
        'capacidad' => 'integer',
        'activa'    => 'boolean',
        'pos_x'     => 'integer',
        'pos_y'     => 'integer',
    ];

    public function categoria()
    {
        return $this->belongsTo(Category::class, 'categoria_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function ordenActiva()
    {
        return $this->hasOne(Venta::class)->where('estado', 'abierta')->latest();
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function reservacion()
    {
        return $this->hasOne(Reservacion::class)->whereIn('estado', ['pendiente', 'confirmada'])->latest('fecha_hora');
    }

    public function scopeDeSucursal($query)
    {
        if ($sucursalId = session('sucursal_id')) {
            return $query->where('sucursal_id', $sucursalId);
        }
        return $query;
    }
}
