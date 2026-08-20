<?php

namespace App\Http\Controllers;

use App\Models\OrdenEmergencia;
use App\Models\Cliente;
use App\Services\OrdenEmergenciaService;
use App\Exports\ClimatizacionOrdenesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Http\Requests\StoreOrdenEmergenciaRequest;
use App\Http\Requests\UpdateOrdenEmergenciaRequest;

class OrdenEmergenciaController extends Controller
{
    public function __construct(private OrdenEmergenciaService $service) {}

    public function index(Request $request)
    {
        $query = OrdenEmergencia::query()
            ->with(['cliente', 'tecnico', 'creadoPor']);

        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo_falla')) {
            $query->where('tipo_falla', $request->tipo_falla);
        }
        if ($search = $this->dtSearch($request)) {
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$search}%"))
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($request->ajax() || $request->wantsJson()) {
            $total = (clone $query)->count();
            $ordenes = $query->latest()->paginate(request('length', 10), ['*'], 'page', (int) floor(request('start', 0) / max(1, (int) request('length', 10))) + 1);

            $rows = $ordenes->map(function ($o) {
                $badgeColor = match ($o->estado) {
                    'reportada' => 'danger',
                    'asignada' => 'warning',
                    'en_camino' => 'info',
                    'en_lugar' => 'primary',
                    'resuelta' => 'success',
                    'cerrada' => 'secondary',
                    default => 'secondary',
                };

                $slaOk = $o->slaCumplido();
                $slaBadge = $slaOk === null ? 'light' : ($slaOk ? 'success' : 'danger');

                return [
                    'DT_RowIndex' => $o->id,
                    'codigo' => $o->codigo,
                    'cliente' => $o->cliente ? $o->cliente->nombre : '-',
                    'prioridad' => OrdenEmergencia::PRIORIDADES[$o->prioridad] ?? $o->prioridad,
                    'tipo_falla' => OrdenEmergencia::TIPOS_FALLA[$o->tipo_falla] ?? $o->tipo_falla,
                    'direccion' => $o->direccion ?? '-',
                    'tecnico' => $o->tecnico ? $o->tecnico->name : '-',
                    'estado' => $o->estado,
                    'estado_label' => OrdenEmergencia::ESTADOS[$o->estado] ?? $o->estado,
                    'badge_color' => $badgeColor,
                    'sla_cumplido' => $slaOk,
                    'sla_badge' => $slaBadge,
                    'costo_final' => number_format($o->costo_final ?? 0, 2),
                    'acciones' => $this->getAccionesHtml($o),
                ];
            });

            return response()->json([
                'draw' => (int) request('draw', 1),
                'recordsTotal' => $total->count(),
                'recordsFiltered' => $total->count(),
                'data' => $rows,
            ]);
        }

        $ordenes = $query->latest()->paginate(20)->withQueryString();
        $clientes = Cliente::orderBy('nombre')->get();
        $criticas = OrdenEmergencia::criticas()->count();
        $activas = $query->whereIn('estado', ['reportada', 'asignada', 'en_camino', 'en_lugar'])->count();
        return view('climatizacion.emergencias.index', compact('ordenes', 'clientes', 'criticas', 'activas'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        return view('climatizacion.emergencias.create', compact('clientes'));
    }

    public function store(StoreOrdenEmergenciaRequest $request)
    {
        try {
            $orden = $this->service->crear($request->validated(), auth()->id());

            return redirect()->route('climatizacion.emergencias.show', $orden)
                ->with('success', 'Orden de emergencia creada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear orden de emergencia: ' . $e->getMessage());
        }
    }

    public function show(OrdenEmergencia $orden)
    {
        $orden->load(['cliente', 'tecnico', 'creadoPor']);
        return view('climatizacion.emergencias.show', compact('orden'));
    }

    public function edit(OrdenEmergencia $orden)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        return view('climatizacion.emergencias.edit', compact('orden', 'clientes'));
    }

    public function update(UpdateOrdenEmergenciaRequest $request, OrdenEmergencia $orden)
    {
        try {
            $this->service->actualizar($orden->id, $request->validated());

            return redirect()->route('climatizacion.emergencias.show', $orden)
                ->with('success', 'Orden de emergencia actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar orden de emergencia: ' . $e->getMessage());
        }
    }

    public function asignar(Request $request, OrdenEmergencia $orden)
    {
        try {
            $this->service->asignar($orden->id, $request->input('tecnico_id'));

            return redirect()->route('climatizacion.emergencias.show', $orden)
                ->with('success', 'Orden asignada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function advance(Request $request, OrdenEmergencia $orden)
    {
        try {
            $this->service->avanzarEstado($orden->id, $request->input('next_state'));

            return redirect()->route('climatizacion.emergencias.show', $orden)
                ->with('success', 'Estado avanzado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cerrar(OrdenEmergencia $orden)
    {
        try {
            $this->service->cerrar($orden->id);

            return redirect()->route('climatizacion.emergencias.index')
                ->with('success', 'Orden cerrada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(OrdenEmergencia $orden)
    {
        try {
            $this->service->eliminar($orden->id);
            return redirect()->route('climatizacion.emergencias.index')
                ->with('success', 'Orden de emergencia eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $query = OrdenEmergencia::query()->with(['cliente', 'tecnico']);
        $this->applyFilters($request, $query);
        return Excel::download(new ClimatizacionOrdenesExport($query), 'ordenes-emergencia.xlsx');
    }

    private function applyFilters(Request $request, $query)
    {
        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo_falla')) {
            $query->where('tipo_falla', $request->tipo_falla);
        }
        if ($search = $this->dtSearch($request)) {
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$search}%"));
            });
        }
    }

    private function getAccionesHtml(OrdenEmergencia $o): string
    {
        $html = '<div class="btn-group btn-group-sm">';
        $html .= '<a href="' . route('climatizacion.emergencias.show', $o) . '" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>';

        if ($o->estado === 'reportada') {
            $html .= '<form action="' . route('climatizacion.emergencias.asignar', $o) . '" method="POST" class="d-inline">';
            $html .= '@csrf @method("PATCH")';
            $html .= '<button type="submit" class="btn btn-outline-warning" title="Asignar"><i class="bi bi-person-check"></i></button>';
            $html .= '</form>';
        } elseif ($o->estado === 'asignada') {
            $html .= '<form action="' . route('climatizacion.emergencias.advance', $o) . '" method="POST" class="d-inline">';
            $html .= '@csrf @method("PATCH")';
            $html .= '<input type="hidden" name="next_state" value="en_camino">';
            $html .= '<button type="submit" class="btn btn-outline-info" title="En Camino"><i class="bi bi-car-front"></i></button>';
            $html .= '</form>';
        } elseif ($o->estado === 'en_camino') {
            $html .= '<form action="' . route('climatizacion.emergencias.advance', $o) . '" method="POST" class="d-inline">';
            $html .= '@csrf @method("PATCH")';
            $html .= '<input type="hidden" name="next_state" value="en_lugar">';
            $html .= '<button type="submit" class="btn btn-outline-primary" title="En Lugar"><i class="bi bi-geo-alt"></i></button>';
            $html .= '</form>';
        } elseif ($o->estado === 'en_lugar') {
            $html .= '<form action="' . route('climatizacion.emergencias.advance', $o) . '" method="POST" class="d-inline">';
            $html .= '@csrf @method("PATCH")';
            $html .= '<input type="hidden" name="next_state" value="resuelta">';
            $html .= '<button type="submit" class="btn btn-outline-success" title="Resolver"><i class="bi bi-check-lg"></i></button>';
            $html .= '</form>';
        } elseif ($o->estado === 'resuelta') {
            $html .= '<form action="' . route('climatizacion.emergencias.cerrar', $o) . '" method="POST" class="d-inline">';
            $html .= '@csrf @method("PATCH")';
            $html .= '<button type="submit" class="btn btn-outline-secondary" title="Cerrar"><i class="bi bi-lock"></i></button>';
            $html .= '</form>';
        }

        if (!in_array($o->estado, ['cerrada'])) {
            $html .= '<a href="' . route('climatizacion.emergencias.edit', $o) . '" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>';
        }

        if ($o->estado === 'reportada') {
            $html .= '<form action="' . route('climatizacion.emergencias.destroy', $o) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar?\');">';
            $html .= csrf_field() . method_field('DELETE');
            $html .= '<button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>';
            $html .= '</form>';
        }

        $html .= '</div>';
        return $html;
    }
}
