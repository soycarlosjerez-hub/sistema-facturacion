<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoInstancia extends Model
{
    protected $table = 'pagos_instancia';

    protected $fillable = [
        'business_instance_id',
        'monto',
        'mes_pagado',
        'fecha_pago',
        'metodo_pago',
        'notas',
        'registrado_por',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'mes_pagado' => 'date',
        'fecha_pago' => 'datetime',
    ];

    public function instance(): BelongsTo
    {
        return $this->belongsTo(BusinessInstance::class, 'business_instance_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
