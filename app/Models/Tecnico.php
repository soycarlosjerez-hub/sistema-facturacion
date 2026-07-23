<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class Tecnico extends Model
{
    use HasFactory;
    use Auditable;
    use TenantScope;

    protected $table = 'tecnicos';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'nombre',
        'cedula',
        'telefono',
        'email',
        'especialidad',
        'tarifa_hora',
        'tarifa_fija',
        'activo',
        'notas',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'tarifa_hora' => 'decimal:2',
        'tarifa_fija' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ordenesReparacion(): HasMany
    {
        return $this->hasMany(OrdenReparacion::class);
    }

    public function serviciosDomotica(): HasMany
    {
        return $this->hasMany(ServicioDomotica::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
