<?php

namespace App\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Database\Eloquent\Builder;

class MarcaTecnologicaExport implements FromQuery, WithHeadings, WithMapping, WithTitle
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
        return ['ID', 'Nombre', 'Logo', 'Website', 'País', 'Contacto Email', 'Activo', 'Orden'];
    }

    public function map($marca): array
    {
        return [
            $marca->id,
            $marca->nombre,
            $marca->logo_url ?? '',
            $marca->website ?? '',
            $marca->pais ?? '',
            $marca->contacto_email ?? '',
            $marca->activo ? 'Sí' : 'No',
            (int) $marca->orden,
        ];
    }

    public function title(): string
    {
        return 'Marcas Tecnológicas';
    }
}
