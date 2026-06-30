<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlquilerContrato extends Model
{
    protected $table = 'alquileres_contratos';

    protected $fillable = [
        'business_instance_id', 'vivienda_id', 'inquilino_id',
        'fecha_inicio', 'fecha_fin', 'monto_alquiler', 'monto_deposito',
        'dia_pago', 'estado', 'deposito_pagado', 'notas',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'monto_alquiler' => 'decimal:2',
        'monto_deposito' => 'decimal:2',
        'dia_pago' => 'integer',
        'deposito_pagado' => 'boolean',
    ];

    public function businessInstance()
    {
        return $this->belongsTo(BusinessInstance::class);
    }

    public function vivienda()
    {
        return $this->belongsTo(AlquilerVivienda::class, 'vivienda_id');
    }

    public function inquilino()
    {
        return $this->belongsTo(AlquilerInquilino::class, 'inquilino_id');
    }

    public function pagos()
    {
        return $this->hasMany(AlquilerPago::class, 'contrato_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePorInstancia($query, $instanceId)
    {
        return $query->where('business_instance_id', $instanceId);
    }
}
