<?php

namespace App\Http\Controllers;

use App\Models\Mantenimiento;
use App\Models\Cliente;
use App\Models\ContratoMantenimiento;
use App\Exports\ClimatizacionMantenimientosExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class MantenimientoController extends Controller
{
    public function index(Request $request)
    {
        $query = Mantenimiento::query()
            ->with(['cliente', 'tecnico', 'contrato', 'creadoPor']);

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tecnico_id')) {
            $query->where('tecnico_id', $request->tecnico_id);
        }
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$search}%"))
                  ->orWhere('descripcion_falla', 'like', "%{$search}%");
            });
        }

        if ($request->ajax() || $request->wantsJson()) {
            $total = $query->copy()->count();
            $mantenimientos = $query->latest()->paginate(request('length', 10), ['*'], 'page', request('start', 0));

            $rows = $mantenimientos->map(function ($mtto) {
                $badgeColor = match ($mtto->estado) {
                    'pendiente' => 'secondary',
                    'programada' => 'info',
                    'en_curso' => 'warning',
                    'completado' => 'success',
                    'cancelado' => 'danger',
                    default => 'secondary',
                };

                return [
                    'DT_RowIndex' => $mtto->id,
                    'numero' => $mtto->numero,
                    'cliente' => $mtto->cliente ? $mtto->cliente->nombre : '-',
                    'tipo' => Mantenimiento::TIPOS[$mtto->tipo] ?? $mtto->tipo,
                    'tecnico' => $mtto->tecnico ? $mtto->tecnico->name : '-',
                    'descripcion_falla' => mb_substr($mtto->descripcion_falla ?? '', 0, 40) . (($mtto->descripcion_falla ?? '') > 40 ? '...' : ''),
                    'total' => number_format($mtto->total ?? 0, 2),
                    'estado' => $mtto->estado,
                    'estado_label' => Mantenimiento::ESTADOS[$mtto->estado] ?? $mtto->estado,
                    'badge_color' => $badgeColor,
                    'acciones' => $this->getAccionesHtml($mtto),
                ];
            });

            return response()->json([
                'draw' => (int) request('draw', 1),
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $rows,
            ]);
        }

        $mantenimientos = $query->latest()->paginate(20)->withQueryString();
        $clientes = Cliente::orderBy('nombre')->get();
        return view('climatizacion.mantenimientos.index', compact('mantenimientos', 'clientes'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $contratos = ContratoMantenimiento::activos()->get();
        return view('climatizacion.mantenimientos.create', compact('clientes', 'contratos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tecnico_id' => 'nullable|exists:users,id',
            'tipo' => 'required|in:preventivo,correctivo',
            'contrato_mantenimiento_id' => 'nullable|exists:contratos_mantenimiento,id',
            'descripcion_falla' => 'nullable|string|max:2000',
            'solucion_aplicada' => 'nullable|string|max:2000',
            'repuestos_usados' => 'nullable|array',
            'repuestos_usados.*.nombre' => 'required_with:repuestos_usados|string|max:100',
            'repuestos_usados.*.cantidad' => 'nullable|integer|min:1',
            'repuestos_usados.*.precio' => 'nullable|numeric|min:0',
            'costo_repuestos' => 'nullable|numeric|min:0',
            'mano_de_obra' => 'nullable|numeric|min:0',
            'programada_para' => 'nullable|date',
        ]);

        $data['estado'] = 'pendiente';
        $data['created_by'] = auth()->id();
        $data['costo_repuestos'] = $data['costo_repuestos'] ?? 0;
        $data['mano_de_obra'] = $data['mano_de_obra'] ?? 0;

        try {
            $mtto = Mantenimiento::create($data);
            $mtto->calcularTotal();

            return redirect()->route('climatizacion.mantenimientos.show', $mtto)
                ->with('success', 'Mantenimiento creado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear mantenimiento: ' . $e->getMessage());
        }
    }

    public function show(Mantenimiento $mantenimiento)
    {
        $mantenimiento->load(['cliente', 'tecnico', 'contrato', 'creadoPor']);
        return view('climatizacion.mantenimientos.show', compact('mantenimiento'));
    }

    public function edit(Mantenimiento $mantenimiento)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $contratos = ContratoMantenimiento::activos()->get();
        return view('climatizacion.mantenimientos.edit', compact('mantenimiento', 'clientes', 'contratos'));
    }

    public function update(Request $request, Mantenimiento $mantenimiento)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tecnico_id' => 'nullable|exists:users,id',
            'tipo' => 'required|in:preventivo,correctivo',
            'estado' => 'required|in:pendiente,programada,en_curso,completado,cancelado',
            'contrato_mantenimiento_id' => 'nullable|exists:contratos_mantenimiento,id',
            'descripcion_falla' => 'nullable|string|max:2000',
            'solucion_aplicada' => 'nullable|string|max:2000',
            'repuestos_usados' => 'nullable|array',
            'repuestos_usados.*.nombre' => 'required_with:repuestos_usados|string|max:100',
            'repuestos_usados.*.cantidad' => 'nullable|integer|min:1',
            'repuestos_usados.*.precio' => 'nullable|numeric|min:0',
            'costo_repuestos' => 'nullable|numeric|min:0',
            'mano_de_obra' => 'nullable|numeric|min:0',
            'programada_para' => 'nullable|date',
            'completada_en' => 'nullable|date',
        ]);

        $data['costo_repuestos'] = $data['costo_repuestos'] ?? 0;
        $data['mano_de_obra'] = $data['mano_de_obra'] ?? 0;

        try {
            $mantenimiento->update($data);
            $mantenimiento->calcularTotal();

            return redirect()->route('climatizacion.mantenimientos.show', $mantenimiento)
                ->with('success', 'Mantenimiento actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar mantenimiento: ' . $e->getMessage());
        }
    }

    public function destroy(Mantenimiento $mantenimiento)
    {
        if ($mantenimiento->estado === 'completado') {
            return back()->with('error', 'No se puede eliminar un mantenimiento completado.');
        }

        try {
            $mantenimiento->delete();
            return redirect()->route('climatizacion.mantenimientos.index')
                ->with('success', 'Mantenimiento eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar mantenimiento: ' . $e->getMessage());
        }
    }

    public function advance(Request $request, Mantenimiento $mantenimiento)
    {
        $nextState = $request->input('next_state');
        $allowedTransitions = [
            'pendiente' => 'programada',
            'programada' => 'en_curso',
            'en_curso' => 'completado',
        ];

        if (!isset($allowedTransitions[$mantenimiento->estado]) || $allowedTransitions[$mantenimiento->estado] !== $nextState) {
            return back()->with('error', 'Transición de estado no válida.');
        }

        $updateData = ['estado' => $nextState];
        if ($nextState === 'completado') {
            $updateData['completada_en'] = now();
        }

        try {
            $mantenimiento->update($updateData);
            return redirect()->route('climatizacion.mantenimientos.show', $mantenimiento)
                ->with('success', 'Estado avanzado a ' . Mantenimiento::ESTADOS[$nextState] . '.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al avanzar estado: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $query = Mantenimiento::query()->with(['cliente', 'tecnico']);
        $this->applyFilters($request, $query);
        return Excel::download(new ClimatizacionMantenimientosExport($query), 'mantenimientos.xlsx');
    }

    private function applyFilters(Request $request, $query)
    {
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$search}%"));
            });
        }
    }

    private function getAccionesHtml(Mantenimiento $mtto): string
    {
        $html = '<div class="btn-group btn-group-sm">';
        $html .= '<a href="' . route('climatizacion.mantenimientos.show', $mtto) . '" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>';
        if (!in_array($mtto->estado, ['completado', 'cancelado'])) {
            $html .= '<a href="' . route('climatizacion.mantenimientos.edit', $mtto) . '" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>';
        }
        if (!in_array($mtto->estado, ['completado', 'cancelado'])) {
            $nextState = match ($mtto->estado) {
                'pendiente' => 'programada',
                'programada' => 'en_curso',
                default => null,
            };
            if ($nextState) {
                $html .= '<form action="' . route('climatizacion.mantenimientos.advance', $mtto) . '" method="POST" class="d-inline">';
                $html .= '@csrf @method("PATCH")';
                $html .= '<input type="hidden" name="next_state" value="' . $nextState . '">';
                $html .= '<button type="submit" class="btn btn-outline-primary" title="Avanzar estado"><i class="bi bi-forward"></i></button>';
                $html .= '</form>';
            }
        }
        if (!in_array($mtto->estado, ['completado', 'cancelado'])) {
            $html .= '<form action="' . route('climatizacion.mantenimientos.destroy', $mtto) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar?\');">';
            $html .= '@csrf @method("DELETE")';
            $html .= '<button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>';
            $html .= '</form>';
        }
        $html .= '</div>';
        return $html;
    }
}
