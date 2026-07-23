<?php

namespace App\Exports;

use App\Models\Mantenimiento;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClimatizacionMantenimientosExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    public function __construct(private ?\Illuminate\Database\Eloquent\Builder $query = null)
    {
    }

    public function query()
    {
        return $this->query ?: Mantenimiento::query()->with(['cliente', 'tecnico'])->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return [
            'Nº Orden',
            'Cliente',
            'Tipo',
            'Técnico',
            'Descripción Falla',
            'Solución',
            'Repuestos Usados',
            'Costo Repuestos',
            'Mano de Obra',
            'Total',
            'Estado',
            'Fecha Programada',
            'Fecha Completada',
        ];
    }

    public function map($mtto): array
    {
        $repuestos = '';
        if ($mtto->repuestos_usados) {
            $repuestos = is_array($mtto->repuestos_usados)
                ? implode(', ', $mtto->repuestos_usados)
                : $mtto->repuestos_usados;
        }

        return [
            $mtto->numero,
            $mtto->cliente ? $mtto->cliente->nombre : '',
            $mtto->tipo ?? '',
            $mtto->tecnico ? $mtto->tecnico->name : '',
            $mtto->descripcion_falla ?? '',
            $mtto->solucion_aplicada ?? '',
            $repuestos,
            number_format($mtto->costo_repuestos ?? 0, 2, '.', ''),
            number_format($mtto->mano_de_obra ?? 0, 2, '.', ''),
            number_format($mtto->total ?? 0, 2, '.', ''),
            $mtto->estado ?? '',
            $mtto->programada_para ? $mtto->programada_para->format('Y-m-d H:i') : '',
            $mtto->completada_en ? $mtto->completada_en->format('Y-m-d H:i') : '',
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
