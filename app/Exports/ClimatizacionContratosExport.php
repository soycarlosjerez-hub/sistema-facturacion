<?php

namespace App\Exports;

use App\Models\ContratoMantenimiento;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClimatizacionContratosExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    public function __construct(private ?\Illuminate\Database\Eloquent\Builder $query = null)
    {
    }

    public function query()
    {
        return $this->query ?: ContratoMantenimiento::query()->with('cliente')->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return [
            'Código',
            'Cliente',
            'Periodicidad',
            'Vigencia Desde',
            'Vigencia Hasta',
            'Valor Mensual',
            'Equipos Cubiertos',
            'Visitas Anuales',
            'Visitas Realizadas',
            'Estado',
            'Deducible',
            'Cobertura Máxima',
        ];
    }

    public function map($contrato): array
    {
        $equipos = '';
        if ($contrato->equipos_cubiertos) {
            $equipos = is_array($contrato->equipos_cubiertos)
                ? json_encode($contrato->equipos_cubiertos, JSON_UNESCAPED_UNICODE)
                : $contrato->equipos_cubiertos;
        }

        return [
            $contrato->codigo,
            $contrato->cliente ? $contrato->cliente->nombre : '',
            $contrato->tipo_periodicidad ?? '',
            $contrato->vigencia_desde ? $contrato->vigencia_desde->format('Y-m-d') : '',
            $contrato->vigencia_hasta ? $contrato->vigencia_hasta->format('Y-m-d') : '',
            number_format($contrato->valor_mensual ?? 0, 2, '.', ''),
            $equipos,
            $contrato->num_visitas_anuales ?? 0,
            $contrato->visitas_realizadas ?? 0,
            $contrato->estado ?? '',
            number_format($contrato->deducible ?? 0, 2, '.', ''),
            number_format($contrato->cobertura_maxima ?? 0, 2, '.', ''),
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
