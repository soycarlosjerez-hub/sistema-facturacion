<?php

namespace App\Http\Controllers;

use App\Models\MarcaTecnologica;
use App\Models\Producto;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class MarcaTecnologicaController extends Controller
{
    public function index(Request $request)
    {
        $query = MarcaTecnologica::query()->withCount(['productos']);

        if ($request->filled('activo')) {
            $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
        }
        if ($request->filled('pais')) {
            $query->where('pais', 'like', "%{$request->pais}%");
        }

        if ($search = $this->dtSearch($request)) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('website', 'like', "%{$search}%")
                    ->orWhere('pais', 'like', "%{$search}%");
            });
        }

        $marcas = $query->latest()->paginate(20)->withQueryString();
        $productosCount = Producto::count();

        return view('marcas-tecnologicas.index', compact('marcas', 'productosCount'));
    }

    public function indexAjax(Request $request)
    {
        $query = MarcaTecnologica::query();

        if ($request->filled('activo')) {
            $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
        }
        if ($request->filled('pais')) {
            $query->where('pais', 'like', "%{$request->pais}%");
        }

        if ($search = $this->dtSearch($request)) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('website', 'like', "%{$search}%")
                    ->orWhere('pais', 'like', "%{$search}%");
            });
        }

        // Ordering
        $columnMapping = ['id', 'nombre', 'website', 'pais', 'productos_count', 'activo'];
        $orderColIdx = (int) $request->input('columns.0.data', 0);
        $orderCol = $columnMapping[$orderColIdx] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'desc');

        // Total count BEFORE skip/take
        $total = (clone $query)->withCount(['productos'])->count();

        // Apply ordering and pagination
        if ($orderCol !== 'productos_count') {
            $query->orderBy($orderCol, $orderDir);
        }
        $query->withCount(['productos']);
        $skip = (int) $request->input('start', 0);
        $length = (int) $request->input('length', -1);
        if ($length > 0) {
            $query->skip($skip)->take($length);
        }

        $marcas = $query->get();

        $rows = $marcas->map(function ($marca) {
            return [
                'DT_RowIndex' => $marca->id,
                'nombre' => $marca->nombre,
                'website' => $marca->website ? '<a href="' . $marca->website . '" target="_blank"><i class="bi bi-globe"></i> ' . $marca->website . '</a>' : '-',
                'pais' => $marca->pais ?? '-',
                'productos_count' => $marca->productos_count ?? 0,
                'activo' => $marca->activo,
                'activo_label' => $marca->activo ? 'Activo' : 'Inactivo',
                'acciones' => $this->getAccionesHtml($marca),
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $rows,
        ]);
    }

    public function create()
    {
        return view('marcas-tecnologicas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:200|unique:marca_tecnologicas,nombre',
            'logo_url' => 'nullable|url|max:500',
            'website' => 'nullable|url|max:255',
            'pais' => 'nullable|string|max:100',
            'contacto_email' => 'nullable|email|max:255',
            'activo' => 'boolean',
            'orden' => 'nullable|integer|min:0',
        ]);

        $data['activo'] = $request->has('activo');
        $data['orden'] = $data['orden'] ?? 0;

        try {
            MarcaTecnologica::create($data);
            return redirect()->route('marcas-tecnologicas.index')
                ->with('success', 'Marca registrada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al registrar marca: ' . $e->getMessage());
        }
    }

    public function show(MarcaTecnologica $marcaTecnologica)
    {
        $marcaTecnologica->load(['productos' => function ($q) {
            $q->withCount(['ordenesCompra', 'ordenesVenta'])->limit(10);
        }]);
        return view('marcas-tecnologicas.show', compact('marcaTecnologica'));
    }

    public function edit(MarcaTecnologica $marcaTecnologica)
    {
        return view('marcas-tecnologicas.edit', compact('marcaTecnologica'));
    }

    public function update(Request $request, MarcaTecnologica $marcaTecnologica)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:200|unique:marca_tecnologicas,nombre,' . $marcaTecnologica->id,
            'logo_url' => 'nullable|url|max:500',
            'website' => 'nullable|url|max:255',
            'pais' => 'nullable|string|max:100',
            'contacto_email' => 'nullable|email|max:255',
            'activo' => 'boolean',
            'orden' => 'nullable|integer|min:0',
        ]);

        $data['activo'] = $request->has('activo');
        $data['orden'] = $data['orden'] ?? 0;

        try {
            $marcaTecnologica->update($data);
            return redirect()->route('marcas-tecnologicas.show', $marcaTecnologica)
                ->with('success', 'Marca actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar marca: ' . $e->getMessage());
        }
    }

    public function destroy(MarcaTecnologica $marcaTecnologica)
    {
        if ($marcaTecnologica->productos()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: la marca tiene productos asociados.');
        }

        try {
            $marcaTecnologica->delete();
            return redirect()->route('marcas-tecnologicas.index')
                ->with('success', 'Marca eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar marca: ' . $e->getMessage());
        }
    }

    public function toggleActivar(MarcaTecnologica $marcaTecnologica)
    {
        try {
            $marcaTecnologica->update(['activo' => !$marcaTecnologica->activo]);
            $status = $marcaTecnologica->activo ? 'activada' : 'desactivada';
            return back()->with('success', "Marca {$status} correctamente.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }

    private function getAccionesHtml(MarcaTecnologica $marca): string
    {
        $html = '<div class="btn-group btn-group-sm">';
        $html .= '<a href="' . route('marcas-tecnologicas.show', $marca) . '" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>';
        $html .= '<a href="' . route('marcas-tecnologicas.edit', $marca) . '" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>';

        $actionClass = $marca->activo ? 'btn-outline-secondary' : 'btn-outline-success';
        $actionText = $marca->activo ? 'Desactivar' : 'Activar';
        $html .= '<a href="' . route('marcas-tecnologicas.toggle', $marca) . '" class="btn ' . $actionClass . '" title="' . $actionText . '">'
            . '<i class="bi bi-' . ($marca->activo ? 'pause-circle' : 'play-circle') . '"></i></a>';

        if ($marca->productos()->count() === 0) {
            $html .= '<form action="' . route('marcas-tecnologicas.destroy', $marca) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar esta marca?\');">';
            $html .= csrf_field() . method_field('DELETE');
            $html .= '<button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>';
            $html .= '</form>';
        }

        $html .= '</div>';
        return $html;
    }
}
