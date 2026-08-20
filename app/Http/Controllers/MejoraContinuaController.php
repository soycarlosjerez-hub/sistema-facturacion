<?php

namespace App\Http\Controllers;

use App\Models\MejoraContinua;
use App\Services\MejoraContinuaService;
use Illuminate\Http\Request;

class MejoraContinuaController extends Controller
{
    public function __construct(
        protected MejoraContinuaService $service
    ) {}

    /**
     * Listar mejoras continuas.
     */
    public function index(Request $request)
    {
        $search  = $this->dtSearch($request);
        $fase    = $request->input('fase');
        $origen  = $request->input('origen');

        $query = MejoraContinua::query()
            ->with(['responsable', 'riesgo', 'nc', 'auditoria']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhere('titulo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($fase) {
            $query->where('fase', $fase);
        }

        if ($origen) {
            $query->where('origen', $origen);
        }

        $mejoras  = $query
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats  = $this->service->stats();

        return view('sgc.mejora_continua.index', compact('mejoras', 'stats', 'search', 'fase', 'origen'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        $stats  = $this->service->stats();
        $responsables = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('sgc.mejora_continua.create', compact('stats', 'responsables'));
    }

    /**
     * Crear una mejora continua.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'titulo'          => 'required|string|max:255',
                'descripcion'     => 'required|string',
                'responsable_id'  => 'required|exists:users,id',
                'prioridad'       => 'required|in:baja,media,alta,urgente',
                'impacto'        => 'required|in:bajo,moderado,alto',
                'ahorro_estimado' => 'nullable|numeric|min:0',
                'costo_estimado'  => 'nullable|numeric|min:0',
                'beneficios_logrados' => 'nullable|string',
                'riesgo_id'       => 'nullable|exists:riesgos,id',
                'nc_id'           => 'nullable|exists:no_conformidades,id',
                'auditoria_id'    => 'nullable|exists:auditorias_internas,id',
            ]);

            $this->service->crear($validated);

            return redirect()->route('sgc.mejora_continua.index')
                ->with('success', 'Mejora continua creada exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_creacion', 'Error al crear la mejora: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle de la mejora.
     */
    public function show(MejoraContinua $mejora)
    {
        $mejora->load(['responsable', 'riesgo', 'nc', 'auditoria', 'propuestas']);
        $stats  = $this->service->stats();

        return view('sgc.mejora_continua.show', compact('mejora', 'stats'));
    }

    /**
     * Listar propuestas de mejora.
     */
    public function propuestas(Request $request)
    {
        $search = $this->dtSearch($request);
        $estado = $request->input('estado');

        $query = \App\Models\PropuestaMejora::query()
            ->with(['autor', 'mejoraContinua']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($estado) {
            $query->where('estado', $estado);
        }

        $propuestas = $query
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats  = $this->service->stats();

        return view('sgc.mejora_continua.propuestas.index', compact('propuestas', 'stats', 'search', 'estado'));
    }

    /**
     * Mostrar formulario de creación de propuesta.
     */
    public function crearPropuesta()
    {
        $stats  = $this->service->stats();
        $mejoras = MejoraContinua::where('estado', '!=', 'cerrado')->orderBy('numero')->get(['id', 'numero', 'titulo']);

        return view('sgc.mejora_continua.propuestas.create', compact('stats', 'mejoras'));
    }

    /**
     * Guardar una propuesta de mejora.
     */
    public function guardarPropuesta(Request $request)
    {
        try {
            $validated = $request->validate([
                'titulo'       => 'required|string|max:255',
                'descripcion'  => 'required|string',
                'mejora_id'    => 'nullable|exists:mejoras_continuas,id',
            ]);

            $this->service->crearPropuesta($validated);

            return redirect()->route('sgc.mejora_continua.propuestas')
                ->with('success', 'Propuesta creada exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_creacion', 'Error al crear la propuesta: ' . $e->getMessage());
        }
    }

    /**
     * Aprobar una propuesta y vincularla a una mejora continua.
     */
    public function aprobarPropuesta(int $propuestaId, Request $request)
    {
        try {
            $validated = $request->validate([
                'mejora_id' => 'required|exists:mejoras_continuas,id',
            ]);

            $propuesta = \App\Models\PropuestaMejora::findOrFail($propuestaId);

            $this->service->aprobarPropuesta($propuesta, MejoraContinua::findOrFail($validated['mejora_id']));

            return redirect()->route('sgc.mejora_continua.propuestas')
                ->with('success', 'Propuesta aprobada y vinculada a la mejora.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_aprobar', 'Error al aprobar la propuesta: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar una propuesta.
     */
    public function rechazarPropuesta(int $propuestaId)
    {
        try {
            $propuesta = \App\Models\PropuestaMejora::findOrFail($propuestaId);
            $this->service->rechazarPropuesta($propuesta);

            return back()->with('success', 'Propuesta rechazada.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al rechazar la propuesta: ' . $e->getMessage());
        }
    }

    /**
     * Marcar una mejora como completada.
     */
    public function completar(MejoraContinua $mejora)
    {
        try {
            $this->service->completar($mejora);
            return back()->with('success', 'Mejora marcada como completada.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al completar la mejora: ' . $e->getMessage());
        }
    }

    /**
     * Cerrar una mejora (después de verificación).
     */
    public function cerrar(MejoraContinua $mejora)
    {
        try {
            $this->service->cerrar($mejora);
            return back()->with('success', 'Mejora cerrada exitosamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al cerrar la mejora: ' . $e->getMessage());
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
