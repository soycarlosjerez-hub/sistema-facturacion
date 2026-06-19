<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class BusinessInstance extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'rnc',
        'email',
        'telefono',
        'direccion',
        'business_type_id',
        'owner_user_id',
        'configuracion',
        'activo',
        'fecha_vencimiento',
        'costo_mensual',
        'bloqueado',
        'motivo_bloqueo',
        'bloqueado_en',
    ];

    protected $casts = [
        'configuracion' => 'array',
        'activo' => 'boolean',
        'bloqueado' => 'boolean',
        'fecha_vencimiento' => 'datetime',
        'bloqueado_en' => 'datetime',
        'costo_mensual' => 'decimal:2',
    ];

    public function businessType(): BelongsTo
    {
        return $this->belongsTo(BusinessType::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'business_instance_id');
    }

    public function sucursales(): HasMany
    {
        return $this->hasMany(Sucursal::class, 'business_instance_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(PagoInstancia::class, 'business_instance_id');
    }

    public function ultimoPago(): HasOne
    {
        return $this->hasOne(PagoInstancia::class, 'business_instance_id')->latestOfMany('mes_pagado');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(BusinessInstanceModule::class);
    }

    public function isModuloVisible(string $moduloKey): bool
    {
        // Check instance-level override first
        $override = $this->modules()->where('modulo_key', $moduloKey)->first();
        if ($override !== null) {
            return $override->visible;
        }
        // Fallback to BusinessType level
        return $this->businessType?->isModuloVisible($moduloKey) ?? false;
    }

    public function getDefaultConfig(): array
    {
        $baseConfig = $this->businessType?->config ?? [];
        return array_merge($baseConfig, $this->configuracion ?? []);
    }

    public function estaAlDia(): bool
    {
        $ultimo = $this->ultimoPago()->first();
        if (!$ultimo) {
            return false;
        }
        $inicioMesActual = now()->startOfMonth();
        return $ultimo->mes_pagado->startOfMonth()->greaterThanOrEqualTo($inicioMesActual);
    }

    public function mesesAtrasados(): int
    {
        $ultimo = $this->ultimoPago()->first();
        if (!$ultimo) {
            $creado = $this->created_at ? $this->created_at->startOfMonth() : now()->startOfMonth()->subMonth();
            return (int) $creado->diffInMonths(now()->startOfMonth()) + 1;
        }
        $siguiente = $ultimo->mes_pagado->startOfMonth()->addMonth();
        return max(0, (int) $siguiente->diffInMonths(now()->startOfMonth()));
    }

    public function deudaEstimada(): float
    {
        if (!$this->costo_mensual) {
            return 0;
        }
        return $this->mesesAtrasados() * (float) $this->costo_mensual;
    }

    public function proximoPagoEsperado(): ?Carbon
    {
        $ultimo = $this->ultimoPago()->first();
        if ($ultimo) {
            return $ultimo->mes_pagado->startOfMonth()->addMonth();
        }
        return $this->created_at->startOfMonth();
    }

    public function scopeActive($query)
    {
        return $query->where('activo', true);
    }

    public function scopeOwnedBy($query, int $userId)
    {
        return $query->where('owner_user_id', $userId);
    }

    public function scopeAlDia($query)
    {
        return $query->where('activo', true)->where('bloqueado', false);
    }

    public function scopeConAtraso($query)
    {
        return $query->where('activo', true)->where('bloqueado', false);
    }

    public function scopeBloqueadas($query)
    {
        return $query->where('bloqueado', true);
    }
}
