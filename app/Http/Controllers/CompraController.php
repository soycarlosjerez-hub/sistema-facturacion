<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\AlmacenMovimiento;
use App\Models\Compra;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\DetalleCompra;
use App\Models\TipoCompra;
use App\Support\RetencionCalculator;
use App\Services\Ecf\EcfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $query = Compra::with([
            'proveedor:id,nombre,rnc,rnc_cedula',
            'almacen:id,nombre',
            'tipoCompra:id,nombre',
            'detalles.producto:id,nombre',
        ]);

        if ($sucursalId = session('sucursal_id')) {
            $query->where('sucursal_id', $sucursalId);
        }

        if ($request->filled('proveedor')) {
            $termino = trim($request->proveedor);
            $query->whereHas('proveedor', function ($q) use ($termino) {
                $q->where('nombre', 'like', '%' . $termino . '%')
                  ->orWhere('rnc_cedula', 'like', '%' . $termino . '%')
                  ->orWhere('rnc', 'like', '%' . $termino . '%');
            });
        }

        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->hasta);
        }

        $compras = $query
            ->orderByDesc('fecha')
            ->paginate(10)
            ->appends($request->all());

        return view('compras.index', compact('compras'));
    }

    public function create()
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')->get();
        $tiposCompra = TipoCompra::orderBy('nombre')->get();
        $almacenes = $this->almacenesSegunSucursal();

        return view('compras.create', compact('proveedores', 'productos', 'tiposCompra', 'almacenes'));
    }

    public function show(Compra $compra)
    {
        $compra->load('detalles.producto', 'proveedor', 'almacen', 'tipoCompra', 'user');
        return view('compras.show', compact('compra'));
    }

    public function store(Request $request)
    {
        $data = $this->validateCompra($request);

        $productos = $this->filterEmptyRows($data['productos']);

        if (empty($productos)) {
            throw ValidationException::withMessages([
                'productos' => 'Debes agregar al menos un producto a la compra.',
            ]);
        }

        $totales = $this->calcularTotales($productos);

        $newProducts = [];
        $updatedProducts = [];

        DB::beginTransaction();
        try {
            $compra = Compra::create([
                'proveedor_id'    => $data['proveedor_id'],
                'sucursal_id'     => session('sucursal_id'),
                'almacen_id'      => $data['almacen_id'] ?? null,
                'tipo_compra_id'  => $data['tipo_compra_id'],
                'user_id'         => Auth::id(),
                'total'           => $totales['total'],
                'itbis_total'     => $totales['itbis_total'],
                'subtotal'        => $totales['subtotal'],
                'fecha'           => $data['fecha'] ?? now(),
                'observaciones'   => $data['observaciones'] ?? null,
            ]);

            foreach ($productos as $item) {
                $producto = $this->resolverProducto($item, $newProducts, $updatedProducts);

                $detalle = DetalleCompra::create([
                    'compra_id'         => $compra->id,
                    'producto_id'       => $producto->id,
                    'cantidad'          => $item['cantidad'],
                    'precio_unitario'   => $item['precio'],
                    'itbis_porcentaje'  => $item['itbis_porcentaje'] ?? 18,
                    'subtotal'          => $this->calcularSubtotal($item),
                ]);

                if ($compra->almacen_id) {
                    AlmacenMovimiento::create([
                        'producto_id'       => $producto->id,
                        'detalle_compra_id'  => $detalle->id,
                        'user_id'           => Auth::id(),
                        'almacen_id'        => $compra->almacen_id,
                        'tipo'              => 'entrada',
                        'cantidad'          => $item['cantidad'],
                        'nota'              => "Entrada por compra #{$compra->id}",
                    ]);
                }
            }

            $proveedor = Proveedor::find($data['proveedor_id']);
            $aplicaIsr = $request->boolean('aplica_retencion_isr') && $proveedor?->sujeto_retencion_isr;
            $aplicaItbis = $request->boolean('aplica_retencion_itbis') && $proveedor?->sujeto_retencion_itbis;

            $retencionIsr = $aplicaIsr
                ? RetencionCalculator::calcularRetencionIsr($totales['total'], $proveedor?->tipo_persona ?? 'juridica')['monto_retenido']
                : 0;

            $retencionItbis = $aplicaItbis
                ? RetencionCalculator::calcularRetencionItbis($totales['itbis_total'])['monto_retenido']
                : 0;

            $compra->update([
                'aplica_retencion_isr'   => $aplicaIsr,
                'aplica_retencion_itbis' => $aplicaItbis,
                'retencion_isr'          => $retencionIsr,
                'retencion_itbis'        => $retencionItbis,
                'total_neto'             => round($totales['total'] - $retencionIsr - $retencionItbis, 2),
            ]);

            DB::commit();

            $message = 'Compra registrada exitosamente.';
            if (!empty($newProducts)) {
                $links = array_map(fn($id) =>
                    '<a href="' . route('productos.edit', $id) . '">Producto #' . $id . '</a>',
                    $newProducts
                );
                $message .= ' Productos nuevos: ' . implode(', ', $links) . '. <strong>Recuerda asignar el precio de venta.</strong>';
            }
            if (!empty($updatedProducts)) {
                $message .= ' Stock actualizado en ' . count($updatedProducts) . ' producto(s) existente(s).';
            }

            return redirect()->route('compras.show', $compra)->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al registrar la compra: ' . $e->getMessage());
        }
    }

    public function edit(Compra $compra)
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')->get();
        $detalles = $compra->detalles()->with('producto')->get();
        $tiposCompra = TipoCompra::orderBy('nombre')->get();
        $almacenes = $this->almacenesSegunSucursal();

        return view('compras.edit', compact('proveedores', 'productos', 'detalles', 'tiposCompra', 'almacenes'));
    }

    public function update(Request $request, Compra $compra)
    {
        $data = $this->validateCompra($request, updating: true);

        $productos = $this->filterEmptyRows($data['productos'] ?? []);

        DB::beginTransaction();
        try {
            // Revertir stock de los detalles actuales
            foreach ($compra->detalles as $detalle) {
                if ($detalle->producto) {
                    $detalle->producto->decrement('stock', $detalle->cantidad);
                }
            }

            // Si no quedan productos, eliminar la compra
            if (empty($productos)) {
                $compra->detalles()->delete();
                $compra->delete();
                DB::commit();
                return redirect()->route('compras.index')
                    ->with('success', 'Compra eliminada porque no tiene productos.');
            }

            $compra->detalles()->delete();

            $totales = $this->calcularTotales($productos);

            $proveedor = Proveedor::find($data['proveedor_id']);
            $aplicaIsr = $request->boolean('aplica_retencion_isr') && $proveedor?->sujeto_retencion_isr;
            $aplicaItbis = $request->boolean('aplica_retencion_itbis') && $proveedor?->sujeto_retencion_itbis;

            $retencionIsr = $aplicaIsr
                ? RetencionCalculator::calcularRetencionIsr($totales['total'], $proveedor?->tipo_persona ?? 'juridica')['monto_retenido']
                : 0;

            $retencionItbis = $aplicaItbis
                ? RetencionCalculator::calcularRetencionItbis($totales['itbis_total'])['monto_retenido']
                : 0;

            $compra->update([
                'proveedor_id'         => $data['proveedor_id'],
                'almacen_id'           => $data['almacen_id'] ?? $compra->almacen_id,
                'tipo_compra_id'       => $data['tipo_compra_id'],
                'total'                => $totales['total'],
                'itbis_total'          => $totales['itbis_total'],
                'subtotal'             => $totales['subtotal'],
                'fecha'                => $data['fecha'] ?? $compra->fecha ?? now(),
                'observaciones'        => $data['observaciones'] ?? null,
                'aplica_retencion_isr' => $aplicaIsr,
                'aplica_retencion_itbis' => $aplicaItbis,
                'retencion_isr'        => $retencionIsr,
                'retencion_itbis'      => $retencionItbis,
                'total_neto'           => round($totales['total'] - $retencionIsr - $retencionItbis, 2),
            ]);

            $newProducts = [];
            $updatedProducts = [];

            foreach ($productos as $item) {
                $producto = $this->resolverProducto($item, $newProducts, $updatedProducts);

                $detalle = DetalleCompra::create([
                    'compra_id'        => $compra->id,
                    'producto_id'      => $producto->id,
                    'cantidad'         => $item['cantidad'],
                    'precio_unitario'  => $item['precio'],
                    'itbis_porcentaje' => $item['itbis_porcentaje'] ?? 18,
                    'subtotal'         => $this->calcularSubtotal($item),
                ]);

                if ($compra->almacen_id) {
                    AlmacenMovimiento::create([
                        'producto_id'       => $producto->id,
                        'detalle_compra_id'  => $detalle->id,
                        'user_id'           => Auth::id(),
                        'almacen_id'        => $compra->almacen_id,
                        'tipo'              => 'entrada',
                        'cantidad'          => $item['cantidad'],
                        'nota'              => "Entrada por compra #{$compra->id}",
                    ]);
                }
            }

            DB::commit();

            $message = 'Compra actualizada exitosamente.';
            if (!empty($newProducts)) {
                $links = array_map(fn($id) =>
                    '<a href="' . route('productos.edit', $id) . '">Producto #' . $id . '</a>',
                    $newProducts
                );
                $message .= ' Productos nuevos: ' . implode(', ', $links) . '.';
            }
            if (!empty($updatedProducts)) {
                $message .= ' Stock actualizado en ' . count($updatedProducts) . ' producto(s).';
            }

            return redirect()->route('compras.show', $compra)->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al actualizar la compra: ' . $e->getMessage());
        }
    }

    public function destroyDetalle(Compra $compra, DetalleCompra $detalle)
    {
        if ($detalle->compra_id !== $compra->id) {
            return back()->with('error', 'El detalle no pertenece a esta compra.');
        }

        DB::transaction(function () use ($detalle) {
            $producto = $detalle->producto;
            if ($producto) {
                $producto->decrement('stock', $detalle->cantidad);
            }
            $detalle->delete();
            $this->recalcularTotales($detalle->compra);
        });

        if (! $compra->detalles()->exists()) {
            $compra->delete();
            return redirect()->route('compras.index')
                ->with('success', 'Detalle eliminado. La compra se eliminó por no tener más productos.');
        }

        return redirect()->route('compras.edit', $compra)->with('success', 'Producto eliminado de la compra.');
    }

    public function destroy(Compra $compra)
    {
        try {
            DB::transaction(function () use ($compra) {
                foreach ($compra->detalles as $detalle) {
                    if ($detalle->producto) {
                        $detalle->producto->decrement('stock', $detalle->cantidad);
                    }
                }
                $compra->detalles()->delete();
                $compra->delete();
            });

            return redirect()->route('compras.index')->with('success', 'Compra eliminada y stock revertido.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la compra: ' . $e->getMessage());
        }
    }

    protected function validateCompra(Request $request, bool $updating = false): array
    {
        $required = $updating ? 'sometimes|required' : 'required';
        return $request->validate([
            'proveedor_id'                 => 'required|exists:proveedores,id',
            'almacen_id'                   => 'nullable|exists:almacenes,id',
            'tipo_compra_id'               => 'required|exists:tipos_compras,id',
            'fecha'                        => 'nullable|date',
            'observaciones'                => 'nullable|string|max:1000',
            'aplica_retencion_isr'         => 'nullable|boolean',
            'aplica_retencion_itbis'       => 'nullable|boolean',
            'productos'                    => $updating ? 'nullable|array' : 'required|array|min:1',
            'productos.*.producto_id'      => 'nullable|integer|exists:productos,id',
            'productos.*.nombre'           => 'required_with:productos|string|max:255',
            'productos.*.codigo_barras'    => 'nullable|string|max:100',
            'productos.*.cantidad'         => "required_with:productos|numeric|min:0.01",
            'productos.*.precio'           => "required_with:productos|numeric|min:0",
            'productos.*.itbis_porcentaje' => 'nullable|numeric|min:0|max:100',
        ], [
            'proveedor_id.required'                => 'Selecciona un proveedor.',
            'proveedor_id.exists'                  => 'El proveedor seleccionado no existe.',
            'tipo_compra_id.required'              => 'Selecciona un tipo de compra.',
            'productos.required'                   => 'Agrega al menos un producto a la compra.',
            'productos.min'                        => 'Agrega al menos un producto a la compra.',
            'productos.*.nombre.required_with'     => 'El nombre del producto es obligatorio.',
            'productos.*.cantidad.required_with'   => 'La cantidad es obligatoria.',
            'productos.*.cantidad.min'             => 'La cantidad debe ser mayor a 0.',
            'productos.*.precio.required_with'     => 'El precio es obligatorio.',
            'productos.*.precio.min'               => 'El precio no puede ser negativo.',
            'productos.*.itbis_porcentaje.max'     => 'El ITBIS no puede ser mayor a 100%.',
        ]);
    }

    protected function filterEmptyRows(array $productos): array
    {
        return collect($productos)
            ->filter(function ($item) {
                $tieneNombre = !empty(trim($item['nombre'] ?? ''));
                $tieneProductoId = !empty($item['producto_id']);
                $tieneCantidad = isset($item['cantidad']) && (float) $item['cantidad'] > 0;
                $tienePrecio = isset($item['precio']) && $item['precio'] !== '' && (float) $item['precio'] >= 0;
                return ($tieneNombre || $tieneProductoId) && $tieneCantidad && $tienePrecio;
            })
            ->values()
            ->all();
    }

    protected function calcularSubtotal(array $item): float
    {
        $cantidad = (float) $item['cantidad'];
        $precio   = (float) $item['precio'];
        $itbis    = (float) ($item['itbis_porcentaje'] ?? 18);
        return round($cantidad * $precio * (1 + $itbis / 100), 2);
    }

    protected function calcularTotales(array $productos): array
    {
        $subtotal    = 0.0;
        $itbisTotal  = 0.0;
        $total       = 0.0;

        foreach ($productos as $item) {
            $cantidad = (float) $item['cantidad'];
            $precio   = (float) $item['precio'];
            $itbis    = (float) ($item['itbis_porcentaje'] ?? 18);
            $base     = $cantidad * $precio;
            $impuesto = $base * ($itbis / 100);
            $subtotal   += $base;
            $itbisTotal += $impuesto;
            $total      += $base + $impuesto;
        }

        return [
            'subtotal'   => round($subtotal, 2),
            'itbis_total'=> round($itbisTotal, 2),
            'total'      => round($total, 2),
        ];
    }

    protected function resolverProducto(array $item, array &$newProducts, array &$updatedProducts): Producto
    {
        $cantidad = (float) $item['cantidad'];
        $precio   = (float) $item['precio'];
        $itbis    = (float) ($item['itbis_porcentaje'] ?? 18);

        // Priorizar producto_id (viene del datalist)
        $producto = null;
        if (!empty($item['producto_id'])) {
            $producto = Producto::find($item['producto_id']);
        }

        // Si no hay producto_id, buscar por nombre exacto
        if (! $producto && !empty($item['nombre'])) {
            $producto = Producto::where('nombre', trim($item['nombre']))->first();
        }

        if ($producto) {
            $producto->stock += $cantidad;
            // Solo actualizar precio_compra si difiere (trazabilidad)
            if ((float) $producto->precio_compra != $precio) {
                $producto->precio_compra = $precio;
            }
            $producto->save();
            $updatedProducts[] = $producto->id;
            return $producto;
        }

        // Crear nuevo producto (sin precio de venta - el usuario lo asigna después)
        $producto = Producto::create([
            'nombre'           => trim($item['nombre']),
            'codigo_barras'    => ! empty($item['codigo_barras']) ? trim($item['codigo_barras']) : null,
            'precio_compra'    => $precio,
            'precio'           => $precio, // se inicializa igual; el usuario deberá ajustar margen
            'stock'            => $cantidad,
            'itbis_porcentaje' => $itbis,
            'unidad_medida'    => 'Unidad',
        ]);
        $newProducts[] = $producto->id;
        return $producto;
    }

    protected function recalcularTotales(Compra $compra): void
    {
        $detalles = $compra->detalles()->get();
        $totales = ['subtotal' => 0, 'itbis_total' => 0, 'total' => 0];

        foreach ($detalles as $d) {
            $base = (float) $d->cantidad * (float) $d->precio_unitario;
            $itbis = (float) ($d->itbis_porcentaje ?? 18);
            $impuesto = $base * ($itbis / 100);
            $totales['subtotal'] += $base;
            $totales['itbis_total'] += $impuesto;
            $totales['total'] += $base + $impuesto;
        }

        $updateData = [
            'subtotal'    => round($totales['subtotal'], 2),
            'itbis_total' => round($totales['itbis_total'], 2),
            'total'       => round($totales['total'], 2),
        ];

        if ($compra->aplica_retencion_isr || $compra->aplica_retencion_itbis) {
            $isr = $compra->aplica_retencion_isr
                ? RetencionCalculator::calcularRetencionIsr($updateData['total'], $compra->proveedor?->tipo_persona ?? 'juridica')['monto_retenido']
                : 0;
            $itbis = $compra->aplica_retencion_itbis
                ? RetencionCalculator::calcularRetencionItbis($updateData['itbis_total'])['monto_retenido']
                : 0;
            $updateData['retencion_isr'] = $isr;
            $updateData['retencion_itbis'] = $itbis;
            $updateData['total_neto'] = round($updateData['total'] - $isr - $itbis, 2);
        }

        $compra->update($updateData);
    }

    private function almacenesSegunSucursal()
    {
        $query = Almacen::orderBy('nombre');
        if ($sucursalId = session('sucursal_id')) {
            $query->where('sucursal_id', $sucursalId);
        }
        return $query->get();
    }

    public function generarE41(Compra $compra)
    {
        if ($compra->ecf_documento_id) {
            return back()->with('error', 'Esta compra ya tiene un e-CF E41 asociado.');
        }
        if (!$compra->puede_generar_ecf) {
            return back()->with('error', 'El proveedor debe tener un RNC registrado para generar e-CF E41.');
        }

        try {
            $ecfService = app(EcfService::class);
            $ecf = $ecfService->generarE41($compra);

            return redirect()->route('ecf.show', $ecf)
                ->with('success', 'e-CF E41 generado exitosamente para la compra.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar e-CF: ' . $e->getMessage());
        }
    }
}
