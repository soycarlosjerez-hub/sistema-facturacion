@extends('layouts.app')

@section('title', $company->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
    .show-hero {
        display: flex;
        gap: 1.5rem;
        align-items: center;
        margin-bottom: 2rem;
    }
    .show-avatar {
        width: 90px; height: 90px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 2.2rem;
        flex-shrink: 0;
        background: rgba(59,130,246,.1);
        border: 2px solid rgba(59,130,246,.2);
    }
    .show-title {
        font-size: 1.6rem;
        font-weight: 800;
        margin: 0 0 .2rem;
        color: #1e293b;
    }
    .show-subtitle {
        color: #64748b;
        font-size: .9rem;
        margin: 0;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1.25rem;
    }
    .info-item {
        padding: 1rem 1.25rem;
        background: rgba(248,250,252,.6);
        border-radius: var(--radius);
        border: 1px solid #f1f5f9;
    }
    .info-label {
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #94a3b8;
        font-weight: 600;
        margin-bottom: .3rem;
    }
    .info-value {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1e293b;
        word-break: break-word;
    }
    .stat-card {
        background: rgba(255,255,255,.7);
        backdrop-filter: blur(20px);
        border-radius: var(--radius-2xl);
        border: 1px solid rgba(255,255,255,.8);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        transition: all .3s ease;
        animation: uiSlideUp .5s ease both;
        margin-bottom: 1rem;
        animation-delay: var(--delay, 0s);
    }
    .stat-card:hover {
        box-shadow: 0 12px 48px rgba(0,0,0,.1);
        transform: translateY(-2px);
    }
    .stat-card .stat-label {
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #64748b;
        font-weight: 600;
        margin-bottom: .25rem;
    }
    .stat-card .stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        margin: 0;
        color: var(--accent, #3b82f6);
    }
    .stat-card .stat-sub {
        font-size: .75rem;
        color: #94a3b8;
        margin-top: .15rem;
    }
    @media (max-width: 575.98px) {
        .show-hero { gap: 1rem; }
        .show-avatar { width: 64px; height: 64px; font-size: 1.6rem; }
        .show-title { font-size: 1.25rem; }
        .info-grid { grid-template-columns: 1fr; }
    }
    /* dark mode */
    body.dark-mode .show-avatar { background: rgba(59,130,246,.15); border-color: rgba(59,130,246,.3); }
    body.dark-mode .show-title { color: #f1f5f9; }
    body.dark-mode .show-subtitle { color: #94a3b8; }
    body.dark-mode .info-item { background: rgba(15,23,42,.5); border-color: #1e293b; }
    body.dark-mode .info-label { color: #64748b; }
    body.dark-mode .info-value { color: #cbd5e1; }
    body.dark-mode .stat-card { background: rgba(15,23,42,.8); border-color: rgba(255,255,255,.08); }
    body.dark-mode .stat-label { color: #94a3b8; }
    body.dark-mode .stat-value { color: #f8fafc; }
    body.dark-mode .stat-sub { color: #64748b; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">

    {{-- Header premium --}}
    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ $company->nombre }}</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-info-circle me-1"></i>
                        <span>Empresa de delivery</span>
                        <span class="divider">•</span>
                        <i class="bi bi-hash me-1"></i>
                        <span>{{ $company->nombre_corto }}</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('delivery-companies.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
                @can('delivery-companies.edit')
                <a href="{{ route('delivery-companies.edit', $company) }}" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- Info principal --}}
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <h6 class="ui-card-title">
                    <i class="bi bi-building"></i>Información
                </h6>
                <div class="ui-card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Empresa</div>
                            <div class="info-value">{{ $company->nombre }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Código</div>
                            <div class="info-value">
                                <span class="ui-badge ui-badge-info">{{ $company->nombre_corto }}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Comisión</div>
                            <div class="info-value">{{ $company->comision_formateada }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Estado</div>
                            <div class="info-value">
                                @if($company->activo)
                                    <span class="ui-badge ui-badge-success">
                                        <i class="bi bi-check-circle-fill me-1"></i>Activo
                                    </span>
                                @else
                                    <span class="ui-badge ui-badge-neutral">
                                        <i class="bi bi-x-circle-fill me-1"></i>Inactivo
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Registrada</div>
                            <div class="info-value">{{ $company->created_at->format('d/M/Y H:i') }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Última actualización</div>
                            <div class="info-value">{{ $company->updated_at->format('d/M/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="row g-3">
                <div class="col-6">
                    <div class="stat-card" style="--delay:.15s">
                        <div class="ui-card-body">
                            <div class="stat-label" style="color: #3b82f6;">
                                <i class="bi bi-receipt me-1"></i>Total Ventas
                            </div>
                            <div class="stat-value" style="color: #3b82f6;">{{ $totalVentas }}</div>
                            <div class="stat-sub">transacciones</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-card" style="--delay:.2s">
                        <div class="ui-card-body">
                            <div class="stat-label" style="color: #10b981;">
                                <i class="bi bi-cash-stack me-1"></i>Delivery Fees
                            </div>
                            <div class="stat-value" style="color: #10b981;">RD$ {{ number_format($totalDeliveryFees, 2) }}</div>
                            <div class="stat-sub">cobrado total</div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="stat-card" style="--delay:.25s">
                        <div class="ui-card-body">
                            <div class="stat-label" style="color: #8b5cf6;">
                                <i class="bi bi-percent me-1"></i>Comisiones estimadas ({{ $company->comision_formateada }})
                            </div>
                            <div class="stat-value" style="color: #8b5cf6;">RD$ {{ number_format($totalComisiones, 2) }}</div>
                            <div class="stat-sub">basado en delivery fees recaudados</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ventas recientes con delivery --}}
    <div class="ui-card" style="--delay:.3s">
        <div class="ui-card-accent"></div>
        <h6 class="ui-card-title">
            <i class="bi bi-truck"></i>Ventas Recientes con Delivery
        </h6>
        <div class="ui-card-body px-0">
            @if($ventasConDelivery->isNotEmpty())
            <div class="table-responsive">
                <table class="ui-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">NCF</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Delivery Fee</th>
                            <th>Comisión ({{ $company->comision_formateada }})</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ventasConDelivery as $venta)
                        <tr>
                            <td class="ps-4 fw-semibold">
                                <a href="{{ route('ventas.show', $venta) }}" class="text-decoration-none" style="color: var(--accent, #3b82f6);">
                                    {{ $venta->ncf }}
                                </a>
                            </td>
                            <td>
                                {{ $venta->cliente ? $venta->cliente->nombre : '<span class="text-muted">Sin cliente</span>' }}
                            </td>
                            <td class="fw-semibold">{{ renderMoneda($venta->total) }}</td>
                            <td>{{ renderMoneda($venta->delivery_fee ?? 0) }}</td>
                            <td class="fw-semibold" style="color: #8b5cf6;">
                                {{ renderMoneda(round(($venta->delivery_fee ?? 0) * ($company->comision_porcentaje / 100), 2)) }}
                            </td>
                            <td>{{ $venta->created_at->format('d/M/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="ui-empty-state">
                <i class="bi bi-receipt"></i>
                <p>No hay ventas con delivery</p>
                <small class="text-muted">Las ventas con delivery aparecerán aquí</small>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
