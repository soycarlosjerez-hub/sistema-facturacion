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

class ClimatizacionProductosExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    public function __construct(private ?Builder $query = null)
    {
    }

    public function query()
    {
        return $this->query ?: Producto::query()->whereNotNull('marca')->orWhereNotNull('tipo_equipo')->orderBy('nombre');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Código de Barras',
            'Categoría',
            'Marca',
            'Modelo',
            'Capacidad (Ton)',
            'BTU',
            'Tipo Equipo',
            'SEER',
            'Gas Refrigerante',
            'Voltaje',
            'Peso (kg)',
            'Dimensiones',
            'Categoría Climática',
            'Precio Venta',
            'Precio Compra',
            'ITBIS %',
            'Stock',
            'Stock Mínimo',
            'Ganancia',
            'Margen %',
            'Estado Stock',
            'Estado',
        ];
    }

    public function map($producto): array
    {
        $ganancia = (float) $producto->precio - (float) ($producto->precio_compra ?? 0);
        $compra = (float) ($producto->precio_compra ?? 0);
        $margen = $compra > 0 ? round((($producto->precio - $compra) / $compra) * 100, 2) : 0;
        $estado = match ($producto->estado_stock ?? 'ok') {
            'critical' => 'Crítico',
            'low' => 'Bajo',
            default => 'Normal',
        };
        $categoria = $producto->categoria ? $producto->categoria->nombre : '';

        return [
            $producto->id,
            $producto->nombre,
            $producto->codigo_barras ?? '',
            $categoria,
            $producto->marca ?? '',
            $producto->modelo ?? '',
            $producto->capacidad_toneladas ?? '',
            $producto->capacidad_btu ?? '',
            $producto->tipo_equipo ?? '',
            $producto->eficiencia_seer ?? '',
            $producto->gas_refrigerante ?? '',
            $producto->voltaje ?? '',
            $producto->peso_kg ?? '',
            $producto->dimensiones ?? '',
            $producto->categoria_clima ?? '',
            number_format($producto->precio, 2, '.', ''),
            number_format($producto->precio_compra ?? 0, 2, '.', ''),
            number_format($producto->itbis_porcentaje ?? 18, 2, '.', ''),
            $producto->stock,
            $producto->stock_minimo ?? 0,
            number_format($ganancia, 2, '.', ''),
            number_format($margen, 2, '.', ''),
            $estado,
            $producto->activo_label ?? 'Activo',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E293B']],
            ],
        ];
    }
}
