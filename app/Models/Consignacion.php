<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class Consignacion extends Model
{
    use HasFactory, Auditable, TenantScope, SoftDeletes;

    protected $table = 'consignaciones';

    protected $fillable = [
        'galeria_nombre',
        'obra_id',
        'fecha_inicio',
        'fecha_fin',
        'comision_percentage',
        'estado',
        'fecha_venta',
        'precio_venta',
        'comision_monto',
        'pago_recibido',
        'pago_fecha',
        'notas',
        'tenant_id',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'comision_percentage' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'comision_monto' => 'decimal:2',
        'pago_recibido' => 'boolean',
        'pago_fecha' => 'date',
    ];

    public const STATUSES = [
        'activa' => ['label' => 'Activa', 'color' => 'primary', 'icon' => 'eye'],
        'vendida' => ['label' => 'Vendida', 'color' => 'success', 'icon' => 'check-circle'],
        'devuelta' => ['label' => 'Devuelta', 'color' => 'warning', 'icon' => 'arrow-return-left'],
    ];

    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    public function getEstadoLabelAttribute(): string
    {
        return $this->estado && isset(self::STATUSES[$this->estado])
            ? self::STATUSES[$this->estado]['label']
            : 'Desconocido';
    }

    public function getEstadoColorAttribute(): string
    {
        return self::STATUSES[$this->estado]['color'] ?? 'secondary';
    }

    public function getEsVendidaAttribute(): bool
    {
        return $this->estado === 'vendida';
    }

    public function getEsActivaAttribute(): bool
    {
        return $this->estado === 'activa';
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->estado === 'activa'
            && $this->fecha_fin
            && $this->fecha_fin->isPast();
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    public function scopeVendidas($query)
    {
        return $query->where('estado', 'vendida');
    }

    public function scopeByGallery($query, $galeria)
    {
        return $query->where('galeria_nombre', 'like', "%{$galeria}%");
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('galeria_nombre', 'like', "%{$search}%")
            ->orWhereHas('obra', function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%");
            });
    }
}
