<?php

namespace App\Services;

use App\Models\OrdenEmergencia;
use Illuminate\Support\Facades\DB;

class OrdenEmergenciaService
{
    protected const ALLOWED_TRANSITIONS = [
        'reportada' => 'asignada',
        'asignada' => 'en_camino',
        'en_camino' => 'en_lugar',
        'en_lugar' => 'resuelta',
        'resuelta' => 'cerrada',
    ];

    public function crear(array $data, ?int $userId): OrdenEmergencia
    {
        $data['estado'] = 'reportada';
        $data['created_by'] = $userId;
        $data['costo_estimado'] = $data['costo_estimado'] ?? 0;

        return DB::transaction(function () use ($data) {
            $orden = OrdenEmergencia::create($data);
            $orden->calcularSLA();
            return $orden;
        });
    }

    public function actualizar(int $id, array $data): OrdenEmergencia
    {
        $data['costo_estimado'] = $data['costo_estimado'] ?? 0;
        $data['costo_final'] = $data['costo_final'] ?? 0;

        return DB::transaction(function () use ($id, $data) {
            $orden = OrdenEmergencia::findOrFail($id);
            $orden->update($data);

            if ($data['estado'] === 'resuelta' && !$orden->resuelta_en) {
                $orden->update(['resuelta_en' => now()]);
            }

            return $orden->fresh();
        });
    }

    public function asignar(int $id, ?int $tecnicoId): OrdenEmergencia
    {
        $orden = OrdenEmergencia::findOrFail($id);

        if ($orden->estado !== 'reportada') {
            throw new \InvalidArgumentException('Solo se puede asignar una orden en estado "reportada".');
        }

        return DB::transaction(fn () => $orden->update(['estado' => 'asignada', 'tecnico_id' => $tecnicoId]))
            ? $orden->fresh()
            : $orden;
    }

    public function avanzarEstado(int $id, string $nuevoEstado): OrdenEmergencia
    {
        $orden = OrdenEmergencia::findOrFail($id);

        if (!isset(self::ALLOWED_TRANSITIONS[$orden->estado]) || self::ALLOWED_TRANSITIONS[$orden->estado] !== $nuevoEstado) {
            throw new \InvalidArgumentException('Transición de estado no válida de "' . $orden->estado . '" a "' . $nuevoEstado . '".');
        }

        return DB::transaction(fn () => $orden->update(['estado' => $nuevoEstado]))
            ? $orden->fresh()
            : $orden;
    }

    public function resolver(int $id, array $datosResolucion = []): OrdenEmergencia
    {
        $orden = OrdenEmergencia::findOrFail($id);

        if ($orden->estado !== 'en_lugar') {
            throw new \InvalidArgumentException('Solo se puede resolver una orden en estado "en_lugar".');
        }

        return DB::transaction(function () use ($orden, $datosResolucion) {
            $updateData = array_merge(['estado' => 'resuelta', 'resuelta_en' => now()], $datosResolucion);
            $orden->update($updateData);
            return $orden->fresh();
        });
    }

    public function cerrar(int $id): OrdenEmergencia
    {
        $orden = OrdenEmergencia::findOrFail($id);

        if ($orden->estado !== 'resuelta') {
            throw new \InvalidArgumentException('Solo se puede cerrar una orden en estado "resuelta".');
        }

        return DB::transaction(fn () => $orden->update(['estado' => 'cerrada', 'resuelta_en' => now()]))
            ? $orden->fresh()
            : $orden;
    }

    public function eliminar(int $id): bool
    {
        $orden = OrdenEmergencia::findOrFail($id);

        if ($orden->estado !== 'reportada') {
            throw new \InvalidArgumentException('Solo se pueden eliminar órdenes en estado "reportada".');
        }

        return (bool) $orden->delete();
    }
}
