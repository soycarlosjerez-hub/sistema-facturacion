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
        $activo = $filters['activo'] ?? null;
        $segmento = $filters['segmento'] ?? null;
        $tipo_cliente = $filters['tipo_cliente'] ?? null;

        return Cliente::when($nombre, fn($q) => $q->where(function ($sub) use ($nombre) {
            $sub->where('nombre', 'like', "%{$nombre}%")
                ->orWhere('email', 'like', "%{$nombre}%")
                ->orWhere('rnc_cedula', 'like', "%{$nombre}%")
                ->orWhere('telefono', 'like', "%{$nombre}%");
        }))
            ->when($activo !== null && $activo !== '', fn($q) => $q->where('activo', $activo))
            ->when($segmento, fn($q) => $q->where('segmento', $segmento))
            ->when($tipo_cliente, fn($q) => $q->where('tipo_cliente', $tipo_cliente))
            ->latest()->paginate(10);
    }

    public function create(array $data): Cliente
    {
        $data['tenant_id'] = auth()->user()->business_instance_id;
        return Cliente::create($data);
    }

    public function update(Cliente $cliente, array $data): Cliente
    {
        $cliente->update($data);
        return $cliente;
    }

    public function delete(Cliente $cliente): void
    {
        $blockingRecords = [];

        $checks = [
            ['table' => 'Conduces', 'relation' => 'conduces'],
            ['table' => 'Órdenes de Reparación', 'relation' => 'ordenesReparacion'],
            ['table' => 'Servicios Domótica', 'relation' => 'serviciosDomotica'],
            ['table' => 'Vehículos', 'relation' => 'vehiculos'],
            ['table' => 'Citas Lavadero', 'relation' => 'lavaderoCitas'],
            ['table' => 'Lavaderos', 'relation' => 'lavaderos'],
            ['table' => 'Alquileres', 'relation' => 'alquileres'],
            ['table' => 'Citas Tatuaje', 'relation' => 'tattooAppointments'],
        ];

        foreach ($checks as $check) {
            $relation = $check['relation'];
            if (method_exists($cliente, $relation)) {
                $count = $cliente->{$relation}()->count();
                if ($count > 0) {
                    $blockingRecords[] = "{$check['table']} ({$count})";
                }
            }
        }

        if (!empty($blockingRecords)) {
            $lista = implode(', ', $blockingRecords);
            throw new \RuntimeException("No se puede eliminar el cliente. Tiene registros asociados a: {$lista}");
        }

        $cliente->delete();
    }

    public function toggleActivo(Cliente $cliente): Cliente
    {
        $cliente->update(['activo' => !$cliente->activo]);
        return $cliente->fresh();
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

    /**
     * Valida si un cliente puede realizar una venta por el monto indicado.
     * Retorna un array con status y mensajes.
     */
    public function validarCredito(Cliente $cliente, float $montoVenta): array
    {
        if ($cliente->limite_credito <= 0) {
            return ['autorizado' => true, 'mensaje' => 'Cliente sin límite de crédito configurado.'];
        }

        $nuevoSaldo = $cliente->balance_pendiente + $montoVenta;
        $disponible = $cliente->credito_disponible;

        if ($nuevoSaldo > $cliente->limite_credito) {
            $exceso = round($nuevoSaldo - $cliente->limite_credito, 2);
            $bloqueado = $cliente->auto_bloquear_credito;

            return [
                'autorizado' => !$bloqueado,
                'bloqueado'  => $bloqueado,
                'mensaje'    => "El cliente excede su límite de crédito por RD\$ {$exceso}. " .
                    ($bloqueado ? 'Venta bloqueada.' : 'Se requiere aprobación manual.'),
                'exceso'     => $exceso,
                'disponible' => $disponible,
                'utilizacion' => $cliente->utilizacion_credito,
            ];
        }

        return [
            'autorizado'  => true,
            'bloqueado'   => false,
            'mensaje'     => 'Crédito disponible suficiente.',
            'exceso'      => 0,
            'disponible'  => $disponible,
            'utilizacion' => $cliente->utilizacion_credito,
        ];
    }

    /**
     * Recalcula el balance pendiente de todos los clientes o de uno específico.
     */
    public function recalcularBalances(?Cliente $cliente = null): void
    {
        $query = $cliente ? Cliente::where('id', $cliente->id) : Cliente::query();
        $query->chunk(50, fn($clientes) => $clientes->each(fn($c) => $c->recalcularBalance()));
    }

    /**
     * Obtiene resumen de créditos para dashboard.
     */
    public function resumenCreditos(): array
    {
        $totalLimite = Cliente::sum('limite_credito');
        $totalBalance = Cliente::sum('balance_pendiente');
        $totalClientes = Cliente::count();
        $enExceso = Cliente::excedeCredito()->count();
        $conDeuda = Cliente::conDeuda()->count();
        $utilizacionPromedio = $totalLimite > 0
            ? round(($totalBalance / $totalLimite) * 100, 1)
            : 0;

        return compact(
            'totalLimite', 'totalBalance', 'totalClientes',
            'enExceso', 'conDeuda', 'utilizacionPromedio'
        );
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
