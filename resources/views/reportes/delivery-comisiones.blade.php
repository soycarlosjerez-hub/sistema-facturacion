@extends('layouts.app')
@section('title', 'Comisiones Delivery')

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .card-header.bg-white { background: rgba(15,23,42,.5) !important; border-color: rgba(255,255,255,.06) !important; }
body.dark-mode .card-header.bg-white h6 { color: #f1f5f9; }
body.dark-mode .card-header.bg-white h6 i { opacity: .9; }
.filter-card > .ui-card-accent { height:5px;border-radius:1.2rem 1.2rem 0 0; }
.filter-card .ui-input:focus,
.filter-card .ui-select:focus { border-color:#7c3aed!important;box-shadow:0 0 0 3px rgba(139,92,246,.15)!important; }
.filter-card .ui-btn-solid { background:linear-gradient(135deg,#7c3aed,#8b5cf6)!important;border:none!important; }
.filter-card .ui-btn-solid:hover { background:linear-gradient(135deg,#6d28d9,#7c3aed)!important;box-shadow:0 6px 20px rgba(139,92,246,.4)!important; }
@media(max-width:575.98px){.filter-card .ui-input,.filter-card .ui-select{min-width:100%;}}
#deliveryResumenTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#deliveryResumenTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#deliveryResumenTable tbody tr { transition:background .15s; }
#deliveryResumenTable tbody tr:hover { background:rgba(139,92,246,.04); }
#deliveryResumenTable tfoot td { padding:14px 12px;border-top:2px solid #e2e8f0;background:#f8fafc; }
body.dark-mode #deliveryResumenTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #deliveryResumenTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #deliveryResumenTable tbody tr:hover { background:rgba(139,92,246,.08); }
body.dark-mode #deliveryResumenTable tfoot td { background:rgba(15,23,42,.6);border-top-color:#334155;color:#f1f5f9; }
#deliveryDetalleTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#deliveryDetalleTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#deliveryDetalleTable tbody tr { transition:background .15s; }
#deliveryDetalleTable tbody tr:hover { background:rgba(139,92,246,.04); }
#deliveryDetalleTable tfoot td { padding:14px 12px;border-top:2px solid #e2e8f0;background:#f8fafc; }
body.dark-mode #deliveryDetalleTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #deliveryDetalleTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #deliveryDetalleTable tbody tr:hover { background:rgba(139,92,246,.08); }
body.dark-mode #deliveryDetalleTable tfoot td { background:rgba(15,23,42,.6);border-top-color:#334155;color:#f1f5f9; }
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
                    <h2 class="ui-header-title">Comisiones Delivery</h2>
                    <div class="ui-header-meta">Reporte de comisiones por empresas de delivery</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('reportes.index') }}" class="ui-btn ui-btn-ghost rounded-pill btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card filter-card mb-4"><div class="ui-card-accent"></div><div class="px-4 py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="ui-label small fw-semibold mb-0">Desde</label>
                <input type="date" name="desde" class="ui-input rounded-3" value="{{ $desde }}">
            </div>
            <div class="col-auto">
                <label class="ui-label small fw-semibold mb-0">Hasta</label>
                <input type="date" name="hasta" class="ui-input rounded-3" value="{{ $hasta }}">
            </div>
            <div class="col-auto d-flex align-items-end">
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-4">
                    <i class="bi bi-filter me-1"></i> Filtrar
                </button>
            </div>
        </form>
    </div></div>

    {{-- Resumen por compañía --}}
    <div class="ui-card mb-4">
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
    <div class="ui-card">
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
