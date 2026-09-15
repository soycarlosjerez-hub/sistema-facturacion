@extends('layouts.app')
@section('title', 'Plantillas de Factura')

@push('styles')
@include('partials.premium-ui')
<style>
    :root {
        --dt-accent: #8b5cf6;
        --dt-accent-gradient: linear-gradient(135deg, #8b5cf6, #a78bfa);
        --dt-accent-rgb: 139,92,246;
        --dt-violet: #8b5cf6;
        --dt-success: #22c55e;
        --dt-danger: #ef4444;
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
        --dt-transition: 0.15s;
    }
    .plantillas-table {
        --bs-table-bg: transparent;
        --bs-table-hover-bg: rgba(139,92,246,.04);
        width: 100%;
        margin: 0;
        table-layout: auto;
    }
    .plantillas-table thead th {
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
    .plantillas-table tbody td {
        padding: .85rem 1rem;
        border-bottom: 1px solid var(--dt-gray-100);
        vertical-align: middle;
        font-size: .9rem;
        overflow: hidden;
        max-width: none;
    }
    .plantillas-table td.text-center,
    .plantillas-table td.text-end {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .plantillas-table tbody tr:last-child td { border-bottom: none; }
    .plantillas-table tbody tr { transition: background var(--dt-transition); }
    .plantillas-table tbody tr:hover { background: rgba(139,92,246,.03); }

    .text-brand { color: var(--dt-violet); }

    .modulo-badge {
        padding: 0.25em 0.65em;
        border-radius: 1rem;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .modulo-ventas { background: rgba(139,92,246,0.12); color: #7c3aed; }
    .modulo-compras { background: rgba(34,197,94,0.12); color: #16a34a; }
    .modulo-cotizaciones { background: rgba(234,179,8,0.12); color: #ca8a04; }
    .modulo-devoluciones { background: rgba(239,68,68,0.12); color: #dc2626; }
    .modulo-ordenes { background: rgba(6,182,212,0.12); color: #0891b2; }
    .modulo-conduces { background: rgba(249,115,22,0.12); color: #ea580c; }
    .modulo-presupuestos { background: rgba(168,85,247,0.12); color: #9333ea; }

    .formato-badge {
        padding: 0.25em 0.65em;
        border-radius: 1rem;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .formato-a4 { background: rgba(59,130,246,0.12); color: #2563eb; }
    .formato-letter { background: rgba(99,102,241,0.12); color: #4f46e5; }
    .formato-ticket_80 { background: rgba(139,92,246,0.12); color: #7c3aed; }
    .formato-ticket_58 { background: rgba(168,85,247,0.12); color: #9333ea; }

    .default-badge {
        padding: 0.25em 0.65em;
        border-radius: 1rem;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .default-yes { background: rgba(139,92,246,0.15); color: #7c3aed; }
    .default-no { background: rgba(100,116,139,0.1); color: #64748b; }

    .status-badge {
        padding: 0.4em 0.8em;
        border-radius: 2rem;
        font-weight: 500;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all .2s;
    }
    .status-badge:hover { filter: brightness(1.1); }

    .sucursal-tag {
        display: inline-block;
        padding: 0.15em 0.5em;
        border-radius: 0.375rem;
        font-size: 0.7rem;
        background: rgba(139,92,246,0.08);
        color: #6d28d9;
        margin: 0.15rem;
    }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-file-earmark-richtext"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Plantillas de Factura</h4>
                    <div class="ui-header-meta">Crea y personaliza las plantillas de facturas, tickets y comprobantes para tu negocio</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('plantillas.create')
                <a href="{{ route('plantillas.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Plantilla
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('plantillas.index') }}" id="filtros-form" class="row g-2 align-items-end">
                <div class="col-lg-3">
                    <label class="ui-label small fw-bold text-muted">Buscar</label>
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" class="ui-input" placeholder="Nombre o código..." value="{{ request('buscar') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <label class="ui-label small fw-bold text-muted">Modulo</label>
                    <select name="modulo" class="ui-select">
                        <option value="">Todos</option>
                        @foreach($modulos as $key => $label)
                        <option value="{{ $key }}" {{ request('modulo') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="ui-label small fw-bold text-muted">Formato</label>
                    <select name="formato" class="ui-select">
                        <option value="">Todos</option>
                        @foreach($formatos as $key => $label)
                        <option value="{{ $key }}" {{ request('formato') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="ui-label small fw-bold text-muted">Estado</label>
                    <select name="activo" class="ui-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activas</option>
                        <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivas</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="ui-label small fw-bold text-muted">Predeterminada</label>
                    <select name="default" class="ui-select">
                        <option value="">Todas</option>
                        <option value="1" {{ request('default') === '1' ? 'selected' : '' }}>Solo predeterminadas</option>
                        <option value="0" {{ request('default') === '0' ? 'selected' : '' }}>Solo no pred.</option>
                    </select>
                </div>
                <div class="col-lg-1 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('plantillas.index') }}" class="ui-btn ui-btn-ghost"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-0">
            <div id="plantillas-table_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                <table id="plantillas-table" class="table plantillas-table no-footer" style="min-width:1000px;">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width:50px;" data-label="#">#</th>
                            <th style="min-width:180px;" data-label="nombre">Nombre</th>
                            <th style="min-width:100px;" data-label="codigo">Codigo</th>
                            <th style="min-width:110px;" data-label="modulo">Modulo</th>
                            <th style="min-width:120px;" data-label="formato">Formato</th>
                            <th style="width:90px;" class="text-center" data-label="default">Default</th>
                            <th style="width:90px;" class="text-center" data-label="estado">Estado</th>
                            <th style="min-width:150px;" data-label="sucursales">Sucursales</th>
                            <th style="width:140px;" class="text-end pe-3" data-label="acciones">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    const API_BASE = '/plantillas';
    const csrfToken = '{{ csrf_token() }}';
    const canEdit = {{ auth()->user()->can('plantillas.edit') ? 'true' : 'false' }};
    const canDelete = {{ auth()->user()->can('plantillas.delete') ? 'true' : 'false' }};
    const canCreate = {{ auth()->user()->can('plantillas.create') ? 'true' : 'false' }};

    let lastDataHash = '';

    function generateHash(obj) {
        return btoa(JSON.stringify(obj).substring(0, 200));
    }

    function showRefreshToast(changed) {
        if (typeof Swal !== 'undefined') {
            const toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
            if (changed) {
                toast.fire({ icon: 'info', title: 'Datos actualizados' });
            } else {
                toast.fire({ title: 'Datos sincronizados' });
            }
        }
    }

    function escapeHtml(str) {
        return String(str || '').replace(/[&<>"']/g, function(c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }

    function renderModuloBadge(modulo) {
        const cls = 'modulo-' + modulo;
        return '<span class="modulo-badge ' + cls + '">' + escapeHtml(modulo.charAt(0).toUpperCase() + modulo.slice(1)) + '</span>';
    }

    function renderFormatoBadge(formato) {
        const cls = 'formato-' + formato;
        const labels = { a4: 'A4', letter: 'Carta', 'ticket_80': '80mm', 'ticket_58': '58mm' };
        return '<span class="formato-badge ' + cls + '">' + (labels[formato] || formato) + '</span>';
    }

    function renderDefaultBadge(esDefault) {
        if (esDefault) {
            return '<span class="default-badge default-yes"><i class="bi bi-star-fill me-1"></i>Si</span>';
        }
        return '<span class="default-badge default-no"><i class="bi bi-star me-1"></i>No</span>';
    }

    function renderStatus(activo, id) {
        const cls = activo ? 'success' : 'secondary';
        const icon = activo ? 'check-circle-fill' : 'x-circle-fill';
        const label = activo ? 'Activa' : 'Inactiva';
        return '<span class="status-badge badge rounded-pill bg-' + cls + ' bg-opacity-10 text-' + cls + ' fw-semibold" data-id="' + id + '">' +
            '<i class="bi bi-' + icon + ' me-1"></i>' + label + '</span>';
    }

    function renderSucursales(sucursales) {
        if (!sucursales || sucursales.length === 0) {
            return '<span class="text-muted small">Global</span>';
        }
        return sucursales.map(function(s) { return '<span class="sucursal-tag">' + escapeHtml(s) + '</span>'; }).join('');
    }

    function renderAcciones(data) {
        let html = '<div class="d-flex justify-content-end gap-1">';
        html += '<a href="' + API_BASE + '/' + data.id + '/preview" target="_blank" class="ui-action" title="Vista previa"><i class="bi bi-eye"></i></a>';
        html += '<a href="' + API_BASE + '/' + data.id + '/pdf-preview" target="_blank" class="ui-action" title="PDF"><i class="bi bi-file-earmark-pdf"></i></a>';
        if (canEdit) {
            html += '<a href="' + API_BASE + '/' + data.id + '/edit" class="ui-action" title="Editar"><i class="bi bi-pencil"></i></a>';
        }
        html += '<a href="' + API_BASE + '/' + data.id + '/duplicate" class="ui-action" title="Duplicar"><i class="bi bi-file-earmark-copy"></i></a>';
        if (!data.es_default) {
            html += '<button type="button" class="ui-action btn-set-default" title="Establecer como predeterminada" data-id="' + data.id + '"><i class="bi bi-star"></i></button>';
        }
        if (canDelete) {
            html += '<button type="button" class="ui-action btn-delete-plantilla" title="Eliminar" data-id="' + data.id + '" data-nombre="' + escapeHtml(data.nombre || '') + '"><i class="bi bi-trash"></i></button>';
        }
        html += '</div>';
        return html;
    }

    const table = $('#plantillas-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: function(data, callback, settings) {
            const params = new URLSearchParams();
            const search = $('input[name="buscar"]').val();
            const modulo = $('select[name="modulo"]').val();
            const formato = $('select[name="formato"]').val();
            const activo = $('select[name="activo"]').val();
            const default_ = $('select[name="default"]').val();

            params.set('search.value', search || '');
            if (modulo) params.set('modulo', modulo);
            if (formato) params.set('formato', formato);
            if (activo !== '') params.set('activo', activo);
            if (default_ !== '') params.set('default', default_);

            params.set('start', data.start);
            params.set('length', data.length);
            params.set('draw', data.draw);

            fetch('{{ route("plantillas.ajax") }}?' + params.toString(), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(function(json) {
                callback({
                    data: json.plantillas || [],
                    draw: data.draw,
                    recordsTotal: json.recordsTotal || 0,
                    recordsFiltered: json.recordsFiltered || 0
                });
            })
            .catch(function(error) {
                console.error('Datatables AJAX error:', error);
                callback({
                    data: [],
                    draw: data.draw,
                    recordsTotal: 0,
                    recordsFiltered: 0
                });
            });
        },
        dataSrc: 'data',
        columns: [
            {
                data: null,
                className: 'text-center ps-4',
                orderable: true,
                searchable: false,
                width: '50px',
                render: function(data, type) {
                    if (type === 'display') return '<span class="text-muted fw-bold">#' + data.id + '</span>';
                    return data.id;
                }
            },
            {
                data: 'nombre',
                orderable: true,
                searchable: true,
                render: function(data, type, row) {
                    let icon = '<i class="bi bi-file-earmark-text text-brand me-1"></i>';
                    if (row.formato_papel && row.formato_papel.includes('ticket')) {
                        icon = '<i class="bi bi-receipt text-brand me-1"></i>';
                    }
                    return '<div class="fw-bold">' + icon + escapeHtml(data || '') + '</div>';
                }
            },
            {
                data: 'codigo',
                orderable: true,
                searchable: true,
                render: function(data) {
                    return '<code class="small text-muted">' + escapeHtml(data || '') + '</code>';
                }
            },
            {
                data: 'modulo',
                orderable: true,
                searchable: true,
                render: function(data) {
                    return renderModuloBadge(data);
                }
            },
            {
                data: 'formato_papel',
                orderable: true,
                searchable: false,
                render: function(data) {
                    return renderFormatoBadge(data);
                }
            },
            {
                data: 'es_default',
                className: 'text-center',
                orderable: false,
                render: function(data) {
                    return renderDefaultBadge(data);
                }
            },
            {
                data: 'activo',
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    return renderStatus(data, row.id);
                }
            },
            {
                data: 'sucursales',
                orderable: false,
                searchable: true,
                render: function(data) {
                    return renderSucursales(data);
                }
            },
            {
                data: null,
                className: 'text-end pe-4',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return renderAcciones(data);
                }
            }
        ],
        language: {
            search: '',
            lengthMenu: '_MENU_',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ plantillas',
            infoEmpty: 'No hay plantillas',
            infoFiltered: '(de _MAX_ totales)',
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>'
            },
            zeroRecords: '<div class="text-center py-5"><i class="bi bi-file-earmark-richtext d-block mb-2" style="font-size:2.5rem;color:#cbd5e1;"></i><p class="fw-semibold mb-1" style="color:#475569;">No se encontraron plantillas</p><p class="text-muted small mb-0">Clic en "Nueva Plantilla" para crear una.</p></div>'
        },
        pageLength: 25,
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, 'Todos']],
        order: [[1, 'asc']],
        autoWidth: false,
        responsive: false,
        dom: '<"row px-3 pt-2"<"col-sm-6"l><"col-sm-6"f>>' +
             '<"row"<"col-12"tr>>' +
             '<"row px-3 pb-2"<"col-sm-5"i><"col-sm-7"p>>'
    });

    function reloadWithFilters() {
        table.ajax.reload(null, false);
    }

    $('#filtros-form').on('submit', function(e) {
        e.preventDefault();
        reloadWithFilters();
    });

    let searchTimeout = null;
    $('input[name="buscar"]').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            reloadWithFilters();
        }, 300);
    });

    $('select[name="modulo"], select[name="formato"], select[name="activo"], select[name="default"]').on('change', function() {
        reloadWithFilters();
    });

    // Toggle activo
    $(document).on('click', '.status-badge', function() {
        const badge = $(this);
        const isActive = badge.find('i.check-circle-fill').length > 0;
        const id = badge.data('id');

        Swal.fire({
            title: isActive ? '¿Desactivar plantilla?' : '¿Activar plantilla?',
            text: isActive ? 'La plantilla quedará inactiva y no se usará para imprimir.' : 'La plantilla volverá a estar activa.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: isActive ? '#dc2626' : '#22c55e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Si, ' + (isActive ? 'desactivar' : 'activar'),
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                fetch(API_BASE + '/' + id + '/toggle', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res.success) {
                        table.ajax.reload();
                    }
                })
                .catch(function() {
                    Swal.fire('Error', 'No se pudo cambiar el estado.', 'error');
                });
            }
        });
    });

    // Delete
    $(document).on('click', '.btn-delete-plantilla', function() {
        const btn = $(this);
        const id = btn.data('id');
        const nombre = btn.data('nombre');

        Swal.fire({
            title: '¿Eliminar plantilla?',
            text: 'Se eliminara: "' + escapeHtml(nombre) + '". Las facturas impresas con esta plantilla mantendran su diseno.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Si, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                fetch(API_BASE + '/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        table.ajax.reload();
                    }
                })
                .catch(function() {
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                });
            }
        });
    });

    // Set default
    $(document).on('click', '.btn-set-default', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: '¿Establecer como predeterminada?',
            text: 'Esta plantilla sera la predeterminada para sus modulos. Las otras plantillas de este modulo dejaran de serlo.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#8b5cf6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Si, establecer',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                fetch(API_BASE + '/' + id + '/set-default', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        table.ajax.reload();
                    }
                })
                .catch(function() {
                    Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
                });
            }
        });
    });
});
</script>
@endpush
