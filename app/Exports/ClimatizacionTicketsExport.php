<?php

namespace App\Exports;

use App\Models\TicketGarantia;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClimatizacionTicketsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    public function __construct(private ?\Illuminate\Database\Eloquent\Builder $query = null)
    {
    }

    public function query()
    {
        return $this->query ?: TicketGarantia::query()->with(['producto', 'cliente'])->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return [
            'Código',
            'Cliente',
            'Producto',
            'Tipo Garantía',
            'Fecha Compra',
            'Fecha Vencimiento',
            'Estado',
            'Descripción Problema',
            'Resultado Evaluación',
            'Técnico Asignado',
        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->codigo,
            $ticket->cliente ? $ticket->cliente->nombre : '',
            $ticket->producto ? $ticket->producto->nombre : '',
            $ticket->tipo_garantia ?? '',
            $ticket->fecha_compra ? $ticket->fecha_compra->format('Y-m-d') : '',
            $ticket->fecha_vencimiento_garantia ? $ticket->fecha_vencimiento_garantia->format('Y-m-d') : '',
            $ticket->estado ?? '',
            $ticket->descripcion_problema ?? '',
            $ticket->resultado_evaluacion ?? '',
            $ticket->tecnicoAsignado ? $ticket->tecnicoAsignado->name : '',
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
