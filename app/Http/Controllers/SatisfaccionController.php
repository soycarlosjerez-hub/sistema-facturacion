<?php

namespace App\Http\Controllers;

use App\Models\EncuestaSatisfaccion;
use App\Models\ReclamoCliente;
use App\Services\SatisfaccionClienteService;
use Illuminate\Http\Request;

class SatisfaccionController extends Controller
{
    public function __construct(
        protected SatisfaccionClienteService $service
    ) {}

    /**
     * Listar encuestas de satisfacción.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $estado = $request->input('estado');

        $query = EncuestaSatisfaccion::query()
            ->with(['creador', 'preguntas', 'respuestas']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($estado) {
            $query->where('estado', $estado);
        }

        $encuestas = $query
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats = $this->service->stats();

        return view('sgc.satisfaccion.index', compact('encuestas', 'stats', 'search', 'estado'));
    }

    /**
     * Mostrar formulario de creación de encuesta.
     */
    public function create()
    {
        $stats = $this->service->stats();
        $estados = ['borrador' => 'Borrador', 'activa' => 'Activa', 'cerrada' => 'Cerrada'];

        return view('sgc.satisfaccion.encuestas.create', compact('stats', 'estados'));
    }

    /**
     * Almacenar una encuesta con preguntas.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'titulo'          => 'required|string|max:255',
                'descripcion'     => 'nullable|string',
                'estado'          => 'required|in:borrador,activa,cerrada',
                'inicio'          => 'required|date',
                'fin'             => 'required|date|after_or_equal:inicio',
                'preguntas'       => 'required|array|min:1',
                'preguntas.*.titulo' => 'required|string|max:500',
                'preguntas.*.tipo'    => 'required|in:escala_5,escala_4,escala_10,texto_libre',
                'preguntas.*.obligatoria' => 'boolean',
            ]);

            $this->service->crearEncuesta($validated);

            return redirect()->route('sgc.satisfaccion.index')
                ->with('success', 'Encuesta creada exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_creacion', 'Error al crear la encuesta: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle de la encuesta con estadísticas.
     */
    public function show(EncuestaSatisfaccion $encuesta)
    {
        $encuesta->load(['creador', 'preguntas.respuestas', 'respuestas']);
        $stats = $this->service->stats();

        return view('sgc.satisfaccion.encuestas.show', compact('encuesta', 'stats'));
    }

    /**
     * Mostrar formulario de respuesta de encuesta.
     */
    public function responderEncuesta(int $id)
    {
        $encuesta = EncuestaSatisfaccion::with('preguntas')
            ->findOrFail($id);

        return view('sgc.satisfaccion.encuestas.responder', compact('encuesta'));
    }

    /**
     * Guardar respuestas a una encuesta.
     */
    public function responderEncuestaStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'encuesta_id'       => 'required|exists:encuestas_satisfaccion,id',
                'respuestas'        => 'required|array',
            ]);

            $encuesta = EncuestaSatisfaccion::findOrFail($validated['encuesta_id']);
            $resultado = $this->service->registrarRespuesta($encuesta, $validated['respuestas']);

            return redirect()->route('sgc.satisfaccion.show', $encuesta)
                ->with('success', "Encuesta respondida. Puntuación: {$resultado['pct']}%");
        } catch (\Throwable $e) {
            return back()->withErrors('error_respuesta', 'Error al registrar la respuesta: ' . $e->getMessage());
        }
    }

    // -- Reclamos --

    /**
     * Listar reclamos de clientes.
     */
    public function reclamos(Request $request)
    {
        $search = $request->input('search');
        $estado = $request->input('estado');

        $query = ReclamoCliente::query()
            ->with(['cliente', 'asignadoA', 'creador']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%")
                  ->orWhere_has('cliente', fn ($q2) => $q2->where('nombre', 'like', "%{$search}%"));
            });
        }

        if ($estado) {
            $query->where('estado', $estado);
        }

        $reclamos = $query
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats = $this->service->stats();

        return view('sgc.satisfaccion.reclamos.index', compact('reclamos', 'stats', 'search', 'estado'));
    }

    /**
     * Mostrar formulario para crear un reclamo.
     */
    public function reclamoCreate()
    {
        $clientes  = \App\Models\Cliente::orderBy('nombre')->get(['id', 'nombre']);
        $estados   = ['abierto' => 'Abierto', 'en_tramite' => 'En Trámite', 'resuelto' => 'Resuelto', 'cerrado' => 'Cerrado'];
        $tipos     = ['reclamo' => 'Reclamo', 'queja' => 'Queja', 'sugerencia' => 'Sugerencia', 'cumpliment' => 'Cumplido'];
        $canales   = ['web' => 'Sitio Web', 'telefono' => 'Teléfono', 'email' => 'Email', 'presencial' => 'Presencial', 'redes_sociales' => 'Redes Sociales'];

        return view('sgc.satisfaccion.reclamos.create', compact('clientes', 'estados', 'tipos', 'canales'));
    }

    /**
     * Crear un reclamo.
     */
    public function reclamoStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'cliente_id'      => 'required|exists:clientes,id',
                'descripcion'     => 'required|string',
                'tipo'            => 'required|in:reclamo,queja,sugerencia,cumpliment',
                'canal'           => 'required|in:web,telefono,email,presencial,redes_sociales',
                'estado'          => 'required|in:abierto,en_tramite,resuelto,cerrado',
            ]);

            $this->service->crearReclamo($validated);

            return redirect()->route('sgc.satisfaccion.reclamos')
                ->with('success', 'Reclamo registrado exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_creacion', 'Error al registrar el reclamo: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle del reclamo con resolución.
     */
    public function reclamoShow(ReclamoCliente $reclamo)
    {
        $reclamo->load(['cliente', 'asignadoA', 'creador']);
        $stats = $this->service->stats();

        return view('sgc.satisfaccion.reclamos.show', compact('reclamo', 'stats'));
    }

    /**
     * Actualizar un reclamo.
     */
    public function reclamoUpdate(int $id, Request $request)
    {
        try {
            $reclamo = ReclamoCliente::findOrFail($id);
            $validated = $request->validate([
                'responsable_id'           => 'nullable|exists:users,id',
                'estado'                   => 'required|in:abierto,en_tramite,resuelto,cerrado',
                'resolucion'               => 'nullable|string',
                'satisfaccion_resolucion'  => 'nullable|integer|min:1|max:5',
            ]);

            $data = $validated;

            // Asignar responsable si se proporciona
            if (isset($data['responsable_id'])) {
                $this->service->asignarReclamo($reclamo, $data['responsable_id']);
                unset($data['responsable_id']);
            }

            // Resolver si se proporciona resolución
            if (isset($data['resolucion'])) {
                $resolvedReclamo = $this->service->resolverReclamo($reclamo, $data['resolucion']);
                unset($data['resolucion']);
            }

            // Actualizar campos restantes
            if (isset($data['satisfaccion_resolucion']) && is_numeric($data['satisfaccion_resolucion'])) {
                $reclamo->setSatisfaccion((int) $data['satisfaccion_resolucion']);
                unset($data['satisfaccion_resolucion']);
            }

            if (isset($data['estado'])) {
                $reclamo->estado = $data['estado'];
                unset($data['estado']);
            }

            $reclamo->saveQuietly();

            return redirect()->route('sgc.satisfaccion.reclamos.show', $reclamo)
                ->with('success', 'Reclamo actualizado exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_actualizacion', 'Error al actualizar el reclamo: ' . $e->getMessage());
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
