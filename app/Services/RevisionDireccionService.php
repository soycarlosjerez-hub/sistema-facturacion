<?php

namespace App\Services;

use App\Models\RevisionDireccion;
use App\Models\AsistenteRevisionDireccion;
use App\Models\RevisionIntroduccion;
use App\Models\RevisionSalida;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RevisionDireccionService
{
    /**
     * Programar una reunión de revisión por la dirección.
     * Genera número REV-YYYY-NNNN.
     */
    public function programarReunion(array $data): RevisionDireccion
    {
        $data['creado_por'] = Auth::id();
        $data['modificado_por'] = Auth::id();
        $data['estado']     = $data['estado'] ?? 'programada';
        $data['tipo']       = $data['tipo'] ?? 'programada';

        // Generar número REV-YYYY-NNNN
        $year  = now()->year;
        $last  = RevisionDireccion::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->value('numero');

        $num = 1;
        if ($last) {
            // Extraer número del último (formato REV-YYYY-NNNN)
            $parts = explode('-', $last);
            $num = (int) last($parts) + 1;
        }

        $data['numero'] = sprintf('REV-%04d-%04d', $year, $num);

        $revision = RevisionDireccion::create($data);
        return $revision->fresh(['asistentes', 'entradas', 'salidas']);
    }

    /**
     * Agregar un asistente a la revisión de dirección.
     */
    public function agregarAsistente(
        RevisionDireccion $revision,
        int $userId,
        array $extra = []
    ): AsistenteRevisionDireccion {
        $data = array_merge([
            'revision_direccion_id' => $revision->id,
            'usuario_id'            => $userId,
            'asistio'               => false,
            'creado_por'            => Auth::id(),
            'modificado_por'        => Auth::id(),
            'tenant_id'             => Auth::user()->business_instance_id,
        ], $extra);

        $asistente = AsistenteRevisionDireccion::firstOrCreate(
            [
                'revision_direccion_id' => $revision->id,
                'usuario_id'            => $userId,
            ],
            $data
        );

        return $asistente->fresh(['revision', 'usuario']);
    }

    /**
     * Remover un asistente de la revisión.
     */
    public function removerAsistente(
        RevisionDireccion $revision,
        int $asId
    ): bool {
        $asistente = AsistenteRevisionDireccion::find($asId);

        if (!$asistente || $asistente->revision_direccion_id !== $revision->id) {
            return false;
        }

        $asistente->delete();
        return true;
    }

    /**
     * Agregar una entrada (introducción/punto de agenda) a la revisión.
     */
    public function agregarEntrada(
        RevisionDireccion $revision,
        array $data
    ): RevisionIntroduccion {
        $data['revision_direccion_id'] = $revision->id;
        $data['creado_por']            = Auth::id();
        $data['modificado_por']        = Auth::id();

        $entrada = RevisionIntroduccion::create($data);
        return $entrada;
    }

    /**
     * Agregar una salida/acción de la revisión.
     */
    public function agregarSalida(
        RevisionDireccion $revision,
        array $data
    ): RevisionSalida {
        $data['revision_direccion_id'] = $revision->id;
        $data['creado_por']            = Auth::id();
        $data['modificado_por']        = Auth::id();
        $data['estado']                = $data['estado'] ?? 'pendiente';

        $salida = RevisionSalida::create($data);
        return $salida->fresh(['responsable', 'revision']);
    }

    /**
     * Marcar que los asistentes asistieron a la reunión.
     */
    public function registrarAsistencia(
        RevisionDireccion $revision,
        int $asistenteId,
        bool $asistio = true
    ): AsistenteRevisionDireccion {
        $asistente = $revision->asistentes()->find($asistenteId);

        if (!$asistente) {
            return $asistente;
        }

        $asistente->asistio = $asistio;
        $asistente->saveQuietly();

        return $asistente->fresh(['revision', 'usuario']);
    }

    /**
     * Completar la revisión de dirección (cambia estado a completada).
     */
    public function completarRevision(RevisionDireccion $revision): RevisionDireccion
    {
        $revision->completar();

        // Contar salidas pendientes para el resumen
        $salidasPendientes = $revision->salidas->where('estado', 'pendiente')->count();
        $salidasTotal      = $revision->salidas->count();

        if ($salidasTotal > 0) {
            $revision->resumen_resoluciones = "{$salidasPendientes} de {$salidasTotal} salidas pendientes";
        }

        $revision->saveQuietly();

        return $revision->fresh(['salidas', 'entradas', 'asistentes']);
    }

    /**
     * Generar el acta de la reunión con contenido estructurado.
     */
    public function generarActa(RevisionDireccion $revision): array
    {
        $asistentes = $revision->asistentes;
        $presencialCount = 0;
        $ausentesCount = 0;

        foreach ($asistentes as $a) {
            if ($a->asistio) {
                $presencialCount++;
            } else {
                $ausentesCount++;
            }
        }

        $entradas      = $revision->entradas()->orderBy('id')->get();
        $salidas       = $revision->salidas()->orderBy('id')->get();
        $salidasPendientes = $salidas->where('estado', 'pendiente');
        $salidasCompletadas = $salidas->where('estado', 'completada');

        // Resumen por tipo de entrada
        $porTipoEntrada = $revision->entradas()
            ->selectRaw('tipo, COUNT(*) as total')
            ->groupBy('tipo')
            ->get()
            ->pluck('total', 'tipo')
            ->toArray();

        // Resumen de salidas por tipo de acción
        $porTipoSalida = $revision->salidas()
            ->selectRaw('tipo_accion, COUNT(*) as total')
            ->groupBy('tipo_accion')
            ->get()
            ->pluck('total', 'tipo_accion')
            ->toArray();

        return [
            'revision' => [
                'numero'        => $revision->numero,
                'fecha'         => $revision->fecha?->format('d/m/Y'),
                'tipo'          => $revision->tipo,
                'estado'        => $revision->estado,
                'duracion_horas'=> $revision->duracion_horas,
                'resumen'       => $revision->resumen ?? 'Sin resumen',
            ],
            'asistentes' => [
                'total'       => $asistentes->count(),
                'presenciales' => $presencialCount,
                'ausentes'     => $ausentesCount,
                'lista'        => $asistentes->map(fn ($a) => [
                    'usuario'    => $a->usuario?->name,
                    'asistio'    => $a->asistio,
                ])->toArray(),
            ],
            'entradas' => [
                'total'  => $entradas->count(),
                'por_tipo' => $porTipoEntrada,
                'lista'  => $entradas->map(fn ($e) => [
                    'id'        => $e->id,
                    'tipo'      => $e->tipo,
                    'tipo_label'=> $e->tipo_label,
                    'contenido' => $e->contenido,
                    'documento' => $e->documento_label,
                ])->toArray(),
            ],
            'salidas' => [
                'total'           => $salidas->count(),
                'pendientes'      => $salidasPendientes->count(),
                'completadas'     => $salidasCompletadas->count(),
                'por_tipo'        => $porTipoSalida,
                'lista'           => $salidas->map(fn ($s) => [
                    'id'             => $s->id,
                    'descripcion'    => $s->descripcion,
                    'tipo_accion'    => $s->tipo_accion,
                    'estado'         => $s->estado,
                    'responsable'    => $s->responsable?->name,
                    'fecha_limite'   => $s->fecha_limite?->format('d/m/Y'),
                ])->toArray(),
            ],
            'acciones_pendientes_count' => $salidasPendientes->count(),
        ];
    }

    /**
     * Estadísticas de revisiones de dirección del tenant.
     */
    public function stats(): array
    {
        $query = RevisionDireccion::withoutSoftDeletes();

        $programadas     = (clone $query)->programadas()->count();
        $en_ejecucion    = (clone $query)->enEjecucion()->count();
        $completadas     = (clone $query)->completadas()->count();
        $total           = (clone $query)->count();

        // Acciones pendientes por salida
        $accionesPendientes = RevisionSalida::query()
            ->where('estado', 'pendiente')
            ->count();

        $accionesCompletadas = RevisionSalida::query()
            ->where('estado', 'completada')
            ->count();

        $accionesVencidas = RevisionSalida::query()
            ->vencidas()->count();

        // Entradas por tipo
        $porTipoEntrada = RevisionIntroduccion::query()
            ->selectRaw('tipo, COUNT(*) as total')
            ->groupBy('tipo')
            ->get()
            ->pluck('total', 'tipo')
            ->toArray();

        // Promedio de asistentes por revisión completada
        $promedioAsistentes = RevisionDireccion::completadas()
            ->join('asistentes_revisiones_direccion', 'revisiones_direccion.id', '=', 'asistentes_revisiones_direccion.revision_direccion_id')
            ->selectRaw('AVG(asistentes_count) as avg_asistentes')
            ->selectRaw('revisiones_direccion.id as rev_id')
            ->groupBy('revisiones_direccion.id')
            ->avg('asistentes_count');

        return [
            'total'              => $total,
            'programadas'        => $programadas,
            'en_ejecucion'       => $en_ejecucion,
            'completadas'        => $completadas,
            'accion_pendientes'  => $accionesPendientes,
            'accion_completadas' => $accionesCompletadas,
            'accion_vencidas'    => $accionesVencidas,
            'tasa_cumplimiento'  => $accionesPendientes + $accionesCompletadas > 0
                ? round(
                    ($accionesCompletadas / ($accionesPendientes + $accionesCompletadas)) * 100, 1
                  )
                : 0,
            'promedio_asistentes'=> round($promedioAsistentes ?? 0, 1),
            'por_tipo_entrada'   => $porTipoEntrada,
        ];
    }
}
