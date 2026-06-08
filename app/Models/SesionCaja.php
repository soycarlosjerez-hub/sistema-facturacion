<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesionCaja extends Model
{
    protected $table = 'sesion_cajas';

    protected $fillable = [
        'caja_id',
        'user_id',
        'fecha_apertura',
        'fecha_cierre',
        'monto_inicial',
        'ventas_efectivo',
        'ventas_tarjeta',
        'ventas_transferencia',
        'monto_declarado',
        'descuadre',
        'estado',
        'notas',
    ];

    protected $casts = [
        'fecha_apertura'      => 'datetime',
        'fecha_cierre'        => 'datetime',
        'monto_inicial'       => 'decimal:2',
        'ventas_efectivo'     => 'decimal:2',
        'ventas_tarjeta'      => 'decimal:2',
        'ventas_transferencia'=> 'decimal:2',
        'monto_declarado'     => 'decimal:2',
        'descuadre'           => 'decimal:2',
    ];

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function scopeAbiertas($query)
    {
        return $query->where('estado', 'abierta');
    }

    public function totalVentas(): float
    {
        return (float) $this->ventas()->sum('total');
    }

    public function totalEsperado(): float
    {
        return (float) $this->monto_inicial + (float) $this->ventas_efectivo;
    }
}
