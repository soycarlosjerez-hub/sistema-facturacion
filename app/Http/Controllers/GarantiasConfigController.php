<?php

namespace App\Http\Controllers;

use App\Models\GarantiasConfig;
use App\Models\Producto;
use Illuminate\Http\Request;

class GarantiasConfigController extends Controller
{
    public function index(Request $request)
    {
        $query = GarantiasConfig::query();

        if ($request->filled('activo')) {
            $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
        }
        if ($request->filled('tipo_garantia')) {
            $query->where('tipo_garantia', $request->tipo_garantia);
        }

        if ($search = $this->dtSearch($request)) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('tipo_producto', 'like', "%{$search}%")
                    ->orWhere('cobertura', 'like', "%{$search}%");
            });
        }

        $garantias = $query->orderBy('orden')->paginate(20)->withQueryString();

        return view('garantias-config.index', compact('garantias'));
    }

    public function indexAjax(Request $request)
    {
        $query = GarantiasConfig::query();

        if ($request->filled('activo')) {
            $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
        }
        if ($request->filled('tipo_garantia')) {
            $query->where('tipo_garantia', $request->tipo_garantia);
        }

        if ($search = $this->dtSearch($request)) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('tipo_producto', 'like', "%{$search}%")
                    ->orWhere('cobertura', 'like', "%{$search}%");
            });
        }

        // Ordering
        $columnMapping = ['id', 'nombre', 'tipo_producto', 'dias_garantia', 'tipo_garantia', 'activo'];
        $orderColIdx = (int) $request->input('columns.0.data', 0);
        $orderCol = $columnMapping[$orderColIdx] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'asc');
        if (in_array($orderCol, ['nombre', 'tipo_producto', 'dias_garantia', 'tipo_garantia'])) {
            $query->orderBy($orderCol, $orderDir);
        }

        // Pagination
        $skip = (int) $request->input('start', 0);
        $length = (int) $request->input('length', -1);
        if ($length > 0) {
            $query->skip($skip)->take($length);
        }

        // Total count BEFORE skip/take
        $total = GarantiasConfig::query()
            ->when($request->filled('activo'), fn($q) => $q->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN)))
            ->when($request->filled('tipo_garantia'), fn($q) => $q->where('tipo_garantia', $request->tipo_garantia))
            ->when($search = $this->dtSearch($request), function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('nombre', 'like', "%{$search}%")
                        ->orWhere('tipo_producto', 'like', "%{$search}%")
                        ->orWhere('cobertura', 'like', "%{$search}%");
                });
            })
            ->count();

        $garantias = $query->get();

        $rows = $garantias->map(function ($garantia) {
            return [
                'DT_RowIndex' => $garantia->id,
                'nombre' => $garantia->nombre,
                'tipo_producto' => $garantia->tipo_producto ?? 'General',
                'dias_garantia' => $garantia->dias_garantia . ' días',
                'tipo_garantia' => $garantia->tipo_garantia_label,
                'activo' => $garantia->activo,
                'activo_label' => $garantia->activo ? 'Activa' : 'Inactiva',
                'acciones' => $this->getAccionesHtml($garantia),
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
        return view('garantias-config.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:200',
            'tipo_producto' => 'nullable|string|max:100',
            'dias_garantia' => 'required|integer|min:0',
            'tipo_garantia' => 'required|in:fabrica,extendida',
            'cobertura' => 'nullable|string',
            'activo' => 'boolean',
            'orden' => 'nullable|integer|min:0',
        ]);

        $data['activo'] = $request->has('activo');
        $data['orden'] = $data['orden'] ?? 0;

        try {
            GarantiasConfig::create($data);
            return redirect()->route('garantias-config.index')
                ->with('success', 'Configuración de garantía registrada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al registrar configuración: ' . $e->getMessage());
        }
    }

    public function show(GarantiasConfig $garantiasConfig)
    {
        return view('garantias-config.show', compact('garantiasConfig'));
    }

    public function edit(GarantiasConfig $garantiasConfig)
    {
        return view('garantias-config.edit', compact('garantiasConfig'));
    }

    public function update(Request $request, GarantiasConfig $garantiasConfig)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:200',
            'tipo_producto' => 'nullable|string|max:100',
            'dias_garantia' => 'required|integer|min:0',
            'tipo_garantia' => 'required|in:fabrica,extendida',
            'cobertura' => 'nullable|string',
            'activo' => 'boolean',
            'orden' => 'nullable|integer|min:0',
        ]);

        $data['activo'] = $request->has('activo');
        $data['orden'] = $data['orden'] ?? 0;

        try {
            $garantiasConfig->update($data);
            return redirect()->route('garantias-config.show', $garantiasConfig)
                ->with('success', 'Configuración de garantía actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar configuración: ' . $e->getMessage());
        }
    }

    public function destroy(GarantiasConfig $garantiasConfig)
    {
        try {
            $garantiasConfig->delete();
            return redirect()->route('garantias-config.index')
                ->with('success', 'Configuración de garantía eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar configuración: ' . $e->getMessage());
        }
    }

    public function toggleActivar(GarantiasConfig $garantiasConfig)
    {
        try {
            $garantiasConfig->update(['activo' => !$garantiasConfig->activo]);
            $status = $garantiasConfig->activo ? 'activada' : 'desactivada';
            return back()->with('success', "Configuración {$status} correctamente.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }

    private function getAccionesHtml(GarantiasConfig $garantia): string
    {
        $html = '<div class="btn-group btn-group-sm">';
        $html .= '<a href="' . route('garantias-config.show', $garantia) . '" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>';
        $html .= '<a href="' . route('garantias-config.edit', $garantia) . '" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>';

        $actionClass = $garantia->activo ? 'btn-outline-secondary' : 'btn-outline-success';
        $actionText = $garantia->activo ? 'Desactivar' : 'Activar';
        $html .= '<a href="' . route('garantias-config.toggle', $garantia) . '" class="btn ' . $actionClass . '" title="' . $actionText . '">'
            . '<i class="bi bi-' . ($garantia->activo ? 'pause-circle' : 'play-circle') . '"></i></a>';

        $html .= '<form action="' . route('garantias-config.destroy', $garantia) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar esta configuración?\');">';
        $html .= csrf_field() . method_field('DELETE');
        $html .= '<button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>';
        $html .= '</form>';

        $html .= '</div>';
        return $html;
    }
}
