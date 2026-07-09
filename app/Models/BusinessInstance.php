<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

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
        'setup_completed',
    ];

    protected $casts = [
        'configuracion' => 'array',
        'activo' => 'boolean',
        'bloqueado' => 'boolean',
        'setup_completed' => 'boolean',
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

    public function apiKeys(): HasMany
    {
        return $this->hasMany(InstanceApiKey::class, 'business_instance_id');
    }

    public function isModuloVisible(string $moduloKey): bool
    {
        // Check instance-level override first
        $override = $this->modules()->where('modulo_key', $moduloKey)->first();
        if ($override !== null) {
            return $override->visible;
        }
        // Fallback to BusinessType level
        return $this->businessType?->isModuloVisible($moduloKey, $this->businessType->slug) ?? false;
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

    protected static function booted(): void
    {
        static::created(function (self $instance) {
            self::seedSmtpSettings($instance->id);
        });
    }

    private static function seedSmtpSettings(int $tenantId): void
    {
        $settings = [
            'mail_mailer'     => 'smtp',
            'mail_host'       => 'mail.armada.do',
            'mail_port'       => '465',
            'mail_username'   => 'no-reply@armada.do',
            'mail_password'   => Crypt::encryptString('Dn%q#U0tV,65FqSU'),
            'mail_encryption' => 'ssl',
            'mail_from_address' => 'no-reply@armada.do',
            'mail_from_name'    => 'Sistema de Facturación',
        ];

        foreach ($settings as $key => $value) {
            \App\Models\SystemSetting::updateOrCreate(
                ['key' => $key, 'tenant_id' => $tenantId],
                ['value' => $value]
            );
        }

        \App\Models\SystemSetting::flush();
    }
}
