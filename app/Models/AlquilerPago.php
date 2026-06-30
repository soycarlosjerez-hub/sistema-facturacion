<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlquilerPago extends Model
{
    protected $table = 'alquileres_pagos';

    protected $fillable = [
        'business_instance_id', 'contrato_id', 'monto', 'fecha_pago',
        'mes_cobrado', 'ano_cobrado', 'metodo_pago',
        'recibo_numero', 'notas', 'registrado_por',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto' => 'decimal:2',
        'mes_cobrado' => 'integer',
        'ano_cobrado' => 'integer',
    ];

    public function businessInstance()
    {
        return $this->belongsTo(BusinessInstance::class);
    }

    public function contrato()
    {
        return $this->belongsTo(AlquilerContrato::class, 'contrato_id');
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function scopePorInstancia($query, $instanceId)
    {
        return $query->where('business_instance_id', $instanceId);
    }
}
