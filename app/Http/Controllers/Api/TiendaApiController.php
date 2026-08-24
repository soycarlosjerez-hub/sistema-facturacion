<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlmacenMovimiento;
use App\Models\Category;
use App\Models\CategorySubcategory;
use App\Models\Producto;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TiendaApiController extends Controller
{
    /**
     * GET /api/tienda/productos?linea=accesorios&categoria_id=1&search=cafe&in_stock=true
     */
    public function productos(Request $request)
    {
        $validLines = ['alimentos', 'bebidas', 'accesorios', 'todos', null];
        $linea = $request->query('linea', 'todos');

        $query = Producto::with(['categoria', 'categorySubcategory'])
            ->activos();

        // Filtrar por linea_negocio
        if ($linea !== 'todos' && in_array($linea, ['alimentos', 'bebidas', 'accesorios'])) {
            $query->where('linea_negocio', $linea);
        }

        // Filtrar por categoria_id
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', (int) $request->categoria_id);
        }

        // Buscar por nombre o codigo_barras
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('codigo_barras', 'like', "%{$search}%");
            });
        }

        // Filtrar por stock disponible
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        $productos = $query->select('id', 'categoria_id', 'category_subcategory_id', 'nombre', 'codigo_barras', 'descripcion',
            'precio', 'stock', 'stock_minimo', 'linea_negocio', 'imagen', 'activo')
            ->orderBy('nombre')
            ->get();

        $data = $productos->map(function ($p) {
            return [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'codigo_barras' => $p->codigo_barras,
                'precio' => $p->precio,
                'stock' => $p->stock,
                'imagen' => $p->imagen_url,
                'linea_negocio' => $p->linea_negocio,
                'categoria' => $p->categoria ? [
                    'id' => $p->categoria->id,
                    'nombre' => $p->categoria->nombre,
                ] : null,
                'subcategoria' => $p->categorySubcategory ? [
                    'id' => $p->categorySubcategory->id,
                    'nombre' => $p->categorySubcategory->nombre,
                ] : null,
            ];
        });

        return response()->json([
            'productos' => $data,
            'total' => $data->count(),
        ]);
    }

    /**
     * GET /api/tienda/categorias
     */
    public function categorias()
    {
        // Obtener business_type del tenant actual (lavadero por defecto)
        $businessTypeSlug = SystemSetting::get('business_type_slug', 'lavadero');

        $categories = Category::whereHas('businessTypes', function ($q) use ($businessTypeSlug) {
            $q->where('business_types.key', $businessTypeSlug);
        })
            ->active()
            ->ordered()
            ->with([
                'businessTypes' => function ($q) use ($businessTypeSlug) {
                    $q->select('business_types.id', 'business_types.key', 'business_types.nombre', 'business_types.color', 'business_types.icon');
                },
            ])
            ->get();

        $data = $categories->map(function ($category) use ($businessTypeSlug) {
            // Obtener subcategorías para este business type
            $subcategorias = CategorySubcategory::where('category_id', $category->id)
                ->where('business_type_id', function ($q) use ($businessTypeSlug) {
                    $q->select('id')->from('business_types')->where('key', $businessTypeSlug);
                })
                ->activas()
                ->orderBy('orden')
                ->orderBy('nombre')
                ->select('id', 'parent_id', 'nombre')
                ->get()
                ->map(function ($sub) {
                    return [
                        'id' => $sub->id,
                        'nombre' => $sub->nombre,
                    ];
                })
                ->values();

            return [
                'id' => $category->id,
                'nombre' => $category->nombre,
                'color' => $category->color ?? $category->getColorForType($businessTypeSlug),
                'icono' => $category->icono ?? $category->getIconForType($businessTypeSlug),
                'subcategorias' => $subcategorias,
            ];
        });

        return response()->json(['categorias' => $data]);
    }

    /**
     * GET /api/tienda/inventario?estado=critico|bajo|todos&linea=accesorios
     */
    public function inventario(Request $request)
    {
        $validEstados = ['critico', 'bajo', 'todos'];
        $estado = $request->query('estado', 'todos');
        $linea = $request->query('linea');

        $query = Producto::select('id', 'nombre', 'codigo_barras', 'stock', 'stock_minimo', 'linea_negocio',
            'precio', 'imagen', 'updated_at')
            ->activos();

        // Filtrar por linea_negocio
        if ($linea && in_array($linea, ['alimentos', 'bebidas', 'accesorios'])) {
            $query->where('linea_negocio', $linea);
        }

        // Filtrar por estado de stock
        switch ($estado) {
            case 'critico':
                // stock <= 0
                $query->where('stock', '<=', 0);
                break;

            case 'bajo':
                // stock > 0 && stock <= stock_minimo
                $query->where(function ($q) {
                    $q->where('stock', '>', 0)
                        ->whereColumn('stock', '<=', 'stock_minimo');
                });
                break;

            case 'todos':
            default:
                // Todos los productos
                break;
        }

        $productos = $query->orderBy('stock', 'asc')->orderBy('nombre')->get();

        $data = $productos->map(function ($p) {
            return [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'codigo_barras' => $p->codigo_barras,
                'stock' => $p->stock,
                'stock_minimo' => $p->stock_minimo,
                'estado_stock' => $p->stock <= 0 ? 'sin_stock' : ($p->stock <= $p->stock_minimo ? 'bajo' : 'ok'),
                'linea_negocio' => $p->linea_negocio,
                'ultima_actualizacion' => $p->updated_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'inventario' => $data,
            'total' => $data->count(),
            'estado_filtro' => $estado,
        ]);
    }

    /**
     * POST /api/tienda/inventario/ajuste
     */
    public function ajusteInventario(Request $request)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:-999999',
            'motivo' => 'required|in:ajuste,merma,inventario',
            'notas' => 'nullable|string|max:500',
        ]);

        $producto = Producto::find($validated['producto_id']);

        if (!$producto) {
            return response()->json([
                'message' => 'Producto no encontrado.',
            ], 404);
        }

        // Verificar que no quede stock negativo
        $nuevoStock = $producto->stock + $validated['cantidad'];
        if ($nuevoStock < 0) {
            return response()->json([
                'message' => 'No se puede realizar el ajuste. El stock no puede quedar negativo.',
                'stock_actual' => $producto->stock,
                'stock_resultante' => $nuevoStock,
            ], 422);
        }

        // Determinar el tipo de movimiento para AlmacenMovimiento
        $tipo = match (true) {
            $validated['cantidad'] > 0 => 'ajuste_positivo',
            $validated['cantidad'] < 0 => 'ajuste_negativo',
            default => 'ajuste_neutro',
        };

        // Construir el motivo del movimiento
        $motivoMovimiento = "{$validated['motivo']}" . ($validated['notas'] ? " - {$validated['notas']}" : '');

        try {
            DB::beginTransaction();

            // Actualizar stock del producto
            $producto->increment('stock', $validated['cantidad']);

            // Crear registro en AlmacenMovimiento
            $movimiento = AlmacenMovimiento::create([
                'tenant_id' => auth()->user()->business_instance_id,
                'producto_id' => $producto->id,
                'user_id' => auth()->id(),
                'tipo' => $tipo,
                'cantidad' => $validated['cantidad'],
                'nota' => $motivoMovimiento,
                'linea_negocio' => $producto->linea_negocio,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Ajuste de inventario realizado correctamente.',
                'nuevo_stock' => $producto->stock,
                'movimiento_id' => $movimiento->id,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al realizar el ajuste de inventario.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/tienda/kardex/{productoId}?desde=2026-01-01&hasta=2026-01-31
     */
    public function kardex($productoId)
    {
        // Validar fechas si se proporcionan
        $desde = request('desde') ? \Carbon\Carbon::parse(request('desde'))->startOfDay() : null;
        $hasta = request('hasta') ? \Carbon\Carbon::parse(request('hasta'))->endOfDay() : null;

        // Obtener el producto
        $producto = Producto::find($productoId);

        if (!$producto) {
            return response()->json([
                'message' => 'Producto no encontrado.',
            ], 404);
        }

        // Obtener movimientos del producto
        $query = AlmacenMovimiento::where('producto_id', $productoId)
            ->with(['user', 'detalleCompra'])
            ->orderBy('created_at', 'desc');

        if ($desde) {
            $query->where('created_at', '>=', $desde);
        }

        if ($hasta) {
            $query->where('created_at', '<=', $hasta);
        }

        $movimientos = $query->get();

        // Construir el kardex con saldo acumulado
        $kardexData = [];
        $saldo = 0;

        // Los movimientos vienen ordenados DESC, los invertimos para calcular saldo
        $movimientosOrdenados = $movimientos->reverse()->values();

        foreach ($movimientosOrdenados as $mov) {
            $cantidad = (int) $mov->cantidad;
            if ($cantidad > 0) {
                $saldo += $cantidad;
            } elseif ($cantidad < 0) {
                $saldo += $cantidad; // resta
            }

            $kardexData[] = [
                'fecha' => $mov->created_at?->toIso8601String(),
                'tipo' => $mov->tipo,
                'cantidad' => $cantidad,
                'saldo' => $saldo,
                'motivo' => $mov->nota,
                'usuario' => $mov->user?->name,
                'referencia' => $mov->detalleCompra_id ? "Compra #{$mov->detalleCompra_id}" : null,
            ];
        }

        // Invertir para que el más reciente sea el último
        krsort($kardexData);
        $kardexOrdenado = [];
        foreach (array_keys($kardexData) as $key) {
            $kardexOrdenado[] = $kardexData[$key];
        }

        return response()->json([
            'producto' => [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'codigo_barras' => $producto->codigo_barras,
                'stock_actual' => $producto->stock,
            ],
            'movimientos' => $kardexOrdenado,
            'total_movimientos' => $movimientos->count(),
        ]);
    }
}
