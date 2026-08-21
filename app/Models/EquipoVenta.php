<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

class EquipoVenta extends Model
{
    use Auditable;

    protected $table = 'equipo_ventas';

    protected $fillable = [
        'equipo_id',
        'venta_id',
        'precio_vendido',
        'tenant_id',
    ];

    protected $casts = [
        'precio_vendido' => 'decimal:2',
    ];

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public static function scopePorFecha($query, $desde, $hasta)
    {
        return $query->whereBetween('created_at', [$desde, $hasta]);
    }

    public static function scopePorMarca($query, $marca)
    {
        return $query->whereHas('equipo', fn($q) => $q->where('marca', 'like', "%{$marca}%"));
    }

    public static function scopePorTipoDispositivo($query, $tipo)
    {
        return $query->whereHas('equipo', fn($q) => $q->where('tipo_dispositivo', $tipo));
    }

    public static function scopePorEstadoEquipo($query, $estado)
    {
        return $query->whereHas('equipo', fn($q) => $q->where('estado', $estado));
    }

    public static function totalIngresosPorTenant(int $tenantId): float
    {
        return static::where('tenant_id', $tenantId)
            ->sum('precio_vendido');
    }

    public static function totalIngresosDesde(int $tenantId, \DateTimeInterface $desde): float
    {
        return static::where('tenant_id', $tenantId)
            ->where('created_at', '>=', $desde)
            ->sum('precio_vendido');
    }
}
