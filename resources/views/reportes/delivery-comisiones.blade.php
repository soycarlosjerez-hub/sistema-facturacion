@extends('layouts.app')
@section('title', 'Comisiones Delivery')

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
body.dark-mode .card-header.bg-white { background: rgba(15,23,42,.5) !important; border-color: rgba(255,255,255,.06) !important; }
body.dark-mode .card-header.bg-white h6 { color: #f1f5f9; }
body.dark-mode .card-header.bg-white h6 i { opacity: .9; }
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
#deliveryResumenTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#deliveryResumenTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#deliveryResumenTable tbody tr { transition:background .15s; }
#deliveryResumenTable tbody tr:hover { background:rgba(59,130,246,.04); }
#deliveryResumenTable tfoot td { padding:14px 12px;border-top:2px solid #e2e8f0;background:#f8fafc; }
body.dark-mode #deliveryResumenTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #deliveryResumenTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #deliveryResumenTable tbody tr:hover { background:rgba(59,130,246,.08); }
body.dark-mode #deliveryResumenTable tfoot td { background:rgba(15,23,42,.6);border-top-color:#334155;color:#f1f5f9; }
#deliveryDetalleTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#deliveryDetalleTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#deliveryDetalleTable tbody tr { transition:background .15s; }
#deliveryDetalleTable tbody tr:hover { background:rgba(59,130,246,.04); }
#deliveryDetalleTable tfoot td { padding:14px 12px;border-top:2px solid #e2e8f0;background:#f8fafc; }
body.dark-mode #deliveryDetalleTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #deliveryDetalleTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #deliveryDetalleTable tbody tr:hover { background:rgba(59,130,246,.08); }
body.dark-mode #deliveryDetalleTable tfoot td { background:rgba(15,23,42,.6);border-top-color:#334155;color:#f1f5f9; }
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
                <h2 class="fw-bold mb-1"><i class="bi bi-bar-chart-line text-white me-2"></i>Comisiones Delivery</h2>
                <p style="color:rgba(255,255,255,.8);" class="mb-0">Reporte de comisiones por empresas de delivery</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary rounded-pill btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
                <div class="premium-avatar-circle ms-2">
                    <i class="bi bi-bar-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card filter-card mb-4"><div class="card-accent blue"></div><div class="px-4 py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small fw-semibold mb-0">Desde</label>
                <input type="date" name="desde" class="form-control rounded-3" value="{{ $desde }}">
            </div>
            <div class="col-auto">
                <label class="form-label small fw-semibold mb-0">Hasta</label>
                <input type="date" name="hasta" class="form-control rounded-3" value="{{ $hasta }}">
            </div>
            <div class="col-auto d-flex align-items-end">
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-filter me-1"></i> Filtrar
                </button>
            </div>
        </form>
    </div></div>

    {{-- Resumen por compañía --}}
    <div class="premium-card card-accent blue mb-4">
        <div class="card-header bg-white rounded-top-4 border-0 pt-3 px-4">
            <h6 class="fw-bold mb-0"><i class="bi bi-building me-2 text-primary"></i>Resumen por Compañía</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive px-3 py-3">
                <table id="deliveryResumenTable" class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Compañía</th>
                            <th class="text-center">Ventas</th>
                            <th class="text-end">Total Comisiones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($companies as $c)
                        <tr>
                            <td class="fw-medium">{{ $c['nombre'] }}</td>
                            <td class="text-center">{{ $c['ventas'] }}</td>
                            <td class="text-end fw-bold">RD$ {{ number_format($c['total_fee'], 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">Sin datos</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="fw-bold">
                        <tr>
                            <th>Total</th>
                            <th class="text-center">{{ collect($companies)->sum('ventas') }}</th>
                            <th class="text-end fw-bold">RD$ {{ number_format($totalFees, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Detalle de ventas con delivery --}}
    <div class="premium-card">
        <div class="card-header bg-white rounded-top-4 border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="bi bi-receipt me-2 text-success"></i>Detalle de Ventas</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive px-3 py-3">
                <table id="deliveryDetalleTable" class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Mesa</th>
                            <th>Compañía</th>
                            <th class="text-end">Total Venta</th>
                            <th class="text-end">Comisión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detalles as $i => $v)
                        <tr>
                            <td>{{ $v->id }}</td>
                            <td>{{ $v->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $v->mesa?->nombre ?? '—' }}</td>
                            <td>{{ $v->deliveryCompany?->nombre ?? '—' }}</td>
                            <td class="text-end">RD$ {{ number_format($v->total, 2) }}</td>
                            <td class="text-end fw-bold">RD$ {{ number_format($v->delivery_fee, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Sin datos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Spacing --><div class="mb-5"></div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#deliveryResumenTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        columnDefs: [{ orderable: false, targets: [1, 2] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
    $('#deliveryDetalleTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        columnDefs: [{ orderable: false, targets: [4, 5] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
@endpush
