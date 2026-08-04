<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryDriver extends Model
{
    use Auditable, TenantScope;

    protected $table = 'delivery_drivers';

    protected $fillable = [
        'tenant_id',
        'nombre',
        'apellido',
        'cedula',
        'telefono',
        'whatsapp',
        'licencia_conducir',
        'activo',
        'notas',
        'avatar_url',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(BusinessInstance::class, 'tenant_id');
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function ordenes(): HasMany
    {
        return $this->hasMany(Orden::class);
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(DriverEarning::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }
}
