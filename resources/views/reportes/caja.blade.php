@extends('layouts.app')
@section('title', 'Reporte de Caja')

@push('styles')
@include('partials.premium-ui')
<style>
.premium-header {
    background: linear-gradient(135deg, #1e40af 0%, #4f46e5 50%, #0891b2 100%) !important;
    background-size: 300% 300% !important;
    animation: premiumGradientShift 6s ease infinite !important;
    box-shadow: 0 8px 32px rgba(59,130,246,.25) !important;
}
.premium-header .btn-outline-secondary {
    color: #fff !important;
    border-color: rgba(255,255,255,.6) !important;
    background: rgba(255,255,255,.1) !important;
}
.premium-header .btn-outline-secondary:hover {
    background: rgba(255,255,255,.25) !important;
    border-color: #fff !important;
}
#cajaTable_wrapper { padding: 0; }
#cajaTable_wrapper .dataTables_length,
#cajaTable_wrapper .dataTables_filter { margin-bottom: 14px; }
#cajaTable_wrapper .dataTables_length select,
#cajaTable_wrapper .dataTables_filter input {
    border: 1.5px solid #e2e8f0;
    border-radius: .65rem;
    padding: .4rem .75rem;
}
#cajaTable { margin-bottom: 0; }
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
#cajaTable tbody tr:hover { background: rgba(59,130,246,.04); }
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
body.dark-mode #cajaTable tbody tr:hover { background: rgba(59,130,246,.08); }
body.dark-mode #cajaTable tfoot td {
    background: rgba(15,23,42,.6);
    border-top-color: #334155;
    color: #f1f5f9;
}
body.dark-mode #cajaTable_wrapper .dataTables_length select,
body.dark-mode #cajaTable_wrapper .dataTables_filter input {
    background: rgba(15,23,42,.6);
    border-color: #334155;
    color: #f1f5f9;
}
.filter-card > .card-accent {
    height: 5px;
    border-radius: 1.2rem 1.2rem 0 0;
}
.filter-card .form-control:focus,
.filter-card .form-select:focus {
    border-color: #4f46e5 !important;
    box-shadow: 0 0 0 3px rgba(79,70,229,.15) !important;
}
.filter-card .form-control,
.filter-card .form-select {
    min-width: 180px;
}
.filter-card .btn-primary {
    background: linear-gradient(135deg, #4f46e5, #3b82f6) !important;
    border: none !important;
}
.filter-card .btn-primary:hover {
    background: linear-gradient(135deg, #4338ca, #2563eb) !important;
    box-shadow: 0 6px 20px rgba(79,70,229,.4) !important;
}
@media (max-width: 575.98px) {
    .filter-card .form-control,
    .filter-card .form-select {
        min-width: 100%;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header d-flex flex-wrap justify-content-between align-items-center">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative w-100" style="z-index:2;">
            <div>
                <h2 class="fw-bold mb-1" style="color:#fff;"><i class="bi bi-bar-chart-line me-2"></i>Reporte de Caja / Turnos</h2>
                <p class="mb-0" style="color:rgba(255,255,255,.8);">Período: {{ $desde }} al {{ $hasta }} &middot; {{ $cantidad }} sesión(es)</p>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap mt-2 mt-md-0">
                <a href="{{ route('reportes.caja.pdf', ['desde' => $desde, 'hasta' => $hasta, 'caja_id' => request('caja_id')]) }}" class="btn btn-danger rounded-pill shadow-sm px-4"><i class="bi bi-file-pdf me-1"></i> PDF</a>
                <a href="{{ route('reportes.caja.csv', ['desde' => $desde, 'hasta' => $hasta, 'caja_id' => request('caja_id')]) }}" class="btn btn-success rounded-pill shadow-sm px-4"><i class="bi bi-download me-1"></i> CSV</a>
                <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-grid me-1"></i> Reportes</a>
                <div class="premium-avatar-circle ms-2">
                    <i class="bi bi-bar-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card filter-card mb-4">
        <div class="card-accent blue"></div>
        <div class="px-4 py-3">
            <form method="GET" class="row gx-3 gy-3 align-items-end">
                <div class="col-12 col-sm-auto">
                    <label class="form-label small fw-semibold mb-1">
                        <i class="bi bi-calendar3 text-primary me-1"></i>Desde
                    </label>
                    <input type="date" name="desde" class="form-control" value="{{ $desde }}">
                </div>
                <div class="col-12 col-sm-auto">
                    <label class="form-label small fw-semibold mb-1">
                        <i class="bi bi-calendar3 text-primary me-1"></i>Hasta
                    </label>
                    <input type="date" name="hasta" class="form-control" value="{{ $hasta }}">
                </div>
                <div class="col-12 col-sm-auto">
                    <label class="form-label small fw-semibold mb-1">
                        <i class="bi bi-layers text-primary me-1"></i>Caja
                    </label>
                    <select name="caja_id" class="form-select">
                        <option value="">Todas las cajas</option>
                        @foreach($cajas as $c)
                            <option value="{{ $c->id }}" {{ request('caja_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-auto">
                    <label class="form-label small fw-semibold mb-1 d-sm-block d-none">&nbsp;</label>
                    <button class="btn btn-primary rounded-pill px-4 shadow-sm w-100 w-sm-auto">
                        <i class="bi bi-funnel me-1"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="premium-stat-card text-center p-3">
                <div class="stat-label">Sesiones</div>
                <div class="stat-value text-primary">{{ $cantidad }}</div>
                <div class="mt-2 d-flex justify-content-center gap-3">
                    <span><span class="badge bg-success rounded-pill">{{ $abiertas }}</span> abiertas</span>
                    <span><span class="badge bg-secondary rounded-pill">{{ $cerradas }}</span> cerradas</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card text-center p-3">
                <div class="stat-label">Total Ventas</div>
                <div class="stat-value text-success">RD$ {{ number_format($totalVentas, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card text-center p-3">
                <div class="stat-label">Descuadre Total</div>
                <div class="stat-value {{ $totalDescuadre >= 0 ? 'text-success' : 'text-danger' }}">RD$ {{ number_format($totalDescuadre, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card text-center p-3">
                <div class="stat-label">Promedio x Sesión</div>
                <div class="stat-value text-info">RD$ {{ $cantidad > 0 ? number_format($totalVentas / $cantidad, 2) : '0.00' }}</div>
            </div>
        </div>
    </div>

    <div class="premium-card overflow-hidden">
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
