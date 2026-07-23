<?php

namespace App\Exports;

use App\Models\Instalacion;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClimatizacionInstalacionesExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    public function __construct(private ?\Illuminate\Database\Eloquent\Builder $query = null)
    {
    }

    public function query()
    {
        return $this->query ?: Instalacion::query()->with(['cliente', 'instalador'])->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return [
            'Nº Orden',
            'Cliente',
            'Teléfono',
            'Dirección',
            'Tipo Inmueble',
            'Instalador',
            'Fecha Programada',
            'Fecha Completada',
            'Estado',
            'Total',
            'Notas',
        ];
    }

    public function map($inst): array
    {
        return [
            $inst->numero,
            $inst->cliente ? $inst->cliente->nombre : '',
            $inst->cliente ? ($inst->cliente->telefono ?? '') : '',
            $inst->direccion_instalacion ?? '',
            $inst->tipo_inmueble ?? '',
            $inst->instalador ? $inst->instalador->name : '',
            $inst->programada_para ? $inst->programada_para->format('Y-m-d H:i') : '',
            $inst->completada_en ? $inst->completada_en->format('Y-m-d H:i') : '',
            $inst->estado ?? '',
            number_format($inst->total ?? 0, 2, '.', ''),
            $inst->nota_interna ?? '',
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
