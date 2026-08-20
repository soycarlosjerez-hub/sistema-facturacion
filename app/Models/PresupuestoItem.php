<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

class PresupuestoItem extends Model
{
    use HasFactory;
    use Auditable;

    protected $table = 'presupuesto_items';

    protected $fillable = [
        'presupuesto_id',
        'producto_id',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'tipo_item',
        'descuento',
        'itbis_porcentaje',
        'subtotal',
        'tenant_id',
    ];

    protected $casts = [
        'cantidad'        => 'integer',
        'precio_unitario' => 'decimal:2',
        'descuento'       => 'decimal:2',
        'itbis_porcentaje' => 'decimal:2',
        'subtotal'        => 'decimal:2',
    ];

    protected $appends = ['tipo_item_label', 'linea_total'];

    public function presupuesto(): BelongsTo
    {
        return $this->belongsTo(Presupuesto::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function calcular(): void
    {
        $subtotal = $this->cantidad * $this->precio_unitario;
        $descuentoLínea = ($subtotal * ($this->descuento ?? 0)) / 100;
        $baseImponible = $subtotal - $descuentoLínea;
        if ($baseImponible < 0) {
            $baseImponible = 0;
        }

        $this->subtotal = round($baseImponible, 2);
        $this->save();
    }

    public function calcularLineaTotal(): float
    {
        $subtotal = $this->cantidad * $this->precio_unitario;
        $descuentoLínea = ($subtotal * ($this->descuento ?? 0)) / 100;
        $baseImponible = $subtotal - $descuentoLínea;
        if ($baseImponible < 0) {
            $baseImponible = 0;
        }

        $itbis = $baseImponible * ($this->itbis_porcentaje ?? 18) / 100;

        return round($baseImponible + $itbis, 2);
    }

    public function getTipoItemLabelAttribute(): ?string
    {
        return match ($this->tipo_item) {
            'producto'       => 'Producto',
            'mano_obra'      => 'Mano de Obra',
            'desplazamiento' => 'Desplazamiento',
            'servicio'       => 'Servicio',
            'licencia'       => 'Licencia',
            'otro'           => 'Otro',
            default          => null,
        };
    }

    public function getLineaTotalAttribute(): float
    {
        return $this->calcularLineaTotal();
    }
}
