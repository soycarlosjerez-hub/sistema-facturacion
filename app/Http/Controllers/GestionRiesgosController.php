<?php

namespace App\Http\Controllers;

use App\Models\Riesgo;
use App\Services\GestionRiesgosService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GestionRiesgosController extends Controller
{
    public function __construct(
        protected GestionRiesgosService $service
    ) {}

    /**
     * Listar riesgos con filtros y paginación.
     */
    public function index(Request $request)
    {
        $buscar   = $request->input('search');
        $estado   = $request->input('estado');
        $clasificacion  = $request->input('clasificacion');
        $area     = $request->input('area');

        $query = Riesgo::query()
            ->with(['creador', 'responsable', 'procesoAfectado']);

        if ($buscar) {
            $query->where(function ($q) use ($buscar) {
                $q->where('descripcion', 'like', "%{$buscar}%")
                  ->orWhere('titulo', 'like', "%{$buscar}%")
                  ->orWhere('area', 'like', "%{$buscar}%")
                  ->orWhere_has('procesoAfectado', fn ($q2) => $q2->where('nombre', 'like', "%{$buscar}%"));
            });
        }

        if ($estado) {
            $query->where('estado', $estado);
        }

        if ($clasificacion) {
            $query->where('clasificacion', $clasificacion);
        }

        if ($area) {
            $query->where('area', 'like', "%{$area}%");
        }

        $riesgos = $query
            ->orderBy('nivel', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats  = $this->service->stats();
        $areas  = $this->getAreas();

        return view('sgc.riesgos.index', compact('riesgos', 'stats', 'areas', 'buscar', 'estado', 'clasificacion', 'area'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        $stats = $this->service->stats();
        $areas = $this->getAreas();
        $procesoAfectado = $this->processosOptions();

        return view('sgc.riesgos.create', compact('stats', 'areas', 'procesoAfectado'));
    }

    /**
     * Almacenar un nuevo riesgo.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'titulo'        => 'required|string|max:255',
                'descripcion'   => 'required|string',
                'area'          => 'nullable|string|max:100',
                'probabilidad'  => 'required|integer|min:1|max:5',
                'impacto'       => 'required|integer|min:1|max:5',
                'responsable_id'=> 'required|exists:users,id',
                'plan_accion'   => 'nullable|string',
                'fecha_limite'  => 'nullable|date',
            ]);

            $this->service->crear($validated);

            return redirect()->route('sgc.riesgos.index')
                ->with('success', 'Riesgo creado exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_creacion', 'Error al crear el riesgo: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle de un riesgo.
     */
    public function show(Riesgo $riesgo)
    {
        $riesgo->load(['creador', 'responsable', 'procesoAfectado', 'tratamientos']);
        $stats  = $this->service->stats();
        $residuo = $this->service->calcularRiesgoResidual($riesgo);
        $estimacion = $this->service->estimarProbabilidadImpacto($riesgo);

        return view('sgc.riesgos.show', compact('riesgo', 'stats', 'residuo', 'estimacion'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Riesgo $riesgo)
    {
        $riesgo->load(['creador', 'responsable', 'procesoAfectado']);
        $stats  = $this->service->stats();
        $areas  = $this->getAreas();
        $procesoAfectado = $this->processosOptions();

        return view('sgc.riesgos.edit', compact('riesgo', 'stats', 'areas', 'procesoAfectado'));
    }

    /**
     * Actualizar un riesgo.
     */
    public function update(Request $request, Riesgo $riesgo)
    {
        try {
            $validated = $request->validate([
                'titulo'        => 'required|string|max:255',
                'descripcion'   => 'required|string',
                'area'          => 'nullable|string|max:100',
                'probabilidad'  => 'required|integer|min:1|max:5',
                'impacto'       => 'required|integer|min:1|max:5',
                'responsable_id'=> 'required|exists:users,id',
                'plan_accion'   => 'nullable|string',
                'fecha_limite'  => 'nullable|date',
                'estado'        => 'required|in:identificado,en_tratamiento,monitoreo,tratado,cerrado',
            ]);

            $this->service->actualizar($riesgo, $validated);

            return redirect()->route('sgc.riesgos.show', $riesgo)
                ->with('success', 'Riesgo actualizado exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_actualizacion', 'Error al actualizar el riesgo: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un riesgo (soft delete).
     */
    public function eliminar(Riesgo $riesgo)
    {
        try {
            $this->service->eliminar($riesgo);
            return back()->with('success', 'Riesgo eliminado correctamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al eliminar el riesgo: ' . $e->getMessage());
        }
    }

    /**
     * Endpoint API para estadísticas de riesgos.
     */
    public function stats()
    {
        return response()->json($this->service->stats());
    }

    /**
     * Obtener lista de áreas desde las estadísticas del servicio.
     */
    private function getAreas(): array
    {
        return $this->service->stats()['por_area'] ?? [];
    }

    /**
     * Obtener opciones de procesos afectados.
     */
    private function procesosOptions(): Collection
    {
        return \App\Models\Proceso::orderBy('nombre')->get(['id', 'nombre']);
    }
}
