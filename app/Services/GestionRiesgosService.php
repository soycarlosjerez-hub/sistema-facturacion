<?php

namespace App\Services;

use App\Models\Riesgo;
use Illuminate\Support\Facades\Auth;

class GestionRiesgosService
{
    /**
     * Crear un riesgo, auto-calculando nivel y clasificación.
     */
    public function crear(array $data): Riesgo
    {
        $data['creado_por'] = Auth::id();
        $data['modificado_por'] = Auth::id();

        $probabilidad = (int) ($data['probabilidad'] ?? 1);
        $impacto      = (int) ($data['impacto'] ?? 1);
        $nivel        = $probabilidad * $impacto;

        $data['probabilidad'] = max(1, min(5, $probabilidad));
        $data['impacto']      = max(1, min(5, $impacto));
        $data['nivel']        = $nivel;
        $data['clasificacion'] = $this->determinarClasificacion($nivel);

        $riesgo = Riesgo::create($data);
        return $riesgo->fresh(['creador', 'responsable', 'procesoAfectado']);
    }

    /**
     * Actualizar un riesgo, recalculando nivel si probabilidad o impacto cambiaron.
     */
    public function actualizar(Riesgo $riesgo, array $data): Riesgo
    {
        $data['modificado_por'] = Auth::id();

        $probOriginal = $riesgo->probabilidad;
        $impactoOriginal = $riesgo->impacto;

        if (isset($data['probabilidad']) || isset($data['impacto'])) {
            $probabilidad = (int) ($data['probabilidad'] ?? $probOriginal);
            $impacto      = (int) ($data['impacto'] ?? $impactoOriginal);

            $data['probabilidad'] = max(1, min(5, $probabilidad));
            $data['impacto']      = max(1, min(5, $impacto));
            $data['nivel']        = $probabilidad * $impacto;
            $data['clasificacion'] = $this->determinarClasificacion((int) ($data['nivel'] ?? $riesgo->nivel));
        }

        $riesgo->update($data);
        return $riesgo->fresh(['creador', 'responsable', 'procesoAfectado']);
    }

    /**
     * Determinar la clasificación textual según el nivel calculado.
     */
    private function determinarClasificacion(int $nivel): string
    {
        if ($nivel <= 4) {
            return 'bajo';
        }

        if ($nivel <= 9) {
            return 'medio';
        }

        if ($nivel <= 15) {
            return 'alto';
        }

        return 'critico';
    }

    /**
     * Estimar la probabilidad/impacto como texto descriptivo.
     */
    public function estimarProbabilidadImpacto(Riesgo $riesgo): string
    {
        $prob = $riesgo->probabilidad;
        $imp  = $riesgo->impacto;

        $probText = match (true) {
            $prob === 1 => 'Muy Baja',
            $prob === 2 => 'Baja',
            $prob === 3 => 'Media',
            $prob === 4 => 'Alta',
            default  => 'Muy Alta',
        };

        $impText = match (true) {
            $imp === 1 => 'Insignificante',
            $imp === 2 => 'Menor',
            $imp === 3 => 'Moderado',
            $imp === 4 => 'Mayor',
            default  => 'Catastrófico',
        };

        return "{$probText} × {$impText} → {$riesgo->clasificacion_label} ({$riesgo->nivel})";
    }

    /**
     * Calcular el riesgo residual (después de mitigaciones).
     */
    public function calcularRiesgoResidual(Riesgo $riesgo): array
    {
        // Si ya tiene valores residuales calculados, los reutiliza
        if ($riesgo->nivel_residual > 0) {
            return [
                'probabilidad' => $riesgo->probabilidad_residual,
                'impacto'      => $riesgo->impacto_residual,
                'nivel'        => $riesgo->nivel_residual,
                'clasificacion'=> $this->determinarClasificacion($riesgo->nivel_residual),
                'reduccion'    => $riesgo->nivel - $riesgo->nivel_residual,
                'mejora_pct'   => round(($riesgo->nivel - $riesgo->nivel_residual) / $riesgo->nivel * 100, 1),
            ];
        }

        // Si no tiene residual, se usa el valor actual
        return [
            'probabilidad'    => $riesgo->probabilidad,
            'impacto'         => $riesgo->impacto,
            'nivel'           => $riesgo->nivel,
            'clasificacion'   => $this->determinarClasificacion($riesgo->nivel),
            'reduccion'       => 0,
            'mejora_pct'      => 0.0,
            'pendiente_calculo'=> true,
        ];
    }

    /**
     * Eliminar un riesgo.
     */
    public function eliminar(Riesgo $riesgo): bool
    {
        $riesgo->delete();
        return true;
    }

    /**
     * Estadísticas generales de riesgos del tenant actual.
     */
    public function stats(): array
    {
        $query = Riesgo::withoutSoftDeletes();

        $total          = (clone $query)->count();
        $criticos       = (clone $query)->criticos()->count();
        $altos          = (clone $query)->where('clasificacion', 'alto')->count();
        $en_tratamiento = (clone $query)->where('estado', 'en_tratamiento')->count();
        $cerrados       = (clone $query)->where('estado', 'cerrado')->count();
        $identificados  = (clone $query)->where('estado', 'identificado')->count();
        $vencidos       = (clone $query)->vencidos()->count();

        $porClasificacion = (clone $query)
            ->selectRaw('clasificacion, COUNT(*) as total')
            ->groupBy('clasificacion')
            ->get()
            ->pluck('total', 'clasificacion')
            ->toArray();

        $porArea = (clone $query)
            ->selectRaw('area, COUNT(*) as total')
            ->whereNotNull('area')
            ->groupBy('area')
            ->get()
            ->pluck('total', 'area')
            ->toArray();

        $conPlanAccion = (clone $query)->whereNotNull('plan_accion')->where('plan_accion', '!=', '')->count();

        return [
            'total'              => $total,
            'criticos'           => $criticos,
            'altos'              => $altos,
            'en_tratamiento'     => $en_tratamiento,
            'cerrados'           => $cerrados,
            'identificados'      => $identificados,
            'vencidos'           => $vencidos,
            'con_plan_accion'    => $conPlanAccion,
            'por_clasificacion'  => $porClasificacion,
            'por_area'           => $porArea,
            'tasa_cierre'        => $total > 0 ? round($cerrados / $total * 100, 1) : 0,
            'tasa_vencimiento'   => $total > 0 ? round($vencidos / max(1, $total) * 100, 1) : 0,
        ];
    }
}
