@extends('layouts.app')

@section('title', 'Mis Entregas')

@push('styles')
@include('partials.premium-ui')
<style>
/* Mis Entregas — Mobile-first driver view */
.orden-card {
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 16px;
    transition: all 0.25s ease;
    overflow: hidden;
    background: #fff;
}
body.dark-mode .orden-card {
    border-color: rgba(255,255,255,0.08);
    background: rgba(15, 23, 42, 0.6);
}
.orden-card:hover {
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}
.orden-card-accent {
    height: 4px;
    border-radius: 16px 16px 0 0;
}
.orden-card-accent.creado { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.orden-card-accent.en_camino { background: linear-gradient(90deg, #0ea5e9, #38bdf8); }
.orden-card-accent.entregado { background: linear-gradient(90deg, #16a34a, #4ade80); }
.orden-card-accent.fallido { background: linear-gradient(90deg, #dc2626, #f87171); }
.status-badge {
    display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px;
    border-radius: 999px; font-size: 0.7rem; font-weight: 600; white-space: nowrap;
}
.status-badge.creado { background: rgba(245,158,11,.12); color: #d97706; }
.status-badge.en_camino { background: rgba(14,165,233,.12); color: #0284c7; }
.status-badge.entregado { background: rgba(22,163,74,.12); color: #15803d; }
.status-badge.fallido { background: rgba(239,68,68,.12); color: #dc2626; }
.stat-card {
    border-radius: 16px; padding: 16px; text-align: center;
    background: rgba(255,255,255,.8); border: 1px solid rgba(0,0,0,0.06);
    transition: all 0.2s ease;
}
body.dark-mode .stat-card {
    background: rgba(15, 23, 42, 0.5);
    border-color: rgba(255,255,255,0.06);
}
.stat-value {
    font-size: 1.6rem; font-weight: 800; line-height: 1.2;
}
.stat-label {
    font-size: 0.65rem; color: #64748b; text-transform: uppercase;
    letter-spacing: 0.05em; font-weight: 600;
}
.stat-card.accent { background: linear-gradient(135deg, #0ea5e9, #0284c7); color: #fff; border: none; }
.stat-card.accent .stat-label { color: rgba(255,255,255,.8); }
.stat-card.warning .stat-value { color: #d97706; }
.stat-card.info .stat-value { color: #0284c7; }
.stat-card.success .stat-value { color: #15803d; }
.stat-card.danger .stat-value { color: #dc2626; }
.order-info-row {
    display: flex; align-items: flex-start; gap: 8px;
    padding: 6px 0; font-size: 0.82rem;
}
.order-info-row:not(:last-child) {
    border-bottom: 1px solid rgba(0,0,0,0.04);
}
body.dark-mode .order-info-row:not(:last-child) {
    border-bottom-color: rgba(255,255,255,0.04);
}
.order-info-label {
    font-weight: 600; color: #64748b; min-width: 70px; white-space: nowrap;
}
.order-info-value {
    color: #1e293b; font-weight: 500;
}
body.dark-mode .order-info-value { color: #e2e8f0; }
body.dark-mode .order-info-label { color: #94a3b8; }
.action-btn-driver {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 10px 18px; border-radius: 12px; font-size: 0.8rem; font-weight: 700;
    border: none; cursor: pointer; transition: all 0.2s ease; text-decoration: none;
    width: 100%;
}
.action-btn-go {
    background: linear-gradient(135deg, #0ea5e9, #0284c7); color: #fff;
    box-shadow: 0 4px 14px rgba(14,165,233,.3);
}
.action-btn-go:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(14,165,233,.4); color: #fff; }
.action-btn-confirm {
    background: linear-gradient(135deg, #16a34a, #15803d); color: #fff;
    box-shadow: 0 4px 14px rgba(22,163,74,.3);
}
.action-btn-confirm:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(22,163,74,.4); color: #fff; }
.action-btn-fail {
    background: rgba(239,68,68,.1); color: #dc2626; border: 1px solid rgba(239,68,68,.2);
}
.action-btn-fail:hover { background: #dc2626; color: #fff; }
.empty-state {
    text-align: center; padding: 40px 20px; color: #94a3b8;
}
.empty-state i { font-size: 2.5rem; margin-bottom: 12px; display: block; opacity: 0.4; }
.empty-state p { margin: 0; font-size: 0.9rem; }
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
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Mis Entregas</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-geo-alt me-1"></i>
                        <span>{{ now()->translatedFormat('l, d \\d\\e F \\d\\e Y') }}</span>
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

    @if(session('error'))
    <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
    </div>
    @endif

    {{-- Resumen --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card stat-card-warning">
                <div class="stat-value">{{ $pendientes->count() }}</div>
                <div class="stat-label">Pendientes</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-card-info">
                <div class="stat-value">{{ $enCamino->count() }}</div>
                <div class="stat-label">En Camino</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-card-success">
                <div class="stat-value">{{ $entregadasHoy }}</div>
                <div class="stat-label">Entregas Hoy</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-card">
                <div class="stat-value">{{ $ventas->count() }}</div>
                <div class="stat-label">Total Asignadas</div>
            </div>
        </div>
    </div>

    {{-- Pendientes --}}
    @if($pendientes->count())
    <div class="mb-4">
        <h6 class="fw-bold mb-3" style="color:#d97706;">
            <i class="bi bi-clock-history me-2"></i>Pendientes ({{ $pendientes->count() }})
        </h6>
        @foreach($pendientes as $venta)
        <div class="orden-card mb-3" style="--delay:.{{ $loop->index }}s">
            <div class="orden-card-accent creado"></div>
            <div class="p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="fw-bold" style="font-size:.95rem;">Venta #{{ $venta->numero_venta ?? $venta->id }}</span>
                        @if($venta->numero_ncf)
                        <span class="ms-2" style="font-size:.7rem;color:#64748b;">NCF: {{ $venta->numero_ncf }}</span>
                        @endif
                    </div>
                    <span class="status-badge creado">
                        <i class="bi bi-clock"></i> Pendiente
                    </span>
                </div>
                <div class="mb-2">
                    <div class="order-info-row">
                        <span class="order-info-label"><i class="bi bi-person me-1"></i>Cliente</span>
                        <span class="order-info-value">{{ $venta->cliente->nombre ?? 'Sin cliente' }}</span>
                    </div>
                    @if($venta->cliente && $venta->cliente->telefono)
                    <div class="order-info-row">
                        <span class="order-info-label"><i class="bi bi-telephone me-1"></i>Tel</span>
                        <span class="order-info-value">{{ $venta->cliente->telefono }}</span>
                    </div>
                    @endif
                    <div class="order-info-row">
                        <span class="order-info-label"><i class="bi bi-cash me-1"></i>Total</span>
                        <span class="order-info-value">${{ number_format($venta->total, 2) }}</span>
                    </div>
                    <div class="order-info-row">
                        <span class="order-info-label"><i class="bi bi-geo-alt me-1"></i>Dirección</span>
                        <span class="order-info-value">{{ $venta->direccion_entrega ?? 'N/A' }}</span>
                    </div>
                    <div class="order-info-row">
                        <span class="order-info-label"><i class="bi bi-clock me-1"></i>Hora</span>
                        <span class="order-info-value">{{ $venta->created_at->format('h:i A') }}</span>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <form action="{{ route('delivery-mis-entregas.updateStatus', $venta->deliveryTracking) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="en_camino">
                        <button type="submit" class="action-btn-driver action-btn-go">
                            <i class="bi bi-truck me-2"></i>Salir a Entregar
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- En Camino --}}
    @if($enCamino->count())
    <div class="mb-4">
        <h6 class="fw-bold mb-3" style="color:#0284c7;">
            <i class="bi bi-truck me-2"></i>En Camino ({{ $enCamino->count() }})
        </h6>
        @foreach($enCamino as $venta)
        <div class="orden-card mb-3" style="--delay:.{{ $loop->index }}s">
            <div class="orden-card-accent en_camino"></div>
            <div class="p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="fw-bold" style="font-size:.95rem;">Venta #{{ $venta->numero_venta ?? $venta->id }}</span>
                        @if($venta->numero_ncf)
                        <span class="ms-2" style="font-size:.7rem;color:#64748b;">NCF: {{ $venta->numero_ncf }}</span>
                        @endif
                    </div>
                    <span class="status-badge en_camino">
                        <i class="bi bi-truck"></i> En Camino
                    </span>
                </div>
                <div class="mb-2">
                    <div class="order-info-row">
                        <span class="order-info-label"><i class="bi bi-person me-1"></i>Cliente</span>
                        <span class="order-info-value">{{ $venta->cliente->nombre ?? 'Sin cliente' }}</span>
                    </div>
                    @if($venta->cliente && $venta->cliente->telefono)
                    <div class="order-info-row">
                        <span class="order-info-label"><i class="bi bi-telephone me-1"></i>Tel</span>
                        <span class="order-info-value">{{ $venta->cliente->telefono }}</span>
                    </div>
                    @endif
                    <div class="order-info-row">
                        <span class="order-info-label"><i class="bi bi-cash me-1"></i>Total</span>
                        <span class="order-info-value">${{ number_format($venta->total, 2) }}</span>
                    </div>
                    <div class="order-info-row">
                        <span class="order-info-label"><i class="bi bi-geo-alt me-1"></i>Dirección</span>
                        <span class="order-info-value">{{ $venta->direccion_entrega ?? 'N/A' }}</span>
                    </div>
                    <div class="order-info-row">
                        <span class="order-info-label"><i class="bi bi-clock me-1"></i>Hora</span>
                        <span class="order-info-value">{{ $venta->created_at->format('h:i A') }}</span>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <form action="{{ route('delivery-mis-entregas.updateStatus', $venta->deliveryTracking) }}" method="POST" class="d-flex gap-2" onsubmit="return UI.confirm.delete('¿Confirmar entrega completada?')">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="entregado">
                        <button type="submit" class="action-btn-driver action-btn-confirm flex-grow-1">
                            <i class="bi bi-check-all me-1"></i>Entregar
                        </button>
                    </form>
                    <form action="{{ route('delivery-mis-entregas.updateStatus', $venta->deliveryTracking) }}" method="POST" onsubmit="return UI.confirm.delete('¿Marcar como fallida?')">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="fallido">
                        <button type="submit" class="action-btn-driver action-btn-fail">
                            <i class="bi bi-x-circle me-1"></i>Fallido
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Empty State --}}
    @if(! $pendientes->count() && ! $enCamino->count())
    <div class="empty-state">
        <i class="bi bi-truck"></i>
        <p class="fw-bold mb-1" style="color:#475569;">No tienes entregas pendientes</p>
        <p style="font-size:.8rem;">Todas tus órdenes asignadas han sido completadas.</p>
    </div>
    @endif
</div>
@endsection
