@php
    $cambioVentasHoy = $pctCambio($kpis['ventasHoy'], $kpis['ventasAyer']);
    $cambioIngresos  = $pctCambio($kpis['ingresosMes'], $kpis['ingresosMesAnt']);
@endphp

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="ui-stat" style="--delay:0s">
            <div class="ui-card-accent" style="background:#4f46e5;"></div>
            <div class="ui-stat-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="ui-stat-label">Ventas {{ request('desde') ? 'filtradas' : 'de hoy' }}</div>
                    <span class="badge {{ $esPositivo($cambioVentasHoy) ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} rounded-pill px-2 py-1">
                        <i class="bi bi-arrow-{{ $esPositivo($cambioVentasHoy) ? 'up' : 'down' }}-short"></i>{{ abs($cambioVentasHoy) }}%
                    </span>
                </div>
                <div class="ui-stat-value" style="font-size:1.6rem;">{{ $moneda }} {{ number_format($kpis['ventasHoy'], 2) }}</div>
                <div class="ui-stat-sub">vs {{ $moneda }} {{ number_format($kpis['ventasAyer'], 0) }} ayer · {{ $kpis['ticketsHoy'] }} ticket(s)</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="ui-stat" style="--delay:.05s">
            <div class="ui-card-accent" style="background:#06b6d4;"></div>
            <div class="ui-stat-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="ui-stat-label">Ingresos del mes</div>
                    <span class="badge {{ $esPositivo($cambioIngresos) ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} rounded-pill px-2 py-1">
                        <i class="bi bi-arrow-{{ $esPositivo($cambioIngresos) ? 'up' : 'down' }}-short"></i>{{ abs($cambioIngresos) }}%
                    </span>
                </div>
                <div class="ui-stat-value" style="font-size:1.6rem;color:#06b6d4;">{{ $moneda }} {{ number_format($kpis['ingresosMes'], 2) }}</div>
                <div class="ui-stat-sub">{{ now()->translatedFormat('F Y') }}</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="ui-stat" style="--delay:.1s">
            <div class="ui-card-accent" style="background:#10b981;"></div>
            <div class="ui-stat-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="ui-stat-label">Utilidad del mes</div>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1">Margen {{ $kpis['margen'] }}%</span>
                </div>
                <div class="ui-stat-value" style="font-size:1.6rem;color:#10b981;">{{ $moneda }} {{ number_format($kpis['utilidadMes'], 2) }}</div>
                <div class="ui-stat-sub">Ganancia bruta del período</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="ui-stat" style="--delay:.15s">
            <div class="ui-card-accent" style="background:#ef4444;"></div>
            <div class="ui-stat-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="ui-stat-label">Cuentas por cobrar</div>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1">{{ $kpis['facturasPendientes'] }} fact.</span>
                </div>
                <div class="ui-stat-value" style="font-size:1.6rem;color:#ef4444;">{{ $moneda }} {{ number_format($kpis['totalCuentasPorCobrar'], 2) }}</div>
                <div class="ui-stat-sub">{{ $kpis['clientesConDeuda'] }} cliente(s) con deuda</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="ui-stat" style="--delay:.2s">
            <div class="ui-stat-body d-flex align-items-center gap-3 py-3">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(245,158,11,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-exclamation-triangle text-warning"></i>
                </div>
                <div>
                    <div class="ui-stat-label" style="font-size:.6rem;">STOCK CRÍTICO</div>
                    <div class="fw-bold text-warning" style="font-size:1.2rem;">{{ $secondaryStats['productosCriticos'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="ui-stat" style="--delay:.25s">
            <div class="ui-stat-body d-flex align-items-center gap-3 py-3">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(6,182,212,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-box text-info"></i>
                </div>
                <div>
                    <div class="ui-stat-label" style="font-size:.6rem;">PRODUCTOS</div>
                    <div class="fw-bold" style="font-size:1.2rem;">{{ $secondaryStats['totalProductos'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="ui-stat" style="--delay:.3s">
            <div class="ui-stat-body d-flex align-items-center gap-3 py-3">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(79,70,229,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-people text-primary"></i>
                </div>
                <div>
                    <div class="ui-stat-label" style="font-size:.6rem;">CLIENTES</div>
                    <div class="fw-bold" style="font-size:1.2rem;">{{ $secondaryStats['totalClientes'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="ui-stat" style="--delay:.35s">
            <div class="ui-stat-body d-flex align-items-center gap-3 py-3">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(15,23,42,.08);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-safe2 text-dark"></i>
                </div>
                <div>
                    <div class="ui-stat-label" style="font-size:.6rem;">INVENTARIO</div>
                    <div class="fw-bold" style="font-size:1.2rem;">{{ $moneda }} {{ number_format($secondaryStats['valorInventario'], 0) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>