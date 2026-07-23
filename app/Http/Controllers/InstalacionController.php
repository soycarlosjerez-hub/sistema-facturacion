<?php

namespace App\Http\Controllers;

use App\Models\Instalacion;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Sucursal;
use App\Exports\ClimatizacionInstalacionesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class InstalacionController extends Controller
{
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
            $total = $query->copy()->count();
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
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'instalador_id' => 'nullable|exists:users,id',
            'direccion_instalacion' => 'nullable|string|max:300',
            'tipo_inmueble' => 'required|in:casa,apartamento,local,industrial',
            'programada_para' => 'nullable|date|after_or_equal:today',
            'nota_interna' => 'nullable|string|max:2000',
            'productos' => 'nullable|array',
            'productos.*.producto_id' => 'exists:productos,id',
            'productos.*.cantidad' => 'integer|min:1',
            'productos.*.precio_unitario' => 'numeric|min:0',
        ]);

        $data['estado'] = 'pendiente';
        $data['created_by'] = auth()->id();

        try {
            $inst = Instalacion::create($data);

            if ($request->filled('productos')) {
                foreach ($request->productos as $prod) {
                    $inst->productos()->attach($prod['producto_id'], [
                        'cantidad' => $prod['cantidad'] ?? 1,
                        'precio_unitario' => $prod['precio_unitario'] ?? 0,
                    ]);
                }
            }

            $inst->refresh();
            $inst->calcularTotal();

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

    public function update(Request $request, Instalacion $instalacion)
    {
        $data = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'instalador_id' => 'nullable|exists:users,id',
            'estado' => 'required|in:pendiente,programada,en_progreso,completada,cancelada',
            'direccion_instalacion' => 'nullable|string|max:300',
            'tipo_inmueble' => 'required|in:casa,apartamento,local,industrial',
            'programada_para' => 'nullable|date',
            'completada_en' => 'nullable|date|after_or_equal:programada_para',
            'nota_interna' => 'nullable|string|max:2000',
            'productos' => 'nullable|array',
            'productos.*.producto_id' => 'exists:productos,id',
            'productos.*.cantidad' => 'integer|min:1',
            'productos.*.precio_unitario' => 'numeric|min:0',
        ]);

        try {
            $instalacion->update($data);

            if ($request->filled('productos')) {
                $instalacion->productos()->detach();
                foreach ($request->productos as $prod) {
                    $instalacion->productos()->attach($prod['producto_id'], [
                        'cantidad' => $prod['cantidad'] ?? 1,
                        'precio_unitario' => $prod['precio_unitario'] ?? 0,
                    ]);
                }
            }

            $instalacion->calcularTotal();

            return redirect()->route('climatizacion.instalaciones.show', $instalacion)
                ->with('success', 'Instalación actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar instalación: ' . $e->getMessage());
        }
    }

    public function destroy(Instalacion $instalacion)
    {
        if (in_array($instalacion->estado, ['completada'])) {
            return back()->with('error', 'No se puede eliminar una instalación completada.');
        }

        try {
            $instalacion->delete();
            return redirect()->route('climatizacion.instalaciones.index')
                ->with('success', 'Instalación eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar instalación: ' . $e->getMessage());
        }
    }

    public function advance(Request $request, Instalacion $instalacion)
    {
        $nextState = $request->input('next_state');
        $allowedTransitions = [
            'pendiente' => 'programada',
            'programada' => 'en_progreso',
            'en_progreso' => 'completada',
        ];

        if (!isset($allowedTransitions[$instalacion->estado]) || $allowedTransitions[$instalacion->estado] !== $nextState) {
            return back()->with('error', 'Transición de estado no válida.');
        }

        $updateData = ['estado' => $nextState];
        if ($nextState === 'completada') {
            $updateData['completada_en'] = now();
        }

        try {
            $instalacion->update($updateData);
            return redirect()->route('climatizacion.instalaciones.show', $instalacion)
                ->with('success', 'Estado avanzado a ' . Instalacion::ESTADOS[$nextState] . '.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al avanzar estado: ' . $e->getMessage());
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
