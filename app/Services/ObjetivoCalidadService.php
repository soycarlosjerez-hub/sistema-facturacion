<?php

namespace App\Services;

use App\Models\ObjetivoCalidad;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ObjetivoCalidadService
{
    public function crear(array $data): ObjetivoCalidad
    {
        $data['creado_por'] = Auth::id();
        $data['modificado_por'] = Auth::id();
        $data['estado'] = $data['estado'] ?? 'en_curso';
        $data['cumplimiento'] = 0;
        $data['valor_actual'] = 0;

        return ObjetivoCalidad::create($data);
    }

    public function actualizar(ObjetivoCalidad $obj, array $data): ObjetivoCalidad
    {
        $data['modificado_por'] = Auth::id();
        $obj->update($data);
        return $obj->fresh(['responsable', 'creador', 'mediciones']);
    }

    public function registrarMedicion(ObjetivoCalidad $obj, float $valor, string $observaciones = ''): \App\Models\MedicionObjetivo
    {
        return $obj->registrarMedicion($valor, $observaciones, Auth::id());
    }

    public function stats(): array
    {
        $query = ObjetivoCalidad::query();

        return [
            'total' => $query->count(),
            'en_curso' => (clone $query)->where('estado', 'en_curso')->count(),
            'cumplidos' => (clone $query)->where('estado', 'cumplido')->count(),
            'no_cumplidos' => (clone $query)->where('estado', 'no_cumplido')->count(),
            'atrasados' => (clone $query)->where('estado', 'atrasado')->count(),
            'cumplimiento_promedio' => round((clone $query)->avg('cumplimiento') ?? 0, 2),
            'estados' => [
                'en_curso' => (clone $query)->where('estado', 'en_curso')->count(),
                'cumplido' => (clone $query)->where('estado', 'cumplido')->count(),
                'no_cumplido' => (clone $query)->where('estado', 'no_cumplido')->count(),
                'atrasado' => (clone $query)->where('estado', 'atrasado')->count(),
            ],
        ];
    }
}
