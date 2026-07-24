<?php

namespace App\Exports;

use App\Models\Venta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class VentasExport implements FromCollection, WithHeadings, WithMapping
{
    protected $cliente;
    protected $desde;
    protected $hasta;
    protected $tenantId;

    public function __construct($cliente = null, $desde = null, $hasta = null, $tenantId = null)
    {
        $this->cliente = $cliente;
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->tenantId = $tenantId;
    }

    public function collection()
    {
        $query = Venta::with(['usuario', 'cliente', 'tipoVenta'])
            ->select('id', 'user_id', 'cliente_id', 'tipo_venta_id', 'fecha', 'subtotal', 'impuestos', 'descuento', 'total', 'estado', 'created_at', 'updated_at', 'tenant_id');

        if ($this->tenantId) {
            $query->where('tenant_id', $this->tenantId);
        }
        if ($this->cliente) {
            $query->whereHas('cliente', fn($q) => $q->where('nombre', 'like', '%' . $this->cliente . '%'));
        }
        if ($this->desde) {
            $query->whereDate('created_at', '>=', $this->desde);
        }
        if ($this->hasta) {
            $query->whereDate('created_at', '<=', $this->hasta);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function map($venta): array
    {
        return [
            $venta->id,
            $venta->usuario->name ?? '—',
            $venta->cliente->nombre ?? '—',
            $venta->tipoVenta?->nombre ?? '—',
            $venta->fecha ? Carbon::parse($venta->fecha)->format('d/m/Y') : Carbon::parse($venta->created_at)->format('d/m/Y'),
            $venta->subtotal,
            $venta->impuestos,
            $venta->descuento,
            $venta->total,
            ucfirst($venta->estado),
            $venta->created_at->format('d/m/Y H:i'),
            $venta->updated_at->format('d/m/Y H:i'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Usuario',
            'Cliente',
            'Tipo de venta',
            'Fecha',
            'Subtotal',
            'Impuestos',
            'Descuento',
            'Total',
            'Estado',
            'Creado',
            'Actualizado',
        ];
    }
}
