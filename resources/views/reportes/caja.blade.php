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
    border-radius: 16px !important;
    padding: 20px 24px !important;
}
.premium-header .btn-outline-secondary {
    color: #fff !important;
    border-color: rgba(255,255,255,.6) !important;
}
.premium-header .btn-outline-secondary:hover {
    background: rgba(255,255,255,.15) !important;
    border-color: #fff !important;
}
body.dark-mode .premium-card { background: rgba(15,23,42,.8); border-color: rgba(255,255,255,.08); }
body.dark-mode .premium-card-title { color: #f1f5f9; }
body.dark-mode .premium-card-subtitle { color: #94a3b8; }
#cajaTable_wrapper .dataTables_length,
#cajaTable_wrapper .dataTables_filter { margin-bottom: 12px; }
#cajaTable td, #cajaTable th { padding: 10px 12px !important; vertical-align: middle; }
#cajaTable tbody tr { transition: background .2s; }
#cajaTable tbody tr:hover { background: rgba(59,130,246,.04); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative w-100" style="z-index:2;">
            <div>
                <h2 class="fw-bold mb-1" style="color:#fff;"><i class="bi bi-bar-chart-line me-2"></i>Reporte de Caja / Turnos</h2>
                <p class="mb-0" style="color:rgba(255,255,255,.8);">Período: {{ $desde }} al {{ $hasta }} &middot; {{ $cantidad }} sesión(es)</p>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <a href="{{ route('reportes.caja.pdf', ['desde' => $desde, 'hasta' => $hasta, 'caja_id' => request('caja_id')]) }}" class="btn btn-danger rounded-pill shadow-sm"><i class="bi bi-file-pdf me-1"></i> PDF</a>
                <a href="{{ route('reportes.caja.csv', ['desde' => $desde, 'hasta' => $hasta, 'caja_id' => request('caja_id')]) }}" class="btn btn-success rounded-pill shadow-sm"><i class="bi bi-download me-1"></i> CSV</a>
                <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-grid me-1"></i> Reportes</a>
                <div class="premium-avatar-circle ms-2">
                    <i class="bi bi-bar-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card card-accent blue p-4 mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-auto">
                <label class="form-label small fw-semibold mb-1">Desde</label>
                <input type="date" name="desde" class="form-control border-0 bg-white" value="{{ $desde }}">
            </div>
            <div class="col-auto">
                <label class="form-label small fw-semibold mb-1">Hasta</label>
                <input type="date" name="hasta" class="form-control border-0 bg-white" value="{{ $hasta }}">
            </div>
            <div class="col-auto">
                <label class="form-label small fw-semibold mb-1">Caja</label>
                <select name="caja_id" class="form-select border-0 bg-white">
                    <option value="">Todas las cajas</option>
                    @foreach($cajas as $c)
                        <option value="{{ $c->id }}" {{ request('caja_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary rounded-pill px-4"><i class="bi bi-funnel me-1"></i>Filtrar</button>
            </div>
        </form>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="premium-card card-accent blue h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Sesiones</small><h4 class="fw-bold mb-0 mt-1">{{ $cantidad }} <small class="text-muted" style="font-size:.6rem;">({{ $abiertas }} abiertas / {{ $cerradas }} cerradas)</small></h4></div></div></div>
        <div class="col-md-3"><div class="premium-card card-accent blue h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Total Ventas</small><h4 class="fw-bold mb-0 mt-1 text-primary">RD$ {{ number_format($totalVentas, 2) }}</h4></div></div></div>
        <div class="col-md-3"><div class="premium-card card-accent blue h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Descuadre Total</small><h4 class="fw-bold mb-0 mt-1 {{ $totalDescuadre >= 0 ? 'text-success' : 'text-danger' }}">RD$ {{ number_format($totalDescuadre, 2) }}</h4></div></div></div>
        <div class="col-md-3"><div class="premium-card card-accent blue h-100"><div class="card-body p-3 text-center"><small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Promedio x Sesión</small><h4 class="fw-bold mb-0 mt-1 text-info">RD$ {{ $cantidad > 0 ? number_format($totalVentas / $cantidad, 2) : '0.00' }}</h4></div></div></div>
    </div>

    <div class="premium-card overflow-hidden">
        <div class="table-responsive">
            <table id="cajaTable" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;">
                        <th class="ps-4 py-3">Caja</th><th>Cajero</th><th>Apertura</th><th>Cierre</th>
                        <th class="text-end">Inicial</th><th class="text-end">Efectivo</th><th class="text-end">Tarjeta</th><th class="text-end">Transf.</th><th class="text-end">Declarado</th><th class="text-end pe-4">Descuadre</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sesiones as $s)
                        <tr>
                            <td class="ps-4"><span class="fw-semibold small">{{ $s->caja?->nombre ?? '' }}</span></td>
                            <td><small>{{ $s->user?->name ?? '' }}</small></td>
                            <td><small>{{ $s->fecha_apertura?->format('d/m/Y H:i') ?? '-' }}</small></td>
                            <td><small>{{ $s->fecha_cierre?->format('d/m/Y H:i') ?? 'Abierta' }}</small></td>
                            <td class="text-end">RD$ {{ number_format($s->monto_inicial ?? 0, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($s->ventas_efectivo ?? 0, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($s->ventas_tarjeta ?? 0, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($s->ventas_transferencia ?? 0, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($s->monto_declarado ?? 0, 2) }}</td>
                            <td class="text-end pe-4 fw-bold {{ ($s->descuadre ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">RD$ {{ number_format($s->descuadre ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1"></i><p class="mt-2 mb-0">No hay sesiones en este período</p></td></tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4" class="ps-4 py-3 text-end text-uppercase small">Totales</td>
                        <td class="text-end py-3">RD$ {{ number_format($sesiones->sum('monto_inicial'), 2) }}</td>
                        <td class="text-end py-3">RD$ {{ number_format($sesiones->sum('ventas_efectivo'), 2) }}</td>
                        <td class="text-end py-3">RD$ {{ number_format($sesiones->sum('ventas_tarjeta'), 2) }}</td>
                        <td class="text-end py-3">RD$ {{ number_format($sesiones->sum('ventas_transferencia'), 2) }}</td>
                        <td class="text-end py-3">RD$ {{ number_format($sesiones->sum('monto_declarado'), 2) }}</td>
                        <td class="text-end pe-4 py-3">RD$ {{ number_format($sesiones->sum('descuadre'), 2) }}</td>
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
        drawCallback: function() {
            $('#cajaTable_wrapper .dataTables_info, #cajaTable_wrapper .dataTables_paginate').addClass('mt-2');
        }
    });
});
</script>
@endpush
