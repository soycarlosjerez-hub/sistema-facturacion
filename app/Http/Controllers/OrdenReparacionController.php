<?php

namespace App\Http\Controllers;

use App\Models\OrdenReparacion;
use App\Models\Cliente;
use App\Models\Equipo;
use App\Models\Tecnico;
use App\Models\DetallePiezaReparacion;
use App\Models\Producto;
use Illuminate\Http\Request;

class OrdenReparacionController extends Controller
{
    public function index(Request $request)
    {
        $query = OrdenReparacion::query()
            ->with(['cliente', 'equipo', 'tecnico'])
            ->select('ordenes_reparacion.*');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo_servicio')) {
            $query->where('tipo_servicio', $request->tipo_servicio);
        }
        if ($request->filled('tecnico_id')) {
            $query->where('tecnico_id', $request->tecnico_id);
        }
        if ($request->filled('desde')) {
            $query->whereDate('fecha_recibo', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha_recibo', '<=', $request->hasta);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_orden', 'like', "%{$search}%")
                    ->orWhereHas('cliente', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%")
                            ->orWhere('rnc_cedula', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->ajax() || $request->wantsJson()) {
            $total = $query->copy()->count();
            $ordenes = $query->latest()->paginate(
                request('length', 10),
                ['*'],
                'page',
                request('start', 0)
            );

            $rows = $ordenes->map(function ($orden) {
                return [
                    'DT_RowIndex' => $orden->id,
                    'numero_orden' => $orden->numero_orden,
                    'cliente' => $orden->cliente ? $orden->cliente->nombre : '-',
                    'equipo' => $orden->equipo ? "{$orden->equipo->marca} {$orden->equipo->modelo}" : '-',
                    'tipo_servicio' => $orden->tipo_servicio_label ?? ucfirst($orden->tipo_servicio),
                    'tecnico' => $orden->tecnico ? $orden->tecnico->nombre : 'Sin asignar',
                    'total' => number_format($orden->total ?? 0, 2),
                    'estado' => $orden->estado,
                    'estado_label' => $orden->estado_label ?? ucfirst($orden->estado),
                    'fecha_recibo' => $orden->fecha_recibo ? $orden->fecha_recibo->format('d/m/Y') : '',
                    'fecha_entrega_estimada' => $orden->fecha_entrega_estimada ? $orden->fecha_entrega_estimada->format('d/m/Y') : '',
                    'acciones' => $this->getAccionesHtml($orden),
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
        $estados = [
            'recibido' => 'Recibido',
            'pendiente' => 'Pendiente',
            'en_reparacion' => 'En Reparación',
            'esperando_piezas' => 'Esperando Piezas',
            'terminado' => 'Terminado',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado',
        ];
        $tecnicos = Tecnico::activos()->orderBy('nombre')->get();

        return view('tecnicas.index', compact('ordenes', 'estados', 'tecnicos'));
    }

    public function create(Request $request)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $equipos = Equipo::where('estado', 'disponible')->orderBy('serial_imei')->get();
        $tecnicos = Tecnico::activos()->orderBy('nombre')->get();

        $clientePreselect = $request->integer('cliente_id');

        return view('tecnicas.create', compact('clientes', 'equipos', 'tecnicos', 'clientePreselect'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_servicio' => 'required|in:hardware,software,desbloqueo,recuperacion_datos,mantenimiento,personalizacion,otro',
            'equipo_id' => 'nullable|exists:equipos,id',
            'tecnico_id' => 'nullable|exists:tecnicos,id',
            'problema_reportado' => 'required|string',
            'fecha_recibo' => 'nullable|date',
            'fecha_entrega_estimada' => 'nullable|date',
            'costo_piezas' => 'nullable|numeric|min:0',
            'mano_obra' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'metodo_pago' => 'nullable|in:efectivo,transferencia,tarjeta,NCF',
            'garantia_extendida' => 'boolean',
            'notas' => 'nullable|string|max:2000',
        ]);

        $data['garantia_extendida'] = $request->has('garantia_extendida') ? true : false;
        $data['fecha_recibo'] = $data['fecha_recibo'] ?? now();

        try {
            $orden = OrdenReparacion::create($data);

            if ($orden->equipo_id) {
                $orden->equipo->update(['estado' => 'en_reparacion']);
            }

            if ($data['costo_piezas'] > 0 || $data['mano_obra'] > 0) {
                $orden->calcularTotales();
            }

            return redirect()->route('tecnicas.show', $orden)
                ->with('success', 'Orden de reparación creada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear la orden: ' . $e->getMessage());
        }
    }

    public function show(OrdenReparacion $orden)
    {
        $orden->load(['cliente', 'equipo', 'tecnico', 'detallesPiezas.producto']);
        return view('tecnicas.show', compact('orden'));
    }

    public function edit(OrdenReparacion $orden)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $equipos = Equipo::orderBy('serial_imei')->get();
        $tecnicos = Tecnico::activos()->orderBy('nombre')->get();

        return view('tecnicas.edit', compact('orden', 'clientes', 'equipos', 'tecnicos'));
    }

    public function update(Request $request, OrdenReparacion $orden)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_servicio' => 'required|in:hardware,software,desbloqueo,recuperacion_datos,mantenimiento,personalizacion,otro',
            'equipo_id' => 'nullable|exists:equipos,id',
            'tecnico_id' => 'nullable|exists:tecnicos,id',
            'problema_reportado' => 'required|string',
            'diagnostico' => 'nullable|string',
            'solucion_aplicada' => 'nullable|string',
            'fecha_entrega_estimada' => 'nullable|date',
            'costo_piezas' => 'nullable|numeric|min:0',
            'mano_obra' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'metodo_pago' => 'nullable|in:efectivo,transferencia,tarjeta,NCF',
            'garantia_extendida' => 'boolean',
            'notas' => 'nullable|string|max:2000',
        ]);

        $data['garantia_extendida'] = $request->has('garantia_extendida') ? true : false;

        try {
            if (isset($data['costo_piezas']) || isset($data['mano_obra']) || isset($data['descuento'])) {
                $orden->fill($data);
                $orden->calcularTotales();
            } else {
                $orden->update($data);
            }

            return redirect()->route('tecnicas.show', $orden)
                ->with('success', 'Orden actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar la orden: ' . $e->getMessage());
        }
    }

    public function cambiarEstado(Request $request, OrdenReparacion $orden)
    {
        $data = $request->validate([
            'estado' => 'required|in:recibido,pendiente,en_reparacion,esperando_piezas,terminado,entregado,cancelado',
        ]);

        try {
            $orden->update(['estado' => $data['estado']]);

            if ($data['estado'] === 'entregado' && $orden->equipo_id) {
                $orden->equipo->update(['estado' => 'disponible']);
            }

            return back()->with('success', "Estado cambiado a '{$orden->estado_label}'.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }

    public function entregar(OrdenReparacion $orden)
    {
        try {
            $orden->update([
                'estado' => 'entregado',
                'fecha_entrega_real' => now(),
            ]);

            if ($orden->equipo_id) {
                $orden->equipo->update(['estado' => 'disponible']);
            }

            return redirect()->route('tecnicas.show', $orden)
                ->with('success', 'Orden entregada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al entregar la orden: ' . $e->getMessage());
        }
    }

    public function cancelar(Request $request, OrdenReparacion $orden)
    {
        $data = $request->validate([
            'motivo' => 'nullable|string|max:500',
        ]);

        try {
            foreach ($orden->detallesPiezas as $detalle) {
                $producto = $detalle->producto;
                if ($producto && $producto->stock !== null) {
                    \DB::table('almacen_movimientos')->insert([
                        'producto_id' => $producto->id,
                        'tipo' => 'entrada',
                        'cantidad' => $detalle->cantidad,
                        'nota' => "Devuelta por cancelación Orden #{$orden->numero_orden}",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $orden->update([
                'estado' => 'cancelado',
                'notas' => ($orden->notas ?? '') . ' [CANCELADA: ' . ($data['motivo'] ?? 'Sin motivo') . ']',
            ]);

            if ($orden->equipo_id) {
                $orden->equipo->update(['estado' => 'disponible']);
            }

            return redirect()->route('tecnicas.show', $orden)
                ->with('success', 'Orden cancelada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cancelar la orden: ' . $e->getMessage());
        }
    }

    public function agregarPieza(Request $request, OrdenReparacion $orden)
    {
        $data = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        try {
            $producto = Producto::findOrFail($data['producto_id']);

            \DB::table('detalle_pieza_reparacion')->insert([
                'orden_reparacion_id' => $orden->id,
                'producto_id' => $producto->id,
                'cantidad' => $data['cantidad'],
                'costo_unitario' => $producto->precio_compra ?? 0,
                'precio_venta' => $producto->precio ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($producto->stock !== null && $producto->stock >= $data['cantidad']) {
                \DB::table('almacen_movimientos')->insert([
                    'producto_id' => $producto->id,
                    'tipo' => 'salida',
                    'cantidad' => $data['cantidad'],
                    'nota' => "Orden #{$orden->numero_orden}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $orden->calcularTotales();

            return back()->with('success', 'Pieza agregada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al agregar pieza: ' . $e->getMessage());
        }
    }

    public function quitarPieza(Request $request, OrdenReparacion $orden, DetallePiezaReparacion $detalle)
    {
        try {
            $producto = $detalle->producto;
            if ($producto && $producto->stock !== null) {
                \DB::table('almacen_movimientos')->insert([
                    'producto_id' => $producto->id,
                    'tipo' => 'entrada',
                    'cantidad' => $detalle->cantidad,
                    'nota' => "Devuelta desde Orden #{$orden->numero_orden}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $detalle->delete();
            $orden->calcularTotales();

            return back()->with('success', 'Pieza retirada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al retirar pieza: ' . $e->getMessage());
        }
    }

    public function destroy(OrdenReparacion $orden)
    {
        if (!in_array($orden->estado, ['cancelado', 'recibido', 'pendiente'])) {
            return back()->with('error', 'Solo se pueden eliminar órdenes canceladas o sin procesar.');
        }

        try {
            $orden->delete();
            return redirect()->route('tecnicas.index')
                ->with('success', 'Orden eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la orden: ' . $e->getMessage());
        }
    }

    public function getEstadisticas()
    {
        $stats = [
            'total' => OrdenReparacion::count(),
            'pendientes' => OrdenReparacion::whereIn('estado', ['recibido', 'pendiente'])->count(),
            'en_reparacion' => OrdenReparacion::where('estado', 'en_reparacion')->count(),
            'listos' => OrdenReparacion::where('estado', 'terminado')->count(),
            'entregadas' => OrdenReparacion::where('estado', 'entregado')->count(),
            'canceladas' => OrdenReparacion::where('estado', 'cancelado')->count(),
            'ingresos_mes' => OrdenReparacion::whereMonth('created_at', now()->month)->sum('total'),
        ];

        return response()->json($stats);
    }

    public function getOrdenPorImei(Request $request)
    {
        $imei = $request->get('q', '');

        if (strlen($imei) < 4) {
            return response()->json([]);
        }

        $equipos = Equipo::where('serial_imei', 'like', "%{$imei}%")
            ->with(['ordenesReparacion.cliente'])
            ->take(10)
            ->get();

        $resultados = [];
        foreach ($equipos as $equipo) {
            foreach ($equipo->ordenesReparacion as $orden) {
                $resultados[] = [
                    'id' => $orden->id,
                    'numero_orden' => $orden->numero_orden,
                    'equipo_serial' => $equipo->serial_imei,
                    'equipo_modelo' => "{$equipo->marca} {$equipo->modelo}",
                    'cliente' => $orden->cliente ? $orden->cliente->nombre : '-',
                    'estado' => $orden->estado_label ?? $orden->estado,
                    'total' => number_format($orden->total ?? 0, 2),
                ];
            }
        }

        return response()->json($resultados);
    }

    private function getAccionesHtml(OrdenReparacion $orden): string
    {
        $html = '<div class="btn-group btn-group-sm">';
        $html .= '<a href="' . route('tecnicas.show', $orden) . '" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>';
        $html .= '<a href="' . route('tecnicas.edit', $orden) . '" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>';

        if (!in_array($orden->estado, ['entregado', 'cancelado'])) {
            $html .= '<form action="' . route('tecnicas.destroy', $orden) . '" method="POST" class="d-inline" onsubmit="return confirm(\'¿Eliminar esta orden?\');">';
            $html .= '@csrf @method("DELETE")';
            $html .= '<button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>';
            $html .= '</form>';
        }

        $html .= '</div>';
        return $html;
    }
}
