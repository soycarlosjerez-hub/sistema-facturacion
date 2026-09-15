<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    use Auditable, HasFactory, TenantScope;

    protected $fillable = [
        'tenant_id',
        'sucursal_id',
        'almacen_id',
        'producto_id',
        'cantidad_anterior',
        'cantidad_nueva',
        'diferencia',
        'tipo',
        'motivo',
        'notas',
        'user_id',
    ];

    protected $casts = [
        'cantidad_anterior' => 'integer',
        'cantidad_nueva' => 'integer',
        'diferencia' => 'integer',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function almacen(): BelongsTo
    {
        return $this->belongsTo(Almacen::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTipoLabelAttribute(): string
    {
        $labels = [
            'ajuste' => 'Ajuste General',
            'merma' => 'Mermas/Pérdidas',
            'inventario' => 'Inventario Físico',
            'dacion' => 'Dación',
            'recepcion' => 'Recepción Extra',
        ];
        return $labels[$this->tipo ?? 'ajuste'] ?? 'Ajuste General';
    }
}
