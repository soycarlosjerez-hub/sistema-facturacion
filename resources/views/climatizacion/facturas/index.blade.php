@extends('layouts.app')

@section('title', 'Facturación Climatización')

@push('styles')
@include('partials.premium-ui')
<style>
    .stat-card {
        background: rgba(255,255,255,.7);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,.5);
        border-radius: 12px;
        padding: 1.25rem;
        transition: transform .2s;
    }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-card .stat-value { font-size: 1.5rem; font-weight: 700; color: var(--accent); }
    .stat-card .stat-label { font-size: .75rem; text-transform: uppercase; color: #64748b; font-weight: 600; }
    body.dark-mode .stat-card { background: rgba(30,41,59,.7); border-color: rgba(71,85,105,.3); }
    body.dark-mode .stat-card .stat-label { color: #94a3b8; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">
    {{-- HEADER --}}
    <div class="ui-header">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-receipt"></i></div>
                <div>
                    <h1 class="ui-header-title">Facturación Climatización</h1>
                    <div class="ui-header-meta">
                        <span>Gestión de facturas del módulo climatización</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS --}}
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Total Vendido (Mes)</div>
                <div class="stat-value">RD$ {{ number_format($stats['total_mes'], 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Borradores</div>
                <div class="stat-value">{{ $stats['pendientes'] }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Generadas</div>
                <div class="stat-value">{{ $stats['generadas'] }}</div>
            </div>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="ui-card mb-3">
        <div class="ui-card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label small mb-1">Origen</label>
                    <select name="origen" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach(\App\Models\ClimatizacionFactura::ORIGENES as $k => $v)
                            <option value="{{ $k }}" {{ request('origen') == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-1">Estado</label>
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach(\App\Models\ClimatizacionFactura::ESTADOS as $k => $v)
                            <option value="{{ $k }}" {{ request('estado') == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-1">Cliente</label>
                    <input type="text" name="cliente" class="form-control form-control-sm" placeholder="Buscar cliente..." value="{{ request('cliente') }}">
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-1">Desde</label>
                    <input type="date" name="desde" class="form-control form-control-sm" value="{{ request('desde') }}">
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-1">Hasta</label>
                    <input type="date" name="hasta" class="form-control form-control-sm" value="{{ request('hasta') }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i> Filtrar</button>
                    <a href="{{ route('climatizacion.facturas.index') }}" class="btn btn-sm btn-outline-secondary ms-1"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="ui-card">
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Origen</th>
                            <th>Referencia</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">ITBIS</th>
                            <th class="text-end">Total</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($facturas as $factura)
                        <tr>
                            <td class="fw-medium">{{ $factura->id }}</td>
                            <td>
                                @if ($factura->cliente)
                                    <a href="{{ route('clientes.show', $factura->cliente) }}" class="text-decoration-none" style="color:var(--accent);">
                                        {{ $factura->cliente->nombre }}
                                    </a>
                                @else
                                    <span class="text-muted">Consumidor Final</span>
                                @endif
                            </td>
                            <td>
                                <span class="ui-badge ui-badge-{{ match($factura->origen) {
                                    'mantenimiento' => 'info',
                                    'contrato_cuota' => 'success',
                                    'instalacion' => 'warning',
                                    'emergencia' => 'danger',
                                    default => 'secondary'
                                } }}">
                                    {{ \App\Models\ClimatizacionFactura::ORIGENES[$factura->origen] ?? $factura->origen }}
                                </span>
                            </td>
                            <td class="small">{{ $factura->referencia ?? '-' }}</td>
                            <td class="text-end">RD$ {{ number_format($factura->subtotal, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($factura->itbis, 2) }}</td>
                            <td class="text-end fw-bold" style="color:var(--accent);">RD$ {{ number_format($factura->total, 2) }}</td>
                            <td>
                                <span class="ui-badge ui-badge-{{ match($factura->estado) {
                                    'borrador' => 'secondary',
                                    'generada' => 'info',
                                    'enviada' => 'success',
                                    'anulada' => 'danger',
                                    default => 'secondary'
                                } }}">
                                    {{ \App\Models\ClimatizacionFactura::ESTADOS[$factura->estado] ?? $factura->estado }}
                                </span>
                            </td>
                            <td class="small">{{ optional($factura->created_at)->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('climatizacion.facturas.show', $factura) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                No hay facturas que coincidan con los filtros.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-3">
        {{ $facturas->links() }}
    </div>
</div>
@endsection
