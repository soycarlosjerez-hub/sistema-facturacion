<div class="row g-3 mb-4">
    <div class="col-xl-3">
        <div class="card chart-card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-person-badge text-info me-2"></i>Ranking cajeros</h5>
                <small class="text-muted">Ventas del mes</small>
            </div>
            <div class="card-body p-4 pt-0">
                @forelse($rankingUsuarios as $i => $user)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-n')) }} d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:32px;height:32px;font-size:.85rem;">
                            {{ $i + 1 }}
                        </div>
                        <div class="overflow-hidden flex-grow-1">
                            <h6 class="fw-bold mb-0 text-truncate" style="max-width:120px;">{{ $user->name }}</h6>
                            <small class="text-muted">{{ $user->tickets }} tickets</small>
                        </div>
                        <div class="text-end">
                            <small class="fw-bold">{{ $moneda }} {{ number_format($user->total_vendido, 0) }}</small>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-person-x fs-1"></i>
                        <p class="mt-2 mb-0">Sin actividad</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card chart-card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="bi bi-receipt text-primary me-2"></i>Ventas recientes</h5>
                <a href="{{ route('ventas.index') }}" class="text-decoration-none small fw-bold">Ver historial completo</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-muted" style="font-size:.7rem; text-transform:uppercase; letter-spacing:1px;">
                            <th class="ps-4 py-3">Ticket</th>
                            <th>Cliente</th>
                            <th>Cajero</th>
                            <th class="text-end">Total</th>
                            <th>Estado</th>
                            <th>Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activity['ultimasVentas'] as $venta)
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('ventas.show', $venta) }}" class="text-decoration-none">
                                        <span class="fw-bold text-primary">#{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    </a>
                                </td>
                                <td>
                                    <span class="small fw-semibold">{{ $venta->cliente->nombre ?? 'Consumidor Final' }}</span>
                                </td>
                                <td><span class="small text-muted">{{ $venta->usuario->name ?? '—' }}</span></td>
                                <td class="text-end fw-bold">{{ $moneda }} {{ number_format($venta->total, 2) }}</td>
                                <td>
                                    @php
                                        $estadoBadge = match($venta->estado) {
                                            'completada', 'pagada' => 'success',
                                            'pendiente' => 'warning',
                                            'cuenta_abierta' => 'info',
                                            'anulada' => 'secondary',
                                            default => 'secondary'
                                        };
                                        $estadoLabel = match($venta->estado) {
                                            'completada', 'pagada' => 'Pagada',
                                            'pendiente' => 'Pendiente',
                                            'cuenta_abierta' => 'Cta. Abierta',
                                            'anulada' => 'Anulada',
                                            default => ucfirst($venta->estado)
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $estadoBadge }} bg-opacity-10 text-{{ $estadoBadge }} rounded-pill px-2 py-1">{{ $estadoLabel }}</span>
                                </td>
                                <td>
                                    <span class="small text-muted">{{ $venta->created_at->diffForHumans() }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mt-2 mb-0">Sin ventas registradas</p>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card chart-card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-bell text-warning me-2"></i>Alertas y acción</h5>
            </div>
            <div class="card-body p-4 pt-0">
                @if($alertas['stock_critico']->count() > 0)
                    <div class="alert-pill bg-warning bg-opacity-10 text-warning mb-2">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div class="flex-grow-1">
                            <strong>{{ $alertas['stock_critico']->count() }} producto(s) con stock crítico</strong>
                            <div class="small text-muted">{{ $alertas['stock_critico']->pluck('nombre')->take(2)->implode(', ') }}@if($alertas['stock_critico']->count() > 2) y más...@endif</div>
                        </div>
                        <a href="{{ route('productos.index', ['stock_status' => 'critical']) }}" class="btn btn-sm btn-warning rounded-pill px-2 py-0">Ver</a>
                    </div>
                @endif

                @if($kpis['facturasPendientes'] > 0)
                    <div class="alert-pill bg-danger bg-opacity-10 text-danger mb-2">
                        <i class="bi bi-receipt-cutoff"></i>
                        <div class="flex-grow-1">
                            <strong>{{ $kpis['facturasPendientes'] }} factura(s) pendiente(s)</strong>
                            <div class="small text-muted">Cuentas abiertas o sin pagar</div>
                        </div>
                        <a href="{{ route('clientes.cuentas') }}" class="btn btn-sm btn-danger rounded-pill px-2 py-0">Cobrar</a>
                    </div>
                @endif

                @if($alertas['ncf_por_vencer']->count() > 0)
                    <div class="alert-pill bg-info bg-opacity-10 text-info mb-2">
                        <i class="bi bi-receipt"></i>
                        <div class="flex-grow-1">
                            <strong>NCF por vencer</strong>
                            <div class="small text-muted">{{ $alertas['ncf_por_vencer']->pluck('nombre')->take(2)->implode(', ') }}</div>
                        </div>
                        <a href="{{ route('ncf.index') }}" class="btn btn-sm btn-info rounded-pill px-2 py-0">Revisar</a>
                    </div>
                @endif

                @if($alertas['productos_sin_rotacion'] > 0)
                    <div class="alert-pill bg-secondary bg-opacity-10 text-secondary mb-2">
                        <i class="bi bi-archive"></i>
                        <div class="flex-grow-1">
                            <strong>{{ $alertas['productos_sin_rotacion'] }} producto(s) sin ventas este mes</strong>
                            <div class="small text-muted">Considera promocionarlos</div>
                        </div>
                    </div>
                @endif

                @if($alertas['stock_critico']->count() === 0 && $kpis['facturasPendientes'] === 0 && $alertas['ncf_por_vencer']->count() === 0 && $alertas['productos_sin_rotacion'] === 0 && count($alertas['sistema']) === 0)
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-emoji-smile text-success fs-1"></i>
                        <p class="mt-2 mb-0">¡Todo en orden!</p>
                        <small>Sin alertas pendientes</small>
                    </div>
                @endif

                @if(count($alertas['sistema']) > 0)
                <div class="mt-3">
                    <small class="text-muted text-uppercase fw-bold"><i class="bi bi-gear me-1"></i>Sistema</small>
                    @foreach($alertas['sistema'] as $alerta)
                    <div class="alert alert-{{ $alerta['color'] }} border-0 rounded-3 py-2 px-3 mb-1 d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi {{ $alerta['icono'] }} me-2"></i>
                            <span>{{ $alerta['mensaje'] }}</span>
                        </div>
                        <a href="{{ $alerta['link'] }}" class="btn btn-sm btn-outline-{{ $alerta['color'] }} rounded-pill ms-2 flex-shrink-0">{{ $alerta['link_text'] }}</a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
