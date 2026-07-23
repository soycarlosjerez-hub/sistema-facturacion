<?php

namespace App\Http\Controllers;

use App\Models\ContratoMantenimiento;
use App\Models\Cliente;
use App\Exports\ClimatizacionContratosExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ContratoMantenimientoController extends Controller
{
    public function index(Request $request)
    {
        $query = ContratoMantenimiento::query()
            ->with(['cliente', 'creadoPor']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('periodicidad')) {
            $query->where('tipo_periodicidad', $request->periodicidad);
        }
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$search}%"));
            });
        }

        if ($request->ajax() || $request->wantsJson()) {
            $total = $query->copy()->count();
            $contratos = $query->latest()->paginate(request('length', 10), ['*'], 'page', request('start', 0));

            $rows = $contratos->map(function ($c) {
                $badgeColor = match ($c->estado) {
                    'borrador' => 'secondary',
                    'activo' => 'success',
                    'vencido' => 'danger',
                    'cancelado' => 'dark',
                    default => 'secondary',
                };

                $proximosVencer = $c->estaActivo() && $c->vigencia_hasta <= now()->addDays(30);

                return [
                    'DT_RowIndex' => $c->id,
                    'codigo' => $c->codigo,
                    'cliente' => $c->cliente ? $c->cliente->nombre : '-',
                    'periodicidad' => ContratoMantenimiento::PERIODICIDADES[$c->tipo_periodicidad] ?? $c->tipo_periodicidad,
                    'vigencia_desde' => $c->vigencia_desde ? $c->vigencia_desde->format('d/m/Y') : '-',
                    'vigencia_hasta' => $c->vigencia_hasta ? $c->vigencia_hasta->format('d/m/Y') : '-',
                    'valor_mensual' => number_format($c->valor_mensual ?? 0, 2),
                    'visitas' => ($c->incluye_visitas ? "{$c->visitas_realizadas}/{$c->num_visitas_anuales}" : 'N/A'),
                    'estado' => $c->estado,
                    'estado_label' => ContratoMantenimiento::ESTADOS[$c->estado] ?? $c->estado,
                    'badge_color' => $proximosVencer ? 'warning' : $badgeColor,
                    'proximos_vencer' => $proximosVencer,
                    'acciones' => $this->getAccionesHtml($c),
                ];
            });

            return response()->json([
                'draw' => (int) request('draw', 1),
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $rows,
            ]);
        }

        $contratos = $query->latest()->paginate(20)->withQueryString();
        $clientes = Cliente::orderBy('nombre')->get();
        $proximosVencer = ContratoMantenimiento::proximosAVencer(30)->count();
        return view('climatizacion.contratos.index', compact('contratos', 'clientes', 'proximosVencer'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        return view('climatizacion.contratos.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_periodicidad' => 'required|in:mensual,trimestral,semestral,aunal',
            'equipos_cubiertos' => 'nullable|array',
            'vigencia_desde' => 'required|date',
            'vigencia_hasta' => 'required|date|after_or_equal:vigencia_desde',
            'valor_mensual' => 'required|numeric|min:0',
            'incluye_visitas' => 'boolean',
            'num_visitas_anuales' => 'nullable|integer|min:0',
            'deducible' => 'nullable|numeric|min:0',
            'cobertura_maxima' => 'nullable|numeric|min:0',
        ]);

        $data['estado'] = 'borrador';
        $data['created_by'] = auth()->id();
        $data['incluye_visitas'] = $request->has('incluye_visitas') ? true : false;
        $data['num_visitas_anuales'] = $data['num_visitas_anuales'] ?? 0;
        $data['visitas_realizadas'] = 0;
        $data['deducible'] = $data['deducible'] ?? 0;
        $data['cobertura_maxima'] = $data['cobertura_maxima'] ?? 0;

        try {
            $contrato = ContratoMantenimiento::create($data);

            return redirect()->route('climatizacion.contratos.show', $contrato)
                ->with('success', 'Contrato creado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear contrato: ' . $e->getMessage());
        }
    }

    public function show(ContratoMantenimiento $contrato)
    {
        $contrato->load(['cliente', 'visitas', 'mantenimientos', 'creadoPor']);
        return view('climatizacion.contratos.show', compact('contrato'));
    }

    public function edit(ContratoMantenimiento $contrato)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        return view('climatizacion.contratos.edit', compact('contrato', 'clientes'));
    }

    public function update(Request $request, ContratoMantenimiento $contrato)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_periodicidad' => 'required|in:mensual,trimestral,semestral,aunal',
            'equipos_cubiertos' => 'nullable|array',
            'vigencia_desde' => 'required|date',
            'vigencia_hasta' => 'required|date|after_or_equal:vigencia_desde',
            'valor_mensual' => 'required|numeric|min:0',
            'incluye_visitas' => 'boolean',
            'num_visitas_anuales' => 'nullable|integer|min:0',
            'deducible' => 'nullable|numeric|min:0',
            'cobertura_maxima' => 'nullable|numeric|min:0',
        ]);

        $data['incluye_visitas'] = $request->has('incluye_visitas') ? true : false;
        $data['num_visitas_anuales'] = $data['num_visitas_anuales'] ?? 0;
        $data['deducible'] = $data['deducible'] ?? 0;
        $data['cobertura_maxima'] = $data['cobertura_maxima'] ?? 0;

        try {
            $contrato->update($data);

            return redirect()->route('climatizacion.contratos.show', $contrato)
                ->with('success', 'Contrato actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar contrato: ' . $e->getMessage());
        }
    }

    public function activar(ContratoMantenimiento $contrato)
    {
        try {
            $contrato->update(['estado' => 'activo']);
            return redirect()->route('climatizacion.contratos.show', $contrato)
                ->with('success', 'Contrato activado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al activar contrato: ' . $e->getMessage());
        }
    }

    public function cancelar(ContratoMantenimiento $contrato)
    {
        try {
            $contrato->update(['estado' => 'cancelado']);
            return redirect()->route('climatizacion.contratos.index')
                ->with('success', 'Contrato cancelado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cancelar contrato: ' . $e->getMessage());
        }
    }

    public function destroy(ContratoMantenimiento $contrato)
    {
        if ($contrato->estado === 'activo') {
            return back()->with('error', 'No se puede eliminar un contrato activo. Cancelarlo primero.');
        }

        try {
            $contrato->delete();
            return redirect()->route('climatizacion.contratos.index')
                ->with('success', 'Contrato eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar contrato: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $query = ContratoMantenimiento::query()->with('cliente');
        $this->applyFilters($request, $query);
        return Excel::download(new ClimatizacionContratosExport($query), 'contratos.xlsx');
    }

    private function applyFilters(Request $request, $query)
    {
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('periodicidad')) {
            $query->where('tipo_periodicidad', $request->periodicidad);
        }
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$search}%"));
            });
        }
    }

    private function getAccionesHtml(ContratoMantenimiento $c): string
    {
        $html = '<div class="btn-group btn-group-sm">';
        $html .= '<a href="' . route('climatizacion.contratos.show', $c) . '" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>';
        if (!in_array($c->estado, ['cancelado'])) {
            $html .= '<a href="' . route('climatizacion.contratos.edit', $c) . '" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>';
        }
        if ($c->estado === 'borrador') {
            $html .= '<form action="' . route('climatizacion.contratos.activar', $c) . '" method="POST" class="d-inline">';
            $html .= '@csrf @method("PATCH")';
            $html .= '<button type="submit" class="btn btn-outline-success" title="Activar"><i class="bi bi-play-circle"></i></button>';
            $html .= '</form>';
        }
        if (in_array($c->estado, ['activo', 'borrador'])) {
            $html .= '<form action="' . route('climatizacion.contratos.cancelar', $c) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Cancelar este contrato?\');">';
            $html .= '@csrf @method("PATCH")';
            $html .= '<button type="submit" class="btn btn-outline-danger" title="Cancelar"><i class="bi bi-x-circle"></i></button>';
            $html .= '</form>';
        }
        if (!in_array($c->estado, ['activo', 'cancelado'])) {
            $html .= '<form action="' . route('climatizacion.contratos.destroy', $c) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar?\');">';
            $html .= '@csrf @method("DELETE")';
            $html .= '<button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>';
            $html .= '</form>';
        }
        $html .= '</div>';
        return $html;
    }
}
