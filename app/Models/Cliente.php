<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use Auditable, TenantScope;

    protected $fillable = [
        'nombre', 'email', 'telefono', 'direccion', 'rnc_cedula', 'rnc',
        'tipo_documento', 'tipo_cliente', 'limite_credito', 'balance_pendiente', 'tenant_id',
    ];

    protected $casts = [
        'limite_credito'    => 'decimal:2',
        'balance_pendiente' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($builder) {
            if (auth()->check() && method_exists($builder->getModel(), 'getTenantIdColumn')) {
                $builder->where('tenant_id', auth()->user()->tenant_id);
            }
        });
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
        return static::firstOrCreate(
            ['nombre' => 'Consumidor Final'],
            [
                'tipo_documento' => '1', // Cédula genérica
                'rnc_cedula'     => '00000000000',
                'tipo_cliente'   => 'consumo',
            ]
        );
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
}
