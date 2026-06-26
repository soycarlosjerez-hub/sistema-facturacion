<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use App\Traits\HasWizardStep;
use App\Traits\TenantScope;

class Sucursal extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasWizardStep, TenantScope;

    protected $table = 'sucursales';

    protected $fillable = [
        'codigo', 'nombre', 'direccion', 'telefono', 'email', 'rnc', 'activa', 'es_matriz', 'tenant_id',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'es_matriz' => 'boolean',
    ];

    public function almacenes(): HasMany
    {
        return $this->hasMany(Almacen::class);
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class);
    }

    public function cajas(): HasMany
    {
        return $this->hasMany(Caja::class);
    }

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }

    public function conduces(): HasMany
    {
        return $this->hasMany(Conduce::class);
    }

    public function cotizaciones(): HasMany
    {
        return $this->hasMany(Cotizacion::class);
    }

    public function mesas(): HasMany
    {
        return $this->hasMany(Mesa::class);
    }

    public function waitlistEntries(): HasMany
    {
        return $this->hasMany(WaitlistEntry::class);
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }
}
