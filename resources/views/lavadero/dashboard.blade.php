@extends('layouts.app')

@section('title', 'Dashboard del Lavadero')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">
    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-droplet-fill"></i></div>
                <div>
                    <h4 class="ui-header-title">Dashboard del Lavadero</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-calendar3 me-1"></i>
                        <span>{{ now()->format('l, j \\d\\e F \\d\\e Y') }}</span>
                        <span class="divider">·</span>
                        <i class="bi bi-clock"></i>
                        <span id="dash-clock">{{ now()->format('h:i A') }}</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('pos.index') }}" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                    <i class="bi bi-cart3 me-1"></i> Abrir POS
                </a>
            </div>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg">
            <div class="ui-stat" style="--delay:.05s">
                <div class="ui-card-accent" style="background:linear-gradient(135deg,#0ea5e9,#3b82f6);"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Vehículos Hoy</div>
                    <div class="ui-stat-value" id="stat-vehiculos">0</div>
                    <div class="ui-stat-sub"><i class="bi bi-droplet"></i> Servicios</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-card-accent" style="background:linear-gradient(135deg,#22c55e,#16a34a);"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Ingresos Hoy</div>
                    <div class="ui-stat-value" id="stat-ingresos">RD$ 0</div>
                    <div class="ui-stat-sub"><i class="bi bi-cash-stack"></i> Total</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-card-accent" style="background:linear-gradient(135deg,#f59e0b,#d97706);"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Alimentos</div>
                    <div class="ui-stat-value" id="stat-alimentos">RD$ 0</div>
                    <div class="ui-stat-sub"><i class="bi bi-cup-straw"></i> F&B</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-card-accent" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Accesorios</div>
                    <div class="ui-stat-value" id="stat-accesorios">RD$ 0</div>
                    <div class="ui-stat-sub"><i class="bi bi-gear"></i> Tienda</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg">
            <div class="ui-stat" style="--delay:.25s">
                <div class="ui-card-accent" style="background:linear-gradient(135deg,#ef4444,#dc2626);"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Baches</div>
                    <div class="ui-stat-value"><span id="stat-baches-ok">0</span>/<span id="stat-baches-total">0</span></div>
                    <div class="ui-stat-sub"><i class="bi bi-grid-3x3"></i> Ocupados</div>
                </div>
            </div>
        </div>
    </div>

    {{-- LINKS RAPIDOS --}}
    <div class="ui-card mb-4">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title"><i class="bi bi-lightning-charge"></i>Accesos Rápidos</div>
        <div class="ui-card-body">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <a href="{{ route('pos.index') }}" class="d-flex flex-column align-items-center p-3 rounded-3 text-decoration-none" style="background:rgba(14,165,233,0.05);">
                        <i class="bi bi-cart3 mb-2" style="font-size:1.5rem;color:#0ea5e9;"></i>
                        <span class="fw-bold small">POS</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('lavadero.paquetes.index') }}" class="d-flex flex-column align-items-center p-3 rounded-3 text-decoration-none" style="background:rgba(245,158,11,0.05);">
                        <i class="bi bi-gift mb-2" style="font-size:1.5rem;color:#f59e0b;"></i>
                        <span class="fw-bold small">Paquetes</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('categorias.index') }}" class="d-flex flex-column align-items-center p-3 rounded-3 text-decoration-none" style="background:rgba(139,92,246,0.05);">
                        <i class="bi bi-tags mb-2" style="font-size:1.5rem;color:#8b5cf6;"></i>
                        <span class="fw-bold small">Categorías</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('productos.index') }}" class="d-flex flex-column align-items-center p-3 rounded-3 text-decoration-none" style="background:rgba(6,182,212,0.05);">
                        <i class="bi bi-box-seam mb-2" style="font-size:1.5rem;color:#06b6d4;"></i>
                        <span class="fw-bold small">Productos</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- ÚLTIMOS SERVICIOS --}}
        <div class="col-lg-6">
            <div class="ui-card">
                <div class="ui-card-accent" style="background:linear-gradient(135deg,#0ea5e9,#3b82f6);"></div>
                <div class="ui-card-title"><i class="bi bi-clock-history"></i>Últimos Servicios</div>
                <div class="ui-card-body p-0">
                    <table class="table ui-table nowrap mb-0">
                        <thead><tr><th>Cliente</th><th>Servicio</th><th>Lavador</th><th>Estado</th></tr></thead>
                        <tbody>
                            @forelse($ultimosServicios ?? [] as $svc)
                            <tr>
                                <td><div class="fw-bold small">{{ $svc->cliente ?? 'Sin cliente' }}</div><small class="text-muted">{{ $svc->placa ?? '' }}</small></td>
                                <td><small>{{ $svc->servicio ?? $svc->nombre ?? '-' }}</small></td>
                                <td><small>{{ $svc->lavador ?? '-' }}</small></td>
                                <td><span class="badge {{ $svc->estado == 'completado' ? 'bg-success' : 'bg-warning' }}">{{ $svc->estado ?? 'pendiente' }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted"><small>No hay servicios hoy</small></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ALERTAS DE INVENTARIO --}}
        <div class="col-lg-6">
            <div class="ui-card">
                <div class="ui-card-accent" style="background:linear-gradient(135deg,#ef4444,#dc2626);"></div>
                <div class="ui-card-title"><i class="bi bi-exclamation-triangle"></i>Alertas de Inventario</div>
                <div class="ui-card-body p-0">
                    <table class="table ui-table nowrap mb-0">
                        <thead><tr><th>Producto</th><th>Stock</th><th>Mínimo</th><th>Línea</th></tr></thead>
                        <tbody>
                            @forelse($alertasInventario ?? [] as $alert)
                            <tr>
                                <td><div class="fw-bold small">{{ $alert->nombre }}</div></td>
                                <td><span class="badge {{ $alert->stock <= 5 ? 'bg-danger' : 'bg-warning' }}">{{ $alert->stock }}</span></td>
                                <td>{{ $alert->stock_minimo }}</td>
                                <td><span class="badge bg-info small">{{ $alert->linea_negocio ?? 'producto' }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted"><small>Todos los stocks están bien</small></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
setInterval(() => { const el = document.getElementById('dash-clock'); if(el) el.textContent = new Date().toLocaleTimeString('es-DO', {hour:'2-digit', minute:'2-digit'}); }, 60000);
// Fetch dashboard data periodically
fetch('{{ route("lavadero.dashboard-data") }}')
    .then(r => r.json()).then(data => {
        if (data.vehiculos_hoy) document.getElementById('stat-vehiculos').textContent = data.vehiculos_hoy;
        if (data.ingresos_hoy) document.getElementById('stat-ingresos').textContent = 'RD$ ' + parseFloat(data.ingresos_hoy).toFixed(2);
        if (data.alimentos_hoy) document.getElementById('stat-alimentos').textContent = 'RD$ ' + parseFloat(data.alimentos_hoy).toFixed(2);
        if (data.accesorios_hoy) document.getElementById('stat-accesorios').textContent = 'RD$ ' + parseFloat(data.accesorios_hoy).toFixed(2);
        if (data.baches_ocupados) document.getElementById('stat-baches-ok').textContent = data.baches_ocupados;
        if (data.baches_total) document.getElementById('stat-baches-total').textContent = data.baches_total;
    }).catch(() => {});
</script>
@endsection
