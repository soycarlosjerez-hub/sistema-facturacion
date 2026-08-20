<?php

namespace App\Http\Controllers;

use App\Models\RedConfig;
use App\Models\Cliente;
use Illuminate\Http\Request;

class RedConfigController extends Controller
{
    public function index(Request $request)
    {
        $query = RedConfig::query()->with('cliente');

        if ($request->filled('activo')) {
            $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
        }
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }
        if ($request->filled('vlan_id')) {
            $query->where('vlan_id', $request->vlan_id);
        }

        if ($search = $this->dtSearch($request)) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre_red', 'like', "%{$search}%")
                    ->orWhere('ssid_wifi', 'like', "%{$search}%")
                    ->orWhereHas('cliente', function ($q2) use ($search) {
                        $q2->where('nombre', 'like', "%{$search}%");
                    });
            });
        }

        $redes = $query->latest()->paginate(20)->withQueryString();
        $clientes = Cliente::where('activo', true)->orderBy('nombre')->get();

        return view('redes-config.index', compact('redes', 'clientes'));
    }

    public function indexAjax(Request $request)
    {
        $query = RedConfig::query()->with('cliente');

        if ($request->filled('activo')) {
            $query->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN));
        }
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }
        if ($request->filled('vlan_id')) {
            $query->where('vlan_id', $request->vlan_id);
        }

        if ($search = $this->dtSearch($request)) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre_red', 'like', "%{$search}%")
                    ->orWhere('ssid_wifi', 'like', "%{$search}%")
                    ->orWhereHas('cliente', function ($q2) use ($search) {
                        $q2->where('nombre', 'like', "%{$search}%");
                    });
            });
        }

        // Ordering
        $columnMapping = ['id', 'nombre_red', 'ssid_wifi', 'vlan_id', 'cliente', 'dhcp_activado', 'activo'];
        $orderColIdx = (int) $request->input('columns.0.data', 0);
        $orderCol = $columnMapping[$orderColIdx] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'desc');
        if (in_array($orderCol, ['nombre_red', 'ssid_wifi', 'vlan_id', 'cliente', 'dhcp_activado'])) {
            $query->orderBy($orderCol, $orderDir);
        }

        // Pagination
        $skip = (int) $request->input('start', 0);
        $length = (int) $request->input('length', -1);
        if ($length > 0) {
            $query->skip($skip)->take($length);
        }

        // Total count BEFORE skip/take
        $total = RedConfig::query()->with('cliente')
            ->when($request->filled('activo'), fn($q) => $q->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN)))
            ->when($request->filled('cliente_id'), fn($q) => $q->where('cliente_id', $request->cliente_id))
            ->when($request->filled('vlan_id'), fn($q) => $q->where('vlan_id', $request->vlan_id))
            ->when($search = $this->dtSearch($request), function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('nombre_red', 'like', "%{$search}%")
                        ->orWhere('ssid_wifi', 'like', "%{$search}%")
                        ->orWhereHas('cliente', function ($q3) use ($search) {
                            $q3->where('nombre', 'like', "%{$search}%");
                        });
                });
            })
            ->count();

        $redes = $query->get();

        $rows = $redes->map(function ($red) {
            return [
                'DT_RowIndex' => $red->id,
                'nombre_red' => $red->nombre_red,
                'ssid_wifi' => $red->ssid_wifi ?? '-',
                'vlan_id' => $red->vlan_id ?? '-',
                'cliente' => $red->cliente ? $red->cliente->nombre : '-',
                'dhcp_activado' => $red->dhcp_activado ? 'Sí' : 'No',
                'activo' => $red->activo,
                'activo_label' => $red->activo ? 'Activa' : 'Inactiva',
                'acciones' => $this->getAccionesHtml($red),
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
        $clientes = Cliente::where('activo', true)->orderBy('nombre')->get();
        return view('redes-config.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'nombre_red' => 'required|string|max:200',
            'direccion_red' => 'nullable|string|max:50',
            'vlan_id' => 'nullable|integer|min:1|max:4094',
            'ssid_wifi' => 'nullable|string|max:100',
            'canal_wifi' => 'nullable|string|max:20',
            'cobertura' => 'nullable|string',
            'dhcp_activado' => 'boolean',
            'dhcp_rango' => 'nullable|string|max:200',
            'notas' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        $data['dhcp_activado'] = $request->has('dhcp_activado');
        $data['activo'] = $request->has('activo');

        try {
            RedConfig::create($data);
            return redirect()->route('redes-config.index')
                ->with('success', 'Configuración de red registrada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al registrar configuración: ' . $e->getMessage());
        }
    }

    public function show(RedConfig $redConfig)
    {
        $redConfig->load('cliente');
        return view('redes-config.show', compact('redConfig'));
    }

    public function edit(RedConfig $redConfig)
    {
        $clientes = Cliente::where('activo', true)->orderBy('nombre')->get();
        return view('redes-config.edit', compact('redConfig', 'clientes'));
    }

    public function update(Request $request, RedConfig $redConfig)
    {
        $data = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'nombre_red' => 'required|string|max:200',
            'direccion_red' => 'nullable|string|max:50',
            'vlan_id' => 'nullable|integer|min:1|max:4094',
            'ssid_wifi' => 'nullable|string|max:100',
            'canal_wifi' => 'nullable|string|max:20',
            'cobertura' => 'nullable|string',
            'dhcp_activado' => 'boolean',
            'dhcp_rango' => 'nullable|string|max:200',
            'notas' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        $data['dhcp_activado'] = $request->has('dhcp_activado');
        $data['activo'] = $request->has('activo');

        try {
            $redConfig->update($data);
            return redirect()->route('redes-config.show', $redConfig)
                ->with('success', 'Configuración de red actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar configuración: ' . $e->getMessage());
        }
    }

    public function destroy(RedConfig $redConfig)
    {
        try {
            $redConfig->delete();
            return redirect()->route('redes-config.index')
                ->with('success', 'Configuración de red eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar configuración: ' . $e->getMessage());
        }
    }

    public function toggleActivar(RedConfig $redConfig)
    {
        try {
            $redConfig->update(['activo' => !$redConfig->activo]);
            $status = $redConfig->activo ? 'activada' : 'desactivada';
            return back()->with('success', "Configuración {$status} correctamente.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }

    private function getAccionesHtml(RedConfig $red): string
    {
        $html = '<div class="btn-group btn-group-sm">';
        $html .= '<a href="' . route('redes-config.show', $red) . '" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>';
        $html .= '<a href="' . route('redes-config.edit', $red) . '" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>';

        $actionClass = $red->activo ? 'btn-outline-secondary' : 'btn-outline-success';
        $actionText = $red->activo ? 'Desactivar' : 'Activar';
        $html .= '<a href="' . route('redes-config.toggle', $red) . '" class="btn ' . $actionClass . '" title="' . $actionText . '">'
            . '<i class="bi bi-' . ($red->activo ? 'pause-circle' : 'play-circle') . '"></i></a>';

        $html .= '<form action="' . route('redes-config.destroy', $red) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar esta configuración?\');">';
        $html .= csrf_field() . method_field('DELETE');
        $html .= '<button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>';
        $html .= '</form>';

        $html .= '</div>';
        return $html;
    }
}
