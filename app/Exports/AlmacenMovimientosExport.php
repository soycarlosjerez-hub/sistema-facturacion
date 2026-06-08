<?php
namespace App\Exports;

use App\Models\AlmacenMovimiento;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AlmacenMovimientosExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = AlmacenMovimiento::with(['producto','user','almacen']);

        if(!empty($this->filters['almacen'])) {
            $query->where('almacen_id', $this->filters['almacen']);
        }

        if(!empty($this->filters['producto'])) {
            $query->whereHas('producto', fn($q)=> $q->where('nombre','like','%'.$this->filters['producto'].'%'));
        }

        return $query->get()->map(function($mov){
            return [
                'ID' => $mov->id,
                'Producto' => $mov->producto?->nombre ?? '—',
                'Almacén' => $mov->almacen->nombre ?? '—',
                'Tipo' => ucfirst($mov->tipo),
                'Cantidad' => $mov->cantidad,
                'Usuario' => $mov->user?->name ?? '—',
                'Nota' => $mov->nota ?? '—',
                'Fecha' => $mov->created_at->format('d/m/Y H:i'),
            ];
        });
    }

    public function headings(): array
    {
        return ['ID','Producto','Almacén','Tipo','Cantidad','Usuario','Nota','Fecha'];
    }
}
