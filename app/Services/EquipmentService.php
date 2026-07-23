<?php

namespace App\Services;

use App\Models\Equipo;
use App\Models\OrdenReparacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EquipmentService
{
    public function __construct() {}

    public function registrarEquipo(array $data): Equipo
    {
        return Equipo::create($data);
    }

    public function actualizarEquipo(int $equipoId, array $data): Equipo
    {
        $equipo = Equipo::findOrFail($equipoId);
        $equipo->update($data);
        return $equipo->fresh();
    }

    public function eliminarEquipo(int $equipoId): void
    {
        $ordenesActivas = OrdenReparacion::where('equipo_id', $equipoId)
            ->whereNotIn('estado', ['entregado', 'cancelado'])
            ->count();

        if ($ordenesActivas > 0) {
            throw new \Exception('No se puede eliminar: el equipo tiene órdenes de reparación activas.');
        }

        Equipo::findOrFail($equipoId)->delete();
    }

    public function buscarPorImei(string $imei): ?Equipo
    {
        return Equipo::where('serial_imei', $imei)->first();
    }

    public function buscarPorModelo(string $marca, string $modelo): \Illuminate\Database\Eloquent\Collection
    {
        return Equipo::where('marca', 'like', "%{$marca}%")
            ->where('modelo', 'like', "%{$modelo}%")
            ->get();
    }

    public function cambiarEstado(int $equipoId, string $nuevoEstado): Equipo
    {
        $allowedTransitions = [
            'disponible' => ['vendido', 'reservado', 'en_reparacion', 'dañado', 'mantenimiento'],
            'vendido' => [],
            'en_reparacion' => ['disponible', 'dañado'],
            'dañado' => ['disponible'],
            'reservado' => ['disponible', 'vendido'],
            'mantenimiento' => ['disponible'],
        ];

        $equipo = Equipo::findOrFail($equipoId);

        if (!empty($allowedTransitions[$equipo->estado]) && !in_array($nuevoEstado, $allowedTransitions[$equipo->estado])) {
            throw new \Exception("No se puede cambiar de '{$equipo->estado}' a '{$nuevoEstado}'. Transición no permitida.");
        }

        $equipo->update(['estado' => $nuevoEstado]);
        return $equipo->fresh();
    }

    public function getEquiposDisponibles(): \Illuminate\Database\Eloquent\Collection
    {
        return Equipo::disponibles()
            ->with(['producto', 'proveedor'])
            ->get();
    }

    public function getEstadisticas(): array
    {
        return [
            'total' => Equipo::count(),
            'disponibles' => Equipo::disponibles()->count(),
            'en_reparacion' => Equipo::enReparacion()->count(),
            'vendidos' => Equipo::where('estado', 'vendido')->count(),
            'dañados' => Equipo::where('estado', 'dañado')->count(),
            'reservados' => Equipo::where('estado', 'reservado')->count(),
            'por_marca' => Equipo::whereNotNull('marca')
                ->groupBy('marca')
                ->selectRaw('marca, COUNT(*) as cuenta')
                ->pluck('cuenta', 'marca'),
            'precio_promedio' => Equipo::avg('precio_venta') ?? 0,
        ];
    }

    public function importarDesdeCSV($filePath): int
    {
        $handle = fopen($filePath, 'r');
        $imported = 0;

        if ($handle) {
            fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 3) {
                    Equipo::create([
                        'serial_imei' => trim($row[0]),
                        'marca' => trim($row[1]),
                        'modelo' => trim($row[2]),
                        'almacenamiento_gb' => isset($row[3]) ? (int)$row[3] : null,
                        'color' => isset($row[4]) ? trim($row[4]) : null,
                        'precio_venta' => isset($row[5]) ? (float)$row[5] : 0,
                        'estado' => 'disponible',
                    ]);
                    $imported++;
                }
            }

            fclose($handle);
        }

        return $imported;
    }
}
