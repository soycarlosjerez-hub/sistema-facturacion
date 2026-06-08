<?php

namespace App\Exports;

use App\Models\Venta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;


class VentasExport implements FromCollection, WithHeadings
{
    /**
     * Devuelve la colección de ventas para exportar
     */
    public function collection()
    {
        return Venta::with(['usuario', 'cliente', 'tipoVenta'])
            ->select('id', 'user_id', 'cliente_id', 'tipo_venta_id', 'fecha', 'subtotal', 'impuestos', 'descuento', 'total', 'estado', 'created_at', 'updated_at')
            ->get()
            ->map(function ($venta) {
                return [
                    'ID' => $venta->id,
                    'Usuario' => $venta->user->name ?? '—',
                    'Cliente' => $venta->cliente->nombre ?? '—',
                    'Tipo de venta' => $venta->tipoVenta?->nombre ?? '—',
                    'Fecha' => $venta->fecha ? Carbon::parse($venta->fecha)->format('d/m/Y') : Carbon::parse($venta->created_at)->format('d/m/Y'),
                    'Subtotal' => $venta->subtotal,
                    'Impuestos' => $venta->impuestos,
                    'Descuento' => $venta->descuento,
                    'Total' => $venta->total,
                    'Estado' => ucfirst($venta->estado),
                    'Creado' => $venta->created_at->format('d/m/Y H:i'),
                    'Actualizado' => $venta->updated_at->format('d/m/Y H:i'),
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
            'Usuario',
            'Cliente',
            'Tipo de venta',
            'Fecha',
            'Subtotal',
            'Impuestos',
            'Descuento',
            'Total',
            'Estado',
            'Creado',
            'Actualizado',
        ];
    }
}
