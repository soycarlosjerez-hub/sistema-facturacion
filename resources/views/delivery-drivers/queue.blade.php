@extends('layouts.app')

@section('title', 'Cola de Repartidores')

@push('styles')
@include('partials.premium-ui')
<style>
/* Drivers Queue Styles */
.driver-card {
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 16px;
    transition: all 0.25s ease;
    overflow: hidden;
}
body.dark-mode .driver-card {
    border-color: rgba(255,255,255,0.08);
    background: rgba(15, 23, 42, 0.6);
}
.driver-card:hover {
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}
body.dark-mode .driver-card:hover {
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
}
.driver-avatar-lg {
    width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; color: #fff; flex-shrink: 0;
}
.status-badge {
    display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 999px; font-size: 0.7rem; font-weight: 600; white-space: nowrap;
}
.status-badge.creado { background: rgba(245,158,11,.12); color: #d97706; }
.status-badge.en_camino { background: rgba(14,165,233,.12); color: #0284c7; }
.status-badge.entregado { background: rgba(22,163,74,.12); color: #15803d; }
.orden-list {
    list-style: none; padding: 0; margin: 0;
}
.orden-item {
    padding: 10px 12px; border-radius: 8px; margin-bottom: 6px;
    background: rgba(248,250,252,.6); transition: all 0.15s ease; border: 1px solid transparent;
}
body.dark-mode .orden-item { background: rgba(30,41,59,.6); }
.orden-item:hover { border-color: rgba(14,165,233,.2); }
.orden-item:last-child { margin-bottom: 0; }
.status-count {
    display: inline-flex; align-items: center; justify-content: center; min-width: 20px; height: 20px; border-radius: 999px; font-size: 0.65rem; font-weight: 700; padding: 0 6px;
}
.status-count.pendientes { background: rgba(245,158,11,.15); color: #d97706; }
.status-count.en-camino { background: rgba(14,165,233,.15); color: #0284c7; }
.status-count.entregadas { background: rgba(22,163,74,.15); color: #15803d; }
.action-btn {
    display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; border: 1px solid transparent; transition: all 0.15s ease; cursor: pointer; font-size: 0.8rem; text-decoration: none;
}
.action-btn-assign { background: rgba(22,163,74,.1); color: #15803d; border-color: rgba(22,163,74,.2); }
.action-btn-assign:hover { background: #16a34a; color: #fff; }
.action-btn-go { background: rgba(14,165,233,.1); color: #0284c7; border-color: rgba(14,165,233,.2); }
.action-btn-go:hover { background: #0ea5e9; color: #fff; }
.action-btn-cancel { background: rgba(239,68,68,.1); color: #dc2626; border-color: rgba(239,68,68,.2); }
.action-btn-cancel:hover { background: #dc2626; color: #fff; }
.driver-stats {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;
}
.driver-stat {
    text-align: center; padding: 8px; border-radius: 8px; background: rgba(248,250,252,.8);
}
body.dark-mode .driver-stat { background: rgba(30,41,59,.8); }
.driver-stat-value { font-size: 1.1rem; font-weight: 800; display: block; line-height: 1.2; }
.driver-stat-label { font-size: 0.6rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }
.no-deliveries {
    text-align: center; padding: 20px 12px; color: #94a3b8; font-size: 0.8rem;
}
.no-deliveries i { font-size: 1.5rem; margin-bottom: 4px; display: block; opacity: 0.5; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#0ea5e9;--accent-rgb:14,165,233;--accent-hover:#0284c7;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-truck-flatbed"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Cola de Repartidores</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-check me-1"></i>
                        <span>Entregas activas por repartidor</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
    @endif

    @php $totalActivas = $drivers->sum('totalActivas'); @endphp

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent" style="background:#f59e0b"></div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="driver-stat" style="background:rgba(245,158,11,.08)">
                            <span class="driver-stat-value text-warning">{{ $drivers->where('totalActivas', '>', 0)->count() }}</span>
                            <span class="driver-stat-label">Con entregas</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent" style="background:#d97706"></div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="driver-stat" style="background:rgba(217,119,6,.08)">
                            <span class="driver-stat-value" style="color:#d97706">{{ $drivers->sum(function($d) { return $d->pendientes->count(); }) }}</span>
                            <span class="driver-stat-label">Pendientes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="background:#0284c7"></div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="driver-stat" style="background:rgba(2,132,199,.08)">
                            <span class="driver-stat-value" style="color:#0284c7">{{ $drivers->sum(function($d) { return $d->enCamino->count(); }) }}</span>
                            <span class="driver-stat-label">En Camino</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-card" style="--delay:.25s">
                <div class="ui-card-accent" style="background:#15803d"></div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="driver-stat" style="background:rgba(22,163,74,.08)">
                            <span class="driver-stat-value" style="color:#15803d">{{ $drivers->sum(function($d) { return $d->entregadasHoy; }) }}</span>
                            <span class="driver-stat-label">Entregadas Hoy</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Drivers Grid --}}
    <div class="row g-4">
        @forelse($drivers as $driver)
        <div class="col-12 col-lg-6 col-xl-4">
            <div class="driver-card">
                {{-- Driver Header --}}
                <div class="p-3 border-bottom" style="border-color:rgba(0,0,0,.06) !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="driver-avatar-lg" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
                            {{ strtoupper(substr($driver->nombre, 0, 1) . substr($driver->apellido, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-0">{{ $driver->nombre }} {{ $driver->apellido }}</h6>
                            @if($driver->telefono)
                            <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $driver->telefono }}</small>
                            @endif
                        </div>
                        @if($driver->totalActivas > 0)
                        <span class="badge bg-primary rounded-pill">{{ $driver->totalActivas }}</span>
                        @else
                        <span class="badge bg-secondary rounded-pill">Libre</span>
                        @endif
                    </div>
                </div>

                {{-- Driver Stats --}}
                <div class="p-2 border-bottom" style="border-color:rgba(0,0,0,.04) !important;">
                    <div class="driver-stats">
                        <div class="driver-stat">
                            <span class="driver-stat-value" style="color:#d97706">{{ $driver->pendientes->count() }}</span>
                            <span class="driver-stat-label">Pendientes</span>
                        </div>
                        <div class="driver-stat">
                            <span class="driver-stat-value" style="color:#0284c7">{{ $driver->enCamino->count() }}</span>
                            <span class="driver-stat-label">En Camino</span>
                        </div>
                        <div class="driver-stat">
                            <span class="driver-stat-value" style="color:#15803d">{{ $driver->entregadasHoy }}</span>
                            <span class="driver-stat-label">Hoy</span>
                        </div>
                    </div>
                </div>

                {{-- Pendientes List --}}
                @if($driver->pendientes->count() > 0)
                <div class="p-3" style="background:rgba(245,158,11,.04);">
                    <h7 class="fw-bold mb-2" style="color:#d97706; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em;">
                        <i class="bi bi-clock me-1"></i>Pendientes ({{ $driver->pendientes->count() }})
                    </h7>
                    <ul class="orden-list">
                        @foreach($driver->pendientes as $p)
                        <li class="orden-item">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="fw-bold" style="font-size:0.8rem">#{{ $p->orden_id }}</span>
                                        <span class="status-badge creado"><i class="bi bi-circle-fill" style="font-size:0.4rem"></i> Creado</span>
                                    </div>
                                    <div class="small text-muted mb-1">
                                        <i class="bi bi-person me-1"></i>{{ $p->orden->cliente->nombre ?? 'Consumidor Final' }}
                                    </div>
                                    <div class="fw-bold" style="font-size:0.82rem">
                                        {{ $systemMoneda ?? 'RD$' }} {{ number_format($p->orden->total, 2) }}
                                    </div>
                                    <div class="small text-muted" style="font-size:0.72rem">
                                        @if($p->orden->direccion_entrega)
                                        <i class="bi bi-geo-alt me-1"></i>{{ Str::limit($p->orden->direccion_entrega, 40) }}
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-1">
                                    <a href="{{ route('delivery-tracking.show', $p) }}" class="action-btn action-btn-go" title="Ver tracking" data-bs-toggle="tooltip">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="action-btn action-btn-cancel btn-sm" title="Liberar driver" onclick="liberarDriver({{ $p->orden_id }}, {{ $driver->id }})">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- En Camino List --}}
                @if($driver->enCamino->count() > 0)
                <div class="p-3" style="background:rgba(14,165,233,.04);">
                    <h7 class="fw-bold mb-2" style="color:#0284c7; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em;">
                        <i class="bi bi-truck me-1"></i>En Camino ({{ $driver->enCamino->count() }})
                    </h7>
                    <ul class="orden-list">
                        @foreach($driver->enCamino as $e)
                        <li class="orden-item">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="fw-bold" style="font-size:0.8rem">#{{ $e->orden_id }}</span>
                                        <span class="status-badge en_camino"><i class="bi bi-circle-fill" style="font-size:0.4rem"></i> En Camino</span>
                                    </div>
                                    <div class="small text-muted mb-1">
                                        <i class="bi bi-person me-1"></i>{{ $e->orden->cliente->nombre ?? 'Consumidor Final' }}
                                    </div>
                                    <div class="fw-bold" style="font-size:0.82rem">
                                        {{ $systemMoneda ?? 'RD$' }} {{ number_format($e->orden->total, 2) }}
                                    </div>
                                    <div class="small text-muted" style="font-size:0.72rem">
                                        @if($e->orden->direccion_entrega)
                                        <i class="bi bi-geo-alt me-1"></i>{{ Str::limit($e->orden->direccion_entrega, 40) }}
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-1">
                                    <a href="{{ route('delivery-tracking.show', $e) }}" class="action-btn action-btn-go" title="Ver tracking" data-bs-toggle="tooltip">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form method="POST" action="{{ route('delivery-tracking.updateStatus', [$e, 'entregado']) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="action-btn action-btn-assign" title="Confirmar entrega" data-bs-toggle="tooltip">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- No Deliveries --}}
                @if($driver->totalActivas === 0 && $driver->entregadasHoy === 0)
                <div class="no-deliveries p-4">
                    <i class="bi bi-check2-circle"></i>
                    Sin entregas hoy
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="ui-card">
                <div class="card-body p-5 text-center text-muted">
                    <i class="bi bi-truck fs-1 d-block mb-3"></i>
                    <p class="fw-semibold">No hay repartidores activos</p>
                    <a href="{{ route('delivery-drivers.create') }}" class="btn btn-sm btn-primary rounded-pill">
                        <i class="bi bi-plus-lg me-1"></i>Agregar Repartidor
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
const driverToken = '{{ csrf_token() }}';

function liberarDriver(ordenId, driverId) {
    if (!confirm('¿Liberar este repartidor de la orden #' + ordenId + '?')) return;

    fetch(`/orden/${ordenId}/liberar-driver`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': driverToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error al liberar driver');
        }
    })
    .catch(err => {
        alert('Error: ' + err.message);
    });
}
</script>
@endpush
