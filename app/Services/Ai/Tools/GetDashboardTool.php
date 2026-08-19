<?php

namespace App\Services\Ai\Tools;

use App\Models\Venta;
use App\Models\Compra;
use App\Models\Gasto;
use App\Models\Producto;
use App\Models\SesionCaja;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        $ventasHoy = Venta::whereDate('created_at', today())->sum('total');
        $ventasMes = Venta::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');
        $comprasMes = Compra::whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->sum('total');
        $productosBajoStock = Producto::where('stock', '>', 0)
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->count();
        $sesionesAbiertas = SesionCaja::where('estado', 'abierta')->count();

        $stats = [
            'total_ventas' => DB::table('ventas')
                ->where('tenant_id', Auth::user()->business_instance_id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total') ?? 0,
            'costo_mes' => 0,
        ];

        $totalVentas = (float) ($stats->total_ventas ?? 0);
        $costoMes = (float) ($stats->costo_mes ?? 0);
        $utilidadMes = $totalVentas - $costoMes;

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
