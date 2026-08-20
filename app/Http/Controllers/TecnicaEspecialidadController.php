<?php

namespace App\Http\Controllers;

use App\Models\TecnicaEspecialidad;
use App\Models\Tecnico;
use Illuminate\Http\Request;

class TecnicaEspecialidadController extends Controller
{
    public function index(Request $request)
    {
        $query = TecnicaEspecialidad::query()
            ->withCount(['tecnicos' => function ($q) {
                $q->wherePivot('activo', true);
            }]);

        if ($request->filled('activo')) {
            $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
        }

        if ($search = $this->dtSearch($request)) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($request->ajax() || $request->wantsJson()) {
            $total = $query->count();
            $especialidades = $query->orderBy('orden')->paginate(request('length', 10), ['*'], 'page', (int) floor(request('start', 0) / max(1, (int) request('length', 10))) + 1);

            $rows = $especialidades->map(function ($esp) {
                return [
                    'DT_RowIndex' => $esp->id,
                    'nombre' => $esp->nombre,
                    'descripcion' => $esp->descripcion ?? '-',
                    'tecnicos_count' => $esp->tecnicos_count ?? 0,
                    'activo' => $esp->activo,
                    'activo_label' => $esp->activo ? 'Activa' : 'Inactiva',
                    'acciones' => $this->getAccionesHtml($esp),
                ];
            });

            return response()->json([
                'draw' => (int) request('draw', 1),
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $rows,
            ]);
        }

        $especialidades = $query->orderBy('orden')->paginate(20)->withQueryString();

        return view('tecnica-especialidades.index', compact('especialidades'));
    }

    public function create()
    {
        return view('tecnica-especialidades.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:200|unique:tecnica_especialidades,nombre',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
            'orden' => 'nullable|integer|min:0',
        ]);

        $data['activo'] = $request->has('activo');
        $data['orden'] = $data['orden'] ?? 0;

        try {
            TecnicaEspecialidad::create($data);
            return redirect()->route('tecnica-especialidades.index')
                ->with('success', 'Especialidad registrada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al registrar especialidad: ' . $e->getMessage());
        }
    }

    public function show(TecnicaEspecialidad $tecnicaEspecialidad)
    {
        $tecnicaEspecialidad->load(['tecnicos' => function ($q) {
            $q->wherePivot('activo', true)->withCount('ordenesReparacion');
        }]);
        return view('tecnica-especialidades.show', compact('tecnicaEspecialidad'));
    }

    public function edit(TecnicaEspecialidad $tecnicaEspecialidad)
    {
        return view('tecnica-especialidades.edit', compact('tecnicaEspecialidad'));
    }

    public function update(Request $request, TecnicaEspecialidad $tecnicaEspecialidad)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:200|unique:tecnica_especialidades,nombre,' . $tecnicaEspecialidad->id,
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
            'orden' => 'nullable|integer|min:0',
        ]);

        $data['activo'] = $request->has('activo');
        $data['orden'] = $data['orden'] ?? 0;

        try {
            $tecnicaEspecialidad->update($data);
            return redirect()->route('tecnica-especialidades.show', $tecnicaEspecialidad)
                ->with('success', 'Especialidad actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar especialidad: ' . $e->getMessage());
        }
    }

    public function destroy(TecnicaEspecialidad $tecnicaEspecialidad)
    {
        if ($tecnicaEspecialidad->tecnicos()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: la especialidad tiene técnicos asociados.');
        }

        try {
            $tecnicaEspecialidad->delete();
            return redirect()->route('tecnica-especialidades.index')
                ->with('success', 'Especialidad eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar especialidad: ' . $e->getMessage());
        }
    }

    public function toggleActivar(TecnicaEspecialidad $tecnicaEspecialidad)
    {
        try {
            $tecnicaEspecialidad->update(['activo' => !$tecnicaEspecialidad->activo]);
            $status = $tecnicaEspecialidad->activo ? 'activada' : 'desactivada';
            return back()->with('success', "Especialidad {$status} correctamente.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }

    public function asignarTecnico(TecnicaEspecialidad $tecnicaEspecialidad, Tecnico $tecnico)
    {
        try {
            $tecnicaEspecialidad->tecnicos()->attach($tecnico->id, [
                'activo' => true,
                'fecha_asignacion' => now(),
            ]);
            return back()->with('success', 'Técnico asignado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al asignar técnico: ' . $e->getMessage());
        }
    }

    public function desasignarTecnico(TecnicaEspecialidad $tecnicaEspecialidad, Tecnico $tecnico)
    {
        try {
            $tecnicaEspecialidad->tecnicos()->detach($tecnico->id);
            return back()->with('success', 'Técnico desasignado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al desasignar técnico: ' . $e->getMessage());
        }
    }

    private function getAccionesHtml(TecnicaEspecialidad $esp): string
    {
        $html = '<div class="btn-group btn-group-sm">';
        $html .= '<a href="' . route('tecnica-especialidades.show', $esp) . '" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>';
        $html .= '<a href="' . route('tecnica-especialidades.edit', $esp) . '" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>';

        $actionClass = $esp->activo ? 'btn-outline-secondary' : 'btn-outline-success';
        $actionText = $esp->activo ? 'Desactivar' : 'Activar';
        $html .= '<a href="' . route('tecnica-especialidades.toggle', $esp) . '" class="btn ' . $actionClass . '" title="' . $actionText . '">'
            . '<i class="bi bi-' . ($esp->activo ? 'pause-circle' : 'play-circle') . '"></i></a>';

        if ($esp->tecnicos()->count() === 0) {
            $html .= '<form action="' . route('tecnica-especialidades.destroy', $esp) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar esta especialidad?\');">';
            $html .= csrf_field() . method_field('DELETE');
            $html .= '<button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>';
            $html .= '</form>';
        }

        $html .= '</div>';
        return $html;
    }
}
