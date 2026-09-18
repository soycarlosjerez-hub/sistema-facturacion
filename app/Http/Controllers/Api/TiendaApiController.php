<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlmacenMovimiento;
use App\Models\BusinessInstance;
use App\Models\Category;
use App\Models\CategorySubcategory;
use App\Models\InstanceApiKey;
use App\Models\Producto;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        if ($request->filled('limit') || $request->filled('page')) {
            $limit = $request->integer('limit', 15);
            $page = $request->integer('page', 1);

            $paginated = $query->select('id', 'categoria_id', 'category_subcategory_id', 'nombre', 'codigo_barras', 'descripcion',
                'precio', 'stock', 'stock_minimo', 'linea_negocio', 'imagen', 'activo')
                ->orderBy('nombre')
                ->paginate($limit, ['*'], 'page', $page);

            $data = $paginated->items();

            $mapped = [];
            foreach ($data as $p) {
                $mapped[] = [
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
            }

            return response()->json([
                'productos' => $mapped,
                'meta' => [
                    'total' => $paginated->total(),
                    'per_page' => $paginated->perPage(),
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                ],
            ]);
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
            $q->where('business_types.slug', $businessTypeSlug);
        })
            ->active()
            ->ordered()
            ->with([
                'businessTypes' => function ($q) {
                    $q->select('business_types.id', 'business_types.slug', 'business_types.nombre', 'business_types.color', 'business_types.icon');
                },
            ])
            ->get();

        $data = $categories->map(function ($category) use ($businessTypeSlug) {
            // Obtener subcategorías para este business type
            $subcategorias = CategorySubcategory::where('category_id', $category->id)
                ->where('business_type_id', function ($q) use ($businessTypeSlug) {
                    $q->select('id')->from('business_types')->where('slug', $businessTypeSlug);
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

        if (! $producto) {
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
        $motivoMovimiento = "{$validated['motivo']}".($validated['notas'] ? " - {$validated['notas']}" : '');

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
     * GET /api/ecomm/tienda/config — Config publica del negocio (FlowApi).
     * Autenticacion: ?api_key=iak_... o Authorization: Bearer iak_...
     * Retorna el contrato de datos para el plugin WordPress ERP Connector.
     */
    public function config(Request $request)
    {
        $apiKey = $this->resolveApiKeyPublic($request);

        if (! $apiKey) {
            return response()->json([
                'status' => 'error',
                'data'   => null,
                'errors' => [['code' => 'INVALID_API_KEY', 'message' => 'API Key inválida o desactivada.']],
                'message' => 'API Key invalida',
            ], 401);
        }

        $instance = $apiKey->instance;

        if (! $instance || ! $instance->activo) {
            return response()->json([
                'status' => 'error',
                'data'   => null,
                'errors' => [['code' => 'INSTANCE_BLOCKED', 'message' => 'Instancia bloqueada o inactiva.']],
                'message' => 'Instancia bloqueada',
            ], 403);
        }

        $config = $instance->configuracion ?? [];

        $brand = [
            'name'          => $instance->nombre,
            'logo'          => $instance->logo ? asset('storage/'.$instance->logo) : null,
            'favicon'       => $config['favicon'] ?? null,
            'tagline'       => $config['tagline'] ?? SystemSetting::get('sistema_slogan', 'Tu tienda de confianza'),
            'color_primary' => $config['color_primary'] ?? '#1a73e8',
            'color_secondary' => $config['color_secondary'] ?? '#34a853',
        ];

        $monedaSimbolo = $config['moneda_simbolo'] ?? SystemSetting::monedaSimbolo();

        $currency = [
            'code'     => 'DOP',
            'symbol'   => $monedaSimbolo,
            'position' => 'before',
        ];

        $contact = [
            'email'     => $instance->email ?? '',
            'phone'     => $instance->telefono ?? '',
            'address'   => $instance->direccion ?? '',
            'whatsapp'  => SystemSetting::get('contact_whatsapp', ''),
            'facebook'  => SystemSetting::get('social_facebook', ''),
            'instagram' => SystemSetting::get('social_instagram', ''),
        ];

        $policies = [
            'terminos'     => $config['terminos_url'] ?? SystemSetting::get('policy_terminos', ''),
            'privacidad'   => SystemSetting::get('policy_privacidad', ''),
            'devoluciones' => $config['devoluciones_text'] ?? SystemSetting::get('policy_devoluciones', ''),
            'envios'       => SystemSetting::get('policy_envios', ''),
        ];

        $pluginContract = [
            'tenant_id' => $instance->id,
            'name'      => $instance->nombre,
            'brand'     => $brand,
            'currency'  => $currency,
            'contact'   => $contact,
            'policies'  => $policies,
        ];

        $flowapiCompat = [
            'modulo'       => 'tienda',
            'instance_id'  => $instance->id,
            'instance_slug' => $instance->slug,
            'nombre'       => $instance->nombre,
            'slogan'       => $brand['tagline'],
            'moneda'       => $currency['symbol'],
            'activo'       => $instance->activo,
            'business_type' => $instance->businessType?->nombre ?? null,
        ];

        return response()->json([
            'status'  => 'success',
            'data'    => array_merge($pluginContract, ['_flowapi_compat' => $flowapiCompat]),
            'errors'  => [],
            'message' => 'Configuracion del negocio obtenida exitosamente.',
        ]);
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

        if (! $producto) {
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

    /**
     * GET /api/tienda/config — Config publica del negocio (tienda API).
     * Autenticacion: api_key via api-auth middleware (Bearer iak_...).
     */
    public function tiendaConfig(Request $request)
    {
        $user = auth()->user();

        if (! $user) {
            return response()->json([
                'message' => 'No autenticado.',
            ], 401);
        }

        $instanceId = $user->business_instance_id ?? null;

        if (! $instanceId) {
            return response()->json([
                'message' => 'No se pudo determinar la instancia del negocio.',
            ], 400);
        }

        $instance = BusinessInstance::with('businessType')->find($instanceId);

        if (! $instance || ! $instance->activo) {
            return response()->json([
                'status' => 'error',
                'data'   => null,
                'errors' => [['code' => 'INSTANCE_BLOCKED', 'message' => 'Instancia bloqueada o inactiva.']],
                'message' => 'Instancia bloqueada',
            ], 403);
        }

        $config = $instance->configuracion ?? [];

        $brand = [
            'logo'          => $instance->logo ? asset('storage/'.$instance->logo) : null,
            'favicon'       => $config['favicon'] ?? null,
            'tagline'       => $config['tagline'] ?? SystemSetting::get('sistema_slogan', 'Tu tienda de confianza'),
            'color_primary' => $config['color_primary'] ?? '#1a73e8',
            'color_secondary' => $config['color_secondary'] ?? '#34a853',
        ];

        $contact = [
            'email'     => $instance->email ?? '',
            'phone'     => $instance->telefono ?? '',
            'whatsapp'  => SystemSetting::get('contact_whatsapp', ''),
            'address'   => $instance->direccion ?? '',
            'facebook'  => SystemSetting::get('social_facebook', ''),
            'instagram' => SystemSetting::get('social_instagram', ''),
        ];

        $policies = [
            'terminos'     => SystemSetting::get('policy_terminos', $config['terminos_url'] ?? ''),
            'privacidad'   => SystemSetting::get('policy_privacidad', ''),
            'devoluciones' => SystemSetting::get('policy_devoluciones', $config['devoluciones_text'] ?? ''),
            'envios'       => SystemSetting::get('policy_envios', ''),
        ];

        $seo = [
            'title'       => SystemSetting::get('seo_title', $instance->nombre),
            'description' => SystemSetting::get('seo_description', $instance->nombre),
        ];

        $paymentMethods = SystemSetting::get('payment_methods', null);

        $paymentMethodsData = $paymentMethods ? json_decode($paymentMethods, true) : [
            [
                'id'         => 'efectivo',
                'label'      => 'Efectivo',
                'instructions' => 'Pague con billetes o monedas en la tienda.',
                'online'     => false,
            ],
        ];

        return response()->json([
            'tenant_id'       => $instance->id,
            'name'            => $instance->nombre,
            'brand'           => $brand,
            'currency'        => [
                'code'     => 'DOP',
                'symbol'   => SystemSetting::monedaSimbolo(),
                'position' => 'before',
            ],
            'payment_methods' => $paymentMethodsData,
            'contact'         => $contact,
            'policies'        => $policies,
            'seo'             => $seo,
        ]);
    }

    /**
     * Resolve api_key from query param or bearer token (public endpoint).
     */
    private function resolveApiKeyPublic(Request $request): ?InstanceApiKey
    {
        $token = $request->query('api_key')
            ?? $request->bearerToken();

        if (! $token || ! str_starts_with($token, 'iak_')) {
            return null;
        }

        $hash = hash('sha256', $token);
        $apiKeyTTL = 300;

        return \Illuminate\Support\Facades\Cache::remember(
            "api_key_hash:{$hash}",
            $apiKeyTTL,
            fn () => InstanceApiKey::where('key', $hash)
                ->orWhere('key_raw', $token)
                ->where('is_active', true)
                ->with('instance.businessType')
                ->first()
        );
    }
}
