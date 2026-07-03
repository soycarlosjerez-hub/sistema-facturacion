<?php

namespace App\Exports;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductosExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    public function __construct(private ?Builder $query = null)
    {
    }

    public function query()
    {
        return $this->query ?: Producto::query()->orderBy('nombre');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Código de Barras',
            'Descripción',
            'Unidad',
            'Precio Venta',
            'Precio Compra',
            'ITBIS %',
            'Stock',
            'Ganancia',
            'Margen %',
            'Estado Stock',
            'Estado',
        ];
    }

    public function map($producto): array
    {
        $ganancia = (float) $producto->precio - (float) ($producto->precio_compra ?? 0);
        $compra   = (float) ($producto->precio_compra ?? 0);
        $margen   = $compra > 0 ? round((($producto->precio - $compra) / $compra) * 100, 2) : 0;
        $estado   = match ($producto->estado_stock) {
            'critical' => 'Crítico',
            'low'      => 'Bajo',
            default    => 'Normal',
        };

        return [
            $producto->id,
            $producto->nombre,
            $producto->codigo_barras ?? '',
            $producto->descripcion ?? '',
            $producto->unidad_medida ?? 'Unidad',
            number_format($producto->precio, 2, '.', ''),
            number_format($producto->precio_compra ?? 0, 2, '.', ''),
            number_format($producto->itbis_porcentaje ?? 18, 2, '.', ''),
            $producto->stock,
            number_format($ganancia, 2, '.', ''),
            number_format($margen, 2, '.', ''),
            $estado,
            $producto->activo_label,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E293B']]],
        ];
    }
}
