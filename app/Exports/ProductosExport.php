<?php

namespace App\Exports;

use App\Models\Producto;
use App\Models\SystemSetting;
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

    public function __construct(private ?Builder $query = null) {}

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
            'Código Referencia',
            'Descripción',
            'Categoría',
            'Unidad',
            'Tipo Servicio',
            'Tipo Producto',
            'Precio Venta',
            'Precio Compra',
            'ITBIS %',
            'Stock',
            'Stock Mínimo',
            'Ganancia',
            'Margen %',
            'Estado Stock',
            'Estado',
            'Marca',
            'Modelo',
            'Especialización',
            'IMEI/Serial',
            'Garantía (días)',
            'Color',
            'Almacenamiento (GB)',
            'Tipo Licencia',
            'Max Usuarios',
            'Línea Negocio',
        ];
    }

    public function map($producto): array
    {
        $ganancia = (float) $producto->precio - (float) ($producto->precio_compra ?? 0);
        $compra = (float) ($producto->precio_compra ?? 0);
        $margen = $compra > 0 ? round((($producto->precio - $compra) / $compra) * 100, 2) : 0;
        $estado = match ($producto->estado_stock) {
            'critical' => 'Crítico',
            'low' => 'Bajo',
            default => 'Normal',
        };

        return [
            $producto->id,
            $producto->nombre,
            $producto->codigo_barras ?? '',
            $producto->codigo_referencia ?? '',
            $producto->descripcion ?? '',
            $producto->categoria?->nombre ?? '',
            $producto->unidad_medida ?? 'Unidad',
            $producto->tipo_servicio ?? 'producto',
            $producto->tipo_producto ?? '',
            number_format($producto->precio, 2, '.', ''),
            number_format($producto->precio_compra ?? 0, 2, '.', ''),
            number_format($producto->itbis_porcentaje ?? SystemSetting::itbisDefault(), 2, '.', ''),
            $producto->stock,
            $producto->stock_minimo ?? 0,
            number_format($ganancia, 2, '.', ''),
            number_format($margen, 2, '.', ''),
            $estado,
            $producto->activo_label,
            $producto->marca ?? '',
            $producto->modelo ?? '',
            $producto->especializacion ?? '',
            $producto->serial_imei ?? '',
            $producto->garantia_dias ?? 0,
            $producto->color ?? '',
            $producto->almacenamiento_gb ?? '',
            $producto->tipo_licencia ?? '',
            $producto->licencia_max_usuarios ?? '',
            $producto->linea_negocio ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E293B']]],
        ];
    }
}
