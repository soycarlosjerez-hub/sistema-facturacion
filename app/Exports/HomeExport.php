<?php

namespace App\Exports;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\Compra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HomeExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return collect([
            ['Total Productos', Producto::count()],
            ['Ventas Hoy', Venta::whereDate('created_at', now())->sum('total')],
            ['Ingresos Mes', Venta::whereMonth('created_at', now()->month)->sum('total')],
            ['Utilidad Mes', Venta::whereMonth('created_at', now()->month)->sum('total')],
            ['Proyección Mensual', Venta::sum('total')],
            ['Clientes', \App\Models\Cliente::count()],
        ]);
    }

    public function headings(): array
    {
        return ['Concepto', 'Valor'];
    }
}
