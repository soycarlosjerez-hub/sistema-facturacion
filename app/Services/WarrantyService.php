<?php

namespace App\Services;

use App\Models\Garantia;
use App\Models\Equipo;
use App\Models\OrdenReparacion;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WarrantyService
{
    public function __construct() {}

    public function crearGarantia(array $data): Garantia
    {
        return Garantia::create(array_merge($data, [
            'estado' => 'activa',
        ]));
    }

    public function extenderGarantia(int $garantiaId, int $diasExtra): Garantia
    {
        $garantia = Garantia::findOrFail($garantiaId);

        $nuevaFechaFin = $garantia->fecha_fin->addDays($diasExtra);

        $garantia->update([
            'fecha_fin' => $nuevaFechaFin,
            'terminos_condiciones' => ($garantia->terminos_condiciones ?? '')
                . "\n\nEXTENSIÓN: {$diasExtra} días adicionales. Fecha nueva: {$nuevaFechaFin->format('Y-m-d')}",
        ]);

        return $garantia->fresh();
    }

    public function procesarReclamo(int $garantiaId, string $motivo, string $resultado = 'aprobado'): Garantia
    {
        $garantia = Garantia::findOrFail($garantiaId);

        if (!$garantia->esta_vigente) {
            throw new \Exception('Esta garantía ya no está vigente.');
        }

        $estado = $resultado === 'aprobado' ? 'en_reclamo' : 'rechazada';

        return DB::transaction(function () use ($garantia, $estado, $motivo) {
            $garantia->update([
                'estado' => $estado,
                'terminos_condiciones' => ($garantia->terminos_condiciones ?? '')
                    . "\n\nRECLAMO: {$motivo} | Resultado: {$estado}",
            ]);

            if ($garantia->orden_reparacion_id && $estado === 'en_reclamo') {
                $orden = $garantia->ordenReparacion;
                if ($orden) {
                    $orden->notas = ($orden->notas ?? '') . ' [GARANTÍA EN RECLAMO]';
                    $orden->save();
                }
            }

            return $garantia->fresh();
        });
    }

    public function verificarGarantiaEquipo(int $equipoId): ?Garantia
    {
        return Garantia::where('equipo_id', $equipoId)
            ->where('estado', 'activa')
            ->where('fecha_fin', '>=', today())
            ->orderBy('fecha_fin', 'desc')
            ->first();
    }

    public function verificarGarantiaOrden(int $ordenId): ?Garantia
    {
        return Garantia::where('orden_reparacion_id', $ordenId)
            ->where('estado', 'activa')
            ->where('fecha_fin', '>=', today())
            ->orderBy('fecha_fin', 'desc')
            ->first();
    }

    public function getGarantiasVigentes(): \Illuminate\Database\Eloquent\Collection
    {
        return Garantia::vigentes()
            ->with(['equipo', 'ordenReparacion.cliente'])
            ->orderBy('fecha_fin')
            ->get();
    }

    public function getGarantiasPorVencer(int $dias = 30): \Illuminate\Database\Eloquent\Collection
    {
        return Garantia::where('estado', 'activa')
            ->where('fecha_fin', '<=', now()->addDays($dias))
            ->where('fecha_fin', '>=', today())
            ->with(['equipo', 'ordenReparacion.cliente'])
            ->orderBy('fecha_fin')
            ->get();
    }

    public function getEstadisticas(): array
    {
        return [
            'total' => Garantia::count(),
            'vigentes' => Garantia::vigentes()->count(),
            'expiradas' => Garantia::where('estado', 'activa')
                ->where('fecha_fin', '<', today())->count(),
            'en_reclamo' => Garantia::where('estado', 'en_reclamo')->count(),
            'rechazadas' => Garantia::where('estado', 'rechazada')->count(),
            'tasa_rechazo' => 0,
        ];
    }
}
