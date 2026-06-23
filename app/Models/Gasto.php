<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class Gasto extends Model
{
    use Auditable;
    use TenantScope;
    protected $fillable = [
        'descripcion',
        'monto',
        'categoria',
        'notas',
        'fecha_gasto',
        'metodo_pago',
        'comprobante',
        'user_id',
        'caja_id',
        'sesion_caja_id',
        'sucursal_id',
        'tenant_id',
    ];

    protected $casts = [
        'fecha_gasto' => 'date',
        'monto' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function sesionCaja(): BelongsTo
    {
        return $this->belongsTo(SesionCaja::class);
    }

    public function scopeOfCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopeDelDia($query)
    {
        return $query->whereDate('fecha_gasto', today());
    }

    public function scopeDelMes($query)
    {
        return $query->whereMonth('fecha_gasto', now()->month)
            ->whereYear('fecha_gasto', now()->year);
    }

    public static function categorias(): array
    {
        return [
            'servicios' => 'Servicios',
            'suministros' => 'Suministros',
            'mantenimiento' => 'Mantenimiento',
            'salarios' => 'Salarios',
            'impuestos' => 'Impuestos',
            'transporte' => 'Transporte',
            'publicidad' => 'Publicidad',
            'alimentacion' => 'Alimentación',
            'otros' => 'Otros',
        ];
    }
}
