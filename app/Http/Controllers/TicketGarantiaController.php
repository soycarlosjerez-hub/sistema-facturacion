<?php

namespace App\Http\Controllers;

use App\Models\TicketGarantia;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Instalacion;
use App\Models\Compra;
use App\Exports\ClimatizacionTicketsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class TicketGarantiaController extends Controller
{
    public function index(Request $request)
    {
        $query = TicketGarantia::query()
            ->with(['producto', 'cliente', 'instalacion', 'tecnicoAsignado', 'creadoPor']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo_garantia')) {
            $query->where('tipo_garantia', $request->tipo_garantia);
        }
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$search}%"))
                  ->orWhere('descripcion_problema', 'like', "%{$search}%");
            });
        }

        if ($request->ajax() || $request->wantsJson()) {
            $total = $query->copy()->count();
            $tickets = $query->latest()->paginate(request('length', 10), ['*'], 'page', request('start', 0));

            $rows = $tickets->map(function ($t) {
                $badgeColor = match ($t->estado) {
                    'abierto' => 'primary',
                    'evaluando' => 'warning',
                    'aprobado' => 'success',
                    'rechazado' => 'danger',
                    'cerrado' => 'secondary',
                    default => 'secondary',
                };

                $vigente = $t->estaVigente();
                $diasRest = $t->diasRestantes();

                return [
                    'DT_RowIndex' => $t->id,
                    'codigo' => $t->codigo,
                    'cliente' => $t->cliente ? $t->cliente->nombre : '-',
                    'producto' => $t->producto ? $t->producto->nombre : '-',
                    'tipo_garantia' => TicketGarantia::TIPOS[$t->tipo_garantia] ?? $t->tipo_garantia,
                    'fecha_vencimiento' => $t->fecha_vencimiento_garantia ? $t->fecha_vencimiento_garantia->format('d/m/Y') : '-',
                    'dias_restantes' => $diasRest,
                    'vigente' => $vigente,
                    'estado' => $t->estado,
                    'estado_label' => TicketGarantia::ESTADOS[$t->estado] ?? $t->estado,
                    'badge_color' => $badgeColor,
                    'tecnico' => $t->tecnicoAsignado ? $t->tecnicoAsignado->name : '-',
                    'acciones' => $this->getAccionesHtml($t),
                ];
            });

            return response()->json([
                'draw' => (int) request('draw', 1),
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $rows,
            ]);
        }

        $tickets = $query->latest()->paginate(20)->withQueryString();
        $clientes = Cliente::orderBy('nombre')->get();
        $ticketsAbiertos = TicketGarantia::abiertos()->count();
        return view('climatizacion.garantias.index', compact('tickets', 'clientes', 'ticketsAbiertos'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::activos()->orderBy('nombre')->get();
        $instalaciones = Instalacion::where('estado', 'completada')->get();
        return view('climatizacion.garantias.create', compact('clientes', 'productos', 'instalaciones'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'producto_id' => 'nullable|exists:productos,id',
            'instalacion_id' => 'nullable|exists:instalaciones,id',
            'compra_original_id' => 'nullable|exists:compras,id',
            'fecha_compra' => 'required|date',
            'fecha_vencimiento_garantia' => 'required|date|after:fecha_compra',
            'tipo_garantia' => 'required|in:fabrica,instalacion',
            'descripcion_problema' => 'required|string|min:10',
            'tecnico_asignado_id' => 'nullable|exists:users,id',
        ]);

        $data['estado'] = 'abierto';
        $data['created_by'] = auth()->id();

        try {
            $ticket = TicketGarantia::create($data);

            return redirect()->route('climatizacion.garantias.show', $ticket)
                ->with('success', 'Ticket de garantía creado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear ticket: ' . $e->getMessage());
        }
    }

    public function show(TicketGarantia $ticket)
    {
        $ticket->load(['producto', 'cliente', 'instalacion', 'tecnicoAsignado', 'creadoPor']);
        return view('climatizacion.garantias.show', compact('ticket'));
    }

    public function edit(TicketGarantia $ticket)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::activos()->orderBy('nombre')->get();
        return view('climatizacion.garantias.edit', compact('ticket', 'clientes', 'productos'));
    }

    public function update(Request $request, TicketGarantia $ticket)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'producto_id' => 'nullable|exists:productos,id',
            'instalacion_id' => 'nullable|exists:instalaciones,id',
            'tipo_garantia' => 'required|in:fabrica,instalacion',
            'descripcion_problema' => 'required|string|min:10',
            'estado' => 'required|in:abierto,evaluando,aprobado,rechazado,cerrado',
            'resultado_evaluacion' => 'nullable|string|max:2000',
            'accion' => 'nullable|in:reparar,reemplazar,devolver',
            'tecnico_asignado_id' => 'nullable|exists:users,id',
            'cerrado_en' => 'nullable|date',
        ]);

        if ($data['estado'] === 'cerrado') {
            $data['cerrado_en'] = $data['cerrado_en'] ?? now();
        }

        try {
            $ticket->update($data);

            return redirect()->route('climatizacion.garantias.show', $ticket)
                ->with('success', 'Ticket actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar ticket: ' . $e->getMessage());
        }
    }

    public function evaluar(Request $request, TicketGarantia $ticket)
    {
        $data = $request->validate([
            'resultado_evaluacion' => 'required|string|max:2000',
            'accion' => 'required|in:reparar,reemplazar,devolver',
            'tecnico_asignado_id' => 'nullable|exists:users,id',
        ]);

        try {
            $ticket->update(array_merge($data, ['estado' => 'aprobado']));

            return redirect()->route('climatizacion.garantias.show', $ticket)
                ->with('success', 'Ticket evaluado y aprobado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al evaluar ticket: ' . $e->getMessage());
        }
    }

    public function rechazar(Request $request, TicketGarantia $ticket)
    {
        $data = $request->validate([
            'resultado_evaluacion' => 'required|string|max:2000',
        ]);

        try {
            $ticket->update(array_merge($data, ['estado' => 'rechazado']));

            return redirect()->route('climatizacion.garantias.show', $ticket)
                ->with('success', 'Ticket rechazado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al rechazar ticket: ' . $e->getMessage());
        }
    }

    public function destroy(TicketGarantia $ticket)
    {
        if ($ticket->estado === 'abierto') {
            return back()->with('error', 'No se puede eliminar un ticket abierto.');
        }

        try {
            $ticket->delete();
            return redirect()->route('climatizacion.garantias.index')
                ->with('success', 'Ticket eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar ticket: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $query = TicketGarantia::query()->with(['producto', 'cliente']);
        $this->applyFilters($request, $query);
        return Excel::download(new ClimatizacionTicketsExport($query), 'tickets-garantia.xlsx');
    }

    private function applyFilters(Request $request, $query)
    {
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo_garantia')) {
            $query->where('tipo_garantia', $request->tipo_garantia);
        }
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$search}%"));
            });
        }
    }

    private function getAccionesHtml(TicketGarantia $t): string
    {
        $html = '<div class="btn-group btn-group-sm">';
        $html .= '<a href="' . route('climatizacion.garantias.show', $t) . '" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>';
        if ($t->estado === 'abierto') {
            $html .= '<form action="' . route('climatizacion.garantias.evaluar', $t) . '" method="POST" class="d-inline">';
            $html .= '@csrf @method("PATCH")';
            $html .= '<button type="submit" class="btn btn-outline-success" title="Evaluar/Aprobar"><i class="bi bi-check-circle"></i></button>';
            $html .= '</form>';
            $html .= '<form action="' . route('climatizacion.garantias.rechazar', $t) . '" method="POST" class="d-inline">';
            $html .= '@csrf @method("PATCH")';
            $html .= '<button type="submit" class="btn btn-outline-danger" title="Rechazar"><i class="bi bi-x-circle"></i></button>';
            $html .= '</form>';
        }
        if (!in_array($t->estado, ['abierto'])) {
            $html .= '<a href="' . route('climatizacion.garantias.edit', $t) . '" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>';
        }
        if (!in_array($t->estado, ['abierto'])) {
            $html .= '<form action="' . route('climatizacion.garantias.destroy', $t) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar?\');">';
            $html .= '@csrf @method("DELETE")';
            $html .= '<button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>';
            $html .= '</form>';
        }
        $html .= '</div>';
        return $html;
    }
}
