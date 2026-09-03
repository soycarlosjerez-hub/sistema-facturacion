@extends('layouts.app')

@section('title', 'Gestión de Productos')

@push('styles')
@include('partials.premium-ui')
<style>
:root {
    --dt-primary: #6366f1;
    --dt-indigo: #6366f1;
    --dt-violet: #4f46e5;
    --dt-success: #22c55e;
    --dt-success-dark: #16a34a;
    --dt-danger: #ef4444;
    --dt-warning: #f59e0b;
    --dt-gray-50: #f8fafc;
    --dt-gray-100: #f1f5f9;
    --dt-gray-200: #e2e8f0;
    --dt-gray-300: #cbd5e1;
    --dt-gray-400: #94a3b8;
    --dt-gray-500: #64748b;
    --dt-gray-600: #475569;
    --dt-gray-700: #334155;
    --dt-gray-800: #1e293b;
    --dt-gray-900: #0f172a;
    --dt-radius: 0.5rem;
    --dt-shadow: 0 2px 8px rgba(99,102,241,.25);
    --dt-transition: 0.15s;
}

/* ===== Base Table ===== */
.productos-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(99,102,241,.04);
    width: 100%;
    margin: 0;
    table-layout: auto;
}
.productos-table thead th {
    background: rgba(241,245,249,.8);
    color: var(--dt-gray-500);
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 2px solid var(--dt-gray-200);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.productos-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid var(--dt-gray-100);
    vertical-align: middle;
    font-size: .9rem;
    overflow: hidden;
    max-width: none;
}
.productos-table td.text-center,
.productos-table td.text-end {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.productos-table tbody tr:last-child td { border-bottom: none; }
.productos-table tbody tr { transition: background var(--dt-transition); }
.productos-table tbody tr:hover { background: rgba(99,102,241,.03); }

.producto-img {
    width: 48px; height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--dt-gray-50);
    flex-shrink: 0;
    transition: transform var(--dt-transition);
}
tr:hover .producto-img { transform: scale(1.1); }

.text-brand { color: var(--dt-violet); }
.text-xs { font-size: .75rem; }
.text-xxs { font-size: .7rem; }
.fs-7 { font-size: .85rem; }

/* ===== DataTables Wrapper ===== */
#productos-table_wrapper { padding: 0; overflow: hidden; }
#productos-table_wrapper .dataTables_scrollBody { overflow-x: hidden !important; }
#productos-table_wrapper .dataTables_scrollHead { overflow-x: hidden !important; }
#productos-table_wrapper .dataTables_scroll { overflow-x: hidden !important; }
#productos-table_wrapper > .row:first-child {
    padding: 0 1rem;
    margin-bottom: 0.5rem;
}
#productos-table_wrapper .dataTables_length {
    font-size: .85rem;
    color: var(--dt-gray-500);
}
#productos-table_wrapper .dataTables_length label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
    font-weight: 500;
}
#productos-table_wrapper .dataTables_length select {
    border-radius: var(--dt-radius);
    border: 1.5px solid var(--dt-gray-200);
    padding: 0.35rem 2rem 0.35rem 0.75rem;
    font-size: .85rem;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E") no-repeat right 0.75rem center;
    appearance: none;
    cursor: pointer;
    transition: border-color var(--dt-transition);
}
#productos-table_wrapper .dataTables_length select:focus {
    border-color: var(--dt-primary);
    box-shadow: 0 0 0 3px rgba(59,130,246,.1);
    outline: none;
}
#productos-table_wrapper .dataTables_filter { text-align: right; }
#productos-table_wrapper .dataTables_filter label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
    font-weight: 500;
    font-size: .85rem;
    color: var(--dt-gray-500);
}
#productos-table_wrapper .dataTables_filter input {
    border-radius: 2rem;
    border: 1.5px solid var(--dt-gray-200);
    padding: 0.45rem 1rem 0.45rem 2.2rem;
    font-size: .85rem;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2394a3b8' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242.156a5 5 0 1 1 0-10 5 5 0 0 1 0 10z'/%3E%3C/svg%3E") no-repeat 0.75rem center;
    width: 240px;
    max-width: 100%;
    transition: all var(--dt-transition);
}
#productos-table_wrapper .dataTables_filter input:focus {
    border-color: var(--dt-primary);
    box-shadow: 0 0 0 3px rgba(59,130,246,.1);
    outline: none;
    width: 280px;
}
#productos-table_wrapper .dataTables_scroll,
#productos-table_wrapper .dataTables_scrollHead,
#productos-table_wrapper .dataTables_scrollBody {
    overflow: visible;
}
#productos-table_wrapper .dataTables_scrollHeadInner {
    width: 100%;
    padding-right: 0;
}
#productos-table_wrapper > .row:last-child {
    padding: 0 1rem;
    margin-top: 0;
}
#productos-table_wrapper .dataTables_info {
    font-size: .8rem;
    color: var(--dt-gray-500);
    padding: 0.75rem 0;
    font-weight: 500;
}
#productos-table_wrapper .dataTables_paginate {
    padding: 0.5rem 0;
    text-align: right;
}
.paginate_button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 0.6rem;
    margin: 0 2px;
    border: 1.5px solid var(--dt-gray-200);
    border-radius: var(--dt-radius);
    background: #fff;
    color: var(--dt-gray-600);
    font-size: .85rem;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--dt-transition);
    text-decoration: none;
    line-height: 1;
}
.paginate_button:hover {
    background: var(--dt-gray-50);
    border-color: var(--dt-gray-300);
    color: var(--dt-gray-800);
}
.paginate_button.current {
    background: linear-gradient(135deg, var(--dt-primary), var(--dt-indigo));
    border-color: var(--dt-primary);
    color: #fff;
    font-weight: 700;
    box-shadow: var(--dt-shadow);
}
.paginate_button.current:hover {
    background: linear-gradient(135deg, #2563eb, #4f46e5);
}
.paginate_button.disabled {
    opacity: 0.4;
    cursor: not-allowed;
    background: transparent;
    border-color: var(--dt-gray-200);
    color: var(--dt-gray-400);
}

/* ===== Responsive child rows ===== */
table.dataTable > tbody > tr.child {
    background: var(--dt-gray-50);
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
    color: var(--dt-gray-600);
}
.child-label {
    font-weight: 600;
    color: var(--dt-gray-500);
    min-width: 100px;
}
.child-value {
    color: var(--dt-gray-800);
}
.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control,
.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control {
    position: relative;
    padding-left: 2.5rem;
    cursor: pointer;
}
.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control::before,
.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control::before {
    top: 50%;
    transform: translateY(-50%);
    left: 0.75rem;
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 6px solid var(--dt-gray-400);
    transition: transform var(--dt-transition);
}
.dataTable.dtr-inline.collapsed > tbody > tr.parent > td.dtr-control::before,
.dataTable.dtr-inline.collapsed > tbody > tr.parent > th.dtr-control::before {
    border-top: none;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-bottom: 6px solid var(--dt-primary);
}
.dataTables_empty {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--dt-gray-400);
}

/* ===== Dark Mode ===== */
body.dark-mode .productos-table thead th {
    background: rgba(15,23,42,.5);
    color: var(--dt-gray-400);
    border-bottom-color: var(--dt-gray-800);
}
body.dark-mode .productos-table tbody td {
    border-bottom-color: var(--dt-gray-800);
    color: var(--dt-gray-300);
}
body.dark-mode .productos-table tbody tr:hover {
    background: rgba(99,102,241,.05);
}
body.dark-mode #productos-table_wrapper .dataTables_length select,
body.dark-mode #productos-table_wrapper .dataTables_filter input {
    background-color: var(--dt-gray-800);
    color: var(--dt-gray-100);
    border-color: var(--dt-gray-700);
}
body.dark-mode #productos-table_wrapper .dataTables_length select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2394a3b8' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
}
body.dark-mode #productos-table_wrapper .dataTables_filter input::placeholder { color: var(--dt-gray-500); }
body.dark-mode #productos-table_wrapper .dataTables_info { color: var(--dt-gray-500); }
body.dark-mode .paginate_button {
    background: var(--dt-gray-800);
    border-color: var(--dt-gray-700);
    color: var(--dt-gray-400);
}
body.dark-mode .paginate_button:hover {
    background: var(--dt-gray-700);
    border-color: var(--dt-gray-600);
    color: var(--dt-gray-100);
}
body.dark-mode .paginate_button.current {
    background: linear-gradient(135deg, var(--dt-primary), var(--dt-indigo));
    border-color: var(--dt-primary);
    color: #fff;
}
body.dark-mode .paginate_button.disabled {
    background: transparent;
    border-color: var(--dt-gray-800);
    color: var(--dt-gray-600);
}
body.dark-mode table.dataTable > tbody > tr.child {
    background: var(--dt-gray-900);
}
body.dark-mode table.dataTable > tbody > tr.child ul li { color: var(--dt-gray-300); }
body.dark-mode .child-label { color: var(--dt-gray-400); }
body.dark-mode .child-value { color: var(--dt-gray-100); }
body.dark-mode .producto-img { border-color: var(--dt-gray-800); }
body.dark-mode #productos-table_wrapper .dataTables_length label,
body.dark-mode #productos-table_wrapper .dataTables_filter label { color: var(--dt-gray-400); }

@keyframes pulse-stock {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.85; transform: scale(1.02); }
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#6366f1;--accent-rgb:99,102,241;--accent-hover:#4f46e5;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Catálogo de Productos</h4>
                    <div class="ui-header-meta">Administración de inventario, precios y existencias</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('productos.create')
                <a href="{{ route('productos.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Producto
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('productos.index') }}" id="filtros-form" class="row g-2 align-items-end" role="search" aria-label="Filtros de productos">
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="ui-label small fw-bold text-muted" for="busqueda-producto">Buscar</label>
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="text" name="nombre" id="busqueda-producto" class="ui-input" placeholder="Nombre, código o SKU..." value="{{ request('nombre') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-6 col-sm-3 col-lg-2">
                    <label class="ui-label small fw-bold text-muted" for="filter-stock">Stock</label>
                    <select name="stock_status" id="filter-stock" class="ui-select">
                        <option value="">Todos</option>
                        <option value="ultimas_unidades" {{ request('stock_status') == 'ultimas_unidades' ? 'selected' : '' }}>Últimas unidades (≤2)</option>
                        <option value="critical" {{ request('stock_status') == 'critical' ? 'selected' : '' }}>Crítico (&le; 5)</option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Bajo (6-15)</option>
                        <option value="ok" {{ request('stock_status') == 'ok' ? 'selected' : '' }}>Normal (&gt; 15)</option>
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-lg-2">
                    <label class="ui-label small fw-bold text-muted" for="filter-activo">Estado</label>
                    <select name="activo" id="filter-activo" class="ui-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                <div class="col-6 col-sm-3 col-lg-2">
                    <label class="ui-label small fw-bold text-muted" for="filter-precio-min">Precio Mín.</label>
                    <input type="number" name="precio_min" id="filter-precio-min" class="ui-input" placeholder="RD$ 0.00" value="{{ request('precio_min') }}" step="0.01" min="0">
                </div>
                <div class="col-6 col-sm-3 col-lg-2">
                    <label class="ui-label small fw-bold text-muted" for="filter-precio-max">Precio Máx.</label>
                    <input type="number" name="precio_max" id="filter-precio-max" class="ui-input" placeholder="RD$ 0.00" value="{{ request('precio_max') }}" step="0.01" min="0">
                </div>
                <div class="col-12 col-lg-3 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('productos.index') }}" class="ui-btn ui-btn-ghost"><i class="bi bi-x-lg"></i></a>
                </div>
                <div class="col-12">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('productos.import') }}" class="ui-btn ui-btn-solid rounded-pill shadow-sm fw-medium">
                            <i class="bi bi-upload me-1"></i> Importar CSV
                        </a>
                        <a href="{{ route('productos.exportar', request()->all()) }}" class="ui-btn ui-btn-solid rounded-pill shadow-sm fw-medium">
                            <i class="bi bi-file-excel me-1"></i> Excel
                        </a>
                        <a href="{{ route('productos.pdf', request()->all()) }}" class="ui-btn ui-btn-solid rounded-pill shadow-sm fw-medium">
                            <i class="bi bi-file-pdf me-1"></i> PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-0" style="overflow-x:auto;">
            <div id="productos-table_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                <table id="productos-table" class="table productos-table no-footer" role="grid" aria-label="Listado de productos" style="min-width:800px;">
                    <thead>
                        <tr>
                    <th style="width:40px;" data-label="id">#</th>
                    <th style="width:auto;min-width:200px;" data-label="producto">Producto</th>
                    <th style="width:80px;" data-label="serial_imei">IMEI / Serial</th>
                    <th style="width:80px;" data-label="categoria">Categoría</th>
                    <th style="width:60px;" class="text-center" data-label="tipo">Tipo</th>
                    <th style="width:110px;" class="text-end" data-label="precios">Venta</th>
                    <th style="width:90px;" class="text-center" data-label="rentabilidad">Ganancia</th>
                    <th style="width:75px;" class="text-center" data-label="inventario">Stock</th>
                    <th style="width:60px;" class="text-center" data-label="estado">Estado</th>
                    <th style="width:90px;" class="text-end pe-3" data-label="acciones">Acciones</th>
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
    // ============================================================
    // CONFIG
    // ============================================================
    const API_BASE = '/productos';
    const csrfToken = '{{ csrf_token() }}';
    const canEdit = {{ auth()->user()->can('productos.edit') ? 'true' : 'false' }};
    const canToggle = canEdit;
    const canDelete = {{ auth()->user()->can('productos.delete') ? 'true' : 'false' }};
    
    // Auto-refresh state
    let refreshInterval = null;
    const REFRESH_MS = 120000; // 120 seconds
    let userActive = false;
    let userActivityTimer = null;
    let lastDataHash = '';
    let isSearchTyping = false;

    // Track user activity
    function resetUserActivityTimer() {
        userActive = true;
        clearTimeout(userActivityTimer);
        userActivityTimer = setTimeout(() => { userActive = false; }, 30000);
    }

    $(document).on('mousedown keydown scroll touchstart', function() {
        resetUserActivityTimer();
    });

    // ============================================================
    // HELPERS
    // ============================================================
    async function fetchJSON(url, formData) {
        const res = await fetch(url, { method: 'POST', body: formData });
        if (!res.ok) throw new Error('El servidor respondió con estado ' + res.status + '. Verifica que tengas permiso de edición.');
        const ct = res.headers.get('content-type') || '';
        if (!ct.includes('application/json')) throw new Error('Respuesta inesperada del servidor (no es JSON). Es posible que la sesión haya expirado.');
        return res.json();
    }

    function generateHash(obj) {
        return btoa(JSON.stringify(obj).substring(0, 200));
    }

    function showRefreshToast(changed) {
        if (typeof Swal !== 'undefined') {
            const toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: changed ? '#fef3c7' : '#ffffff',
                color: changed ? '#92400e' : '#64748b',
                icon: 'info'
            });
            
            if (changed) {
                toast.fire({ icon: 'info', title: '⚡ El inventario se actualizó' });
            } else {
                toast.fire({ title: 'Inventario sincronizado' });
            }
        }
    }

    function swalExito(text) {
        if (typeof Swal !== 'undefined') Swal.fire({ icon: 'success', title: 'Listo', text: text, timer: 1500, showConfirmButton: false });
    }
    function swalError(text) {
        if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: text });
    }

    function escapeHtml(str) {
        return String(str || '').replace(/[&<>"']/g, function(c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }

    function renderEstado(activo) {
        const a = !!activo;
        const cls = a ? 'success' : 'secondary';
        const icon = a ? 'check-circle-fill' : 'x-circle-fill';
        const label = a ? 'Activo' : 'Inactivo';
        return '<span class="badge rounded-pill bg-' + cls + ' bg-opacity-10 text-' + cls + ' fw-semibold">' +
            '<i class="bi bi-' + icon + ' me-1"></i>' + label + '</span>';
    }

    function renderStock(stock) {
        const s = parseInt(stock || 0);
        if (s === 0) return '<span class="badge bg-danger rounded-pill"><i class="bi bi-x-octagon-fill me-1"></i> Agotado</span>';
        if (s === 1) return '<span class="badge rounded-pill" style="background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;font-weight:700;animation:pulse-stock 2s infinite;"><i class="bi bi-lightning-charge-fill me-1"></i> ¡Última unidad!</span>';
        if (s === 2) return '<span class="badge rounded-pill" style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;font-weight:700;"><i class="bi bi-lightning-charge-fill me-1"></i> Últimas unidades (' + s + ')</span>';
        if (s <= 5) return '<span class="badge bg-danger rounded-pill"><i class="bi bi-exclamation-triangle-fill me-1"></i> ' + s + ' unid.</span>';
        if (s <= 15) return '<span class="badge bg-warning text-dark rounded-pill"><i class="bi bi-exclamation-circle-fill me-1"></i> ' + s + ' unid.</span>';
        return '<span class="badge rounded-pill" style="background:rgba(34,197,94,.1);color:#16a34a;font-weight:600;"><i class="bi bi-check-circle-fill me-1"></i> ' + s + ' unid.</span>';
    }

    function actualizarToggleBtn($toggleBtn, activo) {
        $toggleBtn
            .attr('data-activo', activo ? '1' : '0')
            .attr('title', activo ? 'Desactivar' : 'Activar')
            .css({
                background: activo ? 'rgba(239,68,68,.1)' : 'rgba(34,197,94,.1)',
                color: activo ? '#ef4444' : '#22c55e',
                borderColor: activo ? 'rgba(239,68,68,.2)' : 'rgba(34,197,94,.2)'
            })
            .html('<i class="bi bi-' + (activo ? 'pause-circle' : 'play-circle') + '"></i>');
    }

    // ============================================================
    // DATATABLE WITH AJAX
    // ============================================================
    const table = $('#productos-table').DataTable({
        ajax: {
            url: '{{ route("productos.ajax") }}',
            type: 'GET',
            dataSrc: function(json) {
                const newHash = generateHash(json.data);
                if (lastDataHash && newHash !== lastDataHash) {
                    showRefreshToast(true);
                }
                lastDataHash = newHash;
                
                // Smooth transition: fade out old rows briefly
                $('tbody tr').css('transition', 'opacity 0.3s ease');
                $('tbody tr').animate({ opacity: 0.7 }, 150, function() {
                    $(this).css('opacity', 1);
                });
                
                return json.data;
            },
            error: function(xhr, error, thrown) {
                console.error('DataTable AJAX error:', error, thrown);
            }
        },
        columns: [
            {
                data: 'id',
                className: 'text-center ps-4',
                orderable: true,
                searchable: false,
                type: 'num',
                width: '50px',
                render: function(data, type) {
                    if (type === 'display' || type === 'filter') return '<span class="text-muted fw-bold">' + data + '</span>';
                    return data;
                }
            },
            {
                data: null,
                orderable: true,
                searchable: true,
                width: '200px',
                render: function(data) {
                    const img = data.imagen_url || '{{ asset("img/producto-placeholder.svg") }}';
                    const nombre = escapeHtml(data.nombre || '');
                    const codigo = escapeHtml(data.codigo_barras || 'Sin código');
                    return '<div class="d-flex align-items-center" style="min-width:0">' +
                        '<img src="' + img + '" class="producto-img me-3 shadow-sm" alt="' + nombre + '" style="flex-shrink:0">' +
                        '<div style="min-width:0;max-width:100%">' +
                            '<div class="fw-bold fs-7 text-brand" style="white-space:normal;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;word-break:break-word;line-height:1.3;">' + nombre + '</div>' +
                            '<div class="text-muted small" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><i class="bi bi-upc-scan me-1"></i>' + codigo + '</div>' +
                        '</div>' +
                    '</div>';
                }
            },
            {
                data: 'serial_imei',
                orderable: true,
                searchable: true,
                responsivePriority: 2,
                width: '100px',
                render: function(data) {
                    if (!data) return '<span class="text-muted small">—</span>';
                    return '<span class="font-monospace small">' + escapeHtml(data) + '</span>';
                }
            },
            {
                data: 'categoria',
                defaultContent: '<span class="text-muted small">—</span>',
                width: '100px',
                render: function(data) {
                    if (!data) return '<span class="text-muted small">—</span>';
                    return '<span class="badge rounded-pill" style="background:rgba(99,102,241,.1);color:#4f46e5;font-weight:600;"><i class="bi bi-tags me-1"></i>' + escapeHtml(data.nombre) + '</span>';
                }
            },
            {
                data: 'tipo_servicio',
                className: 'text-center',
                width: '80px',
                render: function(data) {
                    if (data === 'servicio') return '<span class="badge rounded-pill" style="background:linear-gradient(135deg,#a855f7,#7c3aed);color:#fff;font-weight:700;font-size:.75rem;"><i class="bi bi-scissors me-1"></i>Servicio</span>';
                    if (data === 'general') return '<span class="badge rounded-pill" style="background:linear-gradient(135deg,#0ea5e9,#0284c7);color:#fff;font-weight:700;font-size:.75rem;"><i class="bi bi-card-heading me-1"></i>General</span>';
                    return '<span class="badge rounded-pill" style="background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;font-weight:700;font-size:.75rem;"><i class="bi bi-box-seam me-1"></i>Producto</span>';
                }
            },
            {
                data: null,
                className: 'text-end',
                width: '120px',
                render: function(data) {
                    const precio = parseFloat(data.precio || 0).toFixed(2);
                    const costo = parseFloat(data.precio_compra || 0).toFixed(2);
                    const itbis = parseFloat(data.itbis_porcentaje || 18).toFixed(2);
                    return '<div class="fw-bold text-brand">RD$ ' + precio + '</div>' +
                        '<div class="text-muted">Costo: RD$ ' + costo + '</div>' +
                        '<div class="text-muted text-xxs">ITBIS: ' + itbis + '%</div>';
                }
            },
            {
                data: null,
                className: 'text-center',
                orderable: false,
                width: '100px',
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
                width: '90px',
                render: function(data) { return renderStock(data); }
            },
            {
                data: 'activo',
                className: 'text-center',
                orderable: false,
                searchable: false,
                width: '70px',
                render: function(data) { return renderEstado(data); }
            },
            {
                data: null,
                className: 'text-end pe-4',
                orderable: false,
                searchable: false,
                width: '100px',
                render: function(data) {
                    let actions = '<div class="d-flex justify-content-end gap-1">';
                    actions += '<a href="' + API_BASE + '/' + data.id + '" class="ui-action ui-action-edit" title="Ver"><i class="bi bi-eye"></i></a>';
                    if (canEdit) actions += '<a href="' + API_BASE + '/' + data.id + '/edit" class="ui-action ui-action-edit" title="Editar"><i class="bi bi-pencil"></i></a>';
                    if (canToggle) {
                        const activo = !!data.activo;
                        actions += '<button type="button" class="ui-action ui-action-edit toggle-activo" title="' + (activo ? 'Desactivar' : 'Activar') + '" data-id="' + data.id + '" data-nombre="' + escapeHtml(data.nombre) + '" data-activo="' + (activo ? '1' : '0') + '"><i class="bi bi-' + (activo ? 'pause-circle' : 'play-circle') + '"></i></button>';
                    }
                    if (canDelete) actions += '<button type="button" class="ui-action ui-action-delete border-0 btn-delete-producto" title="Eliminar" data-id="' + data.id + '" data-nombre="' + escapeHtml(data.nombre) + '"><i class="bi bi-trash"></i></button>';
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
            zeroRecords: '<div class="text-center py-5"><i class="bi bi-box-seam d-block mb-2" style="font-size:2.5rem;color:#cbd5e1;"></i><p class="fw-semibold mb-1" style="color:#475569;">No se encontraron productos</p><p class="text-muted small mb-0">Intenta ajustar los filtros de búsqueda.</p></div>',
            processing: '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>'
        },
        pageLength: -1,
        lengthMenu: [[-1, 10, 25, 50, 100], ['Todos', 10, 25, 50, 100]],
        order: [[1, 'asc']],
        autoWidth: false,
        scrollCollapse: false,
        responsive: {
            details: {
                type: 'column',
                target: 'tr',
                renderer: function(api, rowIdx, columns) {
                    let data = '';
                    columns.forEach(function(col) {
                        if (col.hidden) {
                            data += '<li><span class="child-label">' + col.title + '</span><span class="child-value">' + col.data + '</span></li>';
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

    // ============================================================
    // FILTERS
    // ============================================================
    function buildFilterParams() {
        const nombre = $('#busqueda-producto').val();
        const stockStatus = $('#filter-stock').val();
        const activo = $('#filter-activo').val();
        const precioMin = parseFloat($('#filter-precio-min').val()) || 0;
        const precioMax = parseFloat($('#filter-precio-max').val()) || Infinity;
        
        const params = new URLSearchParams();
        if (nombre) params.set('nombre', nombre);
        if (stockStatus) params.set('stock_status', stockStatus);
        if (activo) params.set('activo', activo);
        if (precioMin > 0) params.set('precio_min', precioMin);
        if (precioMax < Infinity) params.set('precio_max', precioMax);
        
        return params;
    }

    function reloadWithFilters() {
        const params = buildFilterParams();
        table.ajax.url('{{ route("productos.ajax") }}?' + params.toString()).load();
    }

    $('#filtros-form').on('submit', function(e) {
        e.preventDefault();
        reloadWithFilters();
    });

    // Búsqueda en tiempo real con debounce
    // Se usan 'input' y 'keypress' para compatibilidad con terminales POS
    let searchTimeout = null;
    $('#busqueda-producto').on('input keypress', function(e) {
        // Si se presiona Enter, buscar inmediatamente sin debounce
        if (e.type === 'keypress' && e.which === 13) {
            isSearchTyping = false;
            reloadWithFilters();
            return;
        }
        isSearchTyping = true;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            isSearchTyping = false;
            reloadWithFilters();
        }, 300); // Reducido de 500ms a 300ms para respuesta más rápida
    });

    // Change event for selects also triggers reload
    $('#filter-stock, #filter-activo, #filter-precio-min, #filter-precio-max').on('change', function() {
        reloadWithFilters();
    });

    // ============================================================
    // AUTO-REFRESH EVERY 120 SECONDS
    // ============================================================
    function startAutoRefresh() {
        refreshInterval = setInterval(function() {
            // Skip refresh if user is active or typing
            if (userActive || isSearchTyping) {
                return;
            }
            table.ajax.reload(null, false); // false = don't jump to first page
        }, REFRESH_MS);
    }

    function stopAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
            refreshInterval = null;
        }
    }

    // Start auto-refresh on page load
    startAutoRefresh();

    // Pause auto-refresh when tab loses focus
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopAutoRefresh();
        } else {
            startAutoRefresh();
            // Immediate refresh when coming back
            table.ajax.reload(null, false);
        }
    });

    // ============================================================
    // TOGGLE ACTIVO
    // ============================================================
    $(document).on('click', '.toggle-activo', async function() {
        const $btn = $(this);
        const id = $btn.data('id');
        const nombre = $btn.data('nombre');
        const activo = $btn.data('activo') == '1';
        const accion = activo ? 'desactivar' : 'activar';

        const confirmar = async () => {
            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('_token', csrfToken);

            try {
                const data = await fetchJSON(API_BASE + '/' + id + '/toggle', formData);
                if (data.success) {
                    const $row = $btn.closest('tr');
                    const $cells = $row.find('td');
                    $cells.eq(6).html(renderEstado(data.activo));
                    const $toggle = $row.find('.toggle-activo');
                    if ($toggle.length) actualizarToggleBtn($toggle, data.activo);
                    swalExito('Producto ' + (data.activo ? 'activado' : 'desactivado') + ' correctamente.');
                    
                    // Refresh after action
                    setTimeout(function() {
                        table.ajax.reload(null, false);
                    }, 1000);
                } else {
                    swalError(data.message || 'No se pudo actualizar el producto.');
                }
            } catch (err) {
                swalError(err.message || 'No se pudo conectar con el servidor.');
            }
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '¿' + accion.charAt(0).toUpperCase() + accion.slice(1) + ' producto?',
                text: '¿Estás seguro de ' + accion + ' "' + nombre + '"?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, ' + accion,
                cancelButtonText: 'Cancelar',
                confirmButtonColor: activo ? '#ef4444' : '#22c55e'
            }).then(function(result) { if (result.isConfirmed) confirmar(); });
        } else {
            if (confirm('¿' + accion.charAt(0).toUpperCase() + accion.slice(1) + ' "' + nombre + '"?')) confirmar();
        }
    });

    // ============================================================
    // ELIMINAR
    // ============================================================
    $(document).on('click', '.btn-delete-producto', function() {
        const $btn = $(this);
        const id = $btn.data('id');
        const nombre = $btn.data('nombre');

        const ejecutar = async () => {
            const $row = $btn.closest('tr');
            $row.css('opacity', '0.5');

            const formData = new FormData();
            formData.append('_token', csrfToken);

            try {
                const data = await fetchJSON(API_BASE + '/' + id + '/delete-ajax', formData);
                if (data.success) {
                    table.row($row).remove().draw();
                    swalExito(data.message);
                } else {
                    $row.css('opacity', '1');
                    swalError(data.message);
                }
            } catch (err) {
                $row.css('opacity', '1');
                swalError(err.message || 'No se pudo conectar con el servidor.');
            }
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '¿Eliminar producto?',
                text: 'Se eliminará "' + nombre + '". Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#ef4444'
            }).then(function(result) { if (result.isConfirmed) ejecutar(); });
        } else {
            if (confirm('¿Eliminar "' + nombre + '"? Esta acción no se puede deshacer.')) ejecutar();
        }
    });
});
</script>
@endpush
@endsection
