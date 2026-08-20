<?php

namespace App\Services;

use App\Models\MejoraContinua;
use App\Models\PropuestaMejora;
use App\Models\Riesgo;
use App\Models\NoConformidad;
use App\Models\AuditoriaInterna;
use Illuminate\Support\Facades\Auth;

class MejoraContinuaService
{
    /**
     * Crear una mejora continua con código auto-generado (MC-YYYY-NNNN).
     */
    public function crear(array $data): MejoraContinua
    {
        $data['creado_por'] = Auth::id();
        $data['modificado_por'] = Auth::id();
        $data['fase']       = $data['fase'] ?? 'propuesta';

        // Generar código MC-YYYY-NNNN
        $year  = now()->year;
        $last  = MejoraContinua::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->value('numero');

        $num = 1;
        if ($last) {
            $parts = explode('-', $last);
            $num = (int) last($parts) + 1;
        }

        $data['numero'] = sprintf('MC-%04d-%04d', $year, $num);

        $mejora = MejoraContinua::create($data);
        return $mejora->fresh(['responsable', 'riesgo', 'nc', 'auditoria']);
    }

    /**
     * Actualizar una mejora continua.
     */
    public function actualizar(MejoraContinua $mejora, array $data): MejoraContinua
    {
        $data['modificado_por'] = Auth::id();

        // Si se cambia responsable, actualizar
        if (isset($data['responsable_id'])) {
            $data['modificado_por'] = Auth::id();
        }

        $mejora->update($data);
        return $mejora->fresh(['responsable', 'riesgo', 'nc', 'auditoria']);
    }

    /**
     * Crear una propuesta de mejora.
     */
    public function crearPropuesta(array $data): PropuestaMejora
    {
        $data['creado_por'] = Auth::id();
        $data['modificado_por'] = Auth::id();
        $data['estado']     = $data['estado'] ?? 'pendiente';
        $data['autor_id']   = $data['autor_id'] ?? Auth::id();

        $propuesta = PropuestaMejora::create($data);
        return $propuesta->fresh(['autor', 'mejoraContinua']);
    }

    /**
     * Aprobar una propuesta y vincularla a una mejora continua.
     */
    public function aprobarPropuesta(
        PropuestaMejora $propuesta,
        MejoraContinua $mejora
    ): PropuestaMejora {
        $propuesta->estado = 'aprobada';
        $propuesta->mejora_continua_id = $mejora->id;
        $propuesta->saveQuietly();

        // Avanzar la mejora a 'evaluando' si está en fase 'propuesta'
        if ($mejora->fase === 'propuesta') {
            $mejora->fase = 'evaluando';
            $mejora->saveQuietly();
        }

        return $propuesta->fresh(['autor', 'mejoraContinua']);
    }

    /**
     * Rechazar una propuesta.
     */
    public function rechazarPropuesta(PropuestaMejora $propuesta): PropuestaMejora
    {
        $propuesta->estado = 'rechazada';
        $propuesta->saveQuietly();
        return $propuesta->fresh();
    }

    /**
     * Asignar una mejora a un riesgo existente.
     */
    public function vincularRiesgo(
        MejoraContinua $mejora,
        int $riesgoId
    ): MejoraContinua {
        $riesgo = Riesgo::find($riesgoId);
        if (!$riesgo) {
            throw new \RuntimeException("Riesgo #{$riesgoId} no encontrado.");
        }

        $mejora->riesgo_id = $riesgoId;
        $mejora->saveQuietly();
        return $mejora->fresh(['riesgo']);
    }

    /**
     * Crear una mejora partir de una NoConformidad.
     */
    public function desdeNoConformidad(NoConformidad $nc, array $data): MejoraContinua
    {
        $data['nc_id']         = $nc->id;
        $data['origen']        = 'nc';
        $data['fase']          = 'propuesta';
        $data['creado_por']    = Auth::id();
        $data['modificado_por']= Auth::id();

        // Generar código
        $year  = now()->year;
        $last  = MejoraContinua::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->value('numero');
        $num = 1;
        if ($last) {
            $parts   = explode('-', $last);
            $num     = (int) last($parts) + 1;
        }

        $data['numero'] = sprintf('MC-%04d-%04d', $year, $num);

        $mejora = MejoraContinua::create($data);
        return $mejora->fresh(['nc']);
    }

    /**
     * Crear una mejora a partir de un hallazgo de auditoría.
     */
    public function desdeAuditoria(AuditoriaInterna $auditoria, array $data): MejoraContinua
    {
        $data['auditoria_id']    = $auditoria->id;
        $data['origen']          = 'auditoria';
        $data['fase']            = 'propuesta';
        $data['creado_por']      = Auth::id();
        $data['modificado_por']  = Auth::id();

        // Generar código
        $year  = now()->year;
        $last  = MejoraContinua::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->value('numero');
        $num = 1;
        if ($last) {
            $parts   = explode('-', $last);
            $num     = (int) last($parts) + 1;
        }

        $data['numero'] = sprintf('MC-%04d-%04d', $year, $num);

        $mejora = MejoraContinua::create($data);
        return $mejora->fresh(['auditoria']);
    }

    /**
     * Completar una mejora (avanza a completada, verifica, etc.).
     */
    public function completar(MejoraContinua $mejora): MejoraContinua
    {
        $mejora->fase          = 'completada';
        $mejora->fecha_completar = now()->toDateString();
        $mejora->saveQuietly();
        return $mejora->fresh();
    }

    /**
     * Cerrar una mejora (después de verificar resultados).
     */
    public function cerrar(MejoraContinua $mejora): MejoraContinua
    {
        $mejora->fase   = 'cerrada';
        $mejora->saveQuietly();
        return $mejora->fresh();
    }

    /**
     * Ajustar prioridad de una mejora.
     */
    public function ajustarPrioridad(
        MejoraContinua $mejora,
        string $prioridad
    ): MejoraContinua {
        $validPriorities = ['baja', 'media', 'alta', 'urgente'];
        $mejora->prioridad = in_array($prioridad, $validPriorities) ? $prioridad : $mejora->prioridad;
        $mejora->saveQuietly();
        return $mejora->fresh();
    }

    /**
     * Registrar beneficio logrado de una mejora.
     */
    public function registrarBeneficio(
        MejoraContinua $mejora,
        string $beneficioLogrado
    ): MejoraContinua {
        if ($mejora->fase !== 'completada') {
            throw new \RuntimeException('Solo se pueden registrar beneficios de mejoras completadas.');
        }

        if ($beneficioLogrado) {
            $mejora->beneficios_logrados = $beneficioLogrado;
            $mejora->fase = 'verificada';
            $mejora->saveQuietly();
        }

        return $mejora->fresh();
    }

    /**
     * Avanzar a una fase específica de la mejora.
     */
    public function avanzarFase(
        MejoraContinua $mejora,
        string $nuevaFase
    ): MejoraContinua {
        $validFases = [
            'propuesta', 'evaluando', 'aprobada', 'en_curso',
            'completada', 'verificada', 'cerrada',
        ];

        if (!in_array($nuevaFase, $validFases)) {
            throw new \RuntimeException("Fase '{$nuevaFase}' no es válida.");
        }

        $mejora->fase = $nuevaFase;
        $mejora->saveQuietly();
        return $mejora->fresh(['responsable', 'riesgo', 'nc', 'auditoria']);
    }

    /**
     * Estadísticas de mejora continua del tenant.
     */
    public function stats(): array
    {
        $query = MejoraContinua::withoutSoftDeletes();

        $proposals         = (clone $query)->propuestas()->count();
        $evaluando         = (clone $query)->evaluando()->count();
        $aprobadas         = (clone $query)->aprobadas()->count();
        $en_curso          = (clone $query)->enCurso()->count();
        $completadas       = (clone $query)->completadas()->count();
        $verificadas       = (clone $query)->verificadas()->count();
        $cerradas          = (clone $query)->cerradas()->count();
        $total             = (clone $query)->count();

        // Por origen
        $porOrigen = MejoraContinua::selectRaw('origen, COUNT(*) as total')
            ->groupBy('origen')
            ->get()
            ->pluck('total', 'origen')
            ->toArray();

        // Por prioridad
        $porPrioridad = MejoraContinua::selectRaw('prioridad, COUNT(*) as total')
            ->groupBy('prioridad')
            ->get()
            ->pluck('total', 'prioridad')
            ->toArray();

        // Por impacto
        $porImpacto = MejoraContinua::selectRaw('impacto, COUNT(*) as total')
            ->groupBy('impacto')
            ->get()
            ->pluck('total', 'impacto')
            ->toArray();

        // Propuestas vinculadas a mejoras
        $propuestasVinculadas = PropuestaMejora::whereNotNull('mejora_continua_id')->count();
        $propuestasPendientes = PropuestaMejora::pendiente()->count();
        $propuestasAprobadas  = PropuestaMejora::aprobada()->count();
        $propuestasRechazadas = PropuestaMejora::rechazada()->count();

        // Cierre rate
        $cierreRate = $total > 0
            ? round(($cerradas / $total) * 100, 1)
            : 0;

        // Total ahorros y costos
        $totalAhorros = MejoraContinua::whereNotNull('ahorro_estimado')
            ->sum('ahorro_estimado');

        $totalCostos = MejoraContinua::whereNotNull('costo_estimado')
            ->sum('costo_estimado');

        return [
            'total'             => $total,
            'propuestas'        => $proposals,
            'evaluando'         => $evaluando,
            'aprobadas'         => $aprobadas,
            'en_curso'          => $en_curso,
            'completadas'       => $completadas,
            'verificadas'       => $verificadas,
            'cerradas'          => $cerradas,
            'cierre_rate'       => $cierreRate,
            'propuestas_total'  => $propuestasVinculadas,
            'propuestas_pendientes' => $propuestasPendientes,
            'propuestas_aprobadas'  => $propuestasAprobadas,
            'propuestas_rechazadas' => $propuestasRechazadas,
            'total_ahorros'     => round($totalAhorros ?? 0, 2),
            'total_costos'      => round($totalCostos ?? 0, 2),
            'por_origen'        => $porOrigen,
            'por_prioridad'     => $porPrioridad,
            'por_impacto'       => $porImpacto,
        ];
    }
}
