<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VentaResource;
use App\Models\Almacen;
use App\Models\AlmacenMovimiento;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Traits\TenantAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VentaController extends Controller
{
    use TenantAccess;
    public function index(Request $request)
    {
        $query = Venta::with(['usuario', 'cliente', 'sucursal', 'caja', 'detalles.producto', 'pagos', 'tipoVenta', 'mesa'])
            ->when($request->cliente_id, fn ($q) => $q->where('cliente_id', $request->cliente_id))
            ->when($request->sucursal_id, fn ($q) => $q->where('sucursal_id', $request->sucursal_id), fn ($q) => $q->deSucursal())
            ->when($request->estado, fn ($q) => $q->where('estado', $request->estado))
            ->when($request->fecha_desde, fn ($q) => $q->whereDate('fecha', '>=', $request->fecha_desde))
            ->when($request->fecha_hasta, fn ($q) => $q->whereDate('fecha', '<=', $request->fecha_hasta))
            ->when($request->search_ncf, fn ($q) => $q->where('ncf', 'like', '%' . $request->search_ncf . '%'))
            ->when($request->min_total, fn ($q) => $q->where('total', '>=', $request->min_total))
            ->when($request->max_total, fn ($q) => $q->where('total', '<=', $request->max_total));

        return VentaResource::collection($query->orderBy('fecha', 'desc')->paginate(15));
    }

    private function resolverCliente(Request $request): array
    {
        $user = Auth::user();
        $tenantId = $user->business_instance_id;

        if ($request->filled('cliente_id')) {
            $cliente = Cliente::where('id', $request->cliente_id)
                ->where('tenant_id', $tenantId)
                ->first();

            if ($cliente) {
                return ['cliente_id' => $cliente->id];
            }

            Log::warning('[Venta API] cliente_id no encontrado o no pertenece al tenant', [
                'cliente_id' => $request->cliente_id, 'tenant_id' => $tenantId,
            ]);
        }

        $rncCedula = $request->input('cliente_rnc_cedula');
        if (!empty($rncCedula)) {
            $cliente = Cliente::where('rnc_cedula', $rncCedula)
                ->where('tenant_id', $tenantId)
                ->first();

            if ($cliente) {
                Log::info('[Venta API] Cliente resuelto por RNC/Cédula', [
                    'cliente_id' => $cliente->id, 'nombre' => $cliente->nombre,
                ]);
                return ['cliente_id' => $cliente->id];
            }
        }

        $nombre = $request->input('cliente_nombre');
        if (!empty($nombre)) {
            $cliente = Cliente::firstOrCreate(
                ['nombre' => $nombre, 'tenant_id' => $tenantId],
                [
                    'telefono'   => $request->input('cliente_telefono'),
                    'email'      => $request->input('cliente_email'),
                    'rnc_cedula' => $request->input('cliente_rnc_cedula'),
                    'tipo_cliente' => $request->input('tipo_cliente', 'consumo'),
                ]
            );
            Log::info('[Venta API] Cliente resuelto por nombre', ['cliente_id' => $cliente->id, 'nombre' => $cliente->nombre]);
            return ['cliente_id' => $cliente->id];
        }

        return [];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ncf' => 'required|string|max:50',
            'ncf_tipo' => 'required|string|max:20',
            'tipo_comprobante' => 'nullable|string|max:20',
            'encf' => 'nullable|string|max:50',
            'user_id' => 'required|exists:users,id',
            'caja_id' => 'required|exists:cajas,id',
            'cliente_id' => 'nullable|integer|min:1',
            'cliente_nombre' => 'nullable|string|max:200',
            'cliente_telefono' => 'nullable|string|max:30',
            'cliente_email' => 'nullable|email|max:200',
            'cliente_rnc_cedula' => 'nullable|string|max:50',
            'tipo_cliente' => 'nullable|string|in:credito_fiscal,consumo,gubernamental,especial',
            'tipo_venta_id' => 'nullable|exists:tipos_ventas,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'fecha' => 'nullable|date',
            'subtotal' => 'required|numeric|min:0',
            'impuestos' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'propina' => 'nullable|numeric|min:0',
            'cargo_servicio' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'estado' => 'required|string|max:20',
            'notas' => 'nullable|string',
            'tipo_orden' => 'nullable|string|max:20',
            'detalles' => 'required|array',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'detalles.*.descuento' => 'nullable|numeric|min:0',
            'detalles.*.descuento_tipo' => 'nullable|in:monto,porcentaje',
            'detalles.*.impuesto' => 'nullable|numeric|min:0',
            'detalles.*.almacen_id' => 'nullable|exists:almacenes,id',
        ]);

        $clienteData = $this->resolverCliente($request);
        $validated = array_merge($validated, $clienteData);

        $user = Auth::user();
        $tenantId = $user->business_instance_id;

        // Ensure tenant_id is set (TenantScope global auto-sets tenant_id on create)
        if (isset($validated['tenant_id'])) {
            $validated['tenant_id'] = $tenantId;
        } elseif (isset($validated['user_id'])) {
            $validated['tenant_id'] = $tenantId;
        }

        // --- Cálculo autoritativo server-side (F2.5) ---
        $rolesAutorizados = ['admin', 'admin-business', 'root', 'gerente'];
        $puedeSobreescribirPrecio = $user->hasRole($rolesAutorizados)
            || in_array($user->role ?? '', $rolesAutorizados);

        $config = $user->businessInstance->configuracion ?? [];
        $validaStock = ($config['restaurante_valida_stock'] ?? '1') === '1';

        $lineas = [];
        $subtotalTotal = 0.0;
        $descuentosLinea = 0.0;

        foreach ($validated['detalles'] as $i => $detalle) {
            $producto = Producto::where('tenant_id', $tenantId)->find($detalle['producto_id']);
            if (!$producto) {
                return response()->json([
                    'message' => "El producto #{$detalle['producto_id']} no existe o no pertenece a tu instancia.",
                ], 422);
            }

            $cantidad = max(0.01, (float) $detalle['cantidad']);
            $precioBD = (float) $producto->precio;
            $precioCli = (float) ($detalle['precio_unitario'] ?? $precioBD);

            if (abs($precioCli - $precioBD) > 0.02 && !$puedeSobreescribirPrecio) {
                return response()->json([
                    'message' => "No autorizado para modificar el precio de \"{$producto->nombre}\".",
                ], 422);
            }

            $precioBase = (abs($precioCli - $precioBD) > 0.02 && $puedeSobreescribirPrecio)
                ? $precioCli
                : $precioBD;

            $almacenId = (int) ($detalle['almacen_id'] ?? 0);
            if (!$almacenId) {
                $almacen = Almacen::where('tenant_id', $tenantId)->first();
                $almacenId = $almacen?->id;
            }

            if ($validaStock) {
                $stockAlmacen = $almacenId
                    ? (int) (AlmacenMovimiento::where('producto_id', $producto->id)
                        ->where('almacen_id', $almacenId)
                        ->selectRaw('SUM(CASE WHEN tipo = "entrada" THEN cantidad ELSE -cantidad END) as stock')
                        ->value('stock') ?? 0)
                    : (int) $producto->stock;

                if ($stockAlmacen < $cantidad || $producto->stock < $cantidad) {
                    return response()->json([
                        'message' => "Stock insuficiente para: {$producto->nombre} (Disponible en almacén: {$stockAlmacen}, Stock global: {$producto->stock})",
                    ], 422);
                }
            }

            $subtotalLinea = round($precioBase * $cantidad, 2);
            $descuento = (float) ($detalle['descuento'] ?? 0);
            $descuentoTipo = $detalle['descuento_tipo'] ?? 'monto';
            $descuentoAplicado = $descuentoTipo === 'porcentaje'
                ? $subtotalLinea * min($descuento, 100) / 100
                : $descuento;

            $subtotalTotal += $subtotalLinea;
            $descuentosLinea += $descuentoAplicado;

            $lineas[] = [
                'producto_id'      => $producto->id,
                'cantidad'         => $cantidad,
                'precio_unitario'  => $precioBase,
                'subtotal'         => $subtotalLinea,
                'descuento'        => $descuento,
                'descuento_tipo'   => $descuentoTipo,
                'itbis_porcentaje' => (float) ($producto->itbis_porcentaje ?? 0),
                'almacen_id'       => $almacenId,
            ];
        }

        $generalDescuento = max(0, (float) ($validated['descuento'] ?? 0));
        $descuentoTotal = $descuentosLinea + $generalDescuento;

        if ($subtotalTotal > 0) {
            $pctDescuento = ($descuentoTotal / $subtotalTotal) * 100;
            if ($pctDescuento > 50 && !$puedeSobreescribirPrecio) {
                return response()->json([
                    'message' => 'Descuentos superiores al 50% requieren autorización de administrador.',
                ], 422);
            }
        }

        // ITBIS sobre base bruta autoritativa (descuento aplicado por línea y general proporcional)
        $itbisTotal = 0.0;
        foreach ($lineas as $line) {
            $descAplicado = $line['descuento_tipo'] === 'porcentaje'
                ? $line['subtotal'] * min($line['descuento'], 100) / 100
                : $line['descuento'];
            $baseFinal = max(0, $line['subtotal'] - $descAplicado);
            if ($generalDescuento > 0 && $subtotalTotal > 0) {
                $baseFinal = max(0, $baseFinal - ($generalDescuento * ($baseFinal / $subtotalTotal)));
            }
            $itbisTotal += $baseFinal * ($line['itbis_porcentaje'] / 100);
        }
        $itbisTotal = round($itbisTotal, 2);

        $validated['subtotal']  = round($subtotalTotal, 2);
        $validated['impuestos'] = $itbisTotal;
        $validated['descuento'] = round($descuentoTotal, 2);
        $validated['total']     = round($subtotalTotal - $descuentoTotal + $itbisTotal, 2);
        $validated['detalles']  = $lineas;

        $venta = DB::transaction(function () use ($validated, $tenantId, $user, $validaStock) {
            $venta = Venta::create($validated);

            foreach ($validated['detalles'] as $detalle) {
                $venta->detalles()->create(array_merge($detalle, ['venta_id' => $venta->id, 'tenant_id' => $tenantId]));

                if ($validaStock) {
                    AlmacenMovimiento::create([
                        'tenant_id'   => $tenantId,
                        'producto_id' => $detalle['producto_id'],
                        'almacen_id'  => $detalle['almacen_id'],
                        'tipo'        => 'salida',
                        'cantidad'    => $detalle['cantidad'],
                        'nota'        => 'Venta #' . $venta->id . ' (API)',
                        'user_id'     => $user->id,
                    ]);

                    Producto::where('id', $detalle['producto_id'])->decrement('stock', $detalle['cantidad']);
                }
            }

            return $venta;
        });

        return new VentaResource($venta->load(['usuario', 'cliente', 'sucursal', 'caja', 'detalles.producto', 'pagos']));
    }

    public function show(Venta $venta)
    {
        return new VentaResource($venta->load(['usuario', 'cliente', 'sucursal', 'caja', 'detalles.producto', 'pagos', 'tipoVenta', 'mesa']));
    }

    public function update(Request $request, Venta $venta)
    {
        $validated = $request->validate([
            'ncf' => 'sometimes|string|max:50',
            'ncf_tipo' => 'sometimes|string|max:20',
            'estado' => 'sometimes|string|max:20',
            'descuento' => 'sometimes|numeric|min:0',
            'notas' => 'nullable|string',
            'total' => 'sometimes|numeric|min:0',
        ]);

        $venta->update($validated);

        return new VentaResource($venta->load(['usuario', 'cliente', 'sucursal', 'caja', 'detalles.producto', 'pagos']));
    }

    public function destroy(Venta $venta)
    {
        $venta->delete();
        return response()->json(['message' => 'Venta eliminada.']);
    }

    public function resumen(Request $request)
    {
        $query = Venta::where('estado', '!=', 'cancelada')
            ->when($request->sucursal_id, fn ($q) => $q->where('sucursal_id', $request->sucursal_id), fn ($q) => $q->deSucursal());

        if ($request->fecha_desde) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }

        if ($request->fecha_hasta) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        return response()->json([
            'total_ventas' => $query->count(),
            'total_ingresos' => $query->sum('total'),
            'total_subtotal' => $query->sum('subtotal'),
            'total_impuestos' => $query->sum('impuestos'),
            'total_descuentos' => $query->sum('descuento'),
            'promedio_ticket' => $query->avg('total'),
        ]);
    }
}
