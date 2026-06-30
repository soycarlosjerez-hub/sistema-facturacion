<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlquilerVivienda extends Model
{
    protected $table = 'alquileres_viviendas';

    protected $fillable = [
        'business_instance_id', 'sucursal_id', 'nombre', 'direccion', 'descripcion',
        'tipo', 'habitaciones', 'banos', 'area_m2',
        'monto_alquiler', 'monto_deposito', 'estado', 'activo',
    ];

    protected $casts = [
        'habitaciones' => 'integer',
        'banos' => 'integer',
        'area_m2' => 'decimal:2',
        'monto_alquiler' => 'decimal:2',
        'monto_deposito' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function businessInstance()
    {
        return $this->belongsTo(BusinessInstance::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function contratos()
    {
        return $this->hasMany(AlquilerContrato::class, 'vivienda_id');
    }

    public function contratoActivo()
    {
        return $this->hasOne(AlquilerContrato::class, 'vivienda_id')->where('estado', 'activo');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorInstancia($query, $instanceId)
    {
        return $query->where('business_instance_id', $instanceId);
    }
}
