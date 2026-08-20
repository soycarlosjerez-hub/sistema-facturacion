<?php

namespace App\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Database\Eloquent\Builder;

class RedConfigExport implements FromQuery, WithHeadings, WithMapping, WithTitle
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
        return ['ID', 'Cliente', 'Nombre Red', 'Dirección Red', 'VLAN', 'SSID WiFi', 'Cobertura', 'DHCP', 'DHCP Rango', 'Activo', 'Notas'];
    }

    public function map($red): array
    {
        return [
            $red->id,
            $red->cliente?->nombre ?? '-',
            $red->nombre_red,
            $red->direccion_red ?? '-',
            $red->vlan_label ?? '-',
            $red->ssid_wifi ?? '-',
            $red->cobertura ?? '-',
            $red->estado_dhcp_label,
            is_array($red->dhcp_rango) ? json_encode($red->dhcp_rango) : ($red->dhcp_rango ?? '-'),
            $red->activo ? 'Sí' : 'No',
            $red->notas ?? '',
        ];
    }

    public function title(): string
    {
        return 'Configuraciones de Red';
    }
}
