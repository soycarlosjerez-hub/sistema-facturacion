<?php

namespace App\Exports;

use App\Models\Categoria;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CategoriaExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        $query = Categoria::orderBy('nombre');

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
            'Descripción',
            'Activa',
            'Productos Asociados',
            'Fecha de Creación',
        ];
    }

    public function map($categoria): array
    {
        return [
            $categoria->id,
            $categoria->nombre,
            $categoria->descripcion ?? '',
            $categoria->activa ? 'Sí' : 'No',
            $categoria->productos()->count(),
            $categoria->created_at->format('d/m/Y'),
        ];
    }
}
