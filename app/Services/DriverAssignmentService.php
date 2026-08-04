<?php

namespace App\Services;

use App\Models\DeliveryDriver;
use App\Models\DeliveryTracking;
use App\Models\Orden;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DriverAssignmentService
{
    public function asignarDriver($ordenId, $driverId = null)
    {
        $orden = Orden::with('detalles')->findOrFail($ordenId);

        if ($orden->entrega_empresa_id) {
            return ['error' => 'Esta orden ya tiene un driver asignado', 'code' => 422];
        }

        $driver = null;

        if ($driverId) {
            $driver = DeliveryDriver::where('id', $driverId)
                ->where('activo', true)
                ->first();
        }

        if (!$driver) {
            $driver = $this->obtenerDriverMasDisponible($orden->sucursal_id);
        }

        if (!$driver) {
            return ['error' => 'No hay drivers disponibles', 'code' => 422];
        }

        DB::beginTransaction();
        try {
            $orden->update([
                'entrega_empresa_id' => $driver->id,
                'tracking_status' => 'creado',
            ]);

            DeliveryTracking::create([
                'tenant_id' => Auth::user()->business_instance_id,
                'orden_id' => $orden->id,
                'driver_id' => $driver->id,
                'status' => 'creado',
                'notas' => 'Asignado a: ' . $driver->nombreCompleto,
                'creado_por' => Auth::id(),
            ]);

            DB::commit();

            $orden->load('detalles.producto', 'cliente', 'entregaEmpresa');

            return [
                'success' => true,
                'orden' => $orden,
                'driver' => [
                    'id' => $driver->id,
                    'nombre' => $driver->nombreCompleto,
                    'telefono' => $driver->telefono,
                ],
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'code' => 500];
        }
    }

    public function liberarDriver($ordenId)
    {
        $orden = Orden::findOrFail($ordenId);

        if (!$orden->entrega_empresa_id) {
            return ['error' => 'Esta orden no tiene driver asignado', 'code' => 422];
        }

        DB::beginTransaction();
        try {
            $ultimoTracking = DeliveryTracking::where('orden_id', $orden->id)
                ->latest()
                ->first();

            if ($ultimoTracking) {
                $ultimoTracking->update(['status' => 'cancelado']);
            }

            $orden->update([
                'entrega_empresa_id' => null,
                'tracking_status' => null,
            ]);

            DB::commit();

            return ['success' => true];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'code' => 500];
        }
    }

    public function obtenerDriversDisponibles($sucursalId = null)
    {
        $query = DeliveryDriver::where('activo', true)
            ->withCount([
                'ordenes as ordenes_activas' => function ($q) {
                    $q->whereNotNull('entrega_empresa_id')
                      ->whereIn('estado', ['pendiente', 'preparando', 'en_camino']);
                }
            ]);

        if ($sucursalId) {
            $query->whereHas('ordenes', function ($q) use ($sucursalId) {
                $q->where('sucursal_id', $sucursalId);
            });
        }

        return $query->orderBy('ordenes_activas', 'asc')
            ->orderBy('nombre')
            ->get();
    }

    public function contarOrdenesActivasDriver($driverId)
    {
        return Orden::where('entrega_empresa_id', $driverId)
            ->whereIn('estado', ['pendiente', 'preparando', 'en_camino'])
            ->count();
    }

    private function obtenerDriverMasDisponible($sucursalId = null)
    {
        $drivers = $this->obtenerDriversDisponibles($sucursalId);

        if ($drivers->isEmpty()) {
            return null;
        }

        return $drivers->first();
    }
}
