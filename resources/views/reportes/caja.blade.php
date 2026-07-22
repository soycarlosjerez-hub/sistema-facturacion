@extends('layouts.app')
@section('title', 'Reporte de Caja')

@push('styles')
@include('partials.premium-ui')
<style>
#cajaTable thead th {
    border-bottom: 2px solid #e2e8f0;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #64748b;
    padding: 14px 12px;
    background: #f8fafc;
}
#cajaTable tbody td {
    padding: 12px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    font-size: .88rem;
}
#cajaTable tbody tr { transition: background .15s; }
#cajaTable tbody tr:hover { background: rgba(139,92,246,.04); }
#cajaTable tfoot td {
    padding: 14px 12px;
    border-top: 2px solid #e2e8f0;
    background: #f8fafc;
}
.session-open { color: #059669; }
.session-closed { color: #64748b; }
body.dark-mode #cajaTable thead th {
    background: rgba(15,23,42,.6);
    border-bottom-color: #334155;
    color: #94a3b8;
}
body.dark-mode #cajaTable tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
body.dark-mode #cajaTable tbody tr:hover { background: rgba(139,92,246,.08); }
body.dark-mode #cajaTable tfoot td {
    background: rgba(15,23,42,.6);
    border-top-color: #334155;
    color: #f1f5f9;
}
.filter-card > .ui-card-accent {
    height: 5px;
    border-radius: 1.2rem 1.2rem 0 0;
}
.filter-card .ui-input:focus,
.filter-card .ui-select:focus {
    border-color: #7c3aed !important;
    box-shadow: 0 0 0 3px rgba(139,92,246,.15) !important;
}
.filter-card .ui-input,
.filter-card .ui-select {
    min-width: 180px;
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
    .filter-card .ui-input,
    .filter-card .ui-select {
        min-width: 100%;
    }
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
                    <h2 class="ui-header-title">Reporte de Caja / Turnos</h2>
                    <div class="ui-header-meta">Período: {{ $desde }} al {{ $hasta }} &middot; {{ $cantidad }} sesión(es)</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('reportes.caja.pdf', ['desde' => $desde, 'hasta' => $hasta, 'caja_id' => request('caja_id')]) }}" class="ui-btn ui-btn-solid rounded-pill shadow-sm px-4"><i class="bi bi-file-pdf me-1"></i> PDF</a>
                <a href="{{ route('reportes.caja.csv', ['desde' => $desde, 'hasta' => $hasta, 'caja_id' => request('caja_id')]) }}" class="ui-btn ui-btn-solid rounded-pill shadow-sm px-4"><i class="bi bi-download me-1"></i> CSV</a>
                <a href="{{ route('reportes.index') }}" class="ui-btn ui-btn-ghost rounded-pill"><i class="bi bi-grid me-1"></i> Reportes</a>
            </div>
        </div>
    </div>

    <div class="ui-card filter-card mb-4">
        <div class="ui-card-accent"></div>
        <div class="px-4 py-3">
            <form method="GET" class="row gx-3 gy-3 align-items-end">
                <div class="col-12 col-sm-auto">
                    <label class="ui-label small fw-semibold mb-1">
                        <i class="bi bi-calendar3 text-primary me-1"></i>Desde
                    </label>
                    <input type="date" name="desde" class="ui-input" value="{{ $desde }}">
                </div>
                <div class="col-12 col-sm-auto">
                    <label class="ui-label small fw-semibold mb-1">
                        <i class="bi bi-calendar3 text-primary me-1"></i>Hasta
                    </label>
                    <input type="date" name="hasta" class="ui-input" value="{{ $hasta }}">
                </div>
                <div class="col-12 col-sm-auto">
                    <label class="ui-label small fw-semibold mb-1">
                        <i class="bi bi-layers text-primary me-1"></i>Caja
                    </label>
                    <select name="caja_id" class="ui-select">
                        <option value="">Todas las cajas</option>
                        @foreach($cajas as $c)
                            <option value="{{ $c->id }}" {{ request('caja_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-auto">
                    <label class="ui-label small fw-semibold mb-1 d-sm-block d-none">&nbsp;</label>
                    <button class="ui-btn ui-btn-solid rounded-pill px-4 shadow-sm w-100 w-sm-auto">
                        <i class="bi bi-funnel me-1"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="ui-stat text-center p-3">
                <div class="ui-stat-label">Sesiones</div>
                <div class="ui-stat-value text-primary">{{ $cantidad }}</div>
                <div class="mt-2 d-flex justify-content-center gap-3">
                    <span><span class="badge bg-success rounded-pill">{{ $abiertas }}</span> abiertas</span>
                    <span><span class="badge bg-secondary rounded-pill">{{ $cerradas }}</span> cerradas</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat text-center p-3">
                <div class="ui-stat-label">Total Ventas</div>
                <div class="ui-stat-value text-success">RD$ {{ number_format($totalVentas, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat text-center p-3">
                <div class="ui-stat-label">Descuadre Total</div>
                <div class="ui-stat-value {{ $totalDescuadre >= 0 ? 'text-success' : 'text-danger' }}">RD$ {{ number_format($totalDescuadre, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat text-center p-3">
                <div class="ui-stat-label">Promedio x Sesión</div>
                <div class="ui-stat-value text-info">RD$ {{ $cantidad > 0 ? number_format($totalVentas / $cantidad, 2) : '0.00' }}</div>
            </div>
        </div>
    </div>

    <div class="ui-card overflow-hidden">
        <div class="table-responsive px-3 py-3">
            <table id="cajaTable" class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Caja</th>
                        <th>Cajero</th>
                        <th>Apertura</th>
                        <th>Cierre</th>
                        <th class="text-end">Inicial</th>
                        <th class="text-end">Efectivo</th>
                        <th class="text-end">Tarjeta</th>
                        <th class="text-end">Transf.</th>
                        <th class="text-end">Declarado</th>
                        <th class="text-end">Descuadre</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sesiones as $s)
                        <tr>
                            <td><span class="fw-semibold">{{ $s->caja?->nombre ?? '' }}</span></td>
                            <td>{{ $s->user?->name ?? '' }}</td>
                            <td>{{ $s->fecha_apertura?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td>
                                @if($s->estado === 'cerrada')
                                    <span class="session-closed">{{ $s->fecha_cierre?->format('d/m/Y H:i') ?? '-' }}</span>
                                @else
                                    <span class="badge bg-success rounded-pill">Abierta</span>
                                @endif
                            </td>
                            <td class="text-end">RD$ {{ number_format($s->monto_inicial ?? 0, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($s->ventas_efectivo ?? 0, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($s->ventas_tarjeta ?? 0, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($s->ventas_transferencia ?? 0, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($s->monto_declarado ?? 0, 2) }}</td>
                            <td class="text-end fw-bold {{ ($s->descuadre ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">RD$ {{ number_format($s->descuadre ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                <p class="mb-0">No hay sesiones en este período</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end text-uppercase fw-bold">Totales</td>
                        <td class="text-end fw-bold">RD$ {{ number_format($sesiones->sum('monto_inicial'), 2) }}</td>
                        <td class="text-end fw-bold">RD$ {{ number_format($sesiones->sum('ventas_efectivo'), 2) }}</td>
                        <td class="text-end fw-bold">RD$ {{ number_format($sesiones->sum('ventas_tarjeta'), 2) }}</td>
                        <td class="text-end fw-bold">RD$ {{ number_format($sesiones->sum('ventas_transferencia'), 2) }}</td>
                        <td class="text-end fw-bold">RD$ {{ number_format($sesiones->sum('monto_declarado'), 2) }}</td>
                        <td class="text-end fw-bold">RD$ {{ number_format($sesiones->sum('descuadre'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#cajaTable').DataTable({
        responsive: true,
        order: [[2, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        columnDefs: [
            { orderable: false, targets: [4, 5, 6, 7, 8, 9] }
        ],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
@endpush
