@extends('layouts.app')

@section('title', 'Resumen de Créditos')

@push('styles')
@include('partials.premium-ui')
<style>
.credito-stat-card {
    border-radius: 1rem;
    padding: 1.25rem;
    background: white;
    border: 1px solid #e2e8f0;
    transition: all .2s;
}
.credito-stat-card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,.08);
    transform: translateY(-2px);
}
body.dark-mode .credito-stat-card {
    background: rgba(15,23,42,.6);
    border-color: #334155;
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#6366f1;--accent-rgb:99,102,241;--accent-hover:#4f46e5;">

    <div class="ui-header mb-4" style="background:linear-gradient(135deg,#6366f1,#8b5cf6,#a855f7,#6366f1);box-shadow:0 8px 32px rgba(99,102,241,.25);">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle" style="background:rgba(255,255,255,.2);border-color:rgba(255,255,255,.35);">
                    <i class="bi bi-credit-card-2-front"></i>
                </div>
                <div>
                    <div class="ui-header-title">Resumen de Créditos</div>
                    <div class="ui-header-meta">
                        <i class="bi bi-graph-up-arrow me-1"></i>
                        Panorama general de exposición crediticia
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('clientes.cuentas') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-cash-coin me-1"></i>Cuentas por Cobrar
                </a>
                <a href="{{ route('clientes.index') }}" class="btn btn-outline-light rounded-pill px-3 small">
                    <i class="bi bi-arrow-left me-1"></i>Clientes
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="credito-stat-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Total Créditos</span>
                    <i class="bi bi-credit-card fs-4" style="color:#6366f1;"></i>
                </div>
                <div class="fs-4 fw-bold" style="color:#1e293b;">RD$ {{ number_format($resumen['totalLimite'], 2) }}</div>
                <small class="text-muted">{{ $resumen['totalClientes'] }} cliente(s) registrados</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="credito-stat-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Balance Pendiente</span>
                    <i class="bi bi-cash-stack fs-4" style="color:#f59e0b;"></i>
                </div>
                <div class="fs-4 fw-bold" style="color:#f59e0b;">RD$ {{ number_format($resumen['totalBalance'], 2) }}</div>
                <small class="text-muted">{{ $resumen['conDeuda'] }} cliente(s) con deuda</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="credito-stat-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Utilización Promedio</span>
                    <i class="bi bi-pie-chart fs-4" style="color:#10b981;"></i>
                </div>
                <div class="fs-4 fw-bold" style="color:#10b981;">{{ $resumen['utilizacionPromedio'] }}%</div>
                <div class="progress mt-2" style="height:6px;">
                    <div class="progress-bar bg-success" style="width:{{ min($resumen['utilizacionPromedio'], 100) }}%;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="credito-stat-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-bold text-uppercase">En Exceso</span>
                    <i class="bi bi-exclamation-triangle fs-4 text-danger"></i>
                </div>
                <div class="fs-4 fw-bold text-danger">{{ $resumen['enExceso'] }} cliente(s)</div>
                <small class="text-muted">Sobrepasan su límite de crédito</small>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s;">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title">
            <i class="bi bi-exclamation-triangle icon-purple"></i>
            Clientes que Exceden su Límite de Crédito
            <span class="badge bg-danger ms-auto rounded-pill">{{ $clientesEnExceso->count() }}</span>
        </div>
        <div class="card-body p-0">
            @if($clientesEnExceso->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="small text-uppercase text-muted">
                        <tr>
                            <th class="ps-4">Cliente</th>
                            <th class="text-end">Límite</th>
                            <th class="text-end">Balance</th>
                            <th class="text-end">Exceso</th>
                            <th class="text-center">Utilización</th>
                            <th class="text-end pe-4">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientesEnExceso as $c)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $c->nombre }}</td>
                            <td class="text-end">RD$ {{ number_format($c->limite_credito, 2) }}</td>
                            <td class="text-end text-danger fw-bold">RD$ {{ number_format($c->balance_pendiente, 2) }}</td>
                            <td class="text-end text-danger fw-bold">RD$ {{ number_format($c->exceso_credito, 2) }}</td>
                            <td class="text-center">
                                <span class="badge bg-danger rounded-pill">{{ $c->utilizacion_credito }}%</span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('clientes.show', $c) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('clientes.edit', $c) }}" class="btn btn-sm btn-outline-warning rounded-pill">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-check-circle fs-1 text-success"></i>
                <p class="mt-2 fw-semibold">Todos los clientes están dentro de su límite de crédito</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection