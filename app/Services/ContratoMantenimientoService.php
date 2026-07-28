<?php

namespace App\Services;

use App\Models\ContratoMantenimiento;
use Illuminate\Support\Facades\DB;

class ContratoMantenimientoService
{
    public function crear(array $data, ?int $userId): ContratoMantenimiento
    {
        $data['estado'] = 'borrador';
        $data['created_by'] = $userId;
        $data['incluye_visitas'] = $data['incluye_visitas'] ?? false;
        $data['num_visitas_anuales'] = $data['num_visitas_anuales'] ?? 0;
        $data['visitas_realizadas'] = 0;
        $data['deducible'] = $data['deducible'] ?? 0;
        $data['cobertura_maxima'] = $data['cobertura_maxima'] ?? 0;

        return ContratoMantenimiento::create($data);
    }

    public function actualizar(int $id, array $data): ContratoMantenimiento
    {
        $data['incluye_visitas'] = $data['incluye_visitas'] ?? false;
        $data['num_visitas_anuales'] = $data['num_visitas_anuales'] ?? 0;
        $data['deducible'] = $data['deducible'] ?? 0;
        $data['cobertura_maxima'] = $data['cobertura_maxima'] ?? 0;

        return ContratoMantenimiento::findOrFail($id)->update($data)
            ? ContratoMantenimiento::findOrFail($id)
            : abort(500, 'Error al actualizar contrato');
    }

    public function activar(int $id): ContratoMantenimiento
    {
        $contrato = ContratoMantenimiento::findOrFail($id);

        if ($contrato->estado !== 'borrador') {
            throw new \InvalidArgumentException('Solo se puede activar un contrato en estado borrador.');
        }

        return DB::transaction(fn () => $contrato->update(['estado' => 'activo']))
            ? $contrato->fresh()
            : $contrato;
    }

    public function cancelar(int $id, ?string $motivo = null): ContratoMantenimiento
    {
        $contrato = ContratoMantenimiento::findOrFail($id);

        if (!in_array($contrato->estado, ['borrador', 'activo'])) {
            throw new \InvalidArgumentException('Solo se puede cancelar un contrato en estado borrador o activo.');
        }

        return DB::transaction(fn () => $contrato->update(['estado' => 'cancelado']))
            ? $contrato->fresh()
            : $contrato;
    }

    public function eliminar(int $id): bool
    {
        $contrato = ContratoMantenimiento::findOrFail($id);

        if (in_array($contrato->estado, ['activo', 'cancelado'])) {
            throw new \InvalidArgumentException('No se puede eliminar un contrato activo. Cancelarlo primero.');
        }

        return (bool) $contrato->delete();
    }
}
