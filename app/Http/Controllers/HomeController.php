<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HomeExport;

class HomeController extends Controller
{
    protected DashboardService $dashboard;

    public function __construct(DashboardService $dashboard)
    {
        $this->dashboard = $dashboard;
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date|after_or_equal:desde',
        ]);
        $startDate = $validated['desde'] ?? null;
        $endDate = $validated['hasta'] ?? null;

        $kpis = $this->dashboard->getKpis($startDate, $endDate);
        $cajaActual = $this->dashboard->getCashRegisterStatus();
        $secondaryStats = $this->dashboard->getSecondaryStats();
        $chartData = $this->dashboard->getSalesChartData();
        $hourlyData = $this->dashboard->getHourlySalesChart();
        $paymentMethod = $this->dashboard->getPaymentMethodChart();
        $topProductos = $this->dashboard->getTopProducts();
        $topDeudores = $this->dashboard->getTopDebtors();
        $activity = $this->dashboard->getRecentActivity();
        $alertas = $this->dashboard->getAlerts();
        $rankingUsuarios = $this->dashboard->getUserRanking();

        return view('dashboard', compact(
            'kpis', 'cajaActual', 'secondaryStats',
            'chartData', 'hourlyData', 'paymentMethod',
            'topProductos', 'topDeudores',
            'activity', 'alertas', 'rankingUsuarios'
        ));
    }

    public function pdf(Request $request)
    {
        $kpis = $this->dashboard->getKpis();
        $chartData = $this->dashboard->getSalesChartData();
        $topProductos = $this->dashboard->getTopProducts();
        $pdf = Pdf::loadView('dashboard.pdf', compact('kpis', 'chartData', 'topProductos'));
        return $pdf->stream('dashboard-' . date('Y-m-d') . '.pdf');
    }

    public function export(Request $request)
    {
        return Excel::download(new HomeExport, 'dashboard-' . date('Y-m-d') . '.xlsx');
    }

    public function toggleDarkMode(Request $request)
    {
        $darkMode = session('dark_mode', false);
        session(['dark_mode' => !$darkMode]);
        return back();
    }

    public function setSucursalActiva(Request $request)
    {
        $request->validate(['sucursal_id' => 'nullable|exists:sucursales,id']);
        session(['sucursal_id' => $request->sucursal_id]);
        return back()->with('success', 'Sucursal activa cambiada.');
    }
}
