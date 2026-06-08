<?php

namespace App\Exports;

use App\Models\Compra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ComprasExport implements FromCollection, WithHeadings
{
    /**
     * Devuelve la colección de compras para exportar.
     */
    public function collection()
    {
        // Se traen las compras con sus relaciones para que sea más legible
        return Compra::with(['proveedor', 'tipoCompra', 'user'])
            ->select('id', 'proveedor_id', 'user_id', 'tipo_compra_id', 'total', 'created_at', 'updated_at')
            ->get()
            ->map(function($compra) {
                return [
                    'ID' => $compra->id,
                    'Proveedor' => $compra->proveedor?->nombre ?? '—',
                    'Usuario' => $compra->user?->name ?? '—',
                    'Tipo de compra' => $compra->tipoCompra?->nombre ?? '—',
                    'Total' => $compra->total,
                    'Creado' => $compra->created_at->format('d/m/Y H:i'),
                    'Actualizado' => $compra->updated_at->format('d/m/Y H:i'),
                ];
            });
    }

    /**
     * Encabezados de la tabla Excel
     */
    public function headings(): array
    {
        return [
            'ID',
            'Proveedor',
            'Usuario',
            'Tipo de compra',
            'Total',
            'Creado',
            'Actualizado',
        ];
    }
}
