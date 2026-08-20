<?php

namespace App\Http\Controllers;

use App\Models\Presupuesto;
use App\Models\PresupuestoItem;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;

class PresupuestoController extends Controller
{
    public function index(Request $request)
    {
        $query = Presupuesto::query()->with('cliente');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($search = $this->dtSearch($request)) {
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                    ->orWhereHas('cliente', function ($q2) use ($search) {
                        $q2->where('nombre', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->ajax() || $request->wantsJson()) {
            $total = $query->count();
            $presupuestos = $query->latest()->paginate(request('length', 10), ['*'], 'page', (int) floor(request('start', 0) / max(1, (int) request('length', 10))) + 1);

            $rows = $presupuestos->map(function ($presupuesto) {
                return [
                    'DT_RowIndex' => $presupuesto->id,
                    'numero' => $presupuesto->numero,
                    'cliente' => $presupuesto->cliente ? $presupuesto->cliente->nombre : '-',
                    'subtotal' => number_format($presupuesto->subtotal, 2),
                    'itbis' => number_format($presupuesto->itbis, 2),
                    'total' => number_format($presupuesto->total, 2),
                    'estado' => $presupuesto->estado_label,
                    'valido_hasta' => $presupuesto->valido_hasta ? $presupuesto->valido_hasta->format('Y-m-d') : '-',
                    'acciones' => $this->getAccionesHtml($presupuesto),
                ];
            });

            return response()->json([
                'draw' => (int) request('draw', 1),
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $rows,
            ]);
        }

        $presupuestos = $query->latest()->paginate(20)->withQueryString();
        $clientes = Cliente::where('activo', true)->orderBy('nombre')->get();

        return view('presupuestos.index', compact('presupuestos', 'clientes'));
    }

    public function create()
    {
        $clientes = Cliente::where('activo', true)->orderBy('nombre')->get();
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();
        return view('presupuestos.create', compact('clientes', 'productos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'notas' => 'nullable|string',
            'valido_hasta' => 'nullable|date',
        ]);

        try {
            $presupuesto = Presupuesto::create($data);
            return redirect()->route('presupuestos.edit', $presupuesto)
                ->with('success', 'Presupuesto creado. Agregue los ítems.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear presupuesto: ' . $e->getMessage());
        }
    }

    public function show(Presupuesto $presupuesto)
    {
        $presupuesto->load(['items.producto', 'cliente']);
        return view('presupuestos.show', compact('presupuesto'));
    }

    public function edit(Presupuesto $presupuesto)
    {
        $presupuesto->load('items.producto');
        $clientes = Cliente::where('activo', true)->orderBy('nombre')->get();
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();
        return view('presupuestos.edit', compact('presupuesto', 'clientes', 'productos'));
    }

    public function update(Request $request, Presupuesto $presupuesto)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'notas' => 'nullable|string',
            'valido_hasta' => 'nullable|date',
        ]);

        try {
            $presupuesto->update($data);
            return redirect()->route('presupuestos.show', $presupuesto)
                ->with('success', 'Presupuesto actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar presupuesto: ' . $e->getMessage());
        }
    }

    public function destroy(Presupuesto $presupuesto)
    {
        if ($presupuesto->estado !== 'borrador') {
            return back()->with('error', 'Solo se pueden eliminar presupuestos en estado borrador.');
        }

        try {
            $presupuesto->delete();
            return redirect()->route('presupuestos.index')
                ->with('success', 'Presupuesto eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar presupuesto: ' . $e->getMessage());
        }
    }

    public function cambiarEstado(Presupuesto $presupuesto, Request $request)
    {
        $request->validate([
            'estado' => 'required|in:borrador,enviada,aprobada,rechazada,vencida',
        ]);

        try {
            $presupuesto->update(['estado' => $request->estado]);
            return back()->with('success', 'Estado actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar estado: ' . $e->getMessage());
        }
    }

    public function convertirEnVenta(Presupuesto $presupuesto)
    {
        try {
            // Logic to convert presupuesto to a sale
            // This is a simplified version
            return redirect()->route('ventas.create')
                ->with('success', 'Función de conversión a venta - en desarrollo.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al convertir en venta: ' . $e->getMessage());
        }
    }

    public function addItems(Presupuesto $presupuesto, Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.descripcion' => 'required|string',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'items.*.tipo_item' => 'required|in:producto,mano_obra,desplazamiento,servicio,licencia,otro',
            'items.*.descuento' => 'nullable|numeric|min:0|max:100',
            'items.*.itbis_porcentaje' => 'nullable|numeric|min:0|max:18',
        ]);

        try {
            foreach ($data['items'] as $itemData) {
                $item = new PresupuestoItem([
                    'presupuesto_id' => $presupuesto->id,
                    'descripcion' => $itemData['descripcion'],
                    'cantidad' => $itemData['cantidad'],
                    'precio_unitario' => $itemData['precio_unitario'],
                    'tipo_item' => $itemData['tipo_item'],
                    'descuento' => $itemData['descuento'] ?? 0,
                    'itbis_porcentaje' => $itemData['itbis_porcentaje'] ?? 18,
                ]);
                $item->calcular();
                $presupuesto->items()->save($item);
            }

            $presupuesto->calcularTotales();

            return redirect()->route('presupuestos.edit', $presupuesto)
                ->with('success', 'Ítems agregados correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al agregar ítems: ' . $e->getMessage());
        }
    }

    public function removeItem(Presupuesto $presupuesto, PresupuestoItem $item)
    {
        if ($item->presupuesto_id !== $presupuesto->id) {
            return back()->with('error', 'Ítem no pertenece a este presupuesto.');
        }

        try {
            $item->delete();
            $presupuesto->calcularTotales();
            return back()->with('success', 'Ítem eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar ítem: ' . $e->getMessage());
        }
    }

    private function getAccionesHtml(Presupuesto $presupuesto): string
    {
        $html = '<div class="btn-group btn-group-sm">';
        $html .= '<a href="' . route('presupuestos.show', $presupuesto) . '" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>';
        $html .= '<a href="' . route('presupuestos.edit', $presupuesto) . '" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>';

        if ($presupuesto->estado === 'borrador') {
            $html .= '<form action="' . route('presupuestos.destroy', $presupuesto) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar este presupuesto?\');">';
            $html .= csrf_field() . method_field('DELETE');
            $html .= '<button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>';
            $html .= '</form>';
        }

        $html .= '</div>';
        return $html;
    }
}
