<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

class BusinessInstance extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'nombre',
        'slug',
        'rnc',
        'email',
        'telefono',
        'direccion',
        'business_type_id',
        'plan_id',
        'owner_user_id',
        'owner_email',
        'owner_nombre',
        'configuracion',
        'activo',
        'fecha_vencimiento',
        'costo_mensual',
        'bloqueado',
        'motivo_bloqueo',
        'bloqueado_en',
        'setup_completed',
        'deleted_at',
    ];

    protected $casts = [
        'configuracion' => 'array',
        'activo' => 'boolean',
        'bloqueado' => 'boolean',
        'setup_completed' => 'boolean',
        'fecha_vencimiento' => 'datetime',
        'bloqueado_en' => 'datetime',
        'costo_mensual' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    public function businessType(): BelongsTo
    {
        return $this->belongsTo(BusinessType::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function planActivo(): ?Plan
    {
        return $this->plan;
    }

    public function precioMensual(): float
    {
        if ($this->plan) {
            return (float) $this->plan->precio_mensual;
        }

        return (float) $this->costo_mensual;
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

    public function apiKeys(): HasMany
    {
        return $this->hasMany(InstanceApiKey::class, 'business_instance_id');
    }

    /**
     * Cuenta las empresas (instancias) del mismo owner
     */
    public function empresasCount(): int
    {
        return static::where('owner_user_id', $this->owner_user_id)
            ->where('activo', true)
            ->count();
    }

    public function isModuloVisible(string $moduloKey): bool
    {
        $override = $this->modules()->where('modulo_key', $moduloKey)->first();
        if ($override !== null) {
            $visible = $override->visible;
        } else {
            $visible = $this->businessType?->isModuloVisible($moduloKey, $this->businessType->slug) ?? false;
        }

        if (! $visible) {
            return false;
        }

        return $this->plan?->permiteModulo($moduloKey) ?? true;
    }

    public function getDefaultConfig(): array
    {
        $baseConfig = $this->businessType?->config ?? [];
        return array_merge($baseConfig, $this->configuracion ?? []);
    }

    public function graceDays(): int
    {
        return (int) config('system.suscripcion.grace_days', 3);
    }

    public function estaAlDia(): bool
    {
        if ($this->bloqueado) {
            return false;
        }

        return $this->proximoPagoEsperado()->startOfDay()
            ->addDays($this->graceDays())
            ->gte(now()->startOfDay());
    }

    public function mesesAtrasados(): int
    {
        if ($this->estaAlDia()) {
            return 0;
        }

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
        if (!$this->precioMensual()) {
            return 0;
        }
        return $this->mesesAtrasados() * $this->precioMensual();
    }

    public function proximoPagoEsperado(): ?Carbon
    {
        $ultimo = $this->ultimoPago()->first();
        if ($ultimo) {
            return $ultimo->mes_pagado->startOfMonth()->addMonth();
        }
        return $this->created_at->startOfMonth();
    }

    public function bloqueablePorImpago(): bool
    {
        return $this->activo
            && ! $this->bloqueado
            && ! $this->estaAlDia();
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
        return $query->where('activo', true)
            ->where('bloqueado', false)
            ->whereHas('ultimoPago', function ($q) {
                $q->where('mes_pagado', '<', now()->startOfMonth());
            })
            ->orWhereDoesntHave('ultimoPago');
    }

    public function scopeBloqueadas($query)
    {
        return $query->where('bloqueado', true);
    }

    protected static function booted(): void
    {
    }
}
