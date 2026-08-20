<?php

namespace App\Http\Controllers;

use App\Models\Capacitacion;
use App\Models\CompetenciaEmpleado;
use App\Services\GestionCapacitacionService;
use Illuminate\Http\Request;

class CapacitacionController extends Controller
{
    public function __construct(
        protected GestionCapacitacionService $service
    ) {}

    /**
     * Listar todas las capacitaciones.
     */
    public function index(Request $request)
    {
        $search   = $request->input('search');
        $estado   = $request->input('estado');
        $modalidad = $request->input('modalidad');

        $query = Capacitacion::query()
            ->with(['instructorMod', 'participantes.usuario']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($estado) {
            $query->where('estado', $estado);
        }

        if ($modalidad) {
            $query->where('modalidad', $modalidad);
        }

        $capacitaciones = $query
            ->orderBy('fecha', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats = $this->service->stats();

        return view('sgc.capacitaciones.index', compact('capacitaciones', 'stats', 'search', 'estado', 'modalidad'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        $stats = $this->service->stats();
        $instructores = \App\Models\User::orderBy('name')->get(['id', 'name']);
        $estados  = ['programada' => 'Programada', 'en_curso' => 'En Curso', 'completada' => 'Completada', 'cancelada' => 'Cancelada'];

        return view('sgc.capacitaciones.create', compact('stats', 'instructores', 'estados'));
    }

    /**
     * Almacenar una nueva capacitación.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'titulo'             => 'required|string|max:255',
                'descripcion'        => 'nullable|string',
                'instructor_id'      => 'required|exists:users,id',
                'modalidad'          => 'required|in:presencial,virtual',
                'fecha'              => 'required|date',
                'hora_inicio'        => 'required|date_format:H:i',
                'hora_fin'           => 'required|date_format:H:i|after:hora_inicio',
                'lugar'              => 'nullable|string|max:255',
                'duracion_horas'     => 'required|numeric|min:0.5',
                'temas'              => 'nullable|string',
                'estado'             => 'required|in:programada,en_curso,completada,cancelada',
                'participantes'      => 'nullable|array',
                'participantes.*'    => 'exists:users,id',
            ]);

            $data = $validated;
            if (isset($data['participantes'])) {
                $participantIds = $data['participantes'];
                unset($data['participantes']);
            } else {
                $participantIds = [];
            }

            $capacitacion = $this->service->crearCapacitacion($data);

            // Agregar participantes si se especificaron
            if (!empty($participantIds)) {
                foreach ($participantIds as $usuarioId) {
                    $this->service->agregarParticipante($capacitacion, $usuarioId);
                }
            }

            return redirect()->route('sgc.capacitaciones.index')
                ->with('success', 'Capacitación creada exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_creacion', 'Error al crear la capacitación: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle de la capacitación con participantes.
     */
    public function show(Capacitacion $cap)
    {
        $cap->load([
            'instructorMod',
            'participantes.usuario',
            'documentoSgc',
        ]);
        $stats = $this->service->stats();

        return view('sgc.capacitaciones.show', compact('cap', 'stats'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Capacitacion $cap)
    {
        $cap->load(['instructorMod', 'participantes.usuario']);

        $stats           = $this->service->stats();
        $instructores    = \App\Models\User::orderBy('name')->get(['id', 'name']);
        $usuarios        = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('sgc.capacitaciones.edit', compact('cap', 'stats', 'instructores', 'usuarios'));
    }

    /**
     * Actualizar la capacitación.
     */
    public function update(Request $request, Capacitacion $cap)
    {
        try {
            $validated = $request->validate([
                'titulo'       => 'required|string|max:255',
                'descripcion'  => 'nullable|string',
                'instructor_id' => 'required|exists:users,id',
                'modalidad'    => 'required|in:presencial,virtual',
                'fecha'        => 'required|date',
                'hora_inicio'  => 'required|date_format:H:i',
                'hora_fin'     => 'required|date_format:H:i|after:hora_inicio',
                'lugar'        => 'nullable|string|max:255',
                'duracion_horas' => 'required|numeric|min:0.5',
                'temas'        => 'nullable|string',
                'estado'       => 'required|in:programada,en_curso,completada,cancelada',
            ]);

            $this->service->actualizarCapacitacion($cap, $validated);

            return redirect()->route('sgc.capacitaciones.show', $cap)
                ->with('success', 'Capacitación actualizada exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_actualizacion', 'Error al actualizar la capacitación: ' . $e->getMessage());
        }
    }

    /**
     * Agregar un participante a la capacitación.
     */
    public function agregarParticipante(Capacitacion $cap, Request $request)
    {
        try {
            $validated = $request->validate([
                'usuario_id' => 'required|exists:users,id',
            ]);

            $this->service->agregarParticipante($cap, $validated['usuario_id']);

            return back()->with('success', 'Participante agregado exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_participante', 'Error al agregar participante: ' . $e->getMessage());
        }
    }

    /**
     * Remover un participante de la capacitación.
     */
    public function removerParticipante(Capacitacion $cap, int $partId)
    {
        try {
            $this->service->removerParticipante($cap, $partId);
            return back()->with('success', 'Participante removido correctamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al remover participante: ' . $e->getMessage());
        }
    }

    /**
     * Registrar asistencia de múltiples participantes.
     */
    public function registrarAsistencia(Capacitacion $cap, Request $request)
    {
        try {
            $validated = $request->validate([
                'participacion' => 'required|array',
                'participacion.*.part_id' => 'required|exists:participante_capacitacion,id',
                'participacion.*.puntuacion' => 'required|integer|min:0|max:100',
            ]);

            $count = 0;
            foreach ($validated['participacion'] as $item) {
                $participante = $this->service->registrarAsistencia($cap, $item['part_id'], $item['puntuacion']);
                if ($participante && $item['puntuacion'] >= 70) {
                    // Si aprobó, otorgar certificado automáticamente
                    $this->service->otorgarCertificado($participante);
                }
                $count++;
            }

            return back()->with('success', "Asistencia registrada de {$count} participante(s).");
        } catch (\Throwable $e) {
            return back()->withErrors('error_asistencia', 'Error al registrar asistencia: ' . $e->getMessage());
        }
    }

    /**
     * Otorgar certificado a un participante.
     */
    public function otorgarCertificado(int $partId)
    {
        try {
            $participante = \App\Models\ParticipanteCapacitacion::with('capacitacion')->find($partId);
            if (!$participante) {
                return back()->with('error', 'Participante no encontrado.');
            }

            $this->service->otorgarCertificado($participante);

            return back()->with('success', 'Certificado otorgado exitosamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al otorgar certificado: ' . $e->getMessage());
        }
    }

    /**
     * Listar competencias de empleados (skill matrix).
     */
    public function competencias()
    {
        $competencias = CompetenciaEmpleado::query()
            ->with(['usuario', 'evaluadoPor'])
            ->orderBy('fecha_evaluacion', 'desc')
            ->paginate(20);

        $stats = $this->service->stats();

        return view('sgc.capacitaciones.competencias', compact('competencias', 'stats'));
    }

    /**
     * Endpoint API para estadísticas de capacitaciones.
     */
    public function stats()
    {
        return response()->json($this->service->stats());
    }
}
