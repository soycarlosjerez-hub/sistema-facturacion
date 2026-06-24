<?php

namespace App\Services;

use App\Models\Cliente;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ClienteService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $nombre = $filters['nombre'] ?? null;

        return Cliente::when($nombre, fn($q) => $q->where(function ($sub) use ($nombre) {
            $sub->where('nombre', 'like', "%{$nombre}%")
                ->orWhere('email', 'like', "%{$nombre}%")
                ->orWhere('rnc_cedula', 'like', "%{$nombre}%")
                ->orWhere('telefono', 'like', "%{$nombre}%");
        }))->latest()->paginate(10);
    }

    public function create(array $data): Cliente
    {
        return Cliente::create($data);
    }

    public function update(Cliente $cliente, array $data): Cliente
    {
        $cliente->update($data);
        return $cliente;
    }

    public function delete(Cliente $cliente): void
    {
        $cliente->delete();
    }

    public function cuentasPendientes(?string $buscar = null): LengthAwarePaginator
    {
        return Cliente::where('balance_pendiente', '>', 0)
            ->where('nombre', '!=', 'Consumidor Final')
            ->when($buscar, fn($q) => $q->where(function ($sub) use ($buscar) {
                $sub->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('rnc_cedula', 'like', "%{$buscar}%");
            }))
            ->with(['ventas' => fn($q) => $q->whereIn('estado', ['pendiente', 'cuenta_abierta'])->latest()])
            ->latest()
            ->paginate(10);
    }

    public function pdf(array $filters = [])
    {
        $query = Cliente::query();
        if ($busqueda = $filters['busqueda'] ?? null) {
            $query->where('nombre', 'like', "%{$busqueda}%")
                ->orWhere('email', 'like', "%{$busqueda}%");
        }
        $clientes = $query->latest()->get();
        $pdf = Pdf::loadView('clientes.pdf', compact('clientes'));
        return $pdf->stream('clientes.pdf');
    }
}
