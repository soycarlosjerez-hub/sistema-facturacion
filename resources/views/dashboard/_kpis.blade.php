@php
    $cambioVentasHoy = $pctCambio($kpis['ventasHoy'], $kpis['ventasAyer']);
    $cambioIngresos  = $pctCambio($kpis['ingresosMes'], $kpis['ingresosMesAnt']);
    $cambioUtilidad  = $pctCambio($kpis['utilidadMes'], $kpis['utilidadMesAnt']);
@endphp

<style>
    .kpi-premium-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .kpi-premium-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .kpi-premium-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        opacity: 0.8;
    }
    .kpi-primary::before { background: #4f46e5; }
    .kpi-info::before { background: #06b6d4; }
    .kpi-success::before { background: #10b981; }
    .kpi-danger::before { background: #ef4444; }
    .kpi-warning::before { background: #f59e0b; }
    .kpi-dark::before { background: #334155; }
    
    .icon-wrapper-kpi {
        width: 48px;
        height: 48px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    body.dark-mode .kpi-premium-card {
        background: rgba(15,23,42,.7);
        border-color: rgba(255,255,255,.06);
    }
    body.dark-mode .kpi-premium-card h3,
    body.dark-mode .kpi-premium-card h4 { color: #f1f5f9 !important; }
    body.dark-mode .kpi-premium-card .text-muted { color: #94a3b8 !important; }
    body.dark-mode .quick-action-premium { border-color: rgba(255,255,255,.1) !important; }
</style>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="kpi-premium-card kpi-primary h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-wrapper-kpi" style="background: rgba(79,70,229,0.1); color: #4f46e5;"><i class="bi bi-graph-up-arrow"></i></div>
                    <span class="badge {{ $esPositivo($cambioVentasHoy) ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} rounded-pill px-2 py-1 shadow-sm">
                        <i class="bi bi-arrow-{{ $esPositivo($cambioVentasHoy) ? 'up' : 'down' }}-short"></i>{{ abs($cambioVentasHoy) }}%
                    </span>
                </div>
                <small class="text-muted text-uppercase fw-bold" style="font-size:.7rem;letter-spacing:1px;">Ventas {{ request('desde') ? 'filtradas' : 'de hoy' }}</small>
                <h3 class="fw-bold mb-0 mt-1 text-dark">{{ $moneda }} {{ number_format($kpis['ventasHoy'], 2) }}</h3>
                <small class="text-muted">vs {{ $moneda }} {{ number_format($kpis['ventasAyer'], 0) }} ayer</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="kpi-premium-card kpi-info h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-wrapper-kpi" style="background: rgba(6,182,212,0.1); color: #06b6d4;"><i class="bi bi-cash-stack"></i></div>
                    <span class="badge {{ $esPositivo($cambioIngresos) ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} rounded-pill px-2 py-1 shadow-sm">
                        <i class="bi bi-arrow-{{ $esPositivo($cambioIngresos) ? 'up' : 'down' }}-short"></i>{{ abs($cambioIngresos) }}%
                    </span>
                </div>
                <small class="text-muted text-uppercase fw-bold" style="font-size:.7rem;letter-spacing:1px;">Ingresos del mes</small>
                <h3 class="fw-bold mb-0 mt-1 text-dark">{{ $moneda }} {{ number_format($kpis['ingresosMes'], 2) }}</h3>
                <small class="text-muted">{{ now()->translatedFormat('F Y') }}</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="kpi-premium-card kpi-success h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-wrapper-kpi" style="background: rgba(16,185,129,0.1); color: #10b981;"><i class="bi bi-piggy-bank"></i></div>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 shadow-sm" style="font-size:.7rem;">
                        Margen {{ $kpis['margen'] }}%
                    </span>
                </div>
                <small class="text-muted text-uppercase fw-bold" style="font-size:.7rem;letter-spacing:1px;">Utilidad del mes</small>
                <h3 class="fw-bold text-success mb-0 mt-1">{{ $moneda }} {{ number_format($kpis['utilidadMes'], 2) }}</h3>
                <small class="text-muted">Ganancia bruta</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="kpi-premium-card kpi-danger h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-wrapper-kpi" style="background: rgba(239,68,68,0.1); color: #ef4444;"><i class="bi bi-wallet2"></i></div>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1 shadow-sm" style="font-size:.7rem;">
                        {{ $kpis['facturasPendientes'] }} fact.
                    </span>
                </div>
                <small class="text-muted text-uppercase fw-bold" style="font-size:.7rem;letter-spacing:1px;">Cuentas por cobrar</small>
                <h3 class="fw-bold text-danger mb-0 mt-1">{{ $moneda }} {{ number_format($kpis['totalCuentasPorCobrar'], 2) }}</h3>
                <small class="text-muted">{{ $kpis['clientesConDeuda'] }} cliente(s) con deuda</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="kpi-premium-card kpi-warning h-100">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-wrapper-kpi flex-shrink-0" style="background: rgba(245,158,11,0.1); color: #f59e0b; width:44px; height:44px; font-size:1.1rem;">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fw-bold" style="font-size:.65rem;letter-spacing:.5px;">STOCK CRÍTICO</small>
                        <h4 class="fw-bold text-warning mb-0">{{ $secondaryStats['productosCriticos'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="kpi-premium-card kpi-info h-100">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-wrapper-kpi flex-shrink-0" style="background: rgba(6,182,212,0.1); color: #06b6d4; width:44px; height:44px; font-size:1.1rem;">
                        <i class="bi bi-box"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fw-bold" style="font-size:.65rem;letter-spacing:.5px;">TOTAL PRODUCTOS</small>
                        <h4 class="fw-bold text-dark mb-0">{{ $secondaryStats['totalProductos'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="kpi-premium-card kpi-primary h-100">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-wrapper-kpi flex-shrink-0" style="background: rgba(79,70,229,0.1); color: #4f46e5; width:44px; height:44px; font-size:1.1rem;">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fw-bold" style="font-size:.65rem;letter-spacing:.5px;">CLIENTES</small>
                        <h4 class="fw-bold text-dark mb-0">{{ $secondaryStats['totalClientes'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="kpi-premium-card kpi-dark h-100">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-wrapper-kpi flex-shrink-0" style="background: rgba(51,65,85,0.1); color: #334155; width:44px; height:44px; font-size:1.1rem;">
                        <i class="bi bi-safe2"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fw-bold" style="font-size:.65rem;letter-spacing:.5px;">INVENTARIO</small>
                        <h4 class="fw-bold text-dark mb-0">{{ $moneda }} {{ number_format($secondaryStats['valorInventario'], 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
