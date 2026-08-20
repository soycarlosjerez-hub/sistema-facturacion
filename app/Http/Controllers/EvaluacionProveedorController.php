<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Services\EvaluacionProveedorService;
use Illuminate\Http\Request;

class EvaluacionProveedorController extends Controller
{
    public function __construct(
        protected EvaluacionProveedorService $service
    ) {}

    /**
     * Listar evaluaciones de proveedores.
     */
    public function index(Request $request)
    {
        $search = $this->dtSearch($request);
        $estado = $request->input('estado');

        $query = \App\Models\EvaluacionProveedor::query()
            ->with(['proveedor', 'evaluadoPor', 'documentos']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where_has('proveedor', fn ($q2) => $q2->where('nombre', 'like', "%{$search}%"))
                  ->orWhere('codigo', 'like', "%{$search}%");
            });
        }

        if ($estado) {
            $query->where('estado', $estado);
        }

        $evaluaciones = $query
            ->orderBy('fecha', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats = $this->service->stats();

        return view('sgc.evaluaciones_proveedores.index',
            compact('evaluaciones', 'stats', 'search', 'estado')
        );
    }

    /**
     * Mostrar página de un proveedor con sus evaluaciones, rendimiento y evaluación periódica.
     */
    public function show(Proveedor $proveedor)
    {
        $evaluaciones = $proveedor->evaluaciones()
            ->orderBy('fecha', 'desc')
            ->limit(20)
            ->get();

        $periodicas = \App\Models\EvaluacionPeriodicaProveedor::where('proveedor_id', $proveedor->id)
            ->orderBy('periodo', 'desc')
            ->limit(5)
            ->get();

        $desempeno = $this->service->getDesempeno($proveedor);
        $incumplimientos = $proveedor->incumplimientos()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $stats = $this->service->stats();

        return view('sgc.evaluaciones_proveedores.show', compact(
            'proveedor', 'evaluaciones', 'periodicas', 'desempeno', 'incumplimientos', 'stats'
        ));
    }

    /**
     * Crear evaluación de un proveedor.
     */
    public function evaluarProveedor(Proveedor $proveedor, Request $request)
    {
        try {
            $validated = $request->validate([
                'fecha'           => 'required|date',
                'estado'          => 'required|in:provisional,aprobado,rechazado',
                'criterios'       => 'required|array',
                'criterios.*'     => 'numeric|min:0|max:100',
                'observaciones'   => 'nullable|string',
                'documento_evidencia' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            ]);

            $evaluacion = $this->service->evaluarProveedor($proveedor, $validated);

            return redirect()->route('sgc.evaluaciones_proveedores.show', $proveedor)
                ->with('success', "Evaluación del proveedor {$proveedor->nombre} creada exitosamente. Puntuación: {$evaluacion->total_puntuacion}");
        } catch (\Throwable $e) {
            return back()->withErrors('error_creacion', 'Error al evaluar el proveedor: ' . $e->getMessage());
        }
    }

    /**
     * Listar evaluaciones periódicas de proveedores.
     */
    public function periodico(Request $request)
    {
        $periodo = $request->input('periodo');
        $search  = $this->dtSearch($request);

        $query = \App\Models\EvaluacionPeriodicaProveedor::query()
            ->with(['proveedor', 'evaluadoPor']);

        if ($periodo) {
            $query->where('periodo', (int) $periodo);
        }

        if ($search) {
            $query->where_has('proveedor', fn ($q) => $q->where('nombre', 'like', "%{$search}%"));
        }

        $periodicas = $query
            ->orderBy('periodo', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats = $this->service->stats();

        return view('sgc.evaluaciones_proveedores.periodico.index', compact('periodicas', 'stats', 'periodo', 'search'));
    }

    /**
     * Mostrar formulario de creación de evaluación periódica.
     */
    public function newPeriodico()
    {
        $proveedores  = Proveedor::orderBy('nombre')->get(['id', 'nombre']);
        $estados = ['aprobado' => 'Aprobado', 'observaciones' => 'Con Observaciones', 'rechazado' => 'Rechazado'];

        return view('sgc.evaluaciones_proveedores.periodico.create', compact('proveedores', 'estados'));
    }

    /**
     * Guardar evaluación periódica.
     */
    public function savePeriodico(Request $request)
    {
        try {
            $validated = $request->validate([
                'proveedor_id'      => 'required|exists:proveedores,id',
                'periodo'           => 'required|integer|min:2020|max:' . (now()->year + 1),
                'evaluacion_general'=> 'required|numeric|min:0|max:100',
                'cumplimiento_ncf'  => 'required|numeric|min:0|max:100',
                'cumplimiento_calidad' => 'required|numeric|min:0|max:100',
                'tiempo_entrega'    => 'required|numeric|min:0|max:100',
                'comunicacion'      => 'required|numeric|min:0|max:100',
                'estado'            => 'required|in:aprobado,observaciones,rechazado',
                'observaciones'     => 'nullable|string',
            ]);

            $proveedor = Proveedor::findOrFail($validated['proveedor_id']);
            $evaluacion = $this->service->evaluarPeriodo($proveedor, $validated['periodo'], $validated);

            return redirect()->route('sgc.evaluaciones_proveedores.show', $proveedor)
                ->with('success', "Evaluación periódica del año {$validated['periodo']} creada exitosamente.");
        } catch (\Throwable $e) {
            return back()->withErrors('error_creacion', 'Error al crear la evaluación periódica: ' . $e->getMessage());
        }
    }

    /**
     * Endpoint API para estadísticas.
     */
    public function stats()
    {
        return response()->json($this->service->stats());
    }
}
