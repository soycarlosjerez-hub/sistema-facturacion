<?php

namespace App\Exports;

use App\Models\OrdenEmergencia;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClimatizacionOrdenesExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    public function __construct(private ?\Illuminate\Database\Eloquent\Builder $query = null)
    {
    }

    public function query()
    {
        return $this->query ?: OrdenEmergencia::query()->with(['cliente', 'tecnico'])->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return [
            'Código',
            'Cliente',
            'Prioridad',
            'Tipo Falla',
            'Dirección',
            'Teléfono Contacto',
            'Estado',
            'Técnico Asignado',
            'Costo Estimado',
            'Costo Final',
            'SLA Deadline',
            'Respondida En',
            'Resuelta En',
        ];
    }

    public function map($orden): array
    {
        return [
            $orden->codigo,
            $orden->cliente ? $orden->cliente->nombre : '',
            $orden->prioridad ?? '',
            $orden->tipo_falla ?? '',
            $orden->direccion ?? '',
            $orden->contacto_telefono ?? '',
            $orden->estado ?? '',
            $orden->tecnico ? $orden->tecnico->name : '',
            number_format($orden->costo_estimado ?? 0, 2, '.', ''),
            number_format($orden->costo_final ?? 0, 2, '.', ''),
            $orden->sla_deadline ? $orden->sla_deadline->format('Y-m-d H:i') : '',
            $orden->respondida_en ? $orden->respondida_en->format('Y-m-d H:i') : '',
            $orden->resuelta_en ? $orden->resuelta_en->format('Y-m-d H:i') : '',
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
