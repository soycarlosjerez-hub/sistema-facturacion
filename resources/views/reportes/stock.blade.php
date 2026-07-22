@extends('layouts.app')
@section('title', 'Inventario / Stock')

@push('styles')
@include('partials.premium-ui')
<style>
.filter-card > .ui-card-accent {
    height: 5px;
    border-radius: 1.2rem 1.2rem 0 0;
}
.filter-card .ui-input:focus,
.filter-card .ui-select:focus {
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
    .filter-card .ui-input,
    .filter-card .ui-select {
        min-width: 100%;
    }
}
#stockTable thead th {
    border-bottom: 2px solid #e2e8f0;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #64748b;
    padding: 14px 12px;
    background: #f8fafc;
}
#stockTable tbody td {
    padding: 12px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    font-size: .88rem;
}
#stockTable tbody tr { transition: background .15s; }
#stockTable tbody tr:hover { background: rgba(139,92,246,.04); }
#stockTable tfoot td {
    padding: 14px 12px;
    border-top: 2px solid #e2e8f0;
    background: #f8fafc;
}
body.dark-mode #stockTable thead th {
    background: rgba(15,23,42,.6);
    border-bottom-color: #334155;
    color: #94a3b8;
}
body.dark-mode #stockTable tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
body.dark-mode #stockTable tbody tr:hover { background: rgba(139,92,246,.08); }
body.dark-mode #stockTable tfoot td {
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
                    <h2 class="ui-header-title">Inventario / Stock</h2>
                    <div class="ui-header-meta">{{ $totalProductos }} producto(s) &middot; {{ $bajoStock }} bajo stock &middot; {{ $sinStock }} sin stock</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('reportes.stock.csv', request()->all()) }}" class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-download me-1"></i> CSV</a>
                <a href="{{ route('reportes.stock.pdf', request()->all()) }}" class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-file-pdf me-1"></i> PDF</a>
                <a href="{{ route('reportes.index') }}" class="ui-btn ui-btn-ghost rounded-pill"><i class="bi bi-grid me-1"></i> Reportes</a>
            </div>
        </div>
    </div>

    <div class="ui-card filter-card mb-4">
        <div class="ui-card-accent"></div>
        <div class="px-4 py-3">
            <form method="GET" class="row gx-3 gy-3 align-items-end">
                <div class="col-auto">
                    <label class="ui-label small fw-semibold mb-1"><i class="bi bi-filter text-primary me-1"></i>Filtro</label>
                    <select name="filtro" class="ui-select">
                        <option value="todos" {{ $filtro === 'todos' ? 'selected' : '' }}>Todos los productos</option>
                        <option value="bajo_stock" {{ $filtro === 'bajo_stock' ? 'selected' : '' }}>Stock bajo</option>
                        <option value="sin_stock" {{ $filtro === 'sin_stock' ? 'selected' : '' }}>Sin stock</option>
                    </select>
                </div>
                <div class="col-auto">
                    <label class="ui-label small fw-semibold mb-1"><i class="bi bi-search text-primary me-1"></i>Buscar</label>
                    <input type="text" name="buscar" class="ui-input" placeholder="Buscar producto..." value="{{ $buscar }}">
                </div>
                <div class="col-auto">
                    <label class="ui-label small fw-semibold mb-1 d-sm-block d-none">&nbsp;</label>
                    <button class="ui-btn ui-btn-solid rounded-pill px-4 shadow-sm"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="ui-stat text-center p-3"><div class="ui-stat-label">Total Productos</div><div class="ui-stat-value">{{ $totalProductos }}</div></div></div>
        <div class="col-md-3"><div class="ui-stat text-center p-3"><div class="ui-stat-label">Valor Inventario</div><div class="ui-stat-value text-primary">RD$ {{ number_format($totalValorInventario, 2) }}</div></div></div>
        <div class="col-md-3"><div class="ui-stat text-center p-3"><div class="ui-stat-label">Stock Bajo</div><div class="ui-stat-value text-warning">{{ $bajoStock }}</div></div></div>
        <div class="col-md-3"><div class="ui-stat text-center p-3"><div class="ui-stat-label">Sin Stock</div><div class="ui-stat-value text-danger">{{ $sinStock }}</div></div></div>
    </div>

    <div class="ui-card overflow-hidden">
        <div class="table-responsive px-3 py-3">
            <table id="stockTable" class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">Código</th><th>Producto</th>
                        <th class="text-end">Stock</th><th class="text-end">Mínimo</th><th class="text-end">Costo</th><th class="text-end">Precio</th><th class="text-end pe-4">Valor Inv.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $p)
                        @php
                            $estado = $p->stock <= 0 ? 'danger' : ($p->stock <= ($p->stock_minimo ?? 0) ? 'warning' : 'success');
                            $label = $p->stock <= 0 ? 'Sin Stock' : ($p->stock <= ($p->stock_minimo ?? 0) ? 'Stock Bajo' : 'Disponible');
                        @endphp
                        <tr>
                            <td class="ps-4 font-monospace small">{{ $p->codigo_barras ?? $p->referencia ?? '-' }}</td>
                            <td><span class="fw-semibold small">{{ $p->nombre }}</span></td>
                            <td class="text-end"><span class="badge bg-{{ $estado }} bg-opacity-10 text-{{ $estado }} rounded-pill">{{ $p->stock }}</span></td>
                            <td class="text-end">{{ $p->stock_minimo ?? 0 }}</td>
                            <td class="text-end">RD$ {{ number_format($p->precio_compra ?? 0, 2) }}</td>
                            <td class="text-end">RD$ {{ number_format($p->precio ?? 0, 2) }}</td>
                            <td class="text-end pe-4 fw-bold">RD$ {{ number_format($p->stock * ($p->precio_compra ?? 0), 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1"></i><p class="mt-2 mb-0">No hay productos</p></td></tr>
                    @endforelse
                </tbody>
                <tfoot class="fw-bold">
                    <tr>
                        <td colspan="6" class="ps-4 py-3 text-end text-uppercase small">Valor Total Inventario</td>
                        <td class="text-end pe-4 py-3">RD$ {{ number_format($totalValorInventario, 2) }}</td>
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
    $('#stockTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        columnDefs: [
            { orderable: false, targets: [2,3,4,5,6] }
        ],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
@endpush
