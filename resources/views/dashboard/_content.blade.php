<div class="row g-3 mb-4">
    {{-- Sales trend chart --}}
    <div class="col-xl-8">
        <div class="ui-card h-100" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold mb-0"><i class="bi bi-graph-up me-2" style="color:var(--accent);"></i>Tendencia de ventas</h5>
                        <small class="text-muted">Últimos 30 días · Total: {{ $moneda }} {{ number_format(array_sum($chartData['data'] ?? []), 0) }}</small>
                    </div>
                </div>
                <div style="height:220px;">
                    <canvas id="ventasChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Hourly sales chart --}}
    <div class="col-xl-4">
        <div class="ui-card h-100" style="--delay:.15s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body">
                <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart me-2" style="color:var(--accent);"></i>Ventas por hora</h5>
                <small class="text-muted d-block mb-3">Hoy · Pico: {{ $moneda }} {{ number_format(max($hourlyData['data']), 0) }}</small>
                <div style="height:200px;">
                    <canvas id="horasChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Payment methods --}}
    <div class="col-xl-4">
        <div class="ui-card h-100" style="--delay:.2s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-pie-chart me-2" style="color:var(--accent);"></i>Métodos de pago</h5>
                    <span class="ui-badge ui-badge-info">Hoy</span>
                </div>
                <div class="text-center mb-3" style="height:180px;">
                    <canvas id="paymentChart"></canvas>
                </div>
                <div class="row g-2">
                    @foreach($paymentMethod['labels'] as $i => $label)
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2">
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

    {{-- Top products --}}
    <div class="col-xl-4">
        <div class="ui-card h-100" style="--delay:.25s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-trophy me-2" style="color:var(--accent);"></i>Top productos</h5>
                    <span class="ui-badge ui-badge-info">{{ now()->translatedFormat('F') }}</span>
                </div>
                @forelse($topProductos as $i => $prod)
                    @php
                        $maxVendidos = $topProductos->max('cantidad_vendida') ?: 1;
                        $pct = ($prod->cantidad_vendida / $maxVendidos) * 100;
                    @endphp
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:36px;height:36px;background:{{ $i === 0 ? 'linear-gradient(135deg,#f59e0b,#d97706)' : ($i === 1 ? 'linear-gradient(135deg,#94a3b8,#64748b)' : ($i === 2 ? 'linear-gradient(135deg,#b45309,#92400e)' : 'rgba(0,0,0,.05)')) }};color:{{ $i < 3 ? '#fff' : '#64748b' }};">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-grow-1" style="min-width:0;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold small text-truncate" style="max-width:140px;" title="{{ $prod->nombre }}">{{ $prod->nombre }}</span>
                                <small class="text-muted ms-2">{{ $prod->cantidad_vendida }} u.</small>
                            </div>
                            <div class="progress" style="height:5px;">
                                <div class="progress-bar bg-primary" style="width:{{ $pct }}%;background:var(--accent) !important;"></div>
                            </div>
                            <small class="text-muted">{{ $moneda }} {{ number_format($prod->ingreso_total, 0) }} · Util: {{ $moneda }} {{ number_format($prod->utilidad, 0) }}</small>
                        </div>
                    </div>
                @empty
                    <div class="ui-empty-state">
                        <i class="bi bi-emoji-frown"></i>
                        <p>No hay ventas este mes</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Top debtors --}}
    <div class="col-xl-4">
        <div class="ui-card h-100" style="--delay:.3s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-people me-2" style="color:var(--accent);"></i>Mayores deudores</h5>
                    <a href="{{ route('clientes.cuentas') }}" class="small fw-bold text-decoration-none">Ver todos</a>
                </div>
                @forelse($topDeudores as $deudor)
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-3 overflow-hidden">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:42px;height:42px;background:rgba(239,68,68,.1);color:#ef4444;">
                                {{ strtoupper(substr($deudor->nombre, 0, 1)) }}
                            </div>
                            <div style="min-width:0;">
                                <span class="fw-bold small d-block text-truncate" style="max-width:160px;">{{ $deudor->nombre }}</span>
                                <small class="text-muted">{{ $deudor->rnc_cedula ?? 'Sin RNC' }}</small>
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="fw-bold" style="color:#ef4444;">{{ $moneda }} {{ number_format($deudor->balance_pendiente, 0) }}</div>
                            <a href="{{ route('clientes.cuentas') }}" class="small fw-bold text-decoration-none">Cobrar</a>
                        </div>
                    </div>
                @empty
                    <div class="ui-empty-state">
                        <i class="bi bi-check-circle" style="color:#10b981;"></i>
                        <p>¡Sin deudas pendientes!</p>
                    </div>
                @endforelse

                <hr class="my-3">
                <h6 class="fw-bold small mb-2"><i class="bi bi-receipt me-1" style="color:var(--accent);"></i>Resumen Fiscal</h6>
                <div class="d-flex gap-3 flex-wrap">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-file-text" style="color:var(--accent);"></i>
                        <div>
                            <span class="fw-bold d-block">{{ $alertas['ncf_por_vencer']->count() }}</span>
                            <small class="text-muted">NCF por vencer</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-box-seam text-warning"></i>
                        <div>
                            <span class="fw-bold d-block">{{ $alertas['productos_sin_rotacion'] }}</span>
                            <small class="text-muted">Sin rotación</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>