<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use Auditable;

    protected $fillable = [
        'cliente_id', 'placa', 'marca', 'modelo', 'anio', 'color', 'vin', 'tipo', 'notas',
    ];

    protected $casts = [
        'anio' => 'integer',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function citas()
    {
        return $this->hasMany(LavaderoCita::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'vehiculo_id');
    }

    public function scopePorPlaca($query, $placa)
    {
        return $query->where('placa', 'like', "%{$placa}%");
    }

    public function getNombreCompletoAttribute()
    {
        $parts = array_filter([$this->marca, $this->modelo, $this->placa]);
        return implode(' ', $parts) ?: 'Vehículo #' . $this->id;
    }
}
