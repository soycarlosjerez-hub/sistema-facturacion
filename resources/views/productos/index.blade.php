@extends('layouts.app')

@section('title', 'Gestión de Productos')

@push('styles')
@include('partials.premium-ui')
<style>
.productos-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(99,102,241,.04);
    margin: 0;
    width: 100% !important;
}
.productos-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
}
.productos-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
.productos-table tbody tr:last-child td { border-bottom: none; }
.productos-table tbody tr { transition: background .15s; }
.productos-table tbody tr:hover { background: rgba(99,102,241,.03); }
.producto-img {
    width: 48px; height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #f8fafc;
    flex-shrink: 0;
    transition: transform .2s;
}
tr:hover .producto-img { transform: scale(1.1); }

/* ===== DataTables Wrapper ===== */
#productos-table_wrapper {
    padding: 0;
}

/* Top bar: length + search */
#productos-table_wrapper > .row:first-child {
    padding: 0 1rem;
    margin-bottom: 0.5rem;
}
#productos-table_wrapper .dataTables_length {
    font-size: .85rem;
    color: #64748b;
}
#productos-table_wrapper .dataTables_length label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
    font-weight: 500;
}
#productos-table_wrapper .dataTables_length select {
    border-radius: .5rem;
    border: 1.5px solid #e2e8f0;
    padding: 0.35rem 2rem 0.35rem 0.75rem;
    font-size: .85rem;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E") no-repeat right 0.75rem center;
    appearance: none;
    -webkit-appearance: none;
    cursor: pointer;
    transition: border-color .2s;
}
#productos-table_wrapper .dataTables_length select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,.1);
    outline: none;
}
#productos-table_wrapper .dataTables_filter {
    text-align: right;
}
#productos-table_wrapper .dataTables_filter label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
    font-weight: 500;
    font-size: .85rem;
    color: #64748b;
}
#productos-table_wrapper .dataTables_filter input {
    border-radius: 2rem;
    border: 1.5px solid #e2e8f0;
    padding: 0.45rem 1rem 0.45rem 2.2rem;
    font-size: .85rem;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2394a3b8' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242.156a5 5 0 1 1 0-10 5 5 0 0 1 0 10z'/%3E%3C/svg%3E") no-repeat 0.75rem center;
    width: 240px;
    max-width: 100%;
    transition: all .2s;
}
#productos-table_wrapper .dataTables_filter input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,.1);
    outline: none;
    width: 280px;
}

/* Table container */
#productos-table_wrapper .dataTables_scroll {
    overflow: visible;
}
#productos-table_wrapper .dataTables_scrollHead {
    overflow: visible !important;
}
#productos-table_wrapper .dataTables_scrollHeadInner {
    width: 100% !important;
    padding-right: 0 !important;
}
#productos-table_wrapper .dataTables_scrollBody {
    overflow: visible !important;
}

/* Bottom bar: info + paginate */
#productos-table_wrapper > .row:last-child {
    padding: 0 1rem;
    margin-top: 0;
}
#productos-table_wrapper .dataTables_info {
    font-size: .8rem;
    color: #64748b;
    padding: 0.75rem 0;
    font-weight: 500;
}
#productos-table_wrapper .dataTables_paginate {
    padding: 0.5rem 0;
    text-align: right !important;
}
#productos-table_wrapper .dataTables_paginate .paginate_button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 0.6rem;
    margin: 0 2px;
    border: 1.5px solid #e2e8f0;
    border-radius: 0.5rem;
    background: #fff;
    color: #475569;
    font-size: .85rem;
    font-weight: 500;
    cursor: pointer;
    transition: all .15s;
    text-decoration: none;
    line-height: 1;
}
#productos-table_wrapper .dataTables_paginate .paginate_button:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #1e293b;
}
#productos-table_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, #3b82f6, #6366f1) !important;
    border-color: #3b82f6 !important;
    color: #fff !important;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(59,130,246,.25);
}
#productos-table_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: linear-gradient(135deg, #2563eb, #4f46e5) !important;
}
#productos-table_wrapper .dataTables_paginate .paginate_button.disabled {
    opacity: 0.4;
    cursor: not-allowed;
    background: transparent;
    border-color: #e2e8f0;
    color: #94a3b8;
}

/* Responsive child rows */
table.dataTable > tbody > tr.child {
    background: #f8fafc;
}
table.dataTable > tbody > tr.child ul {
    margin: 0;
    padding: 0.5rem 0;
    list-style: none;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem 1.5rem;
}
table.dataTable > tbody > tr.child ul li {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: .85rem;
    color: #475569;
}
table.dataTable > tbody > tr.child ul li .child-label {
    font-weight: 600;
    color: #64748b;
    min-width: 100px;
}
table.dataTable > tbody > tr.child ul li .child-value {
    color: #1e293b;
}

/* Dtr control arrow */
table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control,
table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control {
    position: relative;
    padding-left: 2.5rem;
    cursor: pointer;
}
table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control::before,
table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control::before {
    top: 50%;
    transform: translateY(-50%);
    left: 0.75rem;
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 6px solid #94a3b8;
    transition: transform .2s, border-color .2s;
}
table.dataTable.dtr-inline.collapsed > tbody > tr.parent > td.dtr-control::before,
table.dataTable.dtr-inline.collapsed > tbody > tr.parent > th.dtr-control::before {
    border-top: none;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-bottom: 6px solid #3b82f6;
}

/* No results */
.dataTables_empty {
    text-align: center !important;
    padding: 3rem 1rem !important;
    color: #94a3b8;
}

/* ===== Dark Mode ===== */
body.dark-mode .productos-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-bottom-color: #1e293b;
}
body.dark-mode .productos-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
body.dark-mode .productos-table tbody tr:hover {
    background: rgba(99,102,241,.05);
}
body.dark-mode #productos-table_wrapper .dataTables_length select {
    background-color: #1e293b;
    color: #f1f5f9;
    border-color: #334155;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2394a3b8' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
}
body.dark-mode #productos-table_wrapper .dataTables_filter input {
    background-color: #1e293b;
    color: #f1f5f9;
    border-color: #334155;
}
body.dark-mode #productos-table_wrapper .dataTables_filter input::placeholder {
    color: #64748b;
}
body.dark-mode #productos-table_wrapper .dataTables_info {
    color: #64748b;
}
body.dark-mode #productos-table_wrapper .dataTables_paginate .paginate_button {
    background: #1e293b;
    border-color: #334155;
    color: #94a3b8;
}
body.dark-mode #productos-table_wrapper .dataTables_paginate .paginate_button:hover {
    background: #334155;
    border-color: #475569;
    color: #f1f5f9;
}
body.dark-mode #productos-table_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, #3b82f6, #6366f1) !important;
    border-color: #3b82f6 !important;
    color: #fff !important;
}
body.dark-mode #productos-table_wrapper .dataTables_paginate .paginate_button.disabled {
    background: transparent;
    border-color: #1e293b;
    color: #475569;
}
body.dark-mode table.dataTable > tbody > tr.child {
    background: #0f172a;
}
body.dark-mode table.dataTable > tbody > tr.child ul li {
    color: #cbd5e1;
}
body.dark-mode table.dataTable > tbody > tr.child ul li .child-label {
    color: #94a3b8;
}
body.dark-mode table.dataTable > tbody > tr.child ul li .child-value {
    color: #f1f5f9;
}
body.dark-mode .producto-img {
    border-color: #1e293b;
}
body.dark-mode #productos-table_wrapper .dataTables_length label {
    color: #94a3b8;
}
body.dark-mode #productos-table_wrapper .dataTables_filter label {
    color: #94a3b8;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Catálogo de Productos</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-grid me-1"></i>
                        Administración de inventario, precios y existencias
                    </small>
                </div>
            </div>
            <div>
                @can('productos.create')
                <a href="{{ route('productos.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Producto
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="premium-card mb-4" style="animation-delay:.1s;">
        <div class="card-accent blue"></div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('productos.index') }}" id="filtros-form" class="row g-2 align-items-end">
                <div class="col-lg-3">
                    <label class="form-label small fw-bold text-muted">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="nombre" id="busqueda-producto" class="form-control" placeholder="Nombre, código o SKU..." value="{{ request('nombre') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <label class="form-label small fw-bold text-muted">Stock</label>
                    <select name="stock_status" id="filter-stock" class="form-select">
                        <option value="">Todos</option>
                        <option value="critical" {{ request('stock_status') == 'critical' ? 'selected' : '' }}>Crítico (≤ 5)</option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Bajo (6 - 15)</option>
                        <option value="ok" {{ request('stock_status') == 'ok' ? 'selected' : '' }}>Normal (> 15)</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="form-label small fw-bold text-muted">Estado</label>
                    <select name="activo" id="filter-activo" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="form-label small fw-bold text-muted">Precio Mín.</label>
                    <input type="number" name="precio_min" id="filter-precio-min" class="form-control" placeholder="RD$ 0.00" value="{{ request('precio_min') }}" step="0.01" min="0">
                </div>
                <div class="col-lg-2">
                    <label class="form-label small fw-bold text-muted">Precio Máx.</label>
                    <input type="number" name="precio_max" id="filter-precio-max" class="form-control" placeholder="RD$ 0.00" value="{{ request('precio_max') }}" step="0.01" min="0">
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <a href="{{ route('productos.import') }}" class="btn btn-light rounded-pill shadow-sm fw-medium">
                            <i class="bi bi-upload me-1"></i> Importar CSV
                        </a>
                        <a href="{{ route('productos.exportar', request()->all()) }}" class="btn btn-light rounded-pill shadow-sm fw-medium">
                            <i class="bi bi-file-excel me-1"></i> Excel
                        </a>
                        <a href="{{ route('productos.pdf', request()->all()) }}" class="btn btn-light rounded-pill shadow-sm fw-medium">
                            <i class="bi bi-file-pdf me-1"></i> PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card" style="animation-delay:.15s;">
        <div class="card-accent blue"></div>
        <div class="card-body p-0">
            <div id="productos-table_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                <table id="productos-table" class="table productos-table nowrap no-footer" role="grid" style="width:100%">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width:50px;">#</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th class="text-end">Venta &amp; Costos</th>
                            <th class="text-center">Rentabilidad</th>
                            <th class="text-center">Inventario</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    const productos = @json($productos);
    const csrfToken = '{{ csrf_token() }}';
    const canEdit = {{ auth()->user()->can('productos.edit') ? 'true' : 'false' }};
    const canToggle = {{ auth()->user()->can('productos.toggle') ? 'true' : 'false' }};
    const canDelete = {{ auth()->user()->can('productos.delete') ? 'true' : 'false' }};

    const table = $('#productos-table').DataTable({
        data: productos,
        columns: [
            {
                data: 'id',
                className: 'text-center',
                orderable: true,
                searchable: false,
                type: 'num',
                width: '50px',
                render: function(data, type) {
                    if (type === 'display' || type === 'filter') {
                        return '<span class="text-muted fw-bold">' + data + '</span>';
                    }
                    return data;
                }
            },
            {
                data: null,
                orderable: true,
                searchable: true,
                render: function(data) {
                    const img = data.imagen_url || '{{ asset("img/producto-placeholder.svg") }}';
                    const nombre = escapeHtml(data.nombre || '');
                    const codigo = escapeHtml(data.codigo_barras || 'Sin código');
                    return '<div class="d-flex align-items-center">' +
                        '<img src="' + img + '" class="producto-img me-3 shadow-sm" alt="' + nombre + '">' +
                        '<div class="text-truncate">' +
                            '<div class="fw-bold fs-6 text-truncate" style="color:#1e293b;" title="' + nombre + '">' + nombre + '</div>' +
                            '<div class="text-muted small"><i class="bi bi-upc-scan me-1"></i>' + codigo + '</div>' +
                        '</div>' +
                    '</div>';
                }
            },
            {
                data: 'categoria',
                defaultContent: '<span class="text-muted small">—</span>',
                render: function(data) {
                    if (!data) return '<span class="text-muted small">—</span>';
                    return '<span class="badge rounded-pill" style="background:rgba(99,102,241,.1);color:#4f46e5;font-weight:600;">' +
                        '<i class="bi bi-tags me-1"></i>' + escapeHtml(data.nombre) +
                    '</span>';
                }
            },
            {
                data: null,
                className: 'text-end',
                render: function(data) {
                    const precio = parseFloat(data.precio || 0).toFixed(2);
                    const costo = parseFloat(data.precio_compra || 0).toFixed(2);
                    const itbis = parseFloat(data.itbis_porcentaje || 18).toFixed(2);
                    return '<div class="fw-bold fs-6" style="color:#4f46e5;">RD$ ' + precio + '</div>' +
                        '<div class="text-muted" style="font-size:.75rem;">Costo: RD$ ' + costo + '</div>' +
                        '<div class="text-muted" style="font-size:.7rem;">ITBIS: ' + itbis + '%</div>';
                }
            },
            {
                data: null,
                className: 'text-center',
                orderable: false,
                render: function(data) {
                    const precio = parseFloat(data.precio || 0);
                    const costo = parseFloat(data.precio_compra || 0);
                    const profit = precio - costo;
                    const margen = costo > 0 ? (((precio - costo) / costo) * 100).toFixed(1) : '0.0';
                    const cls = profit >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger';
                    const sign = profit >= 0 ? '+' : '';
                    return '<div class="d-flex flex-column align-items-center gap-1">' +
                        '<span class="badge rounded-pill ' + cls + '">' + sign + 'RD$ ' + profit.toFixed(2) + '</span>' +
                        '<span class="text-muted small fw-medium">' + margen + '% Margen</span>' +
                    '</div>';
                }
            },
            {
                data: 'stock',
                className: 'text-center',
                render: function(data) {
                    const stock = parseInt(data || 0);
                    if (stock <= 5) {
                        return '<span class="badge bg-danger rounded-pill">' +
                            '<i class="bi bi-exclamation-triangle-fill me-1"></i> ' + stock + ' unid.' +
                        '</span>';
                    }
                    if (stock <= 15) {
                        return '<span class="badge bg-warning text-dark rounded-pill">' +
                            '<i class="bi bi-exclamation-circle-fill me-1"></i> ' + stock + ' unid.' +
                        '</span>';
                    }
                    return '<span class="badge rounded-pill" style="background:rgba(34,197,94,.1);color:#16a34a;font-weight:600;">' +
                        '<i class="bi bi-check-circle-fill me-1"></i> ' + stock + ' unid.' +
                    '</span>';
                }
            },
            {
                data: 'activo',
                className: 'text-center',
                orderable: false,
                searchable: false,
                render: function(data) {
                    const activo = !!data;
                    const cls = activo ? 'success' : 'secondary';
                    const icon = activo ? 'check-circle-fill' : 'x-circle-fill';
                    const label = activo ? 'Activo' : 'Inactivo';
                    return '<span class="badge rounded-pill bg-' + cls + ' bg-opacity-10 text-' + cls + ' fw-semibold" style="font-size:.75rem;">' +
                        '<i class="bi bi-' + icon + ' me-1"></i>' + label +
                    '</span>';
                }
            },
            {
                data: null,
                className: 'text-end',
                orderable: false,
                searchable: false,
                render: function(data) {
                    let actions = '<div class="d-flex justify-content-end gap-1">';
                    actions += '<a href="/productos/' + data.id + '" class="premium-btn-edit" title="Ver" style="background:rgba(59,130,246,.1);color:#3b82f6;border-color:rgba(59,130,246,.2);">' +
                        '<i class="bi bi-eye"></i></a>';
                    if (canEdit) {
                        actions += '<a href="/productos/' + data.id + '/edit" class="premium-btn-edit" title="Editar">' +
                            '<i class="bi bi-pencil"></i></a>';
                    }
                    if (canToggle) {
                        const activo = !!data.activo;
                        actions += '<button type="button" class="premium-btn-edit toggle-activo" title="' + (activo ? 'Desactivar' : 'Activar') + '" data-id="' + data.id + '" data-nombre="' + escapeHtml(data.nombre) + '" data-activo="' + (activo ? '1' : '0') + '" style="background:rgba(' + (activo ? '239,68,68' : '34,197,94') + ',.1);color:' + (activo ? '#ef4444' : '#22c55e') + ';border-color:rgba(' + (activo ? '239,68,68' : '34,197,94') + ',.2);">' +
                            '<i class="bi bi-' + (activo ? 'pause-circle' : 'play-circle') + '"></i></button>';
                    }
                    if (canDelete) {
                        actions += '<button type="button" class="premium-btn-delete border-0 btn-delete-producto" title="Eliminar" data-id="' + data.id + '" data-nombre="' + escapeHtml(data.nombre) + '">' +
                            '<i class="bi bi-trash"></i></button>';
                    }
                    actions += '</div>';
                    return actions;
                }
            }
        ],
        language: {
            search: '',
            lengthMenu: '_MENU_',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ productos',
            infoEmpty: 'No hay productos',
            infoFiltered: '(de _MAX_ totales)',
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>'
            },
            zeroRecords: '<div class="text-center py-5"><i class="bi bi-box-seam d-block mb-2" style="font-size:2.5rem;color:#cbd5e1;"></i><p class="fw-semibold mb-1" style="color:#475569;">No se encontraron productos</p><p class="text-muted small mb-0">Intenta ajustar los filtros de búsqueda.</p></div>'
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Todos']],
        order: [[1, 'asc']],
        responsive: {
            details: {
                type: 'column',
                target: 'tr',
                renderer: function(api, rowIdx, columns) {
                    let data = '';
                    columns.forEach(function(col) {
                        if (col.hidden) {
                            data += '<li>' +
                                '<span class="child-label">' + col.title + '</span>' +
                                '<span class="child-value">' + col.data + '</span>' +
                            '</li>';
                        }
                    });
                    return data ? $('<ul class="d-flex flex-wrap gap-2 p-2 mb-0">' + data + '</ul>') : false;
                }
            }
        },
        dom: '<"row px-3 pt-2"<"col-sm-6"l><"col-sm-6"f>>' +
             '<"row"<"col-12"tr>>' +
             '<"row px-3 pb-2"<"col-sm-5"i><"col-sm-7"p>>'
    });

    // Filter form
    $('#filtros-form').on('submit', function(e) {
        e.preventDefault();
        const nombre = $('#busqueda-producto').val();
        const stockStatus = $('#filter-stock').val();
        const activo = $('#filter-activo').val();
        const precioMin = parseFloat($('#filter-precio-min').val()) || 0;
        const precioMax = parseFloat($('#filter-precio-max').val()) || Infinity;

        table.search(nombre).draw();

        $.fn.dataTable.ext.search.push(function(settings, data) {
            const stock = parseInt(data[5]) || 0;
            const precioStr = data[3].replace(/[^0-9.]/g, '');
            const precio = parseFloat(precioStr) || 0;
            const isActivo = (data[6] || '').indexOf('Activo') !== -1;

            if (stockStatus === 'critical' && stock > 5) return false;
            if (stockStatus === 'low' && (stock < 6 || stock > 15)) return false;
            if (stockStatus === 'ok' && stock <= 15) return false;
            if (precio < precioMin) return false;
            if (precio > precioMax) return false;
            if (activo === '1' && !isActivo) return false;
            if (activo === '0' && isActivo) return false;

            return true;
        });

        table.draw();
        $.fn.dataTable.ext.search.pop();
    });

    // Real-time search
    let searchTimeout;
    $('#busqueda-producto').on('input', function() {
        clearTimeout(searchTimeout);
        const val = $(this).val();
        searchTimeout = setTimeout(function() {
            table.search(val).draw();
        }, 300);
    });

    // Toggle activo
    $(document).on('click', '.toggle-activo', function() {
        const btn = $(this);
        const id = btn.data('id');
        const nombre = btn.data('nombre');
        const activo = btn.data('activo') == '1';
        const accion = activo ? 'desactivar' : 'activar';

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '¿' + accion.charAt(0).toUpperCase() + accion.slice(1) + ' producto?',
                text: '¿Estás seguro de ' + accion + ' "' + nombre + '"?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, ' + accion,
                cancelButtonText: 'Cancelar',
                confirmButtonColor: activo ? '#ef4444' : '#22c55e'
            }).then(function(result) {
                if (result.isConfirmed) {
                    toggleProducto(id, btn);
                }
            });
        } else {
            toggleProducto(id, btn);
        }
    });

    function toggleProducto(id, btn) {
        var formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('_token', csrfToken);

        fetch('/productos/' + id + '/toggle', {
            method: 'POST',
            body: formData
        })
        .then(function(r) {
            if (!r.ok) {
                throw new Error('El servidor respondió con estado ' + r.status + '. Verifica que tengas permiso de edición.');
            }
            var ct = r.headers.get('content-type') || '';
            if (ct.indexOf('application/json') === -1) {
                throw new Error('Respuesta inesperada del servidor (no es JSON). Es posible que la sesión haya expirado.');
            }
            return r.json();
        })
        .then(function(data) {
            if (data.success) {
                var row = btn.closest('tr');
                if (row) {
                    var estadoCell = row.querySelectorAll('td')[6];
                    if (estadoCell) {
                        var cls = data.activo ? 'success' : 'secondary';
                        var icon = data.activo ? 'check-circle-fill' : 'x-circle-fill';
                        var label = data.activo ? 'Activo' : 'Inactivo';
                        estadoCell.innerHTML = '<span class="badge rounded-pill bg-' + cls + ' bg-opacity-10 text-' + cls + ' fw-semibold" style="font-size:.75rem;">' +
                            '<i class="bi bi-' + icon + ' me-1"></i>' + label +
                        '</span>';
                    }
                    var actionsCell = row.querySelectorAll('td')[7];
                    if (actionsCell) {
                        var toggleBtn = actionsCell.querySelector('.toggle-activo');
                        if (toggleBtn) {
                            toggleBtn.dataset.activo = data.activo ? '1' : '0';
                            toggleBtn.title = data.activo ? 'Desactivar' : 'Activar';
                            toggleBtn.style.background = data.activo ? 'rgba(239,68,68,.1)' : 'rgba(34,197,94,.1)';
                            toggleBtn.style.color = data.activo ? '#ef4444' : '#22c55e';
                            toggleBtn.style.borderColor = data.activo ? 'rgba(239,68,68,.2)' : 'rgba(34,197,94,.2)';
                            toggleBtn.innerHTML = '<i class="bi bi-' + (data.activo ? 'pause-circle' : 'play-circle') + '"></i>';
                        }
                    }
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Listo', text: 'Producto ' + (data.activo ? 'activado' : 'desactivado') + ' correctamente.', timer: 1500, showConfirmButton: false });
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'No se pudo actualizar el producto.' });
                }
            }
        })
        .catch(function(err) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'No se pudo conectar con el servidor.' });
            }
        });
    }

    // Delete producto via AJAX
    $(document).on('click', '.btn-delete-producto', function() {
        var btn = $(this);
        var id = btn.data('id');
        var nombre = btn.data('nombre');

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '¿Eliminar producto?',
                text: 'Se eliminará "' + nombre + '". Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#ef4444'
            }).then(function(result) {
                if (result.isConfirmed) {
                    deleteProducto(id, btn);
                }
            });
        } else {
            if (confirm('¿Eliminar "' + nombre + '"? Esta acción no se puede deshacer.')) {
                deleteProducto(id, btn);
            }
        }
    });

    function deleteProducto(id, btn) {
        var formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', csrfToken);

        var row = btn.closest('tr');
        if (row) row.style.opacity = '0.5';

        fetch('/productos/' + id + '/delete-ajax', {
            method: 'POST',
            body: formData
        })
        .then(function(r) {
            if (!r.ok) {
                throw new Error('El servidor respondió con estado ' + r.status);
            }
            var ct = r.headers.get('content-type') || '';
            if (ct.indexOf('application/json') === -1) {
                throw new Error('Respuesta inesperada del servidor.');
            }
            return r.json();
        })
        .then(function(data) {
            if (data.success) {
                if (row && row.closest('tbody')) {
                    table.row(row).remove().draw();
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Eliminado', text: data.message, timer: 1500, showConfirmButton: false });
                }
            } else {
                if (row) row.style.opacity = '1';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'No se pudo eliminar', text: data.message });
                }
            }
        })
        .catch(function(err) {
            if (row) row.style.opacity = '1';
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'No se pudo conectar con el servidor.' });
            }
        });
    }

    function escapeHtml(str) {
        return String(str || '').replace(/[&<>"']/g, function(c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }
});
</script>
@endpush
@endsection
