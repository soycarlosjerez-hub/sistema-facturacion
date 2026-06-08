<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use App\Models\DetalleDevolucion;
use App\Models\Venta;
// use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DevolucionController extends Controller
{
    public function index(Request $request)
    {
        $query = Devolucion::with(['venta', 'cliente', 'user', 'detalles.producto']);

        if ($request->filled('cliente')) {
            $query->whereHas('cliente', fn($q) => $q->where('nombre', 'like', '%' . $request->cliente . '%'));
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        $devoluciones = $query->orderBy('id', 'desc')->paginate(20);

        return view('devoluciones.index', compact('devoluciones'));
    }

    public function create(Request $request)
    {
        $venta = null;
        if ($request->filled('venta_id')) {
            $venta = Venta::with(['detalles.producto', 'cliente'])->findOrFail($request->venta_id);
        }
        $clientes = Cliente::orderBy('nombre')->get();

        return view('devoluciones.create', compact('venta', 'clientes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'venta_id' => 'nullable|exists:ventas,id',
            'cliente_id' => 'required|exists:clientes,id',
            'fecha' => 'required|date',
            'motivo' => 'required|string|min:5',
            'tipo' => 'required|in:parcial,total',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'items.*.itbis_porcentaje' => 'nullable|numeric|min:0|max:100',
            'items.*.motivo' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $itbis = 0;

            foreach ($data['items'] as $item) {
                $base = (float) $item['cantidad'] * (float) $item['precio_unitario'];
                $imp = $base * ((float) ($item['itbis_porcentaje'] ?? 18) / 100);
                $subtotal += $base;
                $itbis += $imp;
            }

            $total = $subtotal + $itbis;

            $devolucion = Devolucion::create([
                'codigo' => Devolucion::generarCodigo(),
                'venta_id' => $data['venta_id'] ?? null,
                'cliente_id' => $data['cliente_id'],
                'user_id' => Auth::id(),
                'fecha' => $data['fecha'],
                'motivo' => $data['motivo'],
                'tipo' => $data['tipo'],
                'subtotal' => round($subtotal, 2),
                'itbis' => round($itbis, 2),
                'total' => round($total, 2),
                'estado' => 'borrador',
            ]);

            foreach ($data['items'] as $item) {
                DetalleDevolucion::create([
                    'devolucion_id' => $devolucion->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'itbis_porcentaje' => $item['itbis_porcentaje'] ?? 18,
                    'subtotal' => round((float) $item['cantidad'] * (float) $item['precio_unitario'] * (1 + ((float) ($item['itbis_porcentaje'] ?? 18) / 100)), 2),
                    'motivo' => $item['motivo'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('devoluciones.show', $devolucion)
                ->with('success', 'Devolución registrada. Revisa y confirma para completar el reintegro de stock.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al registrar devolución: ' . $e->getMessage());
        }
    }

    public function show(Devolucion $devolucion)
    {
        $devolucion->load(['venta', 'cliente', 'user', 'detalles.producto', 'notaCredito']);
        return view('devoluciones.show', compact('devolucion'));
    }

    public function confirmar(Devolucion $devolucion)
    {
        if ($devolucion->estado !== 'borrador') {
            return back()->with('error', 'Solo se pueden confirmar devoluciones en estado borrador.');
        }

        DB::beginTransaction();
        try {
            foreach ($devolucion->detalles as $detalle) {
                $producto = $detalle->producto;
                if ($producto) {
                    $producto->increment('stock', $detalle->cantidad);
                }
            }

            $devolucion->update(['estado' => 'completada']);

            DB::commit();

            return redirect()->route('devoluciones.show', $devolucion)
                ->with('success', 'Devolución completada. Stock reintegrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al confirmar devolución: ' . $e->getMessage());
        }
    }

    public function generarNotaCredito(Devolucion $devolucion)
    {
        if ($devolucion->estado !== 'completada') {
            return back()->with('error', 'La devolución debe estar completada para generar una Nota de Crédito.');
        }
        if ($devolucion->nota_credito_id) {
            return back()->with('error', 'Ya se generó una Nota de Crédito para esta devolución.');
        }
        if (!$devolucion->tiene_ecf) {
            return back()->with('error', 'La venta asociada no es un e-CF. No se puede generar Nota de Crédito electrónica.');
        }

        try {
            $ecfService = app(\App\Services\Ecf\EcfService::class);
            $ecfOriginal = $devolucion->venta->ecfDocumento;

            if (!$ecfOriginal) {
                return back()->with('error', 'La venta no tiene un e-CF asociado.');
            }

            $notaCredito = $ecfService->generarE34PorDevolucion($ecfOriginal, $devolucion);

            $devolucion->update(['nota_credito_id' => $notaCredito->id]);

            return redirect()->route('devoluciones.show', $devolucion)
                ->with('success', 'Nota de Crédito E34 generada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar Nota de Crédito: ' . $e->getMessage());
        }
    }

    public function destroy(Devolucion $devolucion)
    {
        if ($devolucion->estado === 'completada') {
            return back()->with('error', 'No se puede eliminar una devolución completada. Anúlala en su lugar.');
        }
        $devolucion->detalles()->delete();
        $devolucion->delete();

        return redirect()->route('devoluciones.index')->with('success', 'Devolución eliminada.');
    }

    public function buscarVenta(Request $request)
    {
        $term = $request->get('q');
        $ventas = Venta::where('id', 'like', "%{$term}%")
            ->orWhereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$term}%"))
            ->with(['cliente', 'detalles.producto'])
            ->limit(10)
            ->get();

        return response()->json($ventas->map(fn($v) => [
            'id' => $v->id,
            'label' => "Venta #{$v->id} - {$v->cliente?->nombre}",
            'total' => $v->total,
            'fecha' => $v->created_at->format('d/m/Y'),
            'detalles' => $v->detalles->map(fn($d) => [
                'producto_id' => $d->producto_id,
                'producto_nombre' => $d->producto->nombre ?? 'N/A',
                'cantidad' => $d->cantidad,
                'precio' => $d->precio_unitario,
                'itbis' => $d->itbis_porcentaje ?? 18,
            ]),
        ]));
    }
}
