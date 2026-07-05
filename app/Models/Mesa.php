<?php

namespace App\Models;

use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Mesa extends Model
{
    use Auditable;
    use TenantScope;
    protected $table = 'mesas';

    protected $fillable = [
        'sucursal_id', 'numero', 'nombre', 'capacidad',
        'ubicacion_id', 'estado', 'activa', 'categoria_id', 'pos_x', 'pos_y', 'tenant_id',
    ];

    protected $casts = [
        'capacidad' => 'integer',
        'activa'    => 'boolean',
        'pos_x'     => 'integer',
        'pos_y'     => 'integer',
    ];

    public function categoria()
    {
        return $this->belongsTo(MesaCategoria::class, 'categoria_id');
    }

    public function ubicacion()
    {
        return $this->belongsTo(MesaUbicacion::class, 'ubicacion_id');
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
            return $query->where(function ($q) use ($sucursalId) {
                $q->where('sucursal_id', $sucursalId)
                  ->orWhereNull('sucursal_id');
            });
        }
        return $query;
    }
}
