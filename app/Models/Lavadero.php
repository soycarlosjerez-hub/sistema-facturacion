<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lavadero extends Model
{
    use HasFactory;

    protected $fillable = [
        'folio',
        'cliente_id',
        'sucursal_id',
        'user_id',
        'vehiculo_id',
        'fecha_ingreso',
        'fecha_entrega',
        'estado',
        'servicio',
        'total',
        'notas',
    ];

    protected $casts = [
        'fecha_ingreso' => 'datetime',
        'fecha_entrega' => 'datetime',
        'total' => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }
}
