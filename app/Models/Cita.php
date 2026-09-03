<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\TenantScope;

class Cita extends Model
{
    use TenantScope;
    protected $fillable = [
        'tenant_id',
        'cliente_id',
        'vehiculo_id',
        'fecha_hora',
        'servicio',
        'notas',
        'estado',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(BusinessInstance::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function scopeActivos($query)
    {
        return $query->whereNotIn('estado', ['cancelada']);
    }
}
