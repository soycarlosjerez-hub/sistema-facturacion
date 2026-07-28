<?php

namespace App\Services;

use App\Models\Mantenimiento;
use Illuminate\Support\Facades\DB;

class MantenimientoService
{
    protected const ALLOWED_TRANSITIONS = [
        'pendiente' => 'programada',
        'programada' => 'en_curso',
        'en_curso' => 'completado',
    ];

    public function crear(array $data, ?int $userId): Mantenimiento
    {
        $data['estado'] = 'pendiente';
        $data['created_by'] = $userId;
        $data['costo_repuestos'] = $data['costo_repuestos'] ?? 0;
        $data['mano_de_obra'] = $data['mano_de_obra'] ?? 0;

        return DB::transaction(function () use ($data) {
            $mtto = Mantenimiento::create($data);
            $mtto->calcularTotal();
            return $mtto;
        });
    }

    public function actualizar(int $id, array $data): Mantenimiento
    {
        $data['costo_repuestos'] = $data['costo_repuestos'] ?? 0;
        $data['mano_de_obra'] = $data['mano_de_obra'] ?? 0;

        return DB::transaction(function () use ($id, $data) {
            $mtto = Mantenimiento::findOrFail($id);
            $mtto->update($data);
            $mtto->calcularTotal();
            return $mtto;
        });
    }

    public function avanzarEstado(int $id, string $nuevoEstado): Mantenimiento
    {
        $mtto = Mantenimiento::findOrFail($id);

        if (!isset(self::ALLOWED_TRANSITIONS[$mtto->estado]) || self::ALLOWED_TRANSITIONS[$mtto->estado] !== $nuevoEstado) {
            throw new \InvalidArgumentException('Transición de estado no válida de "' . $mtto->estado . '" a "' . $nuevoEstado . '".');
        }

        $updateData = ['estado' => $nuevoEstado];
        if ($nuevoEstado === 'completado') {
            $updateData['completada_en'] = now();
        }

        return DB::transaction(fn () => $mtto->update($updateData)) ? $mtto->fresh() : $mtto;
    }

    public function cancelar(int $id): Mantenimiento
    {
        return DB::transaction(function () use ($id) {
            $mtto = Mantenimiento::findOrFail($id);
            if (in_array($mtto->estado, ['completado', 'cancelado'])) {
                throw new \InvalidArgumentException('No se puede cancelar un mantenimiento completado o cancelado.');
            }
            $mtto->update(['estado' => 'cancelado']);
            return $mtto->fresh();
        });
    }

    public function eliminar(int $id): bool
    {
        $mtto = Mantenimiento::findOrFail($id);

        if (in_array($mtto->estado, ['completado', 'cancelado'])) {
            throw new \InvalidArgumentException('No se puede eliminar un mantenimiento completado o cancelado.');
        }

        return DB::transaction(fn () => (bool) $mtto->delete());
    }
}
