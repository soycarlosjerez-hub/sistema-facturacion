<?php

namespace App\Services;

use App\Models\NoConformidad;
use App\Models\AnalisisCausa;
use App\Models\AccionCorrectiva;
use App\Models\VerificacionAccion;
use Illuminate\Support\Facades\Auth;

class NoConformidadService
{
    /**
     * Crear una no conformidad con código auto-generado (NC-YYYY-NNNN).
     */
    public function crear(array $data): NoConformidad
    {
        $data['creado_por']             = Auth::id();
        $data['modificado_por']         = Auth::id();
        $data['estado']                 = $data['estado'] ?? 'abierta';
        $data['fecha_identificacion']   = $data['fecha_identificacion'] ?? now()->toDateString();

        // Generar código NC-YYYY-NNNN
        $year  = now()->year;
        $last  = NoConformidad::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->value('numero');

        $num = 1;
        if ($last) {
            $parts    = explode('-', $last);
            $num      = (int) last($parts) + 1;
        }

        $data['numero'] = sprintf('NC-%04d-%04d', $year, $num);

        return NoConformidad::create($data);
    }

    /**
     * Actualizar una no conformidad.
     */
    public function actualizar(NoConformidad $nc, array $data): NoConformidad
    {
        $data['modificado_por'] = Auth::id();
        $nc->update($data);
        return $nc->fresh(['asignadoA']);
    }

    /**
     * Registrar el análisis de causa de una NC.
     */
    public function registrarAnalisisCausa(
        NoConformidad $nc,
        string $metodo,
        string $causa
    ): AnalisisCausa {
        // Si tiene análisis previo, lo actualiza
        $analisisExistente = $nc->analisisCausa;

        if ($analisisExistente) {
            $analisisExistente->update([
                'metodologia'  => $metodo,
                'causa_raiz'   => $causa,
                'modificado_por' => Auth::id(),
            ]);
        } else {
            $analisisExistente = AnalisisCausa::create([
                'no_conformidad_id' => $nc->id,
                'creado_por' => Auth::id(),
                'modificado_por' => Auth::id(),
                'metodologia' => $metodo,
                'causa_raiz' => $causa,
            ]);
        }

        // Actualizar estado a en_accion si se encuentra causa raíz
        if ($causa) {
            $nc->iniciarAnalisis($metodo);
        }

        return $analisisExistente->fresh(['noConformidad']);
    }

    /**
     * Crear una acción correctiva para la NC.
     */
    public function crearAccionCorrectiva(NoConformidad $nc, array $data): AccionCorrectiva
    {
        $data['no_conformidad_id'] = $nc->id;
        $data['creado_por']        = Auth::id();
        $data['modificado_por']    = Auth::id();
        $data['estado']            = $data['estado'] ?? 'pendiente';

        $accion = AccionCorrectiva::create($data);

        // Si está pendiente, asignar si hay responsable
        if ($accion->estado === 'pendiente' && $data['responsable_id'] ?? null) {
            $nc->asignarResponsable($data['responsable_id']);
        }

        return $accion->fresh(['noConformidad', 'responsable']);
    }

    /**
     * Actualizar una acción correctiva.
     */
    public function actualizarAccionCorrectiva(
        AccionCorrectiva $accion,
        array $data
    ): AccionCorrectiva {
        $data['modificado_por'] = Auth::id();
        $accion->update($data);
        return $accion->fresh(['noConformidad', 'responsable']);
    }

    /**
     * Verificar la eficacia de una acción correctiva completada.
     */
    public function verificarEficacia(
        AccionCorrectiva $accion,
        string $resultado,
        string $evidencia,
        int $verificadorId
    ): VerificacionAccion {
        $validResultados = ['eficaz', 'parcial', 'ineficaz', 'na'];

        if (!in_array($resultado, $validResultados)) {
            $resultado = 'na';
        }

        $verificacion = VerificacionAccion::create([
            'accion_correctiva_id' => $accion->id,
            'resultado'            => $resultado,
            'evidencias'           => $evidencia,
            'verificado_por'       => $verificadorId,
            'creado_por'           => Auth::id(),
            'modificado_por'       => Auth::id(),
        ]);

        // Si es eficaz, marcar NC como verificando
        if ($resultado === 'eficaz' && $accion->noConformidad) {
            $accion->noConformidad->estado = 'verificando';
            $accion->noConformidad->saveQuietly();
        }

        return $verificacion->fresh(['accionCorrectiva', 'verificadoPorMod']);
    }

    /**
     * Cerrar la no conformidad. Requiere que todas las acciones estén completadas y verificadas.
     */
    public function cerrarNoConformidad(NoConformidad $nc): NoConformidad
    {
        $acciones = $nc->accionesCorrectivas;

        if ($acciones->isEmpty()) {
            throw new \RuntimeException('No se puede cerrar la NC sin acciones correctivas planificadas.');
        }

        foreach ($acciones as $accion) {
            $esVerificada = false;

            if ($accion->estado === 'completada') {
                // Verificar si tiene verificación eficaz
                if ($accion->verificacion) {
                    if (in_array($accion->verificacion->resultado, ['eficaz', 'na'])) {
                        $esVerificada = true;
                    }
                } else {
                    // Si no tiene verificación pero está completada, se requiere verificación
                    throw new \RuntimeException(
                        "La acción #{$accion->id} debe ser verificada antes de cerrar la NC."
                    );
                }
            }

            if (!$esVerificada && in_array($accion->estado, ['pendiente', 'en_curso'])) {
                throw new \RuntimeException(
                    "La acción #{$accion->id} ({$accion->estado_label}) debe estar completada y verificada."
                );
            }
        }

        $nc->estado = 'cerrada';
        $nc->saveQuietly();

        return $nc->fresh();
    }

    /**
     * Asignar responsable a la NC.
     */
    public function asignarResponsable(NoConformidad $nc, int $userId): NoConformidad
    {
        $nc->asignarResponsable($userId);
        return $nc->fresh(['asignadoA']);
    }

    /**
     * Vincular una NC a una auditoría.
     */
    public function vincularAuditoria(NoConformidad $nc, int $auditoriaId): NoConformidad
    {
        $nc->auditoria_id = $auditoriaId;
        $nc->saveQuietly();
        return $nc->fresh();
    }

    /**
     * Estadísticas de no conformidades del tenant.
     */
    public function stats(): array
    {
        $query = NoConformidad::withoutSoftDeletes();

        $abiertas        = (clone $query)->abiertas()->count();
        $en_analisis     = (clone $query)->enAnalisis()->count();
        $en_accion       = (clone $query)->enAccion()->count();
        $verificando     = (clone $query)->enVerificacion()->count();
        $cerradas        = (clone $query)->cerradas()->count();
        $total           = (clone $query)->count();

        // Por gravedad
        $porGravedad = NoConformidad::selectRaw('gravedad, COUNT(*) as total')
            ->groupBy('gravedad')
            ->get()
            ->pluck('total', 'gravedad')
            ->toArray();

        // Por origen
        $porOrigen = NoConformidad::selectRaw('origen, COUNT(*) as total')
            ->groupBy('origen')
            ->get()
            ->pluck('total', 'origen')
            ->toArray();

        // Vencidas
        $vencidas = NoConformidad::withoutSoftDeletes()->vencidas()->count();

        // Con acción correctiva
        $conAcciones = NoConformidad::has('accionesCorrectivas')->count();

        // Verificaciones eficaces
        $verificacionesEficaces = VerificacionAccion::query()
            ->eficaces()->count();

        return [
            'total'             => $total,
            'abiertas'          => $abiertas,
            'en_analisis'       => $en_analisis,
            'en_accion'         => $en_accion,
            'verificando'       => $verificando,
            'cerradas'          => $cerradas,
            'vencidas'          => $vencidas,
            'con_acciones'      => $conAcciones,
            'verificaciones_eficaces' => $verificacionesEficaces,
            'por_gravedad'      => $porGravedad,
            'por_origen'        => $porOrigen,
            'tasa_cierre'       => $total > 0 ? round($cerradas / $total * 100, 1) : 0,
            'tasa_eficacia'     => $abiertas + $en_accion + $cerradas > 0
                ? round($verificacionesEficaces / ($abiertas + $en_accion + $cerradas) * 100, 1)
                : 0,
            'estados_flow'      => [
                'abierta'     => 'Abierta',
                'en_analisis' => 'En Análisis',
                'en_accion'   => 'En Acción',
                'verificando' => 'Verificando',
                'cerrada'     => 'Cerrada',
            ],
        ];
    }
}
