@extends('layouts.app')
@section('title', 'Reportes Restaurante')

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
.card-accent-bar { height: 4px; border-radius: 1.2rem 1.2rem 0 0; }
.filter-card > .card-accent { height:5px;border-radius:1.2rem 1.2rem 0 0; }
.filter-card .form-control:focus,
.filter-card .form-select:focus { border-color:#4f46e5!important;box-shadow:0 0 0 3px rgba(79,70,229,.15)!important; }
.filter-card .btn-primary { background:linear-gradient(135deg,#4f46e5,#3b82f6)!important;border:none!important; }
.filter-card .btn-primary:hover { background:linear-gradient(135deg,#4338ca,#2563eb)!important;box-shadow:0 6px 20px rgba(79,70,229,.4)!important; }
@media(max-width:575.98px){.filter-card .form-control,.filter-card .form-select{min-width:100%;}}
#restauranteMeseroTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#restauranteMeseroTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#restauranteMeseroTable tbody tr { transition:background .15s; }
#restauranteMeseroTable tbody tr:hover { background:rgba(59,130,246,.04); }
body.dark-mode #restauranteMeseroTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #restauranteMeseroTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #restauranteMeseroTable tbody tr:hover { background:rgba(59,130,246,.08); }
#restauranteMesaTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#restauranteMesaTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#restauranteMesaTable tbody tr { transition:background .15s; }
#restauranteMesaTable tbody tr:hover { background:rgba(59,130,246,.04); }
body.dark-mode #restauranteMesaTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #restauranteMesaTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #restauranteMesaTable tbody tr:hover { background:rgba(59,130,246,.08); }
#restauranteTurnoTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#restauranteTurnoTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#restauranteTurnoTable tbody tr { transition:background .15s; }
#restauranteTurnoTable tbody tr:hover { background:rgba(59,130,246,.04); }
body.dark-mode #restauranteTurnoTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #restauranteTurnoTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #restauranteTurnoTable tbody tr:hover { background:rgba(59,130,246,.08); }
#restauranteProductosTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#restauranteProductosTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#restauranteProductosTable tbody tr { transition:background .15s; }
#restauranteProductosTable tbody tr:hover { background:rgba(59,130,246,.04); }
body.dark-mode #restauranteProductosTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #restauranteProductosTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #restauranteProductosTable tbody tr:hover { background:rgba(59,130,246,.08); }
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
                <h2 class="fw-bold mb-1"><i class="bi bi-bar-chart-line text-white me-2"></i>Reportes Restaurante</h2>
                <p class="mb-0" style="color:rgba(255,255,255,.8);">Análisis de ventas del terminal de mesas</p>
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

    <div class="premium-card filter-card mb-4">
        <div class="card-accent blue"></div>
        <div class="px-4 py-3">
            <form method="GET" class="row g-2">
                <div class="col-auto">
                    <label class="form-label small fw-bold">Desde</label>
                    <input type="date" name="desde" class="form-control" value="{{ $desde }}">
                </div>
                <div class="col-auto">
                    <label class="form-label small fw-bold">Hasta</label>
                    <input type="date" name="hasta" class="form-control" value="{{ $hasta }}">
                </div>
                <div class="col-auto d-flex align-items-end">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-filter me-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        {{-- Ventas por mesero --}}
        <div class="col-md-6">
            <div class="premium-card h-100">
                <div class="card-accent-bar blue"></div>
                <div class="card-header bg-white rounded-top-4 border-0 pt-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>Ventas por Mesero</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive px-3 py-3">
                        <table id="restauranteMeseroTable" class="table align-middle mb-0">
                            <thead>
                                <tr><th>Mesero</th><th class="text-center">Órdenes</th><th class="text-end">Total</th></tr>
                            </thead>
                            <tbody>
                                @forelse($ventasPorMesero as $v)
                                <tr>
                                    <td>{{ $v->usuario?->name ?? '—' }}</td>
                                    <td class="text-center">{{ $v->total_ordenes }}</td>
                                    <td class="text-end fw-bold">RD$ {{ number_format($v->total_ventas, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">Sin datos</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ventas por mesa --}}
        <div class="col-md-6">
            <div class="premium-card h-100">
                <div class="card-accent-bar blue"></div>
                <div class="card-header bg-white rounded-top-4 border-0 pt-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-grid-3x3 me-2 text-success"></i>Ventas por Mesa</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive px-3 py-3">
                        <table id="restauranteMesaTable" class="table align-middle mb-0">
                            <thead>
                                <tr><th>Mesa</th><th class="text-center">Órdenes</th><th class="text-end">Total</th></tr>
                            </thead>
                            <tbody>
                                @forelse($ventasPorMesa as $v)
                                <tr>
                                    <td>{{ $v->mesa?->nombre ?? 'Mesa ' . ($v->mesa?->numero ?? $v->mesa_id) }}</td>
                                    <td class="text-center">{{ $v->total_ordenes }}</td>
                                    <td class="text-end fw-bold">RD$ {{ number_format($v->total_ventas, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">Sin datos</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ventas por turno --}}
        <div class="col-md-6">
            <div class="premium-card h-100">
                <div class="card-accent-bar blue"></div>
                <div class="card-header bg-white rounded-top-4 border-0 pt-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-sun me-2 text-warning"></i>Ventas por Turno</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive px-3 py-3">
                        <table id="restauranteTurnoTable" class="table align-middle mb-0">
                            <thead>
                                <tr><th>Turno</th><th class="text-center">Órdenes</th><th class="text-end">Total</th></tr>
                            </thead>
                            <tbody>
                                @forelse($ventasPorHora as $v)
                                <tr>
                                    <td>{{ $v->turno }}</td>
                                    <td class="text-center">{{ $v->total_ordenes }}</td>
                                    <td class="text-end fw-bold">RD$ {{ number_format($v->total_ventas, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">Sin datos</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Productos más vendidos --}}
        <div class="col-md-6">
            <div class="premium-card h-100">
                <div class="card-accent-bar blue"></div>
                <div class="card-header bg-white rounded-top-4 border-0 pt-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-trophy me-2 text-danger"></i>Productos más Vendidos</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive px-3 py-3">
                        <table id="restauranteProductosTable" class="table align-middle mb-0">
                            <thead>
                                <tr><th>#</th><th>Producto</th><th class="text-center">Cantidad</th><th class="text-end">Total</th></tr>
                            </thead>
                            <tbody>
                                @forelse($productosTop as $i => $p)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $p->producto->nombre ?? '—' }}</td>
                                    <td class="text-center">{{ $p->total_cantidad }}</td>
                                    <td class="text-end fw-bold">RD$ {{ number_format($p->total_ventas, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">Sin datos</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Spacing --><div class="mb-5"></div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#restauranteMeseroTable').DataTable({
        responsive: true, pageLength: 10, lengthMenu: [[5,10,25,-1],[5,10,25,'Todos']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        columnDefs: [{ orderable: false, targets: [2] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
    $('#restauranteMesaTable').DataTable({
        responsive: true, pageLength: 10, lengthMenu: [[5,10,25,-1],[5,10,25,'Todos']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        columnDefs: [{ orderable: false, targets: [2] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
    $('#restauranteTurnoTable').DataTable({
        responsive: true, pageLength: 10, lengthMenu: [[5,10,25,-1],[5,10,25,'Todos']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        columnDefs: [{ orderable: false, targets: [2] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
    $('#restauranteProductosTable').DataTable({
        responsive: true, pageLength: 10, lengthMenu: [[5,10,25,-1],[5,10,25,'Todos']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        columnDefs: [{ orderable: false, targets: [3] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
@endpush
