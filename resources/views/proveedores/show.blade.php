@extends('layouts.app')

@section('title', $proveedore->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
.info-item {
    background: #f8fafc;
    border-radius: 0.75rem;
    padding: 1rem;
    border-left: 3px solid #3b82f6;
}
.info-item .label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    font-weight: 700;
    margin-bottom: 4px;
}
.info-item .value {
    font-weight: 600;
    color: #1e293b;
}
.compras-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(59,130,246,.04);
    margin: 0;
}
.compras-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.compras-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
body.dark-mode .info-item {
    background: rgba(30,41,59,.8);
}
body.dark-mode .info-item .label { color: #94a3b8; }
body.dark-mode .info-item .value { color: #f1f5f9; }
body.dark-mode .compras-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .compras-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">

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
                    <div class="ui-header-title">{{ $proveedore->nombre }}</div>
                    <div class="ui-header-meta">
                        <i class="bi bi-building me-1"></i>
                        Detalle del proveedor
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('proveedores.edit', $proveedore) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-pencil-square me-1"></i>Editar
                </a>
                <a href="{{ route('proveedores.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="ui-card" style="--delay:.1s;">
                <div class="ui-card-accent"></div>
                <div class="card-body p-4 text-center">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-truck fs-1"></i>
                    </div>
                    <h4 class="fw-bold">{{ $proveedore->nombre }}</h4>
                    <p class="text-muted small mb-1"><i class="bi bi-geo-alt me-1"></i>{{ $proveedore->direccion ?? 'Sin dirección' }}</p>
                    <p class="text-muted small mb-1"><i class="bi bi-envelope me-1"></i>{{ $proveedore->email ?? '—' }}</p>
                    <p class="text-muted small mb-3"><i class="bi bi-telephone me-1"></i>{{ $proveedore->telefono ?? '—' }}</p>
                    @if($proveedore->activo)
                    <span class="ui-badge ui-badge-success"><i class="bi bi-check-circle-fill me-1"></i>Activo</span>
                    @else
                    <span class="ui-badge ui-badge-neutral"><i class="bi bi-x-circle-fill me-1"></i>Inactivo</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="ui-card mb-4" style="--delay:.15s;">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-info-circle icon-blue"></i>
                    Información Fiscal
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="label">RNC</div>
                                <div class="value">{{ $proveedore->rnc ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item" style="border-left-color: #8b5cf6;">
                                <div class="label">Tipo de Persona</div>
                                <div class="value">{{ $proveedore->tipo_persona === 'juridica' ? 'Jurídica' : ($proveedore->tipo_persona === 'fisica' ? 'Física' : '—') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item" style="border-left-color: #f59e0b;">
                                <div class="label">Sujeto a Retención ISR</div>
                                <div class="value">
                                    @if($proveedore->sujeto_retencion_isr)
                                        <span class="ui-badge ui-badge-success">Sí</span>
                                    @else
                                        <span class="ui-badge ui-badge-neutral">No</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item" style="border-left-color: #f59e0b;">
                                <div class="label">Sujeto a Retención ITBIS</div>
                                <div class="value">
                                    @if($proveedore->sujeto_retencion_itbis)
                                        <span class="ui-badge ui-badge-success">Sí</span>
                                    @else
                                        <span class="ui-badge ui-badge-neutral">No</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ui-card" style="--delay:.2s;">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-cart-check icon-blue"></i>
                    Compras Registradas
                </div>
                <div class="card-body p-0">
                    @if($proveedore->compras->count())
                        <div class="table-responsive">
                            <table class="table compras-table">
                                <thead>
                                    <tr>
                                        <th class="ps-4">#</th>
                                        <th>Fecha</th>
                                        <th class="text-end pe-4">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($proveedore->compras->take(10) as $c)
                                        <tr>
                                            <td class="ps-4">{{ $c->id }}</td>
                                            <td>{{ $c->created_at->format('d/m/Y') }}</td>
                                            <td class="text-end pe-4 fw-bold">RD$ {{ number_format($c->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-cart-check fs-1" style="color:#cbd5e1;"></i>
                            <p class="mt-2 mb-0 fw-semibold">No hay compras registradas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
