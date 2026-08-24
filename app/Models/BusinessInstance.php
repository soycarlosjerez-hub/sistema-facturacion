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
        'trial_started_at',
        'trial_ends_at',
        'costo_mensual',
        'bloqueado',
        'motivo_bloqueo',
        'bloqueado_en',
        'setup_completed',
        'deleted_at',
        'logo',
    ];

    protected $casts = [
        'configuracion' => 'array',
        'activo' => 'boolean',
        'bloqueado' => 'boolean',
        'setup_completed' => 'boolean',
        'fecha_vencimiento' => 'datetime',
        'trial_started_at' => 'datetime',
        'trial_ends_at' => 'datetime',
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

    /**
     * Último pago confirmado (completado/pagado). Los pagos pendientes NO
     * mantienen la suscripción al día.
     */
    public function ultimoPagoConfirmado(): HasOne
    {
        return $this->hasOne(PagoInstancia::class, 'business_instance_id')
            ->whereIn('estado_pago', ['completado', 'pagado'])
            ->latestOfMany('mes_pagado');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(BusinessInstanceModule::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(InstanceApiKey::class, 'business_instance_id');
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }
        return asset('storage/' . $this->logo);
    }

    public static function scopeWithLogo($query)
    {
        return $query->whereNotNull('logo')->where('logo', '!=', '');
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

    public function trialDays(): int
    {
        return (int) config('system.suscripcion.trial_days', 15);
    }

    /**
     * ¿La instancia está dentro de su periodo de prueba (15 días) y aún no
     * tiene ningún pago confirmado?
     */
    public function enPeriodoPrueba(): bool
    {
        if (!$this->trial_started_at) {
            return false;
        }

        if ($this->ultimoPagoConfirmado()->exists()) {
            return false;
        }

        $ends = $this->trial_ends_at
            ? $this->trial_ends_at
            : $this->trial_started_at->copy()->addDays($this->trialDays());

        return now()->lessThan($ends);
    }

    public function diasPruebaRestantes(): int
    {
        if (!$this->trial_ends_at || $this->trial_ends_at->lte(now())) {
            return 0;
        }

        return max(0, (int) now()->startOfDay()->diffInDays($this->trial_ends_at->copy()->startOfDay()));
    }

    /**
     * Estado de la suscripción para UI: prueba | activa | atrasada | suspendida.
     */
    public function estadoSuscripcion(): string
    {
        if ($this->bloqueado) {
            return 'suspendida';
        }

        if (!$this->ultimoPagoConfirmado()->exists() && $this->enPeriodoPrueba()) {
            return 'prueba';
        }

        if ($this->estaAlDia()) {
            return 'activa';
        }

        return 'atrasada';
    }

    public function estaAlDia(): bool
    {
        if ($this->bloqueado) {
            return false;
        }

        $ultimo = $this->ultimoPagoConfirmado()->first();
        if ($ultimo) {
            return $this->proximoPagoEsperado()->startOfDay()
                ->addDays($this->graceDays())
                ->gte(now()->startOfDay());
        }

        // Sin pagos confirmados: durante la prueba el sistema sigue activo.
        if ($this->enPeriodoPrueba()) {
            return true;
        }

        // Instancias legacy (sin prueba) con vencimiento futuro explícito.
        if ($this->trial_ends_at === null && $this->fecha_vencimiento) {
            return $this->fecha_vencimiento->startOfDay()->gte(now()->startOfDay());
        }

        return false;
    }

    /**
     * Meses atrasados calculados desde el primer mes sin pagar (pagos confirmados
     * o fin de la prueba).
     */
    public function mesesAtrasados(): int
    {
        if ($this->estaAlDia()) {
            return 0;
        }

        $ultimo = $this->ultimoPagoConfirmado()->first();
        if ($ultimo) {
            $base = $ultimo->mes_pagado->startOfMonth()->addMonth();
        } elseif ($this->trial_ends_at) {
            $base = $this->trial_ends_at->startOfMonth()->addMonth();
        } else {
            $base = ($this->fecha_vencimiento ?: $this->created_at)->startOfMonth()->addMonth();
        }

        $now = now()->startOfMonth();
        if ($base->gt($now)) {
            return 0;
        }

        return max(0, (int) $base->diffInMonths($now) + 1);
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
        $ultimo = $this->ultimoPagoConfirmado()->first();
        if ($ultimo) {
            return $ultimo->mes_pagado->startOfMonth()->addMonth();
        }

        if ($this->trial_ends_at) {
            return $this->trial_ends_at->copy();
        }

        if ($this->fecha_vencimiento) {
            return $this->fecha_vencimiento->copy();
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
            ->whereHas('ultimoPagoConfirmado', function ($q) {
                $q->where('mes_pagado', '<', now()->startOfMonth());
            })
            ->orWhereDoesntHave('ultimoPagoConfirmado');
    }

    public function scopeBloqueadas($query)
    {
        return $query->where('bloqueado', true);
    }

    protected static function booted(): void
    {
    }
}
