<?php

namespace App\Http\Controllers;

use App\Models\NoConformidad;
use App\Services\NoConformidadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoConformidadController extends Controller
{
    public function __construct(
        protected NoConformidadService $service
    ) {}

    /**
     * Listar no conformidades.
     */
    public function index(Request $request)
    {
        $search   = $request->input('search');
        $estado   = $request->input('estado');
        $gravedad = $request->input('gravedad');
        $origen   = $request->input('origen');

        $query = NoConformidad::query()
            ->with(['creador', 'asignadoA', 'nc_causa', 'accionesCorrectivas', 'ncVerificacion']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%");
            });
        }

        if ($estado) {
            $query->where('estado', $estado);
        }

        if ($gravedad) {
            $query->where('gravedad', $gravedad);
        }

        if ($origen) {
            $query->where('origen', $origen);
        }

        $noConformidades = $query
            ->orderBy('fecha_identificacion', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats  = $this->service->stats();

        return view('sgc.no_conformidades.index', compact('noConformidades', 'stats', 'search', 'estado', 'gravedad', 'origen'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        $stats  = $this->service->stats();
        $responsables = \App\Models\User::orderBy('name')->get(['id', 'name']);
        $estados = ['abierta' => 'Abierta', 'en_analisis' => 'En Análisis', 'en_accion' => 'En Acción', 'verificando' => 'Verificando', 'cerrada' => 'Cerrada'];
        $gravedades = ['menor' => 'Menor', 'mayor' => 'Mayor', 'critico' => 'Crítico'];
        $origenes = ['auditoria' => 'Auditoría', 'reclamo' => 'Reclamo', 'proceso' => 'Proceso Interno', 'cliente' => 'Cliente', 'proveedor' => 'Proveedor', 'otro' => 'Otro'];

        return view('sgc.no_conformidades.create', compact('stats', 'responsables', 'estados', 'gravedades', 'origenes'));
    }

    /**
     * Crear una no conformidad.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'descripcion'        => 'required|string',
                'fecha_identificacion' => 'required|date',
                'area'               => 'required|string|max:100',
                'gravedad'           => 'required|in:menor,mayor,critico',
                'origen'             => 'required|in:auditoria,reclamo,proceso,cliente,proveedor,otro',
                'responsable_id'     => 'required|exists:users,id',
                'descripcion_problema' => 'nullable|string',
            ]);

            $this->service->crear($validated);

            return redirect()->route('sgc.no_conformidades.index')
                ->with('success', 'No conformidad creada exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_creacion', 'Error al crear la no conformidad: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle de la NC con causas, acciones y verificaciones.
     */
    public function show(NoConformidad $nc)
    {
        $nc->load([
            'creador', 'asignadoA',
            'ncCausa',
            'accionesCorrectivas' => function ($q) {
                $q->with(['responsable', 'verificacion' => function ($verif) {
                    $verif->with('verificadoPor');
                }]);
            },
            'verificacionVerificadora',
            'ncVerificacion',
        ]);
        $stats = $this->service->stats();
        $auditores = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('sgc.no_conformidades.show', compact('nc', 'stats', 'auditores'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(NoConformidad $nc)
    {
        $nc->load(['asignadoA']);
        $stats  = $this->service->stats();
        $responsables = \App\Models\User::orderBy('name')->get(['id', 'name']);
        $estados  = ['abierta' => 'Abierta', 'en_analisis' => 'En Análisis', 'en_accion' => 'En Acción', 'verificando' => 'Verificando', 'cerrada' => 'Cerrada'];

        return view('sgc.no_conformidades.edit', compact('nc', 'stats', 'responsables', 'estados'));
    }

    /**
     * Actualizar la no conformidad.
     */
    public function update(Request $request, NoConformidad $nc)
    {
        try {
            $validated = $request->validate([
                'descripcion'        => 'required|string',
                'area'               => 'required|string|max:100',
                'gravedad'           => 'required|in:menor,mayor,critico',
                'origen'             => 'required|in:auditoria,reclamo,proceso,cliente,proveedor,otro',
                'responsable_id'     => 'required|exists:users,id',
                'estado'             => 'required|in:abierta,en_analisis,en_accion,verificando,cerrada',
            ]);

            $this->service->actualizar($nc, $validated);

            return redirect()->route('sgc.no_conformidades.show', $nc)
                ->with('success', 'No conformidad actualizada exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_actualizacion', 'Error al actualizar la no conformidad: ' . $e->getMessage());
        }
    }

    /**
     * Registrar el análisis de causa de una NC.
     */
    public function analisisCausa(NoConformidad $nc, Request $request)
    {
        try {
            $validated = $request->validate([
                'metodologia' => 'required|string|max:100',
                'causa_raiz'  => 'required|string',
            ]);

            $this->service->registrarAnalisisCausa($nc, $validated['metodologia'], $validated['causa_raiz']);

            return back()->with('success', 'Análisis de causa registrado exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_causa', 'Error al registrar el análisis de causa: ' . $e->getMessage());
        }
    }

    /**
     * Crear una acción correctiva para la NC.
     */
    public function crearAccion(NoConformidad $nc, Request $request)
    {
        try {
            $validated = $request->validate([
                'descripcion'     => 'required|string',
                'responsable_id'  => 'required|exists:users,id',
                'fecha_limite'    => 'required|date',
                'costo_estimado'  => 'nullable|numeric|min:0',
                'estado'          => 'required|in:pendiente,en_curso,completada',
            ]);

            $this->service->crearAccionCorrectiva($nc, $validated);

            return back()->with('success', 'Acción correctiva creada exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_accion', 'Error al crear la acción correctiva: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar una acción correctiva.
     */
    public function actualizarAccion(Request $request, int $accionId)
    {
        try {
            $accion = \App\Models\AccionCorrectiva::findOrFail($accionId);
            $validated = $request->validate([
                'descripcion'     => 'required|string',
                'responsable_id'  => 'sometimes|exists:users,id',
                'fecha_limite'    => 'sometimes|date',
                'costo_estimado'  => 'nullable|numeric|min:0',
                'estado'          => 'required|in:pendiente,en_curso,completada',
            ]);

            $this->service->actualizarAccionCorrectiva($accion, $validated);

            return back()->with('success', 'Acción correctiva actualizada exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_accion_update', 'Error al actualizar la acción: ' . $e->getMessage());
        }
    }

    /**
     * Verificar la eficacia de una acción correctiva.
     */
    public function verificarEficacia(Request $request, int $accionId)
    {
        try {
            $accion = \App\Models\AccionCorrectiva::findOrFail($accionId);
            $validated = $request->validate([
                'resultado'  => 'required|in:eficaz,parcial,ineficaz,na',
                'evidencia'  => 'required|string',
            ]);

            $this->service->verificarEficacia(
                $accion,
                $validated['resultado'],
                $validated['evidencia'],
                \Illuminate\Support\Facades\Auth::id()
            );

            return back()->with('success', 'Eficacia de la acción verificada exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_verificacion', 'Error al verificar la eficacia: ' . $e->getMessage());
        }
    }

    /**
     * Cerrar la no conformidad.
     */
    public function cerrarNC(NoConformidad $nc)
    {
        try {
            $this->service->cerrarNoConformidad($nc);
            return back()->with('success', 'No conformidad cerrada exitosamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al cerrar la NC: ' . $e->getMessage());
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
