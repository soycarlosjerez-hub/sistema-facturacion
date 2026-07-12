<?php

namespace App\Exports;

use App\Models\Proveedor;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProveedoresExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        $query = Proveedor::orderBy('nombre');

        if (Auth::check() && Auth::user()->business_instance_id !== null) {
            $query->where('tenant_id', Auth::user()->business_instance_id);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Teléfono',
            'Email',
            'RNC',
            'Tipo Persona',
            'Dirección',
            'Retención ISR',
            'Retención ITBIS',
            'Activo',
            'Fecha de Registro',
        ];
    }

    public function map($proveedor): array
    {
        return [
            $proveedor->id,
            $proveedor->nombre,
            $proveedor->telefono ?? '',
            $proveedor->email ?? '',
            $proveedor->rnc ?? '',
            $proveedor->tipo_persona === 'juridica' ? 'Jurídica' : 'Física',
            $proveedor->direccion ?? '',
            $proveedor->sujeto_retencion_isr ? 'Sí' : 'No',
            $proveedor->sujeto_retencion_itbis ? 'Sí' : 'No',
            $proveedor->activo ? 'Sí' : 'No',
            $proveedor->created_at->format('d/m/Y'),
        ];
    }
}
