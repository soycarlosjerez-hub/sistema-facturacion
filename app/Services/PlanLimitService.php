<?php

namespace App\Services;

use App\Models\BusinessInstance;
use App\Models\Plan;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Almacen;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\Gasto;
use App\Models\Caja;
use App\Models\Cotizacion;
use App\Models\Conduce;
use App\Models\Devolucion;
use App\Models\Orden;
use App\Models\Mesa;

class PlanLimitService
{
    /**
     * Mapeo de recursos a sus contadores y límites en el plan
     */
    protected array $resourceMap = [
        'usuario' => [
            'count_fn' => 'usersUsados',
            'max_field' => 'max_usuarios',
            'label' => 'usuario(s)',
        ],
        'sucursal' => [
            'count_fn' => 'sucursalesUsadas',
            'max_field' => 'max_sucursales',
            'label' => 'sucursal(es)',
        ],
        'empresa' => [
            'count_fn' => 'empresasUsadas',
            'max_field' => 'max_empresas',
            'label' => 'empresa(s)',
        ],
        'almacen' => [
            'count_fn' => 'almacenesUsados',
            'max_field' => 'max_almacenes',
            'label' => 'almacén(es)',
        ],
        'producto' => [
            'count_fn' => 'productosUsados',
            'max_field' => 'max_productos',
            'label' => 'producto(s)',
        ],
        'cliente' => [
            'count_fn' => 'clientesUsados',
            'max_field' => 'max_clientes',
            'label' => 'cliente(s)',
        ],
        'proveedor' => [
            'count_fn' => 'proveedoresUsados',
            'max_field' => 'max_proveedores',
            'label' => 'proveedor(es)',
        ],
        'venta' => [
            'count_fn' => 'ventasUsadas',
            'max_field' => 'max_ventas_mensuales',
            'label' => 'venta(s) mensual(es)',
        ],
        'compra' => [
            'count_fn' => 'comprasUsadas',
            'max_field' => 'max_compras_mensuales',
            'label' => 'compra(s) mensual(es)',
        ],
        'gasto' => [
            'count_fn' => 'gastosUsados',
            'max_field' => 'max_gastos_mensuales',
            'label' => 'gasto(s) mensual(es)',
        ],
        'caja' => [
            'count_fn' => 'cajasUsadas',
            'max_field' => 'max_cajas',
            'label' => 'caja(s)',
        ],
        'cotizacion' => [
            'count_fn' => 'cotizacionesUsadas',
            'max_field' => 'max_cotizaciones_mensuales',
            'label' => 'cotización(es) mensual(es)',
        ],
        'conduce' => [
            'count_fn' => 'conducesUsados',
            'max_field' => 'max_conduces_mensuales',
            'label' => 'conduce(s) mensual(es)',
        ],
        'devolucion' => [
            'count_fn' => 'devolucionesUsadas',
            'max_field' => 'max_devoluciones_mensuales',
            'label' => 'devolución(es) mensual(es)',
        ],
        'orden' => [
            'count_fn' => 'ordenesUsadas',
            'max_field' => 'max_ordenes_mensuales',
            'label' => 'orden(es) mensual(es)',
        ],
        'mesa' => [
            'count_fn' => 'mesasUsadas',
            'max_field' => 'max_mesas',
            'label' => 'mesa(s)',
        ],
    ];

    public function usersUsados(BusinessInstance $instance): int
    {
        return User::where('business_instance_id', $instance->id)->count();
    }

    public function sucursalesUsadas(BusinessInstance $instance): int
    {
        return Sucursal::where('business_instance_id', $instance->id)->count();
    }

    public function empresasUsadas(BusinessInstance $instance): int
    {
        return BusinessInstance::where('owner_user_id', $instance->owner_user_id)
            ->where('activo', true)
            ->count();
    }

    public function almacenesUsados(BusinessInstance $instance): int
    {
        return Almacen::where('business_instance_id', $instance->id)->count();
    }

    public function productosUsados(BusinessInstance $instance): int
    {
        return Producto::where('tenant_id', $instance->id)->count();
    }

    public function clientesUsados(BusinessInstance $instance): int
    {
        return Cliente::where('tenant_id', $instance->id)->count();
    }

    public function proveedoresUsados(BusinessInstance $instance): int
    {
        return Proveedor::where('tenant_id', $instance->id)->count();
    }

    public function ventasUsadas(BusinessInstance $instance): int
    {
        return Venta::where('tenant_id', $instance->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function comprasUsadas(BusinessInstance $instance): int
    {
        return Compra::where('tenant_id', $instance->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function gastosUsados(BusinessInstance $instance): int
    {
        return Gasto::where('tenant_id', $instance->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function cajasUsadas(BusinessInstance $instance): int
    {
        return Caja::where('business_instance_id', $instance->id)->count();
    }

    public function cotizacionesUsadas(BusinessInstance $instance): int
    {
        return Cotizacion::where('tenant_id', $instance->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function conducesUsados(BusinessInstance $instance): int
    {
        return Conduce::where('tenant_id', $instance->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function devolucionesUsadas(BusinessInstance $instance): int
    {
        return Devolucion::where('tenant_id', $instance->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function ordenesUsadas(BusinessInstance $instance): int
    {
        return Orden::where('tenant_id', $instance->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function mesasUsadas(BusinessInstance $instance): int
    {
        return Mesa::where('business_instance_id', $instance->id)->count();
    }

    public function puedeAgregar(BusinessInstance $instance, string $resource): bool
    {
        $map = $this->resourceMap[$resource] ?? null;
        if (!$map) {
            return true;
        }

        $max = $instance->plan?->{$map['max_field']};
        if ($max === null) {
            return true;
        }

        $usados = $this->{$map['count_fn']}($instance);

        return $usados < $max;
    }

    public function restantes(BusinessInstance $instance, string $resource): ?int
    {
        $map = $this->resourceMap[$resource] ?? null;
        if (!$map) {
            return null;
        }

        $max = $instance->plan?->{$map['max_field']};
        if ($max === null) {
            return null;
        }

        $usados = $this->{$map['count_fn']}($instance);

        return max(0, $max - $usados);
    }

    /**
     * @return array{ok: bool, mensaje?: string, limite?: int, usados?: int, recurso?: string}
     */
    public function verificar(BusinessInstance $instance, string $resource): array
    {
        $map = $this->resourceMap[$resource] ?? null;
        if (!$map) {
            return ['ok' => true, 'recurso' => $resource];
        }

        if ($this->puedeAgregar($instance, $resource)) {
            return ['ok' => true, 'recurso' => $resource];
        }

        $plan = $instance->plan;
        $limite = $plan?->{$map['max_field']} ?? 0;
        $usados = $this->{$map['count_fn']}($instance);

        return [
            'ok' => false,
            'recurso' => $resource,
            'mensaje' => 'El plan ' . ($plan?->nombre ?? 'actual') . ' permite máximo '
                . $limite . ' ' . $map['label'] . '. Considera migrar a un plan superior.',
            'limite' => $limite,
            'usados' => $usados,
        ];
    }

    /**
     * Verifica múltiples recursos de una vez
     * @return array<string, array{ok: bool, mensaje?: string, limite?: int, usados?: int, recurso?: string}>
     */
    public function verificarMultiples(BusinessInstance $instance, array $resources): array
    {
        $resultados = [];
        foreach ($resources as $resource) {
            $resultados[$resource] = $this->verificar($instance, $resource);
        }
        return $resultados;
    }

    /**
     * Verifica si el usuario dueño puede crear otra empresa (plan Corporativo).
     */
    public function verificarEmpresa(?Plan $plan, int $instanciasActuales): array
    {
        if ($plan === null || $plan->max_empresas === null) {
            return ['ok' => true];
        }

        if ($instanciasActuales < $plan->max_empresas) {
            return ['ok' => true];
        }

        return [
            'ok' => false,
            'mensaje' => 'El plan ' . $plan->nombre . ' permite máximo '
                . $plan->max_empresas . ' empresa(s).',
            'limite' => $plan->max_empresas,
            'usados' => $instanciasActuales,
        ];
    }

    /**
     * Obtiene todos los recursos mapeados
     */
    public function getRecursos(): array
    {
        return array_keys($this->resourceMap);
    }

    /**
     * Obtiene información de un recurso específico
     */
    public function getRecursoInfo(string $resource): ?array
    {
        return $this->resourceMap[$resource] ?? null;
    }
}
