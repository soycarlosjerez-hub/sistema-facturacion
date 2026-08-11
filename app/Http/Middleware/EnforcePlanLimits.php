<?php

namespace App\Http\Middleware;

use App\Services\PlanLimitService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EnforcePlanLimits
{
    protected PlanLimitService $planLimitService;

    /**
     * Mapeo de rutas/acciones a recursos que consumen límites
     * Formato: 'route.name' => 'recurso' o ['recurso1', 'recurso2']
     */
    protected array $routeResourceMap = [
        // Usuarios
        'usuarios.store' => 'usuario',
        'instances.users.store' => 'usuario',

        // Sucursales
        'sucursales.store' => 'sucursal',

        // Almacenes
        'almacenes.store' => 'almacen',

        // Productos
        'productos.store' => 'producto',
        'productos.import.process' => 'producto',

        // Clientes
        'clientes.store' => 'cliente',
        'clientes.importar.procesar' => 'cliente',

        // Proveedores
        'proveedores.store' => 'proveedor',
        'proveedores.importar.procesar' => 'proveedor',

        // Ventas (mensual)
        'ventas.store' => 'venta',
        'ordenes.store' => 'venta',

        // Compras (mensual)
        'compras.store' => 'compra',

        // Gastos (mensual)
        'gastos.store' => 'gasto',

        // Cajas
        'cajas.store' => 'caja',

        // Cotizaciones (mensual)
        'cotizaciones.store' => 'cotizacion',

        // Conduces (mensual)
        'conduces.store' => 'conduce',

        // Devoluciones (mensual)
        'devoluciones.store' => 'devolucion',

        // Órdenes restaurante (mensual)
        'ordenes.store' => 'orden',

        // Mesas
        'mesas.store' => 'mesa',
    ];

    public function __construct(PlanLimitService $planLimitService)
    {
        $this->planLimitService = $planLimitService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->business_instance_id) {
            return $next($request);
        }

        $instance = $user->businessInstance;

        if (!$instance || !$instance->plan) {
            return $next($request);
        }

        // Solo validar en métodos que crean recursos
        if (!$this->esMetodoCreacion($request)) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();

        if (!$routeName || !isset($this->routeResourceMap[$routeName])) {
            return $next($request);
        }

        $recursos = $this->routeResourceMap[$routeName];
        if (!is_array($recursos)) {
            $recursos = [$recursos];
        }

        $resultados = $this->planLimitService->verificarMultiples($instance, $recursos);

        $errores = array_filter($resultados, fn($r) => !$r['ok']);

        if (!empty($errores)) {
            $primerError = reset($errores);
            $mensaje = $primerError['mensaje'] ?? 'Límite del plan excedido.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $mensaje,
                    'error_code' => 'PLAN_LIMIT_EXCEEDED',
                    'detalles' => $errores,
                ], 403);
            }

            return back()
                ->withInput()
                ->with('error', $mensaje);
        }

        return $next($request);
    }

    protected function esMetodoCreacion(Request $request): bool
    {
        return in_array($request->method(), ['POST', 'PUT', 'PATCH'], true);
    }

    /**
     * Permite verificar límites manualmente desde controllers
     */
    public static function verificar(Request $request, string $recurso): array
    {
        $user = $request->user();

        if (!$user || !$user->business_instance_id) {
            return ['ok' => true];
        }

        $instance = $user->businessInstance;

        if (!$instance || !$instance->plan) {
            return ['ok' => true];
        }

        return app(PlanLimitService::class)->verificar($instance, $recurso);
    }
}