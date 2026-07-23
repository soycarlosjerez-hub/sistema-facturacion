<?php

namespace App\Http\Controllers;

use App\Models\TipoClima;
use Illuminate\Http\Request;

class TipoClimaController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoClima::query();

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }
        if ($request->filled('activo')) {
            $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
        }
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->ajax() || $request->wantsJson()) {
            $total = $query->copy()->count();
            $tipos = $query->orderBy('orden')->paginate(request('length', 10), ['*'], 'page', request('start', 0));

            $rows = $tipos->map(function ($tipo) {
                return [
                    'DT_RowIndex' => $tipo->id,
                    'nombre' => $tipo->nombre,
                    'slug' => $tipo->slug,
                    'categoria' => ucfirst($tipo->categoria),
                    'icono' => $tipo->icono ?? '-',
                    'activo' => $tipo->activo,
                    'activo_label' => $tipo->activo ? 'Activo' : 'Inactivo',
                    'orden' => $tipo->orden,
                    'acciones' => $this->getAccionesHtml($tipo),
                ];
            });

            return response()->json([
                'draw' => (int) request('draw', 1),
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $rows,
            ]);
        }

        $tipos = $query->orderBy('orden')->paginate(20)->withQueryString();
        return view('climatizacion.tipos-equipos.index', compact('tipos'));
    }

    public function create()
    {
        return view('climatizacion.tipos-equipos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100|unique:tipos_clima,nombre',
            'slug' => 'required|string|max:50|unique:tipos_clima,slug',
            'categoria' => 'required|in:residencial,comercial,industrial',
            'icono' => 'nullable|string|max:50',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'boolean',
        ]);

        $data['activo'] = $request->has('activo') ? true : false;

        try {
            TipoClima::create($data);
            return redirect()->route('climatizacion.tipos-equipos.index')
                ->with('success', 'Tipo de equipo creado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear tipo de equipo: ' . $e->getMessage());
        }
    }

    public function show(TipoClima $tipo)
    {
        return view('climatizacion.tipos-equipos.show', compact('tipo'));
    }

    public function edit(TipoClima $tipo)
    {
        return view('climatizacion.tipos-equipos.edit', compact('tipo'));
    }

    public function update(Request $request, TipoClima $tipo)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100|unique:tipos_clima,nombre,' . $tipo->id,
            'slug' => 'required|string|max:50|unique:tipos_clima,slug,' . $tipo->id,
            'categoria' => 'required|in:residencial,comercial,industrial',
            'icono' => 'nullable|string|max:50',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'boolean',
        ]);

        $data['activo'] = $request->has('activo') ? true : false;

        try {
            $tipo->update($data);
            return redirect()->route('climatizacion.tipos-equipos.index')
                ->with('success', 'Tipo de equipo actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar tipo de equipo: ' . $e->getMessage());
        }
    }

    public function destroy(TipoClima $tipo)
    {
        try {
            $tipo->delete();
            return redirect()->route('climatizacion.tipos-equipos.index')
                ->with('success', 'Tipo de equipo eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar tipo de equipo: ' . $e->getMessage());
        }
    }

    private function getAccionesHtml(TipoClima $tipo): string
    {
        $html = '<div class="btn-group btn-group-sm">';
        $html .= '<a href="' . route('climatizacion.tipos-equipos.show', $tipo) . '" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>';
        $html .= '<a href="' . route('climatizacion.tipos-equipos.edit', $tipo) . '" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>';
        $html .= '<form action="' . route('climatizacion.tipos-equipos.destroy', $tipo) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar este tipo de equipo?\');">';
        $html .= '@csrf @method("DELETE")';
        $html .= '<button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>';
        $html .= '</form>';
        $html .= '</div>';
        return $html;
    }
}
