<div class="row g-3 mb-4">
    <div class="col-xl-8">
        <div class="card chart-card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-1"><i class="bi bi-graph-up text-primary me-2"></i>Tendencia de ventas</h5>
                    <small class="text-muted">Últimos 30 días · Total: {{ $moneda }} {{ number_format(array_sum($chartData['data']), 0) }}</small>
                </div>
            </div>
            <div class="card-body p-4 pt-0">
                <canvas id="ventasChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card chart-card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 p-4 pb-0">
                <h5 class="fw-bold mb-1"><i class="bi bi-bar-chart text-success me-2"></i>Ventas por hora</h5>
                <small class="text-muted">Hoy · Pico: {{ $moneda }} {{ number_format(max($hourlyData['data']), 0) }}</small>
            </div>
            <div class="card-body p-4 pt-2">
                <canvas id="horasChart" height="180"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-4">
        <div class="card chart-card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="bi bi-pie-chart text-success me-2"></i>Métodos de pago</h5>
                <span class="badge bg-light text-muted">Hoy</span>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="d-flex align-items-center justify-content-center mb-3">
                    <canvas id="paymentChart" height="160"></canvas>
                </div>
                <div class="row g-2">
                    @foreach($paymentMethod['labels'] as $i => $label)
                    <div class="col-6">
                        <div class="payment-method-row d-flex align-items-center gap-2">
                            <span style="width:10px;height:10px;border-radius:50%;background:{{ $paymentMethod['colors'][$i] }};display:inline-block;"></span>
                            <div class="small">
                                <div class="fw-bold">{{ $label }}</div>
                                <small class="text-muted">{{ $paymentMethod['total'] > 0 ? round(($paymentMethod['data'][$i] / $paymentMethod['total']) * 100, 1) : 0 }}%</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card chart-card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="bi bi-trophy text-warning me-2"></i>Top productos</h5>
                <span class="badge bg-light text-muted">{{ now()->translatedFormat('F') }}</span>
            </div>
            <div class="card-body p-4 pt-0">
                @forelse($topProductos as $i => $prod)
                    @php
                        $maxVendidos = $topProductos->max('cantidad_vendida') ?: 1;
                        $porcentaje = ($prod->cantidad_vendida / $maxVendidos) * 100;
                        $rankClass = 'rank-' . ($i + 1);
                        if ($i > 2) $rankClass = 'rank-n';
                    @endphp
                    <div class="top-product d-flex align-items-center gap-3 mb-2">
                        <div class="rounded-circle {{ $rankClass }} d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:36px;height:36px;">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="fw-bold mb-0 text-truncate" title="{{ $prod->nombre }}">{{ $prod->nombre }}</h6>
                                <span class="text-muted small ms-2">{{ $prod->cantidad_vendida }} u.</span>
                            </div>
                            <div class="progress" style="height:6px;">
                                <div class="progress-bar bg-primary bg-gradient" style="width:{{ $porcentaje }}%"></div>
                            </div>
                            <small class="text-muted">{{ $moneda }} {{ number_format($prod->ingreso_total, 0) }} · Util: {{ $moneda }} {{ number_format($prod->utilidad, 0) }}</small>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-emoji-frown fs-1"></i>
                        <p class="mt-2 mb-0">Aún no hay ventas este mes</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card chart-card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="bi bi-people text-danger me-2"></i>Mayores deudores</h5>
                <a href="{{ route('clientes.cuentas') }}" class="text-decoration-none small fw-bold">Ver todos</a>
            </div>
            <div class="card-body p-4 pt-0">
                @forelse($topDeudores as $deudor)
                    <div class="debtor-row d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-3 overflow-hidden">
                            <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:42px;height:42px;">
                                {{ strtoupper(substr($deudor->nombre, 0, 1)) }}
                            </div>
                            <div class="overflow-hidden">
                                <h6 class="fw-bold mb-0 text-truncate" style="max-width:160px;">{{ $deudor->nombre }}</h6>
                                <small class="text-muted">{{ $deudor->rnc_cedula ?? 'Sin RNC' }}</small>
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="fw-bold text-danger">{{ $moneda }} {{ number_format($deudor->balance_pendiente, 0) }}</div>
                            <a href="{{ route('clientes.cuentas') }}" class="text-decoration-none small fw-bold">Cobrar</a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-check-circle text-success fs-1"></i>
                        <p class="mt-2 mb-0">¡Sin deudas pendientes!</p>
                    </div>
                @endforelse

                <hr class="my-3">

                <h6 class="fw-bold mb-2"><i class="bi bi-receipt text-info me-1"></i>Resumen Fiscal</h6>
                <div class="d-flex gap-2 flex-wrap">
                    <div class="fiscal-badge d-flex align-items-center gap-2">
                        <i class="bi bi-file-text text-primary"></i>
                        <div>
                            <small class="d-block fw-bold">{{ $alertas['ncf_por_vencer']->count() }}</small>
                            <small class="text-muted" style="font-size:.65rem;">NCF por vencer</small>
                        </div>
                    </div>
                    <div class="fiscal-badge d-flex align-items-center gap-2">
                        <i class="bi bi-box-seam text-warning"></i>
                        <div>
                            <small class="d-block fw-bold">{{ $alertas['productos_sin_rotacion'] }}</small>
                            <small class="text-muted" style="font-size:.65rem;">Sin rotación</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
