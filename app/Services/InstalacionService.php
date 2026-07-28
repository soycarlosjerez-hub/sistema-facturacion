<?php

namespace App\Services;

use App\Models\Instalacion;
use Illuminate\Support\Facades\DB;

class InstalacionService
{
    protected const ALLOWED_TRANSITIONS = [
        'pendiente' => 'programada',
        'programada' => 'en_progreso',
        'en_progreso' => 'completada',
    ];

    public function crear(array $data, ?int $userId): Instalacion
    {
        $data['estado'] = 'pendiente';
        $data['created_by'] = $userId;

        return DB::transaction(function () use ($data) {
            $inst = Instalacion::create($data);

            if (!empty($data['productos'])) {
                foreach ($data['productos'] as $prod) {
                    $inst->productos()->attach($prod['producto_id'], [
                        'cantidad' => $prod['cantidad'] ?? 1,
                        'precio_unitario' => $prod['precio_unitario'] ?? 0,
                    ]);
                }
            }

            $inst->refresh();
            $inst->calcularTotal();
            return $inst;
        });
    }

    public function actualizar(int $id, array $data): Instalacion
    {
        return DB::transaction(function () use ($id, $data) {
            $inst = Instalacion::findOrFail($id);
            $inst->update($data);

            if (!empty($data['productos'])) {
                $inst->productos()->detach();
                foreach ($data['productos'] as $prod) {
                    $inst->productos()->attach($prod['producto_id'], [
                        'cantidad' => $prod['cantidad'] ?? 1,
                        'precio_unitario' => $prod['precio_unitario'] ?? 0,
                    ]);
                }
            }

            $inst->calcularTotal();
            return $inst->fresh();
        });
    }

    public function avanzarEstado(int $id, string $nuevoEstado): Instalacion
    {
        $inst = Instalacion::findOrFail($id);

        if (!isset(self::ALLOWED_TRANSITIONS[$inst->estado]) || self::ALLOWED_TRANSITIONS[$inst->estado] !== $nuevoEstado) {
            throw new \InvalidArgumentException('Transición de estado no válida de "' . $inst->estado . '" a "' . $nuevoEstado . '".');
        }

        $updateData = ['estado' => $nuevoEstado];
        if ($nuevoEstado === 'completada') {
            $updateData['completada_en'] = now();
        }

        return DB::transaction(fn () => $inst->update($updateData)) ? $inst->fresh() : $inst;
    }

    public function cancelar(int $id): Instalacion
    {
        return DB::transaction(function () use ($id) {
            $inst = Instalacion::findOrFail($id);
            if (in_array($inst->estado, ['completada', 'cancelada'])) {
                throw new \InvalidArgumentException('No se puede cancelar una instalación completada o cancelada.');
            }
            $inst->update(['estado' => 'cancelada']);
            return $inst->fresh();
        });
    }

    public function eliminar(int $id): bool
    {
        $inst = Instalacion::findOrFail($id);

        if (in_array($inst->estado, ['completada', 'cancelada'])) {
            throw new \InvalidArgumentException('No se puede eliminar una instalación completada o cancelada.');
        }

        return DB::transaction(fn () => (bool) $inst->delete());
    }
}
