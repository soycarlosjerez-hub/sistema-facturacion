<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Equipo;
use App\Models\OrdenReparacion;
use App\Models\Tecnico;
use App\Models\Cliente;
use App\Models\Presupuesto;
use App\Models\LicenciaSoftware;
use App\Models\RedConfig;
use App\Models\MarcaTecnologica;
use App\Models\GarantiasConfig;
use Illuminate\Http\Request;

class DashboardTecnologiaController extends Controller
{
    public function index(Request $request)
    {
        $kpis = $this->getKpis();

        $recentOrdenes = OrdenReparacion::with(['cliente', 'tecnico', 'equipo'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $presupuestosPendientes = Presupuesto::where('estado', 'borrador')
            ->where('valido_hasta', '>=', now())
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $licenciasPorVencer = LicenciaSoftware::where('licencia_activa', true)
            ->where('fecha_vencimiento', '<=', now()->addDays(30))
            ->whereNotNull('fecha_vencimiento')
            ->orderBy('fecha_vencimiento')
            ->limit(5)
            ->get();

        $estadosOrdenes = OrdenReparacion::selectRaw('estado, COUNT(*) as count')
            ->groupBy('estado')
            ->get()
            ->pluck('count', 'estado');

        return view('tecnologia.dashboard', compact(
            'kpis',
            'recentOrdenes',
            'presupuestosPendientes',
            'licenciasPorVencer',
            'estadosOrdenes'
        ));
    }

    public function getKpis()
    {
        $totalProductos = Producto::where('activo', true)->count();
        $totalEquipos = Equipo::count();
        $equiposDisponibles = Equipo::where('estado', 'disponible')->count();
        $equiposEnReparacion = Equipo::where('estado', 'en_reparacion')->count();
        $ordenesPendientes = OrdenReparacion::whereIn('estado', ['recibido', 'pendiente', 'diagnosticando', 'en_reparacion'])->count();
        $ordenesListas = OrdenReparacion::whereIn('estado', ['listo_para_entrega', 'terminado'])->count();
        $totalTecnicos = Tecnico::where('activo', true)->count();
        $presupuestosActivos = Presupuesto::where('estado', '!=', 'vencida')->count();
        $totalMarcas = MarcaTecnologica::where('activo', true)->count();
        $totalRedes = RedConfig::where('activo', true)->count();
        $licenciasActivas = LicenciaSoftware::where('licencia_activa', true)->count();
        $garantiasConfig = GarantiasConfig::where('activo', true)->count();

        $ventasMes = \App\Models\Venta::where('created_at', '>=', now()->startOfMonth())
            ->where('created_at', '<=', now()->endOfMonth())
            ->sum('total');

        $ingresosMes = \App\Models\OrdenReparacion::where('created_at', '>=', now()->startOfMonth())
            ->where('created_at', '<=', now()->endOfMonth())
            ->sum('total');

        return [
            'totalProductos' => $totalProductos,
            'totalEquipos' => $totalEquipos,
            'equiposDisponibles' => $equiposDisponibles,
            'equiposEnReparacion' => $equiposEnReparacion,
            'ordenesPendientes' => $ordenesPendientes,
            'ordenesListas' => $ordenesListas,
            'totalTecnicos' => $totalTecnicos,
            'presupuestosActivos' => $presupuestosActivos,
            'totalMarcas' => $totalMarcas,
            'totalRedes' => $totalRedes,
            'licenciasActivas' => $licenciasActivas,
            'garantiasConfig' => $garantiasConfig,
            'ventasMes' => $ventasMes,
            'ingresosMes' => $ingresosMes,
        ];
    }

    public function getRecentOrders()
    {
        $orders = OrdenReparacion::with(['cliente', 'tecnico'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return response()->json($orders);
    }
}
