<?php

namespace App\Http\Controllers;

use App\Models\Tecnico;
use App\Models\OrdenReparacion;
use App\Models\ServicioDomotica;
use Illuminate\Http\Request;

class TecnicoController extends Controller
{
    /**
     * Display a listing of technicians.
     */
    public function index(Request $request)
    {
        $query = Tecnico::query()->withCount(['ordenesReparacion']);

        // Filtros
        if ($request->filled('activo')) {
            $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
        }
        if ($request->filled('especialidad')) {
            $query->where('especialidad', 'like', "%{$request->especialidad}%");
        }

        // Búsqueda
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('cedula', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('especialidad', 'like', "%{$search}%");
            });
        }

        // Soporte DataTables AJAX
        if ($request->ajax() || $request->wantsJson()) {
            $total = $query->copy()->count();
            $tecnicos = $query->latest()->paginate(
                request('length', 10),
                ['*'],
                'page',
                request('start', 0)
            );

            $rows = $tecnicos->map(function ($tecnico) {
                return [
                    'DT_RowIndex' => $tecnico->id,
                    'nombre' => $tecnico->nombre,
                    'cedula' => $tecnico->cedula ?? '-',
                    'telefono' => $tecnico->telefono ?? '-',
                    'email' => $tecnico->email ?? '-',
                    'especialidad' => $tecnico->especialidad,
                    'tarifa_hora' => number_format($tecnico->tarifa_hora ?? 0, 2),
                    'tarifa_fija' => number_format($tecnico->tarifa_fija ?? 0, 2),
                    'activo' => $tecnico->activo,
                    'activo_label' => $tecnico->activo ? 'Activo' : 'Inactivo',
                    'ordenes_count' => $tecnico->ordenes_reparacion_count ?? 0,
                    'acciones' => $this->getAccionesHtml($tecnico),
                ];
            });

            return response()->json([
                'draw' => (int) request('draw', 1),
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $rows,
            ]);
        }

        $tecnicos = $query->latest()->paginate(20)->withQueryString();

        return view('tecnicos.index', compact('tecnicos'));
    }

    /**
     * Show the form for creating a new technician.
     */
    public function create()
    {
        return view('tecnicos.create');
    }

    /**
     * Store a newly created technician.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:200',
            'cedula'        => 'nullable|string|max:50',
            'telefono'      => 'nullable|string|max:30',
            'email'         => 'nullable|email|max:150',
            'especialidad'  => 'required|string|max:100',
            'tarifa_hora'   => 'nullable|numeric|min:0',
            'tarifa_fija'   => 'nullable|numeric|min:0',
            'activo'        => 'boolean',
            'notas'         => 'nullable|string|max:2000',
        ]);

        $data['activo'] = $request->has('activo') ? true : false;

        try {
            $tecnico = Tecnico::create($data);

            return redirect()->route('tecnicos.show', $tecnico)
                ->with('success', 'Técnico registrado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al registrar técnico: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified technician.
     */
    public function show(Tecnico $tecnico)
    {
        $tecnico->load(['ordenesReparacion.cliente', 'ordenesReparacion.equipo', 'user']);
        return view('tecnicos.show', compact('tecnico'));
    }

    /**
     * Show the form for editing the specified technician.
     */
    public function edit(Tecnico $tecnico)
    {
        return view('tecnicos.edit', compact('tecnico'));
    }

    /**
     * Update the specified technician.
     */
    public function update(Request $request, Tecnico $tecnico)
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:200',
            'cedula'        => 'nullable|string|max:50',
            'telefono'      => 'nullable|string|max:30',
            'email'         => 'nullable|email|max:150',
            'especialidad'  => 'required|string|max:100',
            'tarifa_hora'   => 'nullable|numeric|min:0',
            'tarifa_fija'   => 'nullable|numeric|min:0',
            'activo'        => 'boolean',
            'notas'         => 'nullable|string|max:2000',
        ]);

        $data['activo'] = $request->has('activo') ? true : false;

        try {
            $tecnico->update($data);

            return redirect()->route('tecnicos.show', $tecnico)
                ->with('success', 'Técnico actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar técnico: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified technician.
     */
    public function destroy(Tecnico $tecnico)
    {
        // Solo eliminar si no tiene órdenes asociadas
        if ($tecnico->ordenesReparacion()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: el técnico tiene órdenes de reparación asociadas.');
        }

        try {
            $tecnico->delete();
            return redirect()->route('tecnicos.index')
                ->with('success', 'Técnico eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar técnico: ' . $e->getMessage());
        }
    }

    /**
     * Toggle technician active/inactive status.
     */
    public function toggleActivar(Tecnico $tecnico)
    {
        try {
            $tecnico->update(['activo' => !$tecnico->activo]);

            $status = $tecnico->activo ? 'activado' : 'desactivado';
            return back()->with('success', "Técnico {$status} correctamente.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }

    /**
     * AJAX endpoint: technician statistics.
     */
    public function getStats()
    {
        $stats = [
            'total'              => Tecnico::count(),
            'activos'            => Tecnico::activos()->count(),
            'ordenes_totales'    => OrdenReparacion::whereHas('tecnico')->count(),
            'ordenes_pendientes' => OrdenReparacion::whereIn('estado', ['recibido', 'pendiente', 'en_reparacion'])
                ->whereHas('tecnico')->count(),
            'prom_tarifa_hora'   => Tecnico::activos()->avg('tarifa_hora') ?? 0,
            'prom_ordenes'       => Tecnico::activos()->avg(function ($tech) {
                return $tech->ordenesReparacion()->count();
            }) ?? 0,
        ];

        return response()->json($stats);
    }

    /**
     * Generate HTML actions for DataTables.
     */
    private function getAccionesHtml(Tecnico $tecnico): string
    {
        $html = '<div class="btn-group btn-group-sm">';
        $html .= '<a href="' . route('tecnicos.show', $tecnico) . '" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>';
        $html .= '<a href="' . route('tecnicos.edit', $tecnico) . '" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>';

        // Toggle activo
        $actionRoute = $tecnico->activo ? route('tecnicos.toggle-activar', $tecnico) : route('tecnicos.toggle-activar', $tecnico);
        $actionText = $tecnico->activo ? 'Desactivar' : 'Activar';
        $actionClass = $tecnico->activo ? 'btn-outline-secondary' : 'btn-outline-success';
        $html .= '<a href="' . $actionRoute . '" class="btn ' . $actionClass . '" title="' . $actionText . '">'
            . '<i class="bi bi-' . ($tecnico->activo ? 'pause-circle' : 'play-circle') . '"></i></a>';

        // Delete (only if no orders)
        if ($tecnico->ordenesReparacion()->count() === 0) {
            $html .= '<form action="' . route('tecnicos.destroy', $tecnico) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar este técnico?\');">';
            $html .= '@csrf @method("DELETE")';
            $html .= '<button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>';
            $html .= '</form>';
        }

        $html .= '</div>';
        return $html;
    }
}
