<?php

namespace App\Services;

use App\Models\Proveedor;
use App\Models\EvaluacionProveedor;
use App\Models\EvaluacionProveedorDocumento;
use App\Models\EvaluacionPeriodicaProveedor;
use App\Models\IncumplimientoProveedor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvaluacionProveedorService
{
    /**
     * Evaluar un proveedor, calculando la puntuación total automáticamente.
     */
    public function evaluarProveedor(Proveedor $proveedor, array $data): EvaluacionProveedor
    {
        $data['proveedor_id'] = $proveedor->id;
        $data['evaluado_por'] = Auth::id();
        $data['estado']       = $data['estado'] ?? 'provisional';
        $data['fecha']        = $data['fecha'] ?? now()->toDateString();

        $evaluacion = EvaluacionProveedor::create($data);

        // Calcular total_puntuacion a partir de los criterios
        if (isset($data['criterios'])) {
            $evaluacion->criterios = $data['criterios'];
            $evaluacion->total_puntuacion = $evaluacion->calcularTotalPuntuacion();
        }

        // Auto-determinar estado según puntuación
        if (isset($data['criterios'])) {
            $evaluacion->actualizarPuntuacion();
        }

        $evaluacion->saveQuietly();
        return $evaluacion->fresh(['proveedor', 'evaluadoPor', 'documentos']);
    }

    /**
     * Registrar un documento de evaluación del proveedor.
     */
    public function crearDocumentoEvaluacion(EvaluacionProveedor $evaluacion, array $data): EvaluacionProveedorDocumento
    {
        $data['evaluacion_proveedor_id'] = $evaluacion->id;
        $data['created_by'] ??= Auth::id();
        $data['modificado_por'] ??= Auth::id();

        $documento = EvaluacionProveedorDocumento::create($data);

        if (isset($data['archivo'])) {
            $file = $data['archivo'];
            if ($file && $file->isValid()) {
                $fileName = time() . '_' . \Illuminate\Support\Str::random(20)
                    . '_' . \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                    . '.' . $file->getClientOriginalExtension();

                $path = $file->storeAs('sgc_evaluaciones_proveedores_documentos', $fileName, 'public');
                $documento->archivo_path = $path;
                $documento->archivo_original_name = $file->getClientOriginalName();
                $documento->archivo_mime_type = $file->getMimeType();
                $documento->archivo_size_bytes = $file->getSize();
                $documento->saveQuietly();
            }
        }

        return $documento;
    }

    /**
     * Registrar un incumplimiento del proveedor.
     */
    public function registrarIncumplimiento(
        Proveedor $proveedor,
        array $data
    ): IncumplimientoProveedor {
        $data['creado_por'] = Auth::id();
        $data['modificado_por'] = Auth::id();
        $data['estado'] = $data['estado'] ?? 'abierta';
        $data['gravedad'] = $data['gravedad'] ?? 'leve';
        $data['tipo'] = $data['tipo'] ?? 'otro';

        // Asociar a la evaluación más reciente de este proveedor
        $ultimaEvaluacion = EvaluacionProveedor::where('proveedor_id', $proveedor->id)
            ->orderBy('created_at', 'desc')
            ->value('id');

        if ($ultimaEvaluacion) {
            $data['evaluacion_proveedor_id'] = $ultimaEvaluacion;
        }

        $incumplimiento = IncumplimientoProveedor::create($data);
        return $incumplimiento;
    }

    /**
     * Evaluar el desempeño de un proveedor en un periodo anual.
     */
    public function evaluarPeriodo(
        Proveedor $proveedor,
        int $anio,
        array $data
    ): EvaluacionPeriodicaProveedor {
        $data['proveedor_id']  = $proveedor->id;
        $data['periodo']       = $anio;
        $data['evaluado_por']  = Auth::id();
        $data['estado']        = $data['estado'] ?? 'observaciones';

        $evaluacion = EvaluacionPeriodicaProveedor::create($data);

        // Auto-determinar estado según evaluación general
        if (isset($data['evaluacion_general'])) {
            $evaluacion->determinarEstado();
        }

        return $evaluacion->fresh(['proveedor', 'evaluadoPor']);
    }

    /**
     * Obtener el desempeño histórico de un proveedor (promedios por área).
     */
    public function getDesempeno(Proveedor $proveedor): array
    {
        // Promedios de evaluaciones puntuales
        $promediosEvaluaciones = EvaluacionProveedor::where('proveedor_id', $proveedor->id)
            ->whereNotNull('total_puntuacion')
            ->avg('total_puntuacion');

        // Promedios de evaluaciones periódicas
        $periodicos = EvaluacionPeriodicaProveedor::where('proveedor_id', $proveedor->id)
            ->selectRaw(
                'AVG(evaluacion_general) as avg_eval,
                 AVG(cumplimiento_ncf) as avg_ncf,
                 AVG(cumplimiento_calidad) as avg_calidad,
                 AVG(tiempo_entrega) as avg_entrega,
                 AVG(comunicacion) as avg_comunicacion'
            )
            ->first();

        // Incumplimientos totales y por gravedad
        $incumplimientos = IncumplimientoProveedor::whereHas('evaluacion', fn ($q) => $q->where('proveedor_id', $proveedor->id))
            ->select('gravedad', DB::raw('COUNT(*) as total'))
            ->groupBy('gravedad')
            ->get()
            ->pluck('total', 'gravedad')
            ->toArray();

        // Evaluaciones por año
        $porAnio = EvaluacionPeriodicaProveedor::where('proveedor_id', $proveedor->id)
            ->selectRaw('periodo, AVG(evaluacion_general) as avg_eval')
            ->groupBy('periodo')
            ->orderBy('periodo', 'desc')
            ->get()
            ->map(fn ($r) => [
                'periodo' => (int) $r->periodo,
                'avg_eval' => round((float) $r->avg_eval, 1),
            ])
            ->toArray();

        return [
            'proveedor_id'          => $proveedor->id,
            'proveedor_nombre'      => $proveedor->nombre,
            'promedio_evaluacion'   => round($promediosEvaluaciones ?? 0, 2),
            'promedio_general'      => round($periodicos?->avg_eval ?? 0, 2),
            'promedio_ncf'          => round($periodicos?->avg_ncf ?? 0, 2),
            'promedio_calidad'      => round($periodicos?->avg_calidad ?? 0, 2),
            'promedio_entrega'      => round($periodicos?->avg_entrega ?? 0, 2),
            'promedio_comunicacion' => round($periodicos?->avg_comunicacion ?? 0, 2),
            'incumplimientos'       => $incumplimientos,
            'total_incumplimientos' => array_sum($incumplimientos),
            'por_anio'              => $porAnio,
        ];
    }

    /**
     * Estadísticas de evaluaciones de proveedores del tenant.
     */
    public function stats(): array
    {
        $query = EvaluacionProveedor::withoutSoftDeletes();

        $totalEvaluaciones    = (clone $query)->count();
        $proveedoresCalificados    = (clone $query)->calificados()->count();
        $noCalificados            = (clone $query)->noCalificados()->count();
        $provisionales           = (clone $query)->provisionales()->count();

        // Promedio general de puntuaciones
        $promedioGeneral = EvaluacionProveedor::whereNotNull('total_puntuacion')
            ->avg('total_puntuacion');

        // Por estado
        $porEstado = EvaluacionProveedor::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado')
            ->toArray();

        // Proveedores únicos evaluados
        $proveedoresUnicos = EvaluacionProveedor::query()
            ->selectRaw('COUNT(DISTINCT proveedor_id) as total')
            ->value('total');

        // Evaluaciones periódicas
        $totalPeriodicas = EvaluacionPeriodicaProveedor::query()->count();
        $aprobadasPeriodicas = EvaluacionPeriodicaProveedor::query()
            ->where('estado', 'aprobado')
            ->count();

        // Incumplimientos
        $incumplimientosAbiertos = IncumplimientoProveedor::query()
            ->abiertos()->count();
        $incumplimientosGraves = IncumplimientoProveedor::query()
            ->where('gravedad', 'grave')->count();

        return [
            'total_evaluaciones'           => $totalEvaluaciones,
            'proveedores_calificados'      => $proveedoresCalificados,
            'proveedores_no_calificados'   => $noCalificados,
            'proveedores_provisionales'    => $provisionales,
            'proveedores_unicos'           => $proveedoresUnicos,
            'promedio_general'             => round($promedioGeneral ?? 0, 2),
            'total_evaluaciones_periodicas'=> $totalPeriodicas,
            'periodicas_aprobadas'         => $aprobadasPeriodicas,
            'incumplimientos_abiertos'     => $incumplimientosAbiertos,
            'incumplimientos_graves'       => $incumplimientosGraves,
            'por_estado'                   => $porEstado,
            'criterios_evaluacion'         => [
                'calidad'                 => 'Calidad',
                'precio'                  => 'Precio',
                'entrega_puntualidad'     => 'Entrega Puntualidad',
                'servicio_soporte'        => 'Servicio y Soporte',
                'cumplimiento_normas'     => 'Cumplimiento Normas',
            ],
        ];
    }
}
