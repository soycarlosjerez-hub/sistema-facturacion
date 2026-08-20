<?php

namespace App\Http\Controllers;

use App\Models\LicenciaSoftware;
use App\Models\Producto;
use Illuminate\Http\Request;

class LicenciaSoftwareController extends Controller
{
    public function index(Request $request)
    {
        $query = LicenciaSoftware::query()->with('producto');

        if ($request->filled('licencia_activa')) {
            $query->where('licencia_activa', filter_var($request->licencia_activa, FILTER_VALIDATE_BOOLEAN));
        }
        if ($request->filled('plataforma')) {
            $query->where('plataforma', $request->plataforma);
        }
        if ($request->filled('tipo_licencia')) {
            $query->where('tipo_licencia', $request->tipo_licencia);
        }

        if ($search = $this->dtSearch($request)) {
            $query->where(function ($q) use ($search) {
                $q->where('clave_licencia', 'like', "%{$search}%")
                    ->orWhere('usuario_asignado', 'like', "%{$search}%")
                    ->orWhereHas('producto', function ($q2) use ($search) {
                        $q2->where('nombre', 'like', "%{$search}%");
                    });
            });
        }

        $licencias = $query->latest()->paginate(20)->withQueryString();
        $productos = Producto::where('es_licencia', true)->get();

        return view('licencias-software.index', compact('licencias', 'productos'));
    }

    public function indexAjax(Request $request)
    {
        $query = LicenciaSoftware::query()->with('producto');

        if ($request->filled('licencia_activa')) {
            $query->where('licencia_activa', filter_var($request->licencia_activa, FILTER_VALIDATE_BOOLEAN));
        }
        if ($request->filled('plataforma')) {
            $query->where('plataforma', $request->plataforma);
        }
        if ($request->filled('tipo_licencia')) {
            $query->where('tipo_licencia', $request->tipo_licencia);
        }

        if ($search = $this->dtSearch($request)) {
            $query->where(function ($q) use ($search) {
                $q->where('clave_licencia', 'like', "%{$search}%")
                    ->orWhere('usuario_asignado', 'like', "%{$search}%")
                    ->orWhereHas('producto', function ($q2) use ($search) {
                        $q2->where('nombre', 'like', "%{$search}%");
                    });
            });
        }

        // Ordering
        $columnMapping = ['id', 'clave_licencia', 'producto', 'tipo_licencia', 'plataforma', 'usuario_asignado', 'fecha_vencimiento', 'estado'];
        $orderColIdx = (int) $request->input('columns.0.data', 0);
        $orderCol = $columnMapping[$orderColIdx] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'desc');
        if (in_array($orderCol, ['clave_licencia', 'producto', 'tipo_licencia', 'plataforma', 'usuario_asignado', 'fecha_vencimiento'])) {
            $query->orderBy($orderCol, $orderDir);
        }

        // Pagination
        $skip = (int) $request->input('start', 0);
        $length = (int) $request->input('length', -1);
        if ($length > 0) {
            $query->skip($skip)->take($length);
        }

        // Total count BEFORE skip/take
        $total = LicenciaSoftware::query()->with('producto')
            ->when($request->filled('licencia_activa'), fn($q) => $q->where('licencia_activa', filter_var($request->licencia_activa, FILTER_VALIDATE_BOOLEAN)))
            ->when($request->filled('plataforma'), fn($q) => $q->where('plataforma', $request->plataforma))
            ->when($request->filled('tipo_licencia'), fn($q) => $q->where('tipo_licencia', $request->tipo_licencia))
            ->when($search = $this->dtSearch($request), function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('clave_licencia', 'like', "%{$search}%")
                        ->orWhere('usuario_asignado', 'like', "%{$search}%")
                        ->orWhereHas('producto', function ($q3) use ($search) {
                            $q3->where('nombre', 'like', "%{$search}%");
                        });
                });
            })
            ->count();

        $licencias = $query->get();

        $rows = $licencias->map(function ($licencia) {
            $estado = 'Activa';
            if (!$licencia->licencia_activa) {
                $estado = 'Inactiva';
            } elseif ($licencia->fecha_vencimiento && $licencia->fecha_vencimiento->lt(today())) {
                $estado = 'Vencida';
            } elseif ($licencia->fecha_vencimiento && $licencia->fecha_vencimiento->lte(today()->addDays(30))) {
                $estado = 'Por Vencer';
            }

            return [
                'DT_RowIndex' => $licencia->id,
                'clave_licencia' => $licencia->clave_licencia,
                'producto' => $licencia->producto ? $licencia->producto->nombre : '-',
                'tipo_licencia' => $licencia->tipo_licencia ?? '-',
                'plataforma' => $licencia->plataforma ?? '-',
                'usuario_asignado' => $licencia->usuario_asignado ?? '-',
                'fecha_vencimiento' => $licencia->fecha_vencimiento ? $licencia->fecha_vencimiento->format('Y-m-d') : '-',
                'estado' => $estado,
                'licencia_activa' => $licencia->licencia_activa,
                'acciones' => $this->getAccionesHtml($licencia),
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
        $productos = Producto::where('es_licencia', true)->get();
        return view('licencias-software.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'producto_id' => 'nullable|exists:productos,id',
            'clave_licencia' => 'required|string|max:255|unique:licencias_software,clave_licencia',
            'tipo_licencia' => 'nullable|string|max:100',
            'usuario_asignado' => 'nullable|string|max:255',
            'licencia_activa' => 'boolean',
            'fecha_vencimiento' => 'nullable|date',
            'plataforma' => 'nullable|string|max:50',
            'notas' => 'nullable|string',
        ]);

        $data['licencia_activa'] = $request->has('licencia_activa');

        try {
            LicenciaSoftware::create($data);
            return redirect()->route('licencias-software.index')
                ->with('success', 'Licencia registrada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al registrar licencia: ' . $e->getMessage());
        }
    }

    public function show(LicenciaSoftware $licenciaSoftware)
    {
        $licenciaSoftware->load('producto');
        return view('licencias-software.show', compact('licenciaSoftware'));
    }

    public function edit(LicenciaSoftware $licenciaSoftware)
    {
        $productos = Producto::where('es_licencia', true)->get();
        return view('licencias-software.edit', compact('licenciaSoftware', 'productos'));
    }

    public function update(Request $request, LicenciaSoftware $licenciaSoftware)
    {
        $data = $request->validate([
            'producto_id' => 'nullable|exists:productos,id',
            'clave_licencia' => 'required|string|max:255|unique:licencias_software,clave_licencia,' . $licenciaSoftware->id,
            'tipo_licencia' => 'nullable|string|max:100',
            'usuario_asignado' => 'nullable|string|max:255',
            'licencia_activa' => 'boolean',
            'fecha_vencimiento' => 'nullable|date',
            'plataforma' => 'nullable|string|max:50',
            'notas' => 'nullable|string',
        ]);

        $data['licencia_activa'] = $request->has('licencia_activa');

        try {
            $licenciaSoftware->update($data);
            return redirect()->route('licencias-software.show', $licenciaSoftware)
                ->with('success', 'Licencia actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar licencia: ' . $e->getMessage());
        }
    }

    public function destroy(LicenciaSoftware $licenciaSoftware)
    {
        try {
            $licenciaSoftware->delete();
            return redirect()->route('licencias-software.index')
                ->with('success', 'Licencia eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar licencia: ' . $e->getMessage());
        }
    }

    public function toggleActivar(LicenciaSoftware $licenciaSoftware)
    {
        try {
            $licenciaSoftware->update(['licencia_activa' => !$licenciaSoftware->licencia_activa]);
            $status = $licenciaSoftware->licencia_activa ? 'activada' : 'desactivada';
            return back()->with('success', "Licencia {$status} correctamente.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }

    private function getAccionesHtml(LicenciaSoftware $licencia): string
    {
        $html = '<div class="btn-group btn-group-sm">';
        $html .= '<a href="' . route('licencias-software.show', $licencia) . '" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>';
        $html .= '<a href="' . route('licencias-software.edit', $licencia) . '" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>';

        $actionClass = $licencia->licencia_activa ? 'btn-outline-secondary' : 'btn-outline-success';
        $actionText = $licencia->licencia_activa ? 'Desactivar' : 'Activar';
        $html .= '<a href="' . route('licencias-software.toggle', $licencia) . '" class="btn ' . $actionClass . '" title="' . $actionText . '">'
            . '<i class="bi bi-' . ($licencia->licencia_activa ? 'pause-circle' : 'play-circle') . '"></i></a>';

        $html .= '<form action="' . route('licencias-software.destroy', $licencia) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar esta licencia?\');">';
        $html .= csrf_field() . method_field('DELETE');
        $html .= '<button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>';
        $html .= '</form>';

        $html .= '</div>';
        return $html;
    }
}
