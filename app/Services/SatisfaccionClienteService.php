<?php

namespace App\Services;

use App\Models\EncuestaSatisfaccion;
use App\Models\PreguntaEncuesta;
use App\Models\RespuestaEncuesta;
use App\Models\ReclamoCliente;
use App\Models\User;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;

class SatisfaccionClienteService
{
    /**
     * Crear una encuesta de satisfacción con preguntas.
     */
    public function crearEncuesta(array $data): EncuestaSatisfaccion
    {
        $data['creado_por'] = Auth::id();
        $data['modificado_por'] = Auth::id();
        $data['estado']     = $data['estado'] ?? 'borrador';

        $preguntasData = $data['preguntas'] ?? [];
        unset($data['preguntas']);

        $encuesta = EncuestaSatisfaccion::create($data);

        // Crear preguntas en orden
        foreach ($preguntasData as $orden => $pregunta) {
            if (!is_array($pregunta)) {
                continue;
            }
            $pregunta['orden'] = (int) $orden + 1;
            $pregunta['obligatoria'] = $pregunta['obligatoria'] ?? true;
            $pregunta['tipo'] = $pregunta['tipo'] ?? 'escala_5';
            PreguntaEncuesta::create($pregunta);
        }

        return $encuesta->fresh(['preguntas']);
    }

    /**
     * Actualizar una encuesta existente.
     */
    public function actualizarEncuesta(EncuestaSatisfaccion $encuesta, array $data): EncuestaSatisfaccion
    {
        $data['modificado_por'] = Auth::id();

        $preguntasData = $data['preguntas'] ?? null;
        unset($data['preguntas']);

        $encuesta->update($data);

        // Si se proporcionan nuevas preguntas, reemplazar las existentes
        if ($preguntasData !== null && is_array($preguntasData)) {
            $encuesta->preguntas()->delete();

            foreach ($preguntasData as $orden => $pregunta) {
                if (!is_array($pregunta)) {
                    continue;
                }
                $pregunta['orden'] = (int) $orden + 1;
                $pregunta['obligatoria'] = $pregunta['obligatoria'] ?? true;
                $pregunta['tipo'] = $pregunta['tipo'] ?? 'escala_5';
                PreguntaEncuesta::create($pregunta);
            }
        }

        return $encuesta->fresh(['preguntas']);
    }

    /**
     * Registrar respuestas a una encuesta.
     * Retorna [total_points, max_points, percentage].
     */
    public function registrarRespuesta(EncuestaSatisfaccion $encuesta, array $respuestas): array
    {
        $totalPoints = 0;
        $maxPoints   = 0;
        $tipoEscala  = 'escala_5';

        foreach ($respuestas as $preguntaId => $valor) {
            // Buscar la encuesta_satisfaccion_id desde el modelo PreguntaEncuesta
            $preguntaObj = PreguntaEncuesta::find($preguntaId);
            if (!$preguntaObj) {
                continue;
            }

            $respuestaExistente = RespuestaEncuesta::where('encuesta_satisfaccion_id', $encuesta->id)
                ->where('pregunta_encuesta_id', $preguntaId)
                ->first();

            if ($respuestaExistente) {
                $respuestaExistente->update([
                    'valor' => is_numeric($valor) ? (float) $valor : $valor,
                    'comentario' => is_string($valor) ? $valor : ($respuestaExistente->comentario ?? ''),
                ]);
                $tipoEscala = $preguntaObj->tipo ?? 'escala_5';
                $maxEscala  = match ($tipoEscala) {
                    'escala_10' => 10,
                    'escala_5'  => 5,
                    'escala_4'  => 4,
                    default     => 5,
                };
                $maxPoints += $maxEscala;
                $totalPoints += is_numeric($valor) ? (float) $valor : 0;
                continue;
            }

            $tipoEscala = $preguntaObj->tipo ?? 'escala_5';
            $maxEscala  = match ($tipoEscala) {
                'escala_10' => 10,
                'escala_5'  => 5,
                'escala_4'  => 4,
                default     => 5,
            };

            RespuestaEncuesta::create([
                'encuesta_satisfaccion_id' => $encuesta->id,
                'pregunta_encuesta_id'     => $preguntaId,
                'valor'                    => is_numeric($valor) ? (string) $valor : $valor,
                'comentario'               => is_string($valor) ? $valor : (string) $valor,
                'respondido_por'           => Auth::id(),
            ]);

            $maxPoints += $maxEscala;
            $totalPoints += is_numeric($valor) ? (float) $valor : 0;
        }

        $porcentaje = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 1) : 0;

        return [
            'total_points' => round($totalPoints, 2),
            'max_points'   => $maxPoints,
            'percentage'   => $porcentaje,
            'total'        => round($totalPoints, 2),
            'max'          => $maxPoints,
            'pct'          => $porcentaje,
        ];
    }

    /**
     * Crear un reclamo de cliente con código auto-generado (REC-YYYY-NNNN).
     */
    public function crearReclamo(array $data): ReclamoCliente
    {
        $data['creado_por'] = Auth::id();
        $data['modificado_por'] = Auth::id();
        $data['estado']     = $data['estado'] ?? 'abierto';

        $reclamo = ReclamoCliente::create($data);
        return $reclamo->fresh(['cliente']);
    }

    /**
     * Asignar un reclamo a un responsable.
     */
    public function asignarReclamo(ReclamoCliente $reclamo, int $userId): ReclamoCliente
    {
        $reclamo->asignarResponsable($userId);
        return $reclamo->fresh(['asignadoA']);
    }

    /**
     * Resolver un reclamo con una resolución.
     */
    public function resolverReclamo(ReclamoCliente $reclamo, string $resolucion): ReclamoCliente
    {
        $tiempoHoras = now()->diffInHours($reclamo->created_at);

        $reclamo->resolver($resolucion, $tiempoHoras);
        return $reclamo->fresh(['cliente']);
    }

    /**
     * Estadísticas de satisfacción y reclamos del tenant.
     */
    public function stats(): array
    {
        $query = ReclamoCliente::withoutSoftDeletes();

        $totalEncuestas  = EncuestaSatisfaccion::count();
        $activas         = EncuestaSatisfaccion::activas()->count();
        $cerradas        = (clone $query)->count();

        $totalReclamos   = (clone $query)->count();
        $abiertos        = (clone $query)->abiertos()->count();
        $en_tramite      = (clone $query)->enTramite()->count();
        $resueltos       = (clone $query)->resueltos()->count();
        $cerradosReclamo = (clone $query)->cerrados()->count();

        // Por tipo
        $porTipo = ReclamoCliente::selectRaw('tipo, COUNT(*) as total')
            ->groupBy('tipo')
            ->get()
            ->pluck('total', 'tipo')
            ->toArray();

        // Por canal
        $porCanal = ReclamoCliente::selectRaw('canal, COUNT(*) as total')
            ->groupBy('canal')
            ->get()
            ->pluck('total', 'canal')
            ->toArray();

        // Satisfacción con resoluciones
        $satisfacciones = ReclamoCliente::whereNotNull('satisfaccion_resolucion')
            ->avg('satisfaccion_resolucion');

        $tasaResolucion  = $totalReclamos > 0
            ? round(($resueltos / $totalReclamos) * 100, 1)
            : 0;

        $tasaCierre      = $totalReclamos > 0
            ? round(($cerradosReclamo / $totalReclamos) * 100, 1)
            : 0;

        $tiempoPromedio  = ReclamoCliente::whereNotNull('fecha_resolucion')
            ->whereNotNull('tiempo_respuesta_horas')
            ->avg('tiempo_respuesta_horas');

        // Encuestas completadas y respuesta rate
        $encuestasConRespuestas = EncuestaSatisfaccion::has('respuestas')->count();

        return [
            'satisfaccion_promedio'       => round($satisfacciones ?? 0, 2),
            'total_encuestas'             => $totalEncuestas,
            'encuestas_activas'           => $activas,
            'encuestas_con_respuestas'    => $encuestasConRespuestas,
            'total_reclamos'              => $totalReclamos,
            'reclamos_abiertos'           => $abiertos,
            'reclamos_en_tramite'         => $en_tramite,
            'reclamos_resueltos'          => $resueltos,
            'reclamos_cerrados'           => $cerradosReclamo,
            'tasa_resolucion'             => $tasaResolucion,
            'tasa_cierre_reclamos'        => $tasaCierre,
            'tiempo_respuesta_promedio_h' => round($tiempoPromedio ?? 0, 1),
            'por_tipo'                    => $porTipo,
            'por_canal'                   => $porCanal,
            'escalas_resolucion'          => [
                1 => 'Muy Insatisfecho',
                2 => 'Insatisfecho',
                3 => 'Neutral',
                4 => 'Satisfecho',
                5 => 'Muy Satisfecho',
            ],
        ];
    }
}
