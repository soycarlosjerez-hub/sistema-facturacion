<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caja extends Model
{
    protected $fillable = [
        'nombre',
        'codigo',
        'sucursal_id',
        'ubicacion',
        'estado',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function sesiones(): HasMany
    {
        return $this->hasMany(SesionCaja::class);
    }

    public function sesionActiva(): ?SesionCaja
    {
        return $this->sesiones()->where('estado', 'abierta')->latest('fecha_apertura')->first();
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function getCodigoCortoAttribute(): string
    {
        if ($this->codigo) {
            return $this->codigo;
        }
        return 'C' . str_pad($this->id, 2, '0', STR_PAD_LEFT);
    }
}
