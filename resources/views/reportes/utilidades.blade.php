@extends('layouts.app')
@section('title', 'Utilidades / Rentabilidad')

@push('styles')
@include('partials.premium-ui')
<style>
.filter-card > .ui-card-accent {
    height: 5px;
    border-radius: 1.2rem 1.2rem 0 0;
}
.filter-card .ui-input:focus {
    border-color: #7c3aed !important;
    box-shadow: 0 0 0 3px rgba(139,92,246,.15) !important;
}
.filter-card .ui-btn-solid {
    background: linear-gradient(135deg, #7c3aed, #8b5cf6) !important;
    border: none !important;
}
.filter-card .ui-btn-solid:hover {
    background: linear-gradient(135deg, #6d28d9, #7c3aed) !important;
    box-shadow: 0 6px 20px rgba(139,92,246,.4) !important;
}
@media (max-width: 575.98px) {
    .filter-card .ui-input {
        min-width: 100%;
    }
}
#utilidadesTable thead th {
    border-bottom: 2px solid #e2e8f0;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #64748b;
    padding: 14px 12px;
    background: #f8fafc;
}
#utilidadesTable tbody td {
    padding: 12px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    font-size: .88rem;
}
#utilidadesTable tbody tr { transition: background .15s; }
#utilidadesTable tbody tr:hover { background: rgba(139,92,246,.04); }
#utilidadesTable tfoot td {
    padding: 14px 12px;
    border-top: 2px solid #e2e8f0;
    background: #f8fafc;
}
body.dark-mode #utilidadesTable thead th {
    background: rgba(15,23,42,.6);
    border-bottom-color: #334155;
    color: #94a3b8;
}
body.dark-mode #utilidadesTable tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
body.dark-mode #utilidadesTable tbody tr:hover { background: rgba(139,92,246,.08); }
body.dark-mode #utilidadesTable tfoot td {
    background: rgba(15,23,42,.6);
    border-top-color: #334155;
    color: #f1f5f9;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">
    <div class="ui-header d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body w-100">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-bar-chart-line"></i>
                </div>
                <div>
                    <h2 class="ui-header-title">Utilidades / Rentabilidad</h2>
                    <div class="ui-header-meta">Período: {{ $desde }} al {{ $hasta }} &middot; {{ $detalles->count() }} línea(s)</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('reportes.utilidades.csv', ['desde' => $desde, 'hasta' => $hasta]) }}" class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-download me-1"></i> CSV</a>
                <a href="{{ route('reportes.index') }}" class="ui-btn ui-btn-ghost rounded-pill"><i class="bi bi-grid me-1"></i> Reportes</a>
            </div>
        </div>
    </div>

    <div class="ui-card filter-card mb-4">
        <div class="ui-card-accent"></div>
        <div class="px-4 py-3">
            <form method="GET" class="row gx-3 gy-3 align-items-end">
                <div class="col-auto">
                    <label class="ui-label small fw-semibold mb-1"><i class="bi bi-calendar3 text-primary me-1"></i>Desde</label>
                    <input type="date" name="desde" class="ui-input" value="{{ $desde }}">
                </div>
                <div class="col-auto">
                    <label class="ui-label small fw-semibold mb-1"><i class="bi bi-calendar3 text-primary me-1"></i>Hasta</label>
                    <input type="date" name="hasta" class="ui-input" value="{{ $hasta }}">
                </div>
                <div class="col-auto">
                    <label class="ui-label small fw-semibold mb-1 d-sm-block d-none">&nbsp;</label>
                    <button class="ui-btn ui-btn-solid rounded-pill px-4 shadow-sm"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="ui-stat text-center p-3"><div class="ui-stat-label">Ventas</div><div class="ui-stat-value">RD$ {{ number_format($totalVentas, 2) }}</div></div></div>
        <div class="col-md-3"><div class="ui-stat text-center p-3"><div class="ui-stat-label">Costo</div><div class="ui-stat-value text-warning">RD$ {{ number_format($totalCosto, 2) }}</div></div></div>
        <div class="col-md-3"><div class="ui-stat text-center p-3"><div class="ui-stat-label">Utilidad Bruta</div><div class="ui-stat-value {{ $utilidadBruta >= 0 ? 'text-success' : 'text-danger' }}">RD$ {{ number_format($utilidadBruta, 2) }}</div></div></div>
        <div class="col-md-3"><div class="ui-stat text-center p-3"><div class="ui-stat-label">Margen</div><div class="ui-stat-value text-info">{{ number_format($margen, 1) }}%</div></div></div>
    </div>

    <div class="ui-card overflow-hidden">
        <div class="table-responsive px-3 py-3">
            <table id="utilidadesTable" class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">Venta</th><th>Fecha</th><th>Cliente</th><th>Producto</th>
                        <th class="text-end">Cant.</th><th class="text-end">Precio</th><th class="text-end">Costo</th><th class="text-end">Subtotal</th><th class="text-end pe-4">Ganancia</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($detalles as $d)
                        <tr>
                            <td class="ps-4 font-monospace small">#{{ str_pad($d['venta_id'], 5, '0', STR_PAD_LEFT) }}</td>
                            <td><small>{{ $d['fecha'] }}</small></td>
                            <td><span class="small">{{ $d['cliente'] }}</span></td>
                            <td><span class="fw-semibold small">{{ $d['producto'] }}</span></td>
                            <td class="text-end">{{ $d['cantidad'] }}</td>
                            <td class="text-end">RD$ {{ number_format($d['precio'], 2) }}</td>
                            <td class="text-end text-warning">RD$ {{ number_format($d['costo'], 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($d['subtotal'], 2) }}</td>
                            <td class="text-end pe-4 fw-bold {{ $d['ganancia'] >= 0 ? 'text-success' : 'text-danger' }}">RD$ {{ number_format($d['ganancia'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1"></i><p class="mt-2 mb-0">No hay datos en este período</p></td></tr>
                    @endforelse
                </tbody>
                <tfoot class="fw-bold">
                    <tr>
                        <td colspan="4" class="ps-4 py-3 text-end text-uppercase small">Totales ({{ $totalProductosVendidos }} unidades)</td>
                        <td class="text-end py-3">{{ $totalProductosVendidos }}</td>
                        <td class="text-end py-3"></td>
                        <td class="text-end py-3 text-warning">RD$ {{ number_format($totalCosto, 2) }}</td>
                        <td class="text-end py-3">RD$ {{ number_format($totalVentas, 2) }}</td>
                        <td class="text-end pe-4 py-3 {{ $utilidadBruta >= 0 ? 'text-success' : 'text-danger' }}">RD$ {{ number_format($utilidadBruta, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<!-- Spacing --><div class="mb-5"></div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#utilidadesTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        columnDefs: [
            { orderable: false, targets: [4,5,6,7,8] }
        ],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
@endpush
