<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaInterna;
use App\Models\ProgramaAuditoria;
use App\Models\ChecklistaAuditoria;
use App\Services\AuditoriaInternaService;
use Illuminate\Http\Request;

class AuditoriaInternaController extends Controller
{
    public function __construct(
        protected AuditoriaInternaService $service
    ) {}

    /**
     * Listar todas las auditorías internas.
     */
    public function index(Request $request)
    {
        $search   = $request->input('search');
        $estado   = $request->input('estado');
        $area     = $request->input('area');

        $query = AuditoriaInterna::query()
            ->with(['programa', 'responsableAuditor', 'auditorJefe']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('area_auditar', 'like', "%{$search}%")
                  ->orWhere('objetivo', 'like', "%{$search}%");
            });
        }

        if ($estado) {
            $query->where('estado', $estado);
        }

        if ($area) {
            $query->where('area_auditar', 'like', "%{$area}%");
        }

        $auditorias = $query
            ->orderBy('fecha_programada', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats = $this->service->stats();

        return view('sgc.auditorias.index', compact('auditorias', 'stats', 'search', 'estado', 'area'));
    }

    /**
     * Listar programas de auditoría.
     */
    public function programas(Request $request)
    {
        $search = $request->input('search');
        $estado = $request->input('estado');

        $query = ProgramaAuditoria::query()
            ->with(['creador'])
            ->orderBy('anio', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('tema', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($estado) {
            $query->where('estado', $estado);
        }

        $programas = $query->paginate(20)->withQueryString();
        $stats = $this->service->stats();

        return view('sgc.auditorias.programas.index', compact('programas', 'stats', 'search', 'estado'));
    }

    /**
     * Mostrar formulario de creación de programa de auditoría.
     */
    public function crearPrograma()
    {
        $stats  = $this->service->stats();
        $estados = ['programada' => 'Programada', 'en_curso' => 'En Curso', 'completada' => 'Completada'];
        $auditores = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('sgc.auditorias.programas.create', compact('stats', 'estados', 'auditores'));
    }

    /**
     * Guardar un programa de auditoría.
     */
    public function storePrograma(Request $request)
    {
        try {
            $validated = $request->validate([
                'tema'           => 'required|string|max:255',
                'descripcion'    => 'nullable|string',
                'anio'           => 'required|integer|min:2020|max:' . (now()->year + 1),
                'auditor_jefe_id'=> 'required|exists:users,id',
                'estado'         => 'required|in:programada,en_curso,completada',
                'fecha_inicio'   => 'required|date',
                'fecha_fin'      => 'required|date|after_or_equal:fecha_inicio',
            ]);

            $this->service->crearPrograma($validated);

            return redirect()->route('sgc.auditorias.programas')
                ->with('success', 'Programa de auditoría creado exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_creacion', 'Error al crear el programa: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle de una auditoría con checklists, hallazgos, informe.
     */
    public function show(AuditoriaInterna $auditoria)
    {
        $auditoria->load([
            'programa', 'responsableAuditor', 'auditorJefe',
            'checklistItems', 'hallazgos',
            'ncs', 'ncs.causa', 'ncs.accionCorrectiva',
        ]);

        $stats  = $this->service->stats();
        $informe = $this->service->generarInforme($auditoria);

        // Generar PDF del informe si se solicita
        if (request()->boolean('generate_report')) {
            return $this->generateInformePDF($auditoria, $informe);
        }

        return view('sgc.auditorias.show', compact('auditoria', 'stats', 'informe'));
    }

    /**
     * Agregar un item al checklist de la auditoría.
     */
    public function agregarChecklistItem(AuditoriaInterna $auditoria, Request $request)
    {
        try {
            $validated = $request->validate([
                'descripcion'    => 'required|string|max:500',
                'criterio'       => 'nullable|string|max:255',
                'orden'          => 'nullable|integer|min:1',
            ]);

            // Reordenar si se proporciona
            if (isset($validated['orden'])) {
                // Insertar en posición específica
                $existingOrder = ChecklistaAuditoria::where('auditoria_interna_id', $auditoria->id)
                    ->where('orden', '>=', $validated['orden'])
                    ->get();
                foreach ($existingOrder as $item) {
                    $item->orden = $item->orden + 1;
                    $item->saveQuietly();
                }
            }

            unset($validated['orden']);
            $this->service->agregarChecklistItem($auditoria, $validated);

            return back()->with('success', 'Item de checklist agregado exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_checklist', 'Error al agregar item de checklist: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un item del checklist.
     */
    public function eliminarChecklistItem(ChecklistaAuditoria $item)
    {
        try {
            $auditoriaId = $item->auditoria_interna_id;
            $this->service->eliminarChecklistItem($item);

            return redirect()->route('sgc.auditorias.show', $auditoriaId)
                ->with('success', 'Item de checklist eliminado.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al eliminar item: ' . $e->getMessage());
        }
    }

    /**
     * Registrar un hallazgo en la auditoría.
     */
    public function registrarHallazgo(AuditoriaInterna $auditoria, Request $request)
    {
        try {
            $validated = $request->validate([
                'descripcion'  => 'required|string',
                'tipo'         => 'required|in:no_conforme_mayor:no_conforme_menor:observacion:conforme',
                'referencia'   => 'nullable|string|max:255',
                'nc_id'        => 'nullable|integer|exists:no_conformidades,id',
            ]);

            $this->service->registrarHallazgo($auditoria, $validated);

            return back()->with('success', 'Hallazgo registrado exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_hallazgo', 'Error al registrar el hallazgo: ' . $e->getMessage());
        }
    }

    /**
     * Iniciar una auditoría (cambia estado a en_curso).
     */
    public function iniciarAuditoria(AuditoriaInterna $auditoria)
    {
        try {
            $this->service->iniciarAuditoria($auditoria);
            return back()->with('success', 'Auditoría iniciada exitosamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al iniciar la auditoría: ' . $e->getMessage());
        }
    }

    /**
     * Completar una auditoría.
     */
    public function completarAuditoria(AuditoriaInterna $auditoria, Request $request)
    {
        try {
            $validated = $request->validate([
                'cumplimiento'          => 'required|numeric|min:0|max:100',
                'conclusiones'          => 'nullable|string',
            ]);

            $this->service->completarAuditoria($auditoria, $validated);

            return redirect()->route('sgc.auditorias.show', $auditoria)
                ->with('success', 'Auditoría completada exitosamente.');
        } catch (\Throwable $e) {
            return back()->withErrors('error_completar', 'Error al completar la auditoría: ' . $e->getMessage());
        }
    }

    /**
     * Generar informe PDF de la auditoría.
     */
    public function generarInforme(AuditoriaInterna $auditoria)
    {
        $informe = $this->service->generarInforme($auditoria);

        $html = view('sgc.auditorias.partials.informe_pdf', compact('auditoria', 'informe'))->render();

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
