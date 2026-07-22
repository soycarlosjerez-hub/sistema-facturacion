@extends('layouts.app')
@section('title', 'Reportes Restaurante')

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .card-header.bg-white { background: rgba(15,23,42,.5) !important; border-color: rgba(255,255,255,.06) !important; }
body.dark-mode .card-header.bg-white h6 { color: #f1f5f9; }
body.dark-mode .card-header.bg-white h6 i { opacity: .9; }
.card-accent-bar { height: 4px; border-radius: 1.2rem 1.2rem 0 0; }
.filter-card > .ui-card-accent { height:5px;border-radius:1.2rem 1.2rem 0 0; }
.filter-card .ui-input:focus,
.filter-card .ui-select:focus { border-color:#7c3aed!important;box-shadow:0 0 0 3px rgba(139,92,246,.15)!important; }
.filter-card .ui-btn-solid { background:linear-gradient(135deg,#7c3aed,#8b5cf6)!important;border:none!important; }
.filter-card .ui-btn-solid:hover { background:linear-gradient(135deg,#6d28d9,#7c3aed)!important;box-shadow:0 6px 20px rgba(139,92,246,.4)!important; }
@media(max-width:575.98px){.filter-card .ui-input,.filter-card .ui-select{min-width:100%;}}
#restauranteMeseroTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#restauranteMeseroTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#restauranteMeseroTable tbody tr { transition:background .15s; }
#restauranteMeseroTable tbody tr:hover { background:rgba(139,92,246,.04); }
body.dark-mode #restauranteMeseroTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #restauranteMeseroTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #restauranteMeseroTable tbody tr:hover { background:rgba(139,92,246,.08); }
#restauranteMesaTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#restauranteMesaTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#restauranteMesaTable tbody tr { transition:background .15s; }
#restauranteMesaTable tbody tr:hover { background:rgba(139,92,246,.04); }
body.dark-mode #restauranteMesaTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #restauranteMesaTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #restauranteMesaTable tbody tr:hover { background:rgba(139,92,246,.08); }
#restauranteTurnoTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#restauranteTurnoTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#restauranteTurnoTable tbody tr { transition:background .15s; }
#restauranteTurnoTable tbody tr:hover { background:rgba(139,92,246,.04); }
body.dark-mode #restauranteTurnoTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #restauranteTurnoTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #restauranteTurnoTable tbody tr:hover { background:rgba(139,92,246,.08); }
#restauranteProductosTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#restauranteProductosTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#restauranteProductosTable tbody tr { transition:background .15s; }
#restauranteProductosTable tbody tr:hover { background:rgba(139,92,246,.04); }
body.dark-mode #restauranteProductosTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #restauranteProductosTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #restauranteProductosTable tbody tr:hover { background:rgba(139,92,246,.08); }
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
                    <h2 class="ui-header-title">Reportes Restaurante</h2>
                    <div class="ui-header-meta">Análisis de ventas del terminal de mesas</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('reportes.index') }}" class="ui-btn ui-btn-ghost rounded-pill btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card filter-card mb-4">
        <div class="ui-card-accent"></div>
        <div class="px-4 py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="ui-label small fw-semibold mb-0">Desde</label>
                    <input type="date" name="desde" class="ui-input" value="{{ $desde }}">
                </div>
                <div class="col-auto">
                    <label class="ui-label small fw-semibold mb-0">Hasta</label>
                    <input type="date" name="hasta" class="ui-input" value="{{ $hasta }}">
                </div>
                <div class="col-auto d-flex align-items-end">
                    <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-4">
                        <i class="bi bi-filter me-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        {{-- Ventas por mesero --}}
        <div class="col-md-6">
            <div class="ui-card h-100">
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
            <div class="ui-card h-100">
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
            <div class="ui-card h-100">
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
            <div class="ui-card h-100">
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
