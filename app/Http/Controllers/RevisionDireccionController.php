<?php

namespace App\Http\Controllers;

use App\Models\RevisionDireccion;
use App\Models\AsistenteRevisionDireccion;
use App\Services\RevisionDireccionService;
use Illuminate\Http\Request;

class RevisionDireccionController extends Controller
{
    public function __construct(
        protected RevisionDireccionService $service
    ) {}

    /**
     * Listar revisiones de dirección.
     */
    public function index(Request $request)
    {
        $search = $this->dtSearch($request);
        $estado = $request->input('estado');
        $tipo   = $request->input('tipo');

        $query = RevisionDireccion::query()
            ->with(['creador', 'asistentes.usuario', 'entradas', 'salidas']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhere('tema', 'like', "%{$search}%")
                  ->orWhere('resumen', 'like', "%{$search}%");
            });
        }

        if ($estado) {
            $query->where('estado', $estado);
        }

        if ($tipo) {
            $query->where('tipo', $tipo);
        }

        $revisiones = $query
            ->orderBy('fecha', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats = $this->service->stats();

        return view('sgc.revision_direccion.index', compact('revisiones', 'stats', 'search', 'estado', 'tipo'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        $stats  = $this->service->stats();
        $usuarios = \App\Models\User::orderBy('name')->get(['id', 'name']);
        $estados = ['programada' => 'Programada', 'en_ejecucion' => 'En Ejecución', 'completada' => 'Completada'];
        $tipos   = ['programada' => 'Programada', 'extraordinaria' => 'Extraordinaria', 'trimestral' => 'Trimestral', 'anual' => 'Anual'];

        return view('sgc.revision_direccion.create', compact('stats', 'usuarios', 'estados', 'tipos'));
    }

    /**
     * Almacenar una nueva revisión de dirección.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tema'           => 'required|string|max:255',
                'descripcion'    => 'nullable|string',
                'fecha'          => 'required|date',
                'hora_inicio'    => 'required|date_format:H:i',
                'hora_fin'       => 'required|date_format:H:i|after:hora_inicio',
                'lugar'          => 'nullable|string|max:255',
                'tipo'           => 'required|in:programada,extraordinaria,trimestral,anual',
                'estado'         => 'required|in:programada,en_ejecucion,completada',
                'duracion_horas' => 'required|numeric|min:0.5|max:12',
            ]);

            $this->service->programarReunion($validated);

            return redirect()->route('sgc.revision_direccion.index')
                ->with('success', 'Revisión de dirección programada exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_creacion', 'Error al programar la revisión: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle de la revisión con entradas, salidas y asistentes.
     */
    public function show(RevisionDireccion $revision)
    {
        $revision->load(['creador', 'asistentes', 'entradas', 'salidas.responsable', 'salidas']);
        $stats    = $this->service->stats();
        $acta     = $this->service->generarActa($revision);
        $usuarios = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('sgc.revision_direccion.show', compact('revision', 'stats', 'acta', 'usuarios'));
    }

    /**
     * Agregar un asistente a la revisión.
     */
    public function addAsistente(RevisionDireccion $revision, Request $request)
    {
        try {
            $validated = $request->validate([
                'usuario_id' => 'required|exists:users,id',
            ]);

            $this->service->agregarAsistente($revision, $validated['usuario_id']);

            return back()->with('success', 'Asistente agregado exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_asistente', 'Error al agregar asistente: ' . $e->getMessage());
        }
    }

    /**
     * Remover un asistente de la revisión.
     */
    public function removeAsistente(RevisionDireccion $revision, int $asId)
    {
        try {
            $this->service->removerAsistente($revision, $asId);
            return back()->with('success', 'Asistente removido correctamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al remover asistente: ' . $e->getMessage());
        }
    }

    /**
     * Agregar una entrada (punto de agenda) a la revisión.
     */
    public function addEntrada(RevisionDireccion $revision, Request $request)
    {
        try {
            $validated = $request->validate([
                'tipo'       => 'required|in:informe_sgc:auditoria_anterior:objetivos:consecuencias:no_conformidades:oportunidades:mejoras:otro',
                'contenido'  => 'required|string',
                'documento'  => 'nullable|string|max:255',
            ]);

            $this->service->agregarEntrada($revision, $validated);

            return back()->with('success', 'Entrada de agenda agregada exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_entrada', 'Error al agregar entrada: ' . $e->getMessage());
        }
    }

    /**
     * Agregar una salida/acción a la revisión.
     */
    public function addSalida(RevisionDireccion $revision, Request $request)
    {
        try {
            $validated = $request->validate([
                'descripcion'    => 'required|string',
                'tipo_accion'    => 'required|in:implementacion:seguimiento:capacitacion:actualizacion:otro',
                'responsable_id' => 'required|exists:users,id',
                'fecha_limite'   => 'required|date',
            ]);

            $this->service->agregarSalida($revision, $validated);

            return back()->with('success', 'Salida/acción agregada exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_salida', 'Error al agregar salida: ' . $e->getMessage());
        }
    }

    /**
     * Registrar asistencia a la revisión (marcar que asistió).
     */
    public function registrarAsistencia(RevisionDireccion $revision, int $asistenteId, Request $request)
    {
        try {
            $validated = $request->validate([
                'asistio' => 'required|boolean',
            ]);

            $this->service->registrarAsistencia($revision, $asistenteId, $validated['asistio']);

            return back()->with('success', 'Asistencia marcada exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_asistencia', 'Error al marcar asistencia: ' . $e->getMessage());
        }
    }

    /**
     * Completar la revisión de dirección.
     */
    public function completarRevision(RevisionDireccion $revision)
    {
        try {
            $this->service->completarRevision($revision);
            return back()->with('success', 'Revisión de dirección completada exitosamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al completar la revisión: ' . $e->getMessage());
        }
    }

    /**
     * Generar acta PDF de la revisión.
     */
    public function generarActa(RevisionDireccion $revision)
    {
        $actaData = $this->service->generarActa($revision);

        $html = view('sgc.revision_direccion.partials.acta_pdf', compact('revision', 'actaData'))->render();

        return response($html, 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Endpoint API para estadísticas.
     */
    public function stats()
    {
        return response()->json($this->service->stats());
    }
}
