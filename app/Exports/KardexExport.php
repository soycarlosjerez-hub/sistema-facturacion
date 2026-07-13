<?php

namespace App\Exports;

use App\Models\AlmacenMovimiento;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KardexExport implements FromCollection, WithHeadings, WithMapping
{
    protected $productoId;
    protected $almacenId;
    protected $buscar;

    public function __construct($productoId = null, $almacenId = null, $buscar = null)
    {
        $this->productoId = $productoId;
        $this->almacenId = $almacenId;
        $this->buscar = $buscar;
    }

    public function collection()
    {
        $query = AlmacenMovimiento::with(['producto', 'almacen', 'user'])
            ->orderBy('created_at', 'desc');

        if (Auth::check() && Auth::user()->business_instance_id !== null) {
            $query->where('tenant_id', Auth::user()->business_instance_id);
        }

        if ($this->productoId) {
            $query->where('producto_id', $this->productoId);
        }

        if ($this->almacenId) {
            $query->where('almacen_id', $this->almacenId);
        }

        if ($this->buscar) {
            $query->where(function($q) {
                $q->whereHas('producto', fn($pq) => $pq->where('nombre', 'like', "%{$this->buscar}%"))
                  ->orWhere('nota', 'like', "%{$this->buscar}%");
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Fecha',
            'Hora',
            'Producto',
            'Almacén',
            'Tipo',
            'Cantidad',
            'Concepto / Nota',
            'Usuario',
        ];
    }

    public function map($movimiento): array
    {
        return [
            $movimiento->id,
            $movimiento->created_at->format('d/m/Y'),
            $movimiento->created_at->format('h:i A'),
            $movimiento->producto->nombre,
            $movimiento->almacen->nombre,
            ucfirst($movimiento->tipo),
            ($movimiento->tipo === 'entrada' ? '+' : '-') . $movimiento->cantidad,
            $movimiento->nota ?? 'Movimiento de inventario',
            $movimiento->user->name ?? 'Sistema',
        ];
    }
}
