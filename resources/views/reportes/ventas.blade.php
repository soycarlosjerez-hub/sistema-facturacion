@extends('layouts.app')
@section('title', 'Resumen de Ventas')

@push('styles')
@include('partials.premium-ui')
<style>
.premium-header {
    background: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #06b6d4 100%) !important;
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
body.dark-mode .premium-card { background: rgba(15,23,42,.8); border-color: rgba(255,255,255,.08); }
body.dark-mode .premium-card-title { color: #f1f5f9; }
body.dark-mode .premium-card-subtitle { color: #94a3b8; }
.filter-card > .card-accent { height:5px;border-radius:1.2rem 1.2rem 0 0; }
.filter-card .form-control:focus,
.filter-card .form-select:focus { border-color:#4f46e5!important;box-shadow:0 0 0 3px rgba(79,70,229,.15)!important; }
.filter-card .btn-primary { background:linear-gradient(135deg,#4f46e5,#3b82f6)!important;border:none!important; }
.filter-card .btn-primary:hover { background:linear-gradient(135deg,#4338ca,#2563eb)!important;box-shadow:0 6px 20px rgba(79,70,229,.4)!important; }
@media(max-width:575.98px){.filter-card .form-control,.filter-card .form-select{min-width:100%;}}
.premium-stat-card { background:rgba(255,255,255,.85);border-radius:1.2rem;box-shadow:0 4px 24px rgba(0,0,0,.04);transition:transform .2s,box-shadow .2s;position:relative;overflow:hidden; }
.premium-stat-card:hover { transform:translateY(-2px);box-shadow:0 8px 32px rgba(0,0,0,.08); }
.premium-stat-card .stat-label { font-size:.65rem;text-transform:uppercase;letter-spacing:.5px;color:#64748b;font-weight:700;margin-bottom:4px; }
.premium-stat-card .stat-value { font-size:1.5rem;font-weight:800; }
body.dark-mode .premium-stat-card { background:rgba(15,23,42,.7);border:1px solid rgba(255,255,255,.06); }
body.dark-mode .premium-stat-card .stat-label { color:#94a3b8; }
body.dark-mode .premium-stat-card .stat-value { color:#f1f5f9; }
#ventasTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#ventasTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#ventasTable tbody tr { transition:background .15s; }
#ventasTable tbody tr:hover { background:rgba(59,130,246,.04); }
#ventasTable tfoot td { padding:14px 12px;border-top:2px solid #e2e8f0;background:#f8fafc; }
body.dark-mode #ventasTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #ventasTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #ventasTable tbody tr:hover { background:rgba(59,130,246,.08); }
body.dark-mode #ventasTable tfoot td { background:rgba(15,23,42,.6);border-top-color:#334155;color:#f1f5f9; }
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
                <h2 class="fw-bold mb-1"><i class="bi bi-bar-chart-line text-white me-2"></i>Resumen de Ventas</h2>
                <p style="color:rgba(255,255,255,.8);" class="mb-0">Período: {{ $desde }} al {{ $hasta }} &middot; {{ $cantidad }} venta(s)</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('reportes.ventas', ['desde' => $desde, 'hasta' => $hasta, 'csv' => 1]) }}" class="btn btn-success rounded-pill" onclick="event.preventDefault();window.location='{{ route('reportes.ventas.csv', ['desde' => $desde, 'hasta' => $hasta]) }}'"><i class="bi bi-download me-1"></i> CSV</a>
                <a href="{{ route('reportes.ventas.pdf', ['desde' => $desde, 'hasta' => $hasta]) }}" class="btn btn-danger rounded-pill"><i class="bi bi-file-pdf me-1"></i> PDF</a>
                <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-grid me-1"></i> Reportes</a>
                <div class="premium-avatar-circle ms-2">
                    <i class="bi bi-bar-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card filter-card mb-4"><div class="card-accent blue"></div><div class="px-4 py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><label class="form-label small fw-semibold mb-0">Desde</label></div>
            <div class="col-auto"><input type="date" name="desde" class="form-control" value="{{ $desde }}"></div>
            <div class="col-auto"><label class="form-label small fw-semibold mb-0">Hasta</label></div>
            <div class="col-auto"><input type="date" name="hasta" class="form-control" value="{{ $hasta }}"></div>
            <div class="col-auto"><button class="btn btn-primary rounded-pill"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div></div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="premium-stat-card text-center p-3">
                <div class="stat-label">Cantidad</div>
                <div class="stat-value">{{ $cantidad }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card text-center p-3">
                <div class="stat-label">Total General</div>
                <div class="stat-value text-primary">RD$ {{ number_format($totalGeneral, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card text-center p-3">
                <div class="stat-label">ITBIS</div>
                <div class="stat-value text-warning">RD$ {{ number_format($totalItbis, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card text-center p-3">
                <div class="stat-label">Efectivo Recibido</div>
                <div class="stat-value text-success">RD$ {{ number_format($totalEfectivo, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="premium-card overflow-hidden">
        <div class="table-responsive px-3 py-3">
            <table id="ventasTable" class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">#</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>NCF/e-CF</th>
                        <th>Fecha</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">ITBIS</th>
                        <th class="text-end pe-4">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $v)
                        <tr>
                            <td class="ps-4">{{ str_pad($v->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td><span class="fw-semibold small">{{ $v->cliente?->nombre ?? 'Consumidor Final' }}</span><br><small class="text-muted font-monospace">{{ $v->cliente?->rnc_cedula ?? '' }}</small></td>
                            <td><small>{{ $v->usuario?->name ?? '' }}</small></td>
                            <td><span class="font-monospace small">{{ $v->ncf ?? $v->encf ?? 'S/N' }}</span></td>
                            <td><small>{{ $v->created_at->format('d/m/Y h:i A') }}</small></td>
                            <td class="text-end">RD$ {{ number_format($v->subtotal ?? 0, 2) }}</td>
                            <td class="text-end text-warning fw-semibold">RD$ {{ number_format($v->impuestos ?? 0, 2) }}</td>
                            <td class="text-end pe-4 fw-bold">RD$ {{ number_format($v->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1"></i><p class="mt-2 mb-0">No hay ventas en este período</p></td></tr>
                    @endforelse
                </tbody>
                <tfoot class="fw-bold">
                    <tr>
                        <td colspan="5" class="ps-4 py-3 text-end text-uppercase small">Totales</td>
                        <td class="text-end py-3">RD$ {{ number_format($ventas->sum('subtotal'), 2) }}</td>
                        <td class="text-end py-3 text-warning">RD$ {{ number_format($ventas->sum('impuestos'), 2) }}</td>
                        <td class="text-end pe-4 py-3">RD$ {{ number_format($ventas->sum('total'), 2) }}</td>
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
    $('#ventasTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        columnDefs: [{ orderable: false, targets: [5, 6, 7] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
@endpush
