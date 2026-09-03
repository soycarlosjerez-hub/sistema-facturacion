<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\TenantScope;

class LealtadCuenta extends Model
{
    use TenantScope;

    protected $table = 'lealtad_cuentas';

    protected $fillable = [
        'tenant_id',
        'cliente_id',
        'puntos_acumulados',
        'puntos_canjeados',
        'puntos_vencidos',
        'nivel',
        'tasa_cambio',
        'ultima_actividad',
    ];

    protected $casts = [
        'puntos_acumulados' => 'integer',
        'puntos_canjeados' => 'integer',
        'puntos_vencidos' => 'integer',
        'tasa_cambio' => 'decimal:2',
        'ultima_actividad' => 'date',
    ];

    public function movimientos(): HasMany
    {
        return $this->hasMany(LealtadMovimiento::class, 'cuenta_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function puntosDisponibles(): int
    {
        return $this->puntos_acumulados - $this->puntos_canjeados - $this->puntos_vencidos;
    }

    public function puedeCanjear(int $puntos): bool
    {
        return $this->puntosDisponibles() >= $puntos;
    }

    public function canjearPuntos(int $puntos): bool
    {
        if (!$this->puedeCanjear($puntos)) {
            return false;
        }

        $this->puntos_canjeados += $puntos;
        $this->ultima_actividad = now()->toDateString();
        return $this->save();
    }

    public function ganarPuntos(int $puntos): bool
    {
        $this->puntos_acumulados += $puntos;
        $this->ultima_actividad = now()->toDateString();

        $puntosDisp = $this->puntosDisponibles();
        $this->nivel = match (true) {
            $puntosDisp >= 10000 => 'oro',
            $puntosDisp >= 5000 => 'plata',
            default => 'bronce',
        };

        return $this->save();
    }

    public function scopeNivel($query, string $nivel)
    {
        return $query->where('nivel', $nivel);
    }
}
