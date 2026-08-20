<?php

namespace App\Http\Controllers;

use App\Models\ObjetivoCalidad;
use App\Services\GestionRiesgosService;
use Illuminate\Http\Request;

class ObjetivoCalidadController extends Controller
{
    protected GestionRiesgosService $riesgosService;

    public function __construct(
        protected ObjetivoCalidadService $service,
        GestionRiesgosService $riesgosService
    ) {
        $this->riesgosService = $riesgosService;
    }

    /**
     * Listar objetivos de calidad con filtro por periodo.
     */
    public function index(Request $request)
    {
        $search   = $this->dtSearch($request);
        $periodoInicio = $request->input('periodo_inicio');
        $periodoFin  = $request->input('periodo_fin');

        $query = ObjetivoCalidad::query()
            ->with(['creador', 'responsable', 'kpiAsociado'])
            ->where('estado', '!=', 'cerrado');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('titulo', 'like', "%{$search}%")
                  ->orWhere('indicador', 'like', "%{$search}%");
            });
        }

        if ($periodoInicio || $periodoFin) {
            $query->delPeriodo($periodoInicio, $periodoFin);
        }

        $objetivos = $query
            ->orderBy('periodo_fin', 'asc')
            ->paginate(20)
            ->withQueryString();

        $stats = $this->service->stats();

        return view('sgc.objetivos.index', compact('objetivos', 'stats', 'search', 'periodoInicio', 'periodoFin'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        $stats = $this->service->stats();
        $responsables = \App\Models\User::orderBy('name')->get(['id', 'name']);
        $estado = $this->service->stats()['estados'] ?? [];

        return view('sgc.objetivos.create', compact('stats', 'responsables'));
    }

    /**
     * Almacenar un nuevo objetivo de calidad.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'codigo'          => 'required|string|max:50|unique:objetivos_calidad,codigo',
                'titulo'          => 'required|string|max:255',
                'descripcion'     => 'nullable|string',
                'indicador'       => 'required|string|max:255',
                'meta'            => 'required|numeric|min:0',
                'unidad'          => 'nullable|string|max:20',
                'periodo_inicio'  => 'required|date',
                'periodo_fin'     => 'required|date|after_or_equal:periodo_inicio',
                'responsable_id'  => 'required|exists:users,id',
            ]);

            $this->service->crear($validated);

            return redirect()->route('sgc.objetivos.index')
                ->with('success', 'Objetivo de calidad creado exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_creacion', 'Error al crear el objetivo: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle del objetivo con mediciones y datos del gráfico.
     */
    public function show(ObjetivoCalidad $obj)
    {
        $obj->load(['responsable', 'creador', 'mediciones', 'kpiAsociado']);
        $stats  = $this->service->stats();
        $mediciones = $obj->mediciones()->orderBy('fecha', 'desc')->get();
        $porcentajeCumplimiento = $obj->cumplimiento;

        return view('sgc.objetivos.show', compact(
            'obj', 'stats', 'mediciones', 'porcentajeCumplimiento'
        ));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(ObjetivoCalidad $obj)
    {
        $obj->load(['responsable', 'creador']);
        $stats  = $this->service->stats();
        $responsables = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('sgc.objetivos.edit', compact('obj', 'stats', 'responsables'));
    }

    /**
     * Actualizar un objetivo de calidad.
     */
    public function update(Request $request, ObjetivoCalidad $obj)
    {
        try {
            $validated = $request->validate([
                'titulo'          => 'required|string|max:255',
                'descripcion'     => 'nullable|string',
                'indicador'       => 'required|string|max:255',
                'meta'            => 'required|numeric|min:0',
                'unidad'          => 'nullable|string|max:20',
                'periodo_inicio'  => 'required|date',
                'periodo_fin'     => 'required|date|after_or_equal:periodo_inicio',
                'responsable_id'  => 'required|exists:users,id',
                'estado'          => 'required|in:en_curso,cumplido,no_cumplido,cerrado',
            ]);

            $this->service->actualizar($obj, $validated);

            return redirect()->route('sgc.objetivos.show', $obj)
                ->with('success', 'Objetivo actualizado exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_actualizacion', 'Error al actualizar el objetivo: ' . $e->getMessage());
        }
    }

    /**
     * Registrar una medición del objetivo.
     */
    public function registrarMedicion(ObjetivoCalidad $obj, Request $request)
    {
        try {
            $validated = $request->validate([
                'valor'         => 'required|numeric|min:0',
                'observaciones' => 'nullable|string|max:500',
            ]);

            $this->service->registrarMedicion($obj, $validated['valor'], $validated['observaciones'] ?? '');

            return back()->with('success', 'Medición registrada exitosamente. Cumplimiento actualizado.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_medicion', 'Error al registrar la medición: ' . $e->getMessage());
        }
    }

    /**
     * Endpoint API para estadísticas de objetivos.
     */
    public function stats()
    {
        return response()->json($this->service->stats());
    }
}
