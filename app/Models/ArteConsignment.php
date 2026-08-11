<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArteConsignment extends Model
{
    use HasFactory;
    use Auditable;
    use TenantScope;

    protected $fillable = [
        'tenant_id',
        'obra_id',
        'consignante',
        'porcentaje_comision',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'monto_entregado',
        'notas',
    ];

    protected $casts = [
        'porcentaje_comision' => 'decimal:2',
        'monto_entregado' => 'decimal:2',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    public function obra(): BelongsTo
    {
        return $this->belongsTo(ArteObra::class);
    }

    public function getEstadoLabelAttribute(): string
    {
        return match($this->estado) {
            'activa' => 'Activa',
            'completada' => 'Completada',
            'cancelada' => 'Cancelada',
            default => ucfirst($this->estado),
        };
    }

    public function getEstadoBadgeClassAttribute(): string
    {
        return match($this->estado) {
            'activa' => 'success',
            'completada' => 'info',
            'cancelada' => 'danger',
            default => 'secondary',
        };
    }
}