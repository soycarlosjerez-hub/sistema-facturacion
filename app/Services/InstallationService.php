<?php

namespace App\Services;

use App\Models\ServicioDomotica;
use App\Models\InstalacionEquipoDomotico;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InstallationService
{
    public function __construct() {}

    public function crearServicioDomotica(array $data, int $userId): ServicioDomotica
    {
        $data['user_id'] = $userId;
        $data['estado'] = 'pendiente';

        $year = date('Y');
        $ultimo = ServicioDomotica::where('numero_proyecto', 'like', "SD-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        $num = $ultimo ? ((int) substr($ultimo->numero_proyecto, -6) + 1) : 1;
        $data['numero_proyecto'] = sprintf('SD-%s-%06d', $year, $num);

        return DB::transaction(function () use ($data) {
            $servicio = ServicioDomotica::create($data);
            $servicio->calcularTotales();
            return $servicio;
        });
    }

    public function agregarEquipo(int $servicioId, int $productoId, int $cantidad, string $ubicacion): InstalacionEquipoDomotico
    {
        return DB::transaction(function () use ($servicioId, $productoId, $cantidad, $ubicacion) {
            $instalacion = InstalacionEquipoDomotico::create([
                'servicio_domotica_id' => $servicioId,
                'producto_id' => $productoId,
                'cantidad' => $cantidad,
                'precio_unitario' => Producto::findOrFail($productoId)->precio,
                'ubicacion_instalacion' => $ubicacion,
                'estado' => 'pendiente',
            ]);

            $servicio = ServicioDomotica::findOrFail($servicioId);
            $servicio->calcularTotales();

            return $instalacion;
        });
    }

    public function eliminarEquipo(int $instalacionId): void
    {
        DB::transaction(function () use ($instalacionId) {
            $instalacion = InstalacionEquipoDomotico::findOrFail($instalacionId);
            $servicioId = $instalacion->servicio_domotica_id;
            $instalacion->delete();

            $servicio = ServicioDomotica::findOrFail($servicioId);
            $servicio->calcularTotales();
        });
    }

    public function cambiarEstado(int $servicioId, string $nuevoEstado): ServicioDomotica
    {
        $servicio = ServicioDomotica::findOrFail($servicioId);
        $servicio->update(['estado' => $nuevoEstado]);
        return $servicio->fresh();
    }

    public function completarServicio(int $servicioId): ServicioDomotica
    {
        return DB::transaction(function () use ($servicioId) {
            $servicio = ServicioDomotica::findOrFail($servicioId);
            $servicio->update([
                'estado' => 'completado',
                'fecha_completada' => now(),
            ]);

            InstalacionEquipoDomotico::where('servicio_domotica_id', $servicioId)
                ->update(['estado' => 'instaldo']);

            return $servicio->fresh();
        });
    }

    public function getServicioCompleto(int $servicioId): ServicioDomotica
    {
        return ServicioDomotica::with([
            'cliente',
            'tecnico',
            'instalaciones.producto',
        ])->findOrFail($servicioId);
    }

    public function getEstadisticas(array $filters = []): array
    {
        $query = ServicioDomotica::query();

        if (isset($filters['desde'])) {
            $query->whereDate('created_at', '>=', $filters['desde']);
        }
        if (isset($filters['hasta'])) {
            $query->whereDate('created_at', '<=', $filters['hasta']);
        }

        return [
            'total' => (clone $query)->count(),
            'pendientes' => (clone $query)->whereIn('estado', ['pendiente', 'programado'])->count(),
            'en_curso' => (clone $query)->where('estado', 'en_curso')->count(),
            'completados' => (clone $query)->where('estado', 'completado')->count(),
            'cancelados' => (clone $query)->where('estado', 'cancelado')->count(),
            'ingresos_periodo' => (clone $query)->sum('total'),
            'tasa_completado' => 0,
        ];
    }
}
