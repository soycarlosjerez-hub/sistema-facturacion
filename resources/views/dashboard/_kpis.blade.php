@php
    $cambioVentasHoy = $pctCambio($kpis['ventasHoy'], $kpis['ventasAyer']);
    $cambioIngresos  = $pctCambio($kpis['ingresosMes'], $kpis['ingresosMesAnt']);
    $cambioUtilidad  = $pctCambio($kpis['utilidadMes'], $kpis['utilidadMesAnt']);
@endphp

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card accent-primary border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-bubble bg-soft-primary"><i class="bi bi-graph-up-arrow"></i></div>
                    <span class="trend-pill {{ $esPositivo($cambioVentasHoy) ? 'trend-up' : 'trend-down' }}">
                        <i class="bi bi-arrow-{{ $esPositivo($cambioVentasHoy) ? 'up' : 'down' }}-short"></i>{{ abs($cambioVentasHoy) }}%
                    </span>
                </div>
                <small class="text-muted text-uppercase fw-bold" style="font-size:.7rem;letter-spacing:1px;">Ventas {{ request('desde') ? 'filtradas' : 'de hoy' }}</small>
                <h3 class="fw-bold mb-0 mt-1">{{ $moneda }} {{ number_format($kpis['ventasHoy'], 2) }}</h3>
                <small class="text-muted">vs {{ $moneda }} {{ number_format($kpis['ventasAyer'], 0) }} ayer</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card accent-info border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-bubble bg-soft-info"><i class="bi bi-cash-stack"></i></div>
                    <span class="trend-pill {{ $esPositivo($cambioIngresos) ? 'trend-up' : 'trend-down' }}">
                        <i class="bi bi-arrow-{{ $esPositivo($cambioIngresos) ? 'up' : 'down' }}-short"></i>{{ abs($cambioIngresos) }}%
                    </span>
                </div>
                <small class="text-muted text-uppercase fw-bold" style="font-size:.7rem;letter-spacing:1px;">Ingresos del mes</small>
                <h3 class="fw-bold mb-0 mt-1">{{ $moneda }} {{ number_format($kpis['ingresosMes'], 2) }}</h3>
                <small class="text-muted">{{ now()->translatedFormat('F Y') }}</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card accent-success border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-bubble bg-soft-success"><i class="bi bi-piggy-bank"></i></div>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1" style="font-size:.7rem;">
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
        <div class="card kpi-card accent-danger border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-bubble bg-soft-danger"><i class="bi bi-wallet2"></i></div>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1" style="font-size:.7rem;">
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
        <div class="card kpi-card accent-warning border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-soft-warning flex-shrink-0" style="width:44px;height:44px;font-size:1.1rem;">
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
        <div class="card kpi-card accent-info border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-soft-info flex-shrink-0" style="width:44px;height:44px;font-size:1.1rem;">
                        <i class="bi bi-box"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fw-bold" style="font-size:.65rem;letter-spacing:.5px;">TOTAL PRODUCTOS</small>
                        <h4 class="fw-bold mb-0">{{ $secondaryStats['totalProductos'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card kpi-card accent-primary border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-soft-primary flex-shrink-0" style="width:44px;height:44px;font-size:1.1rem;">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fw-bold" style="font-size:.65rem;letter-spacing:.5px;">CLIENTES</small>
                        <h4 class="fw-bold mb-0">{{ $secondaryStats['totalClientes'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card kpi-card accent-dark border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-soft-dark flex-shrink-0" style="width:44px;height:44px;font-size:1.1rem;">
                        <i class="bi bi-safe2"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fw-bold" style="font-size:.65rem;letter-spacing:.5px;">INVENTARIO</small>
                        <h4 class="fw-bold mb-0">{{ $moneda }} {{ number_format($secondaryStats['valorInventario'], 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
