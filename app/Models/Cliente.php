<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Cliente extends Authenticatable
{
    use Auditable, TenantScope, Notifiable;

    protected $fillable = [
        'nombre', 'email', 'telefono', 'direccion', 'rnc_cedula', 'rnc',
        'tipo_documento', 'tipo_cliente', 'limite_credito', 'balance_pendiente',
        'plazo_pago_dias', 'tasa_descuento_pct', 'moneda',
        'auto_bloquear_credito', 'notas_internas',
        'regimen_mensual', 'nit',
        'persona_contacto', 'cargo_contacto', 'whatsapp',
        'ciudad', 'provincia', 'codigo_postal',
        'segmento', 'origen_cliente', 'sector_actividad',
        'activo', 'acceso_api', 'tenant_id',
        'password', 'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'limite_credito'        => 'decimal:2',
        'balance_pendiente'     => 'decimal:2',
        'tasa_descuento_pct'    => 'decimal:2',
        'activo'                => 'boolean',
        'acceso_api'            => 'boolean',
        'auto_bloquear_credito' => 'boolean',
        'regimen_mensual'        => 'boolean',
        'plazo_pago_dias'       => 'integer',
        'email_verified_at'     => 'datetime',
    ];

    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function apiTokens(): HasMany
    {
        return $this->hasMany(ClientApiToken::class);
    }

    public function createToken(string $name, array $abilities = ['*'], ?\DateTime $expiresAt = null): ClientApiToken
    {
        $plain = bin2hex(random_bytes(32));
        $token = $this->apiTokens()->create([
            'name'       => $name,
            'token'      => hash('sha256', $plain),
            'abilities'  => $abilities,
            'expires_at' => $expiresAt,
        ]);
        $token->plain_text = $plain;
        return $token;
    }

    public function currentAccessToken(): ?ClientApiToken
    {
        return request()->attributes->get('client_api_token');
    }

    public function tokenCan(string $ability): bool
    {
        return $this->currentAccessToken()?->tokenCan($ability) ?? false;
    }

    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill(['email_verified_at' => $this->freshTimestamp()])->save();
    }

    public function routeNotificationForMail(): string
    {
        return $this->email;
    }

    // Relationships
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class);
    }

    public function conduces()
    {
        return $this->hasMany(Conduce::class);
    }

    // Helper to get or create the generic consumer client
    public static function consumidorFinal(): self
    {
        $tenantId = Auth::check() ? Auth::user()->business_instance_id : null;
        return static::firstOrCreate(
            ['nombre' => 'Consumidor Final', 'tenant_id' => $tenantId],
            [
                'tipo_documento' => '1', // Cédula genérica
                'rnc_cedula'     => '00000000000',
                'tipo_cliente'   => 'consumo',
            ]
        );
    }

    // Accessor for activo label
    public function getActivoLabelAttribute(): string
    {
        return $this->activo ? 'Activo' : 'Inactivo';
    }

    public function getColorBadgeActivoAttribute(): string
    {
        return $this->activo ? 'success' : 'secondary';
    }

    // Accessor for a pretty label
    public function getTipoClienteLabelAttribute(): string
    {
        $labels = [
            'credito_fiscal'  => 'Crédito Fiscal',
            'consumo'         => 'Consumo',
            'gubernamental'   => 'Gubernamental',
            'especial'        => 'Especial',
        ];
        return $labels[$this->tipo_cliente ?? 'consumo'] ?? 'Consumo';
    }

    // Accessor for badge colour class
    public function getColorBadgeAttribute(): string
    {
        $colors = [
            'credito_fiscal'  => 'primary',
            'consumo'         => 'success',
            'gubernamental'   => 'warning',
            'especial'        => 'info',
        ];
        return $colors[$this->tipo_cliente ?? 'consumo'] ?? 'secondary';
    }

    // ─── Credit Limit Helpers ───────────────────────────────────────────

    public function getUtilizacionCreditoAttribute(): float
    {
        if ($this->limite_credito <= 0) return 0;
        return round(($this->balance_pendiente / $this->limite_credito) * 100, 1);
    }

    public function getCreditoDisponibleAttribute(): float
    {
        return max(0, ($this->limite_credito - $this->balance_pendiente));
    }

    public function excedeCredito(float $montoAdicional = 0): bool
    {
        if ($this->limite_credito <= 0) return false;
        return ($this->balance_pendiente + $montoAdicional) > $this->limite_credito;
    }

    public function getExcesoCreditoAttribute(): float
    {
        if ($this->limite_credito <= 0) return 0;
        return max(0, $this->balance_pendiente - $this->limite_credito);
    }

    public function getEstadoCreditoAttribute(): string
    {
        if ($this->limite_credito <= 0) return 'sin_limite';
        $uso = $this->utilizacion_credito;
        if ($uso >= 100) return 'excedido';
        if ($uso >= 80) return 'critico';
        if ($uso >= 50) return 'moderado';
        return 'normal';
    }

    public function getEstadoCreditoLabelAttribute(): string
    {
        $labels = [
            'sin_limite' => 'Sin Límite',
            'excedido'   => 'Excedido',
            'critico'    => 'Crítico',
            'moderado'   => 'Moderado',
            'normal'     => 'Normal',
        ];
        return $labels[$this->estado_credito] ?? 'Normal';
    }

    public function getColorBadgeEstadoCreditoAttribute(): string
    {
        $colors = [
            'sin_limite' => 'secondary',
            'excedido'   => 'danger',
            'critico'    => 'warning',
            'moderado'   => 'info',
            'normal'     => 'success',
        ];
        return $colors[$this->estado_credito] ?? 'secondary';
    }

    // ─── Segmento Labels ────────────────────────────────────────────────

    public function getSegmentoLabelAttribute(): string
    {
        $labels = [
            'micro'     => 'Micro',
            'pequeno'   => 'Pequeño',
            'mediano'   => 'Mediano',
            'grande'    => 'Grande',
            'gobierno'  => 'Gobierno',
        ];
        return $labels[$this->segmento ?? 'micro'] ?? 'Micro';
    }

    // ─── Origen Labels ──────────────────────────────────────────────────

    public function getOrigenLabelAttribute(): string
    {
        $labels = [
            'referencia'  => 'Referido',
            'web'         => 'Sitio Web',
            'walkin'      => 'Presencial',
            'publicidad'  => 'Publicidad',
            'otro'        => 'Otro',
        ];
        return $labels[$this->origen_cliente ?? 'walkin'] ?? 'Presencial';
    }

    // ─── Moneda Label ───────────────────────────────────────────────────

    public function getMonedaLabelAttribute(): string
    {
        $labels = [
            'RD'  => 'RD$',
            'USD' => 'US$',
            'EUR' => '€',
        ];
        return $labels[$this->moneda ?? 'RD'] ?? 'RD$';
    }

    // ─── Métricas Automáticas ───────────────────────────────────────────

    public function getUltimaCompraAttribute()
    {
        return $this->ventas()->latest()->value('created_at');
    }

    public function getTotalComprasAttribute(): float
    {
        return (float) $this->ventas()->sum('total');
    }

    public function getPromedioCompraAttribute(): float
    {
        $count = $this->ventas()->count();
        if ($count === 0) return 0;
        return (float) ($this->ventas()->sum('total') / $count);
    }

    public function getCantidadVentasAttribute(): int
    {
        return $this->ventas()->count();
    }

    // ─── Recalcular balance desde ventas ────────────────────────────────

    public function recalcularBalance(): static
    {
        $pendiente = $this->ventas()
            ->whereIn('estado', ['pendiente', 'cuenta_abierta'])
            ->get()
            ->sum(fn($v) => $v->total - $v->montoPagado());

        $this->balance_pendiente = round($pendiente, 2);
        $this->saveQuietly();

        return $this;
    }

    // ─── Scopes ─────────────────────────────────────────────────────────

    public function scopeConDeuda($query)
    {
        return $query->where('balance_pendiente', '>', 0);
    }

    public function scopeExcedeCredito($query)
    {
        return $query->where('limite_credito', '>', 0)
            ->whereRaw('balance_pendiente > limite_credito');
    }

    public function scopeDelSegmento($query, string $segmento)
    {
        return $query->where('segmento', $segmento);
    }
}
