<?php

namespace App\Services\Ai\Tools;

use App\Models\Venta;
use App\Models\Compra;
use App\Models\Gasto;
use App\Models\Producto;
use App\Models\SesionCaja;
use App\Models\Caja;
use App\Models\Sucursal;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

class GetReportsTool implements AiToolInterface
{
    public function getName(): string
    {
        return 'get_reports';
    }

    public function getDescription(): string
    {
        return 'Obtiene reportes financieros y operativos. Accepta tipo (resumen, stock, utilidades), desde, hasta. Usa esta herramienta cuando el usuario pregunte por reportes, resmenes financieros, KPIs.';
    }

    public function getParameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'tipo' => [
                    'type' => 'string',
                    'description' => 'Tipo de reporte: "resumen" (por defecto), "stock", "utilidades".',
                ],
                'desde' => [
                    'type' => 'string',
                    'description' => 'Fecha inicio (YYYY-MM-DD). Opcional.',
                ],
                'hasta' => [
                    'type' => 'string',
                    'description' => 'Fecha fin (YYYY-MM-DD). Opcional.',
                ],
            ],
            'required' => [],
        ];
    }

    public function execute(array $input, Authenticatable $user): array
    {
        $tenantId = $user->business_instance_id;
        $tipo = $input['tipo'] ?? 'resumen';

        if ($tipo === 'stock') {
            return $this->stockReport();
        }

        return $this->resumenReport($input);
    }

    private function resumenReport(array $input): array
    {
        $desde = $input['desde'] ?? now()->startOfMonth()->format('Y-m-d');
        $hasta = $input['hasta'] ?? now()->endOfMonth()->format('Y-m-d');

        $ventasMes = Venta::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $comprasMes = Compra::whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->sum('total');

        $gastosMes = Gasto::whereMonth('fecha_gasto', now()->month)
            ->whereYear('fecha_gasto', now()->year)
            ->sum('monto');

        $productosBajoStock = Producto::where('stock', '>', 0)
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->count();

        $ventasHoy = Venta::whereDate('created_at', today())->sum('total');
        $comprasHoy = Compra::whereDate('fecha', today())->sum('total');

        $utilidadBruta = $ventasMes - $comprasMes - $gastosMes;

        return [
            'tipo' => 'resumen',
            'ventas_mes' => number_format($ventasMes, 2, '.', ','),
            'compras_mes' => number_format($comprasMes, 2, '.', ','),
            'gastos_mes' => number_format($gastosMes, 2, '.', ','),
            'utilidad_estimada' => number_format($utilidadBruta, 2, '.', ','),
            'ventas_hoy' => number_format($ventasHoy, 2, '.', ','),
            'compras_hoy' => number_format($comprasHoy, 2, '.', ','),
            'productos_bajo_stock' => $productosBajoStock,
            'desde' => $desde,
            'hasta' => $hasta,
        ];
    }

    private function stockReport(): array
    {
        $totalProductos = Producto::count();
        $conStock = Producto::where('stock', '>', 0)->count();
        $sinStock = Producto::where('stock', '<=', 0)->count();
        $bajoStock = Producto::where('stock', '>', 0)
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->count();

        return [
            'tipo' => 'stock',
            'total_productos' => $totalProductos,
            'con_stock' => $conStock,
            'sin_stock' => $sinStock,
            'bajo_stock' => $bajoStock,
        ];
    }
}
