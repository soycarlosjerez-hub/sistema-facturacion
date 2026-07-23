<?php

namespace App\Http\Controllers;

use App\Models\OrdenEmergencia;
use App\Models\Cliente;
use App\Exports\ClimatizacionOrdenesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class OrdenEmergenciaController extends Controller
{
    public function index(Request $request)
    {
        $query = OrdenEmergencia::query()
            ->with(['cliente', 'tecnico', 'creadoPor']);

        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo_falla')) {
            $query->where('tipo_falla', $request->tipo_falla);
        }
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$search}%"))
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($request->ajax() || $request->wantsJson()) {
            $total = $query->copy()->count();
            $ordenes = $query->latest()->paginate(request('length', 10), ['*'], 'page', request('start', 0));

            $rows = $ordenes->map(function ($o) {
                $badgeColor = match ($o->estado) {
                    'reportada' => 'danger',
                    'asignada' => 'warning',
                    'en_camino' => 'info',
                    'en_lugar' => 'primary',
                    'resuelta' => 'success',
                    'cerrada' => 'secondary',
                    default => 'secondary',
                };

                $slaOk = $o->slaCumplido();
                $slaBadge = $slaOk === null ? 'light' : ($slaOk ? 'success' : 'danger');

                return [
                    'DT_RowIndex' => $o->id,
                    'codigo' => $o->codigo,
                    'cliente' => $o->cliente ? $o->cliente->nombre : '-',
                    'prioridad' => OrdenEmergencia::PRIORIDADES[$o->prioridad] ?? $o->prioridad,
                    'tipo_falla' => OrdenEmergencia::TIPOS_FALLA[$o->tipo_falla] ?? $o->tipo_falla,
                    'direccion' => $o->direccion ?? '-',
                    'tecnico' => $o->tecnico ? $o->tecnico->name : '-',
                    'estado' => $o->estado,
                    'estado_label' => OrdenEmergencia::ESTADOS[$o->estado] ?? $o->estado,
                    'badge_color' => $badgeColor,
                    'sla_cumplido' => $slaOk,
                    'sla_badge' => $slaBadge,
                    'costo_final' => number_format($o->costo_final ?? 0, 2),
                    'acciones' => $this->getAccionesHtml($o),
                ];
            });

            return response()->json([
                'draw' => (int) request('draw', 1),
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $rows,
            ]);
        }

        $ordenes = $query->latest()->paginate(20)->withQueryString();
        $clientes = Cliente::orderBy('nombre')->get();
        $criticas = OrdenEmergencia::criticas()->count();
        $activas = $query->whereIn('estado', ['reportada', 'asignada', 'en_camino', 'en_lugar'])->count();
        return view('climatizacion.emergencias.index', compact('ordenes', 'clientes', 'criticas', 'activas'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        return view('climatizacion.emergencias.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'prioridad' => 'required|in:critica,alta,media,baja',
            'tipo_falla' => 'required|in:sin_frio,sin_calor,fuga_gas,ruido_excesivo,cortocircuito,otro',
            'direccion' => 'nullable|string|max:300',
            'contacto_telefono' => 'nullable|string|max:30',
            'descripcion' => 'required|string|min:10',
            'tecnico_id' => 'nullable|exists:users,id',
            'costo_estimado' => 'nullable|numeric|min:0',
        ]);

        $data['estado'] = 'reportada';
        $data['created_by'] = auth()->id();
        $data['costo_estimado'] = $data['costo_estimado'] ?? 0;

        try {
            $orden = OrdenEmergencia::create($data);
            $orden->calcularSLA();

            return redirect()->route('climatizacion.emergencias.show', $orden)
                ->with('success', 'Orden de emergencia creada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear orden de emergencia: ' . $e->getMessage());
        }
    }

    public function show(OrdenEmergencia $orden)
    {
        $orden->load(['cliente', 'tecnico', 'creadoPor']);
        return view('climatizacion.emergencias.show', compact('orden'));
    }

    public function edit(OrdenEmergencia $orden)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        return view('climatizacion.emergencias.edit', compact('orden', 'clientes'));
    }

    public function update(Request $request, OrdenEmergencia $orden)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'prioridad' => 'required|in:critica,alta,media,baja',
            'tipo_falla' => 'required|in:sin_frio,sin_calor,fuga_gas,ruido_excesivo,cortocircuito,otro',
            'direccion' => 'nullable|string|max:300',
            'contacto_telefono' => 'nullable|string|max:30',
            'estado' => 'required|in:reportada,asignada,en_camino,en_lugar,resuelta,cerrada',
            'descripcion' => 'required|string|min:10',
            'tecnico_id' => 'nullable|exists:users,id',
            'costo_estimado' => 'nullable|numeric|min:0',
            'costo_final' => 'nullable|numeric|min:0',
            'respondida_en' => 'nullable|date',
            'resuelta_en' => 'nullable|date',
        ]);

        $data['costo_estimado'] = $data['costo_estimado'] ?? 0;
        $data['costo_final'] = $data['costo_final'] ?? 0;

        try {
            $orden->update($data);

            if ($data['estado'] === 'resuelta' && !$orden->resuelta_en) {
                $orden->update(['resuelta_en' => now()]);
            }

            return redirect()->route('climatizacion.emergencias.show', $orden)
                ->with('success', 'Orden de emergencia actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar orden de emergencia: ' . $e->getMessage());
        }
    }

    public function asignar(Request $request, OrdenEmergencia $orden)
    {
        $data = $request->validate([
            'tecnico_id' => 'required|exists:users,id',
        ]);

        try {
            $orden->update(array_merge($data, ['estado' => 'asignada']));

            return redirect()->route('climatizacion.emergencias.show', $orden)
                ->with('success', 'Orden asignada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al asignar orden: ' . $e->getMessage());
        }
    }

    public function cerrar(OrdenEmergencia $orden)
    {
        try {
            $orden->update(['estado' => 'cerrada', 'resuelta_en' => now()]);

            return redirect()->route('climatizacion.emergencias.index')
                ->with('success', 'Orden cerrada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cerrar orden: ' . $e->getMessage());
        }
    }

    public function destroy(OrdenEmergencia $orden)
    {
        if (!in_array($orden->estado, ['reportada'])) {
            return back()->with('error', 'Solo se pueden eliminar órdenes en estado "Reportada".');
        }

        try {
            $orden->delete();
            return redirect()->route('climatizacion.emergencias.index')
                ->with('success', 'Orden de emergencia eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar orden de emergencia: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $query = OrdenEmergencia::query()->with(['cliente', 'tecnico']);
        $this->applyFilters($request, $query);
        return Excel::download(new ClimatizacionOrdenesExport($query), 'ordenes-emergencia.xlsx');
    }

    private function applyFilters(Request $request, $query)
    {
        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo_falla')) {
            $query->where('tipo_falla', $request->tipo_falla);
        }
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$search}%"));
            });
        }
    }

    private function getAccionesHtml(OrdenEmergencia $o): string
    {
        $html = '<div class="btn-group btn-group-sm">';
        $html .= '<a href="' . route('climatizacion.emergencias.show', $o) . '" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>';

        if ($o->estado === 'reportada') {
            $html .= '<form action="' . route('climatizacion.emergencias.asignar', $o) . '" method="POST" class="d-inline">';
            $html .= '@csrf @method("PATCH")';
            $html .= '<button type="submit" class="btn btn-outline-warning" title="Asignar"><i class="bi bi-person-check"></i></button>';
            $html .= '</form>';
        } elseif ($o->estado === 'asignada') {
            $html .= '<form action="' . route('climatizacion.emergencias.advance', $o) . '" method="POST" class="d-inline">';
            $html .= '@csrf @method("PATCH")';
            $html .= '<input type="hidden" name="next_state" value="en_camino">';
            $html .= '<button type="submit" class="btn btn-outline-info" title="En Camino"><i class="bi bi-car-front"></i></button>';
            $html .= '</form>';
        } elseif ($o->estado === 'en_camino') {
            $html .= '<form action="' . route('climatizacion.emergencias.advance', $o) . '" method="POST" class="d-inline">';
            $html .= '@csrf @method("PATCH")';
            $html .= '<input type="hidden" name="next_state" value="en_lugar">';
            $html .= '<button type="submit" class="btn btn-outline-primary" title="En Lugar"><i class="bi bi-geo-alt"></i></button>';
            $html .= '</form>';
        } elseif ($o->estado === 'en_lugar') {
            $html .= '<form action="' . route('climatizacion.emergencias.advance', $o) . '" method="POST" class="d-inline">';
            $html .= '@csrf @method("PATCH")';
            $html .= '<input type="hidden" name="next_state" value="resuelta">';
            $html .= '<button type="submit" class="btn btn-outline-success" title="Resolver"><i class="bi bi-check-lg"></i></button>';
            $html .= '</form>';
        } elseif ($o->estado === 'resuelta') {
            $html .= '<form action="' . route('climatizacion.emergencias.cerrar', $o) . '" method="POST" class="d-inline">';
            $html .= '@csrf @method("PATCH")';
            $html .= '<button type="submit" class="btn btn-outline-secondary" title="Cerrar"><i class="bi bi-lock"></i></button>';
            $html .= '</form>';
        }

        if (!in_array($o->estado, ['cerrada'])) {
            $html .= '<a href="' . route('climatizacion.emergencias.edit', $o) . '" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>';
        }

        if ($o->estado === 'reportada') {
            $html .= '<form action="' . route('climatizacion.emergencias.destroy', $o) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar?\');">';
            $html .= '@csrf @method("DELETE")';
            $html .= '<button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>';
            $html .= '</form>';
        }

        $html .= '</div>';
        return $html;
    }
}
