<?php

namespace App\Http\Controllers;

use App\Models\Instalacion;
use App\Models\Producto;
use App\Models\Cliente;
use App\Services\InstalacionService;
use App\Exports\ClimatizacionInstalacionesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Http\Requests\StoreInstalacionRequest;
use App\Http\Requests\UpdateInstalacionRequest;

class InstalacionController extends Controller
{
    public function __construct(private InstalacionService $service) {}

    public function index(Request $request)
    {
        $query = Instalacion::query()
            ->with(['cliente', 'instalador', 'creadoPor']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo_inmueble')) {
            $query->where('tipo_inmueble', $request->tipo_inmueble);
        }
        if ($request->filled('instalador_id')) {
            $query->where('instalador_id', $request->instalador_id);
        }
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$search}%"))
                  ->orWhere('direccion_instalacion', 'like', "%{$search}%");
            });
        }

        if ($request->ajax() || $request->wantsJson()) {
            $total = clone $query;
            $instalaciones = $query->latest()->paginate(request('length', 10), ['*'], 'page', request('start', 0));

            $rows = $instalaciones->map(function ($inst) {
                $badgeColor = match ($inst->estado) {
                    'pendiente' => 'secondary',
                    'programada' => 'info',
                    'en_progreso' => 'warning',
                    'completada' => 'success',
                    'cancelada' => 'danger',
                    default => 'secondary',
                };

                return [
                    'DT_RowIndex' => $inst->id,
                    'numero' => $inst->numero,
                    'cliente' => $inst->cliente ? $inst->cliente->nombre : '-',
                    'direccion' => $inst->direccion_instalacion ?? '-',
                    'tipo_inmueble' => $inst->tipo_inmueble ?? '-',
                    'instalador' => $inst->instalador ? $inst->instalador->name : '-',
                    'programada_para' => $inst->programada_para ? $inst->programada_para->format('d/m/Y H:i') : '-',
                    'completada_en' => $inst->completada_en ? $inst->completada_en->format('d/m/Y H:i') : '-',
                    'estado' => $inst->estado,
                    'estado_label' => Instalacion::ESTADOS[$inst->estado] ?? $inst->estado,
                    'badge_color' => $badgeColor,
                    'total' => number_format($inst->total ?? 0, 2),
                    'acciones' => $this->getAccionesHtml($inst),
                ];
            });

            return response()->json([
                'draw' => (int) request('draw', 1),
                'recordsTotal' => $total->count(),
                'recordsFiltered' => $total->count(),
                'data' => $rows,
            ]);
        }

        $instalaciones = $query->latest()->paginate(20)->withQueryString();
        $clientes = Cliente::orderBy('nombre')->get();
        return view('climatizacion.instalaciones.index', compact('instalaciones', 'clientes'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::activos()->orderBy('nombre')->get();
        return view('climatizacion.instalaciones.create', compact('clientes', 'productos'));
    }

    public function store(StoreInstalacionRequest $request)
    {
        try {
            $data = $request->validated();
            $inst = $this->service->crear($data, auth()->id());

            return redirect()->route('climatizacion.instalaciones.show', $inst)
                ->with('success', 'Instalación creada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear instalación: ' . $e->getMessage());
        }
    }

    public function show(Instalacion $instalacion)
    {
        $instalacion->load(['cliente', 'instalador', 'productos', 'creadoPor']);
        return view('climatizacion.instalaciones.show', compact('instalacion'));
    }

    public function edit(Instalacion $instalacion)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::activos()->orderBy('nombre')->get();
        return view('climatizacion.instalaciones.edit', compact('instalacion', 'clientes', 'productos'));
    }

    public function update(UpdateInstalacionRequest $request, Instalacion $instalacion)
    {
        try {
            $this->service->actualizar($instalacion->id, $request->validated());

            return redirect()->route('climatizacion.instalaciones.show', $instalacion)
                ->with('success', 'Instalación actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar instalación: ' . $e->getMessage());
        }
    }

    public function destroy(Instalacion $instalacion)
    {
        try {
            $this->service->eliminar($instalacion->id);
            return redirect()->route('climatizacion.instalaciones.index')
                ->with('success', 'Instalación eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function advance(Request $request, Instalacion $instalacion)
    {
        try {
            $this->service->avanzarEstado($instalacion->id, $request->input('next_state'));
            return redirect()->route('climatizacion.instalaciones.show', $instalacion)
                ->with('success', 'Estado avanzado a ' . Instalacion::ESTADOS[$request->next_state] . '.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $query = Instalacion::query()->with(['cliente', 'instalador']);
        $this->applyFilters($request, $query);
        return Excel::download(new ClimatizacionInstalacionesExport($query), 'instalaciones.xlsx');
    }

    private function applyFilters(Request $request, $query)
    {
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo_inmueble')) {
            $query->where('tipo_inmueble', $request->tipo_inmueble);
        }
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$search}%"));
            });
        }
    }

    private function getAccionesHtml(Instalacion $inst): string
    {
        $html = '<div class="btn-group btn-group-sm">';
        $html .= '<a href="' . route('climatizacion.instalaciones.show', $inst) . '" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>';
        if (!in_array($inst->estado, ['completada', 'cancelada'])) {
            $html .= '<a href="' . route('climatizacion.instalaciones.edit', $inst) . '" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>';
        }
        if (!in_array($inst->estado, ['completada', 'cancelada'])) {
            $nextState = match ($inst->estado) {
                'pendiente' => 'programada',
                'programada' => 'en_progreso',
                default => null,
            };
            if ($nextState) {
                $html .= '<form action="' . route('climatizacion.instalaciones.advance', $inst) . '" method="POST" class="d-inline">';
                $html .= '@csrf @method("PATCH")';
                $html .= '<input type="hidden" name="next_state" value="' . $nextState . '">';
                $html .= '<button type="submit" class="btn btn-outline-primary" title="Avanzar estado"><i class="bi bi-forward"></i></button>';
                $html .= '</form>';
            }
        }
        if (!in_array($inst->estado, ['completada', 'cancelada'])) {
            $html .= '<form action="' . route('climatizacion.instalaciones.destroy', $inst) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar?\');">';
            $html .= '@csrf @method("DELETE")';
            $html .= '<button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>';
            $html .= '</form>';
        }
        $html .= '</div>';
        return $html;
    }
}
