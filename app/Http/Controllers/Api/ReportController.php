<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReportDashboardResource;
use App\Http\Resources\TopProductoResource;
use App\Http\Resources\TopClienteResource;
use App\Http\Resources\InventarioBajoStockResource;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\AlmacenProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function dashboard()
    {
        $hoy = now()->toDateString();
        $primerDiaMes = now()->startOfMonth()->toDateString();
        $ultimoDiaMes = now()->endOfMonth()->toDateString();

        return new ReportDashboardResource([
            'ventas_hoy' => Venta::whereDate('fecha', $hoy)->sum('total'),
            'ventas_mes' => Venta::whereBetween('fecha', [$primerDiaMes, $ultimoDiaMes])->sum('total'),
            'compras_hoy' => Compra::whereDate('fecha_compra', $hoy)->sum('total_compra'),
            'compras_mes' => Compra::whereBetween('fecha_compra', [$primerDiaMes, $ultimoDiaMes])->sum('total_compra'),
            'clientes_totales' => Cliente::count(),
            'productos_activos' => Producto::where('activo', true)->count(),
            'inventario_bajo_stock' => AlmacenProducto::whereColumn('stock_actual', '<=', 'stock_minimo')->count(),
            'ingresos_mes' => Venta::whereBetween('fecha', [$primerDiaMes, $ultimoDiaMes])->sum('total'),
            'gastos_mes' => Compra::whereBetween('fecha_compra', [$primerDiaMes, $ultimoDiaMes])->sum('total_compra'),
            'ganancia_neta' => Venta::whereBetween('fecha', [$primerDiaMes, $ultimoDiaMes])->sum('total')
                - Compra::whereBetween('fecha_compra', [$primerDiaMes, $ultimoDiaMes])->sum('total_compra'),
        ]);
    }

    public function topProductos(Request $request)
    {
        $limit = $request->input('limit', 10);

        $productos = DB::table('venta_detalles')
            ->join('productos', 'venta_detalles.producto_id', '=', 'productos.id')
            ->select(
                'venta_detalles.producto_id',
                'productos.nombre',
                DB::raw('SUM(venta_detalles.cantidad) as cantidad_vendida'),
                DB::raw('SUM(venta_detalles.subtotal) as ingresos')
            )
            ->groupBy('venta_detalles.producto_id', 'productos.nombre')
            ->orderByDesc('cantidad_vendida')
            ->limit($limit)
            ->get();

        return TopProductoResource::collection($productos);
    }

    public function topClientes(Request $request)
    {
        $limit = $request->input('limit', 10);

        $clientes = DB::table('ventas')
            ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')
            ->select(
                'ventas.cliente_id',
                'clientes.nombre',
                DB::raw('COUNT(ventas.id) as compras_total'),
                DB::raw('SUM(ventas.total) as monto_gastado')
            )
            ->groupBy('ventas.cliente_id', 'clientes.nombre')
            ->orderByDesc('monto_gastado')
            ->limit($limit)
            ->get();

        return TopClienteResource::collection($clientes);
    }

    public function inventarioBajoStock()
    {
        $productos = DB::table('almacen_productos')
            ->join('productos', 'almacen_productos.producto_id', '=', 'productos.id')
            ->select(
                'productos.id as producto_id',
                'productos.nombre',
                'almacen_productos.stock_actual',
                'productos.stock_minimo',
                'productos.unidad_medida'
            )
            ->whereColumn('almacen_productos.stock_actual', '<=', 'productos.stock_minimo')
            ->orderByAsc('almacen_productos.stock_actual')
            ->get();

        return InventarioBajoStockResource::collection($productos);
    }
}
