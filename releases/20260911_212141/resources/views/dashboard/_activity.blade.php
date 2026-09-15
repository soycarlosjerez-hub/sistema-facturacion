<div class="row g-3 mb-4">
    {{-- User ranking --}}
    <div class="col-xl-3">
        <div class="ui-card h-100" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body">
                <h5 class="fw-bold mb-0"><i class="bi bi-person-badge me-2" style="color:var(--accent);"></i>Ranking cajeros</h5>
                <small class="text-muted d-block mb-3">Ventas del mes</small>
                @forelse($rankingUsuarios as $i => $user)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width:32px;height:32px;font-size:.85rem;background:{{ $i === 0 ? 'linear-gradient(135deg,#f59e0b,#d97706)' : ($i === 1 ? 'linear-gradient(135deg,#94a3b8,#64748b)' : ($i === 2 ? 'linear-gradient(135deg,#b45309,#92400e)' : 'rgba(0,0,0,.05)')) }};color:{{ $i < 3 ? '#fff' : '#64748b' }};">
                            {{ $i + 1 }}
                        </div>
                        <div style="min-width:0;flex-grow:1;">
                            <span class="fw-bold small d-block text-truncate" style="max-width:110px;">{{ $user->name }}</span>
                            <small class="text-muted">{{ $user->tickets }} tickets</small>
                        </div>
                        <small class="fw-bold flex-shrink-0">{{ $moneda }} {{ number_format($user->total_vendido, 0) }}</small>
                    </div>
                @empty
                    <div class="ui-empty-state">
                        <i class="bi bi-person-x"></i>
                        <p>Sin actividad</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent sales --}}
    <div class="col-xl-5">
        <div class="ui-card h-100" style="--delay:.15s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body p-0">
                <div class="d-flex justify-content-between align-items-center p-4 pb-0">
                    <h5 class="fw-bold mb-0"><i class="bi bi-receipt me-2" style="color:var(--accent);"></i>Ventas recientes</h5>
                    <a href="{{ route('ventas.index') }}" class="small fw-bold text-decoration-none">Ver historial</a>
                </div>
                <div class="table-responsive">
                    <table class="ui-table mb-0">
                        <thead>
                            <tr>
                                <th>Ticket</th>
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
                                    <td><a href="{{ route('ventas.show', $venta) }}" class="fw-bold text-decoration-none">#{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}</a></td>
                                    <td><span class="small fw-semibold">{{ $venta->cliente->nombre ?? 'Consumidor Final' }}</span></td>
                                    <td><span class="small text-muted">{{ $venta->usuario->name ?? '—' }}</span></td>
                                    <td class="text-end fw-bold">{{ $moneda }} {{ number_format($venta->total, 2) }}</td>
                                    <td>
                                        @php
                                            $map = ['completada'=>'success','pagada'=>'success','pendiente'=>'warning','cuenta_abierta'=>'info','anulada'=>'danger'];
                                            $label = ['completada'=>'Pagada','pagada'=>'Pagada','pendiente'=>'Pendiente','cuenta_abierta'=>'Cta.Abierta','anulada'=>'Anulada'];
                                        @endphp
                                        <span class="ui-badge ui-badge-{{ $map[$venta->estado] ?? 'neutral' }}">{{ $label[$venta->estado] ?? $venta->estado }}</span>
                                    </td>
                                    <td><small class="text-muted">{{ $venta->created_at->diffForHumans() }}</small></td>
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
    </div>

    {{-- Alerts --}}
    <div class="col-xl-4">
        <div class="ui-card h-100" style="--delay:.2s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body">
                <h5 class="fw-bold mb-3"><i class="bi bi-bell me-2" style="color:var(--accent);"></i>Alertas y acción</h5>

                @if($alertas['stock_critico']->count() > 0)
                    <div class="d-flex align-items-center gap-2 p-3 rounded-3 mb-2" style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.15);">
                        <i class="bi bi-exclamation-triangle-fill text-warning flex-shrink-0"></i>
                        <div class="flex-grow-1" style="min-width:0;">
                            <strong class="small">{{ $alertas['stock_critico']->count() }} producto(s) con stock crítico</strong>
                            <div class="small text-muted text-truncate">{{ $alertas['stock_critico']->pluck('nombre')->take(2)->implode(', ') }}@if($alertas['stock_critico']->count() > 2) y más...@endif</div>
                        </div>
                        <a href="{{ route('productos.index', ['stock_status' => 'critical']) }}" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill flex-shrink-0">Ver</a>
                    </div>
                @endif

                @if($kpis['facturasPendientes'] > 0)
                    <div class="d-flex align-items-center gap-2 p-3 rounded-3 mb-2" style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.15);">
                        <i class="bi bi-receipt-cutoff text-danger flex-shrink-0"></i>
                        <div class="flex-grow-1">
                            <strong class="small">{{ $kpis['facturasPendientes'] }} factura(s) pendiente(s)</strong>
                            <div class="small text-muted">Cuentas abiertas o sin pagar</div>
                        </div>
                        <a href="{{ route('clientes.cuentas') }}" class="ui-btn ui-btn-danger ui-btn-sm rounded-pill flex-shrink-0">Cobrar</a>
                    </div>
                @endif

                @if($alertas['ncf_por_vencer']->count() > 0)
                    <div class="d-flex align-items-center gap-2 p-3 rounded-3 mb-2" style="background:rgba(14,165,233,.08);border:1px solid rgba(14,165,233,.15);">
                        <i class="bi bi-receipt text-info flex-shrink-0"></i>
                        <div class="flex-grow-1">
                            <strong class="small">NCF por vencer</strong>
                            <div class="small text-muted">{{ $alertas['ncf_por_vencer']->pluck('nombre')->take(2)->implode(', ') }}</div>
                        </div>
                        <a href="{{ route('ncf.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill flex-shrink-0">Revisar</a>
                    </div>
                @endif

                @if($alertas['productos_sin_rotacion'] > 0)
                    <div class="d-flex align-items-center gap-2 p-3 rounded-3 mb-2" style="background:rgba(100,116,139,.08);border:1px solid rgba(100,116,139,.15);">
                        <i class="bi bi-archive text-secondary flex-shrink-0"></i>
                        <div class="flex-grow-1">
                            <strong class="small">{{ $alertas['productos_sin_rotacion'] }} producto(s) sin ventas este mes</strong>
                            <div class="small text-muted">Considera promocionarlos</div>
                        </div>
                    </div>
                @endif

                @php
                    $hasAlerts = $alertas['stock_critico']->count() > 0
                        || $kpis['facturasPendientes'] > 0
                        || $alertas['ncf_por_vencer']->count() > 0
                        || $alertas['productos_sin_rotacion'] > 0
                        || count($alertas['sistema']) > 0;
                @endphp

                @if(!$hasAlerts)
                    <div class="ui-empty-state">
                        <i class="bi bi-emoji-smile" style="color:#10b981;"></i>
                        <p>¡Todo en orden!</p>
                        <small class="text-muted">Sin alertas pendientes</small>
                    </div>
                @endif

                @if(count($alertas['sistema']) > 0)
                    <div class="mt-3">
                        <small class="text-muted text-uppercase fw-bold small"><i class="bi bi-gear me-1"></i>Sistema</small>
                        @foreach($alertas['sistema'] as $alerta)
                            <div class="d-flex justify-content-between align-items-center gap-2 p-2 rounded-2 mb-1 small" style="background:rgba(0,0,0,.03);">
                                <div class="d-flex align-items-center gap-2" style="min-width:0;">
                                    <i class="bi {{ $alerta['icono'] }} flex-shrink-0"></i>
                                    <span class="text-truncate">{{ $alerta['mensaje'] }}</span>
                                </div>
                                <a href="{{ $alerta['link'] }}" class="text-decoration-none fw-bold flex-shrink-0 text-nowrap">{{ $alerta['link_text'] }}</a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>