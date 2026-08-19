<?php

namespace App\Services\Ai\Tools;

use App\Models\Venta;
use App\Models\Compra;
use App\Models\Gasto;
use App\Models\Producto;
use App\Models\SesionCaja;
use Illuminate\Contracts\Auth\Authenticatable;

class GetDashboardTool implements AiToolInterface
{
    public function getName(): string
    {
        return 'get_dashboard';
    }

    public function getDescription(): string
    {
        return 'Obtiene un resumen del dashboard del negocio: ventas de hoy, ventas del mes, compras del mes, productos con bajo stock, sesiones de caja activas y utilidad del mes. Usa esta herramienta cuando el usuario pida un resumen general, estado del negocio, o informacion de ventas/gastos/inventario.';
    }

    public function getParameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [],
            'required' => [],
        ];
    }

    public function execute(array $input, Authenticatable $user): array
    {
        $tenantId = $user->business_instance_id;

        $ventasHoy = Venta::where('tenant_id', $tenantId)->whereDate('created_at', today())->sum('total');
        $ventasMes = Venta::where('tenant_id', $tenantId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');
        $comprasMes = Compra::where('tenant_id', $tenantId)
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->sum('total');
        $productosBajoStock = Producto::where('tenant_id', $tenantId)
            ->where('stock', '>', 0)
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->count();
        $sesionesAbiertas = SesionCaja::where('tenant_id', $tenantId)->where('estado', 'abierta')->count();

        $costoMes = 0;
        $utilidadMes = $ventasMes - $costoMes;

        return [
            'ventas_hoy' => number_format($ventasHoy, 2, '.', ','),
            'ventas_mes' => number_format($ventasMes, 2, '.', ','),
            'compras_mes' => number_format($comprasMes, 2, '.', ','),
            'productos_bajo_stock' => $productosBajoStock,
            'sesiones_caja_activas' => $sesionesAbiertas,
            'utilidad_mes' => number_format($utilidadMes, 2, '.', ','),
            'moneda' => 'RD$',
        ];
    }
}
