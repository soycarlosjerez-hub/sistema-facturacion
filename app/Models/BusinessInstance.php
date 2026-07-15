<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

class BusinessInstance extends Model
{
    use SoftDeletes;

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
        'trashed_at',
    ];

    protected $casts = [
        'configuracion' => 'array',
        'activo' => 'boolean',
        'bloqueado' => 'boolean',
        'setup_completed' => 'boolean',
        'fecha_vencimiento' => 'datetime',
        'bloqueado_en' => 'datetime',
        'costo_mensual' => 'decimal:2',
        'trashed_at' => 'datetime',
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

    public function scopeWithTrashed($query)
    {
        return $query->withTrashed();
    }

    public function scopeWithoutTrashed($query)
    {
        return $query->withoutTrashed();
    }

    public function restore()
    {
        if ($this->trashed()) {
            $this->trashed_at = null;
            $this->save();
            $this->fireModelEvent('restored', false);
        }
    }

    public function forceRestore()
    {
        return $this->restore();
    }

    public function forceDelete()
    {
        if ($this->trashed()) {
            $this->fireModelEvent('deleting', false);
            parent::forceDelete();
            $this->fireModelEvent('deleted', false);
        }
    }

    public function trashed()
    {
        return $this->trashed_at !== null;
    }

    protected static function booted(): void
    {
        static::created(function (self $instance) {
            self::seedSmtpSettings($instance->id);
        });

        static::restoring(function (self $instance) {
            $instance->trashed_at = null;
        });

        static::removing(function (self $instance) {
            if (!$instance->trashed()) {
                $instance->trashed_at = now();
                $instance->save();
            }
            return true;
        });
    }

    private static function seedSmtpSettings(int $tenantId): void
    {
        $mailPassword = env('MAIL_SMTP_PASSWORD', env('SMTP_PASSWORD', ''));
        
        $settings = [
            'mail_mailer'     => env('MAIL_MAILER', 'smtp'),
            'mail_host'       => env('MAIL_HOST', '127.0.0.1'),
            'mail_port'       => env('MAIL_PORT', '2525'),
            'mail_username'   => env('MAIL_USERNAME'),
            'mail_password'   => $mailPassword ? Crypt::encryptString($mailPassword) : null,
            'mail_encryption' => env('MAIL_ENCRYPTION'),
            'mail_from_address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
            'mail_from_name'    => env('MAIL_FROM_NAME', env('APP_NAME', 'Laravel')),
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
