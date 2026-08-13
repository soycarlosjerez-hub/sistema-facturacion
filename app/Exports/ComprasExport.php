<?php

namespace App\Exports;

use App\Models\Compra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ComprasExport implements FromCollection, WithHeadings, WithMapping
{
    protected $proveedor;
    protected $desde;
    protected $hasta;
    protected $sucursalId;

    public function __construct($proveedor = null, $desde = null, $hasta = null, $sucursalId = null)
    {
        $this->proveedor = $proveedor;
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->sucursalId = $sucursalId;
    }

    public function collection()
    {
        $query = Compra::with(['proveedor', 'tipoCompra', 'user'])
            ->select(
                'id',
                'proveedor_id',
                'user_id',
                'tipo_compra_id',
                'fecha',
                'subtotal',
                'itbis_total',
                'retencion_isr',
                'retencion_itbis',
                'total',
                'created_at',
                'updated_at'
            );

        if ($this->sucursalId) {
            $query->where('sucursal_id', $this->sucursalId);
        }

        if ($this->proveedor) {
            $termino = trim($this->proveedor);
            $query->whereHas('proveedor', function ($q) use ($termino) {
                $q->where('nombre', 'like', '%' . $termino . '%')
                  ->orWhere('rnc_cedula', 'like', '%' . $termino . '%')
                  ->orWhere('rnc', 'like', '%' . $termino . '%');
            });
        }

        if ($this->desde) {
            $query->whereDate('fecha', '>=', $this->desde);
        }

        if ($this->hasta) {
            $query->whereDate('fecha', '<=', $this->hasta);
        }

        return $query->orderByDesc('fecha')->get();
    }

    public function map($compra): array
    {
        return [
            $compra->id,
            $compra->proveedor?->nombre ?? '—',
            $compra->proveedor?->rnc_cedula ?: ($compra->proveedor?->rnc ?: '—'),
            $compra->user?->name ?? '—',
            $compra->tipoCompra?->nombre ?? '—',
            $compra->fecha ? $compra->fecha->format('d/m/Y') : $compra->created_at->format('d/m/Y'),
            $compra->subtotal,
            $compra->itbis_total,
            $compra->total_retenciones,
            $compra->total,
            $compra->total_pagar,
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Proveedor',
            'RNC/Cédula',
            'Usuario',
            'Tipo de compra',
            'Fecha',
            'Subtotal',
            'ITBIS',
            'Retenciones',
            'Total',
            'Total a pagar',
        ];
    }
}