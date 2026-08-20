<?php

namespace App\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Database\Eloquent\Builder;

class LicenciaSoftwareExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    protected $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return ['ID', 'Producto', 'Clave Licencia', 'Tipo', 'Usuario Asignado', 'Plataforma', 'Activa', 'Estado', 'Fecha Vencimiento', 'Días Hasta Vencer', 'Notas'];
    }

    public function map($licencia): array
    {
        return [
            $licencia->id,
            $licencia->producto?->nombre ?? '-',
            $licencia->clave_licencia ?? '---',
            $licencia->tipo_licencia ?? 'Singular',
            $licencia->usuario_asignado ?? '-',
            $licencia->plataforma ?? '-',
            $licencia->licencia_activa ? 'Sí' : 'No',
            $licencia->estado,
            $licencia->fecha_vencimiento?->format('d/m/Y') ?? '',
            (int) $licencia->dias_hasta_vencer,
            $licencia->notas ?? '',
        ];
    }

    public function title(): string
    {
        return 'Licencias de Software';
    }
}
