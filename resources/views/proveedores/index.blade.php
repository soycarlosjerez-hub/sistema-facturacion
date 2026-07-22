@extends('layouts.app')
@section('title', 'Gestión de Proveedores')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
:root {
    --dt-accent: #3b82f6;
    --dt-accent-gradient: linear-gradient(135deg, #3b82f6, #6366f1);
    --dt-accent-rgb: 59,130,246;
}
.avatar-circle {
    width: 44px; height: 44px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 600; font-size: 1.2rem;
    transition: transform 0.2s;
    flex-shrink: 0;
}
tr:hover .avatar-circle { transform: scale(1.1); }
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
body.dark-mode .avatar-circle { color: #f1f5f9 !important; }
body.dark-mode .fw-bold.text-dark { color: #f1f5f9 !important; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">

    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <div class="ui-header-title">Catálogo de Proveedores</div>
                    <div class="ui-header-meta">
                        <i class="bi bi-building me-1"></i>
                        Gestión de proveedores y contactos de negocio
                        <span class="mx-2">·</span>
                        <i class="bi bi-list-ul me-1"></i>
                        {{ $proveedores->count() }} registro(s)
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('proveedores.create')
                <a href="{{ route('proveedores.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Proveedor
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s;">
        <div class="ui-card-accent"></div>
        <div class="card-body p-3">
            <form method="GET" id="search-form" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" id="busqueda-proveedor" class="ui-input" placeholder="Nombre, RNC, teléfono o email..." value="{{ request('buscar') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2 d-flex align-items-center">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="incluir_inactivos" value="1" id="incluir_inactivos" {{ request('incluir_inactivos') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label small fw-bold text-muted" for="incluir_inactivos">Incluir inactivos</label>
                    </div>
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('proveedores.index') }}" class="ui-btn ui-btn-ghost"><i class="bi bi-x-lg"></i></a>
                </div>
                <div class="col-lg-4 text-end">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('proveedores.importar') }}" class="ui-btn ui-btn-primary rounded-pill shadow-sm fw-medium">
                            <i class="bi bi-upload me-1"></i> Importar CSV
                        </a>
                        <a href="{{ route('proveedores.exportar', request()->all()) }}" class="ui-btn ui-btn-primary rounded-pill shadow-sm fw-medium">
                            <i class="bi bi-file-excel me-1"></i> Excel
                        </a>
                        <a href="{{ route('proveedores.pdf', request()->all()) }}" class="ui-btn ui-btn-primary rounded-pill shadow-sm fw-medium">
                            <i class="bi bi-file-pdf me-1"></i> PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s;">
        <div class="ui-card-accent"></div>
        <div class="card-body p-0">
            <table id="proveedores-table" class="ui-table dt-table nowrap no-footer" style="width:100%">
                <thead>
                    <tr>
                        <th class="ps-4" style="width:50px;">#</th>
                        <th>Proveedor</th>
                        <th>Contacto</th>
                        <th>RNC</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    const data = @json($proveedores);
    const csrfToken = '{{ csrf_token() }}';

    const table = $('#proveedores-table').DataTable({
        data: data,
        columns: [
            {
                data: null,
                className: 'text-center ps-4',
                orderable: false,
                searchable: false,
                width: '50px',
                render: function(data, type, row, meta) {
                    return '<span class="text-muted fw-bold">' + (meta.row + meta.settings._iDisplayStart + 1) + '</span>';
                }
            },
            {
                data: null,
                orderable: true,
                searchable: true,
                render: function(data) {
                    const nombre = escapeHtml(data.nombre || '');
                    const letter = nombre.charAt(0).toUpperCase();
                    const colors = ['#f87171','#60a5fa','#34d399','#fbbf24','#a78bfa','#f472b6','#f97316','#14b8a6'];
                    const color = colors[crc32(nombre) % colors.length];
                    return '<div class="d-flex align-items-center">' +
                        '<div class="avatar-circle text-white me-3 shadow-sm" style="background:' + color + ';">' +
                            '<i class="bi bi-building fs-5"></i>' +
                        '</div>' +
                        '<div class="text-truncate">' +
                            '<div class="fw-bold text-dark fs-6 text-truncate" title="' + escapeHtml(nombre) + '">' + escapeHtml(nombre) + '</div>' +
                            (data.direccion ? '<div class="text-muted small text-truncate" style="max-width:220px;"><i class="bi bi-geo-alt me-1"></i>' + escapeHtml(data.direccion) + '</div>' : '<div class="text-muted small"><i class="bi bi-geo-alt me-1"></i>Sin dirección</div>') +
                        '</div></div>';
                }
            },
            {
                data: null,
                orderable: true,
                searchable: true,
                render: function(data) {
                    let html = '<div class="fw-medium text-dark"><i class="bi bi-telephone text-muted me-2"></i>' + escapeHtml(data.telefono || '—') + '</div>';
                    if (data.email) {
                        html += '<div class="text-muted small mt-1"><i class="bi bi-envelope text-muted me-2"></i>' + escapeHtml(data.email) + '</div>';
                    }
                    return html;
                }
            },
            {
                data: 'rnc',
                defaultContent: '',
                render: function(data) {
                    return data ? '<span class="badge bg-light text-dark border rounded-pill"><i class="bi bi-card-text me-1"></i> ' + escapeHtml(data) + '</span>' : '<span class="text-muted small">—</span>';
                }
            },
            {
                data: 'activo',
                className: 'text-center',
                render: function(data, type, row) {
                    const id = row.id;
                    const active = data;
                    return active
                        ? '<span class="status-badge bg-success bg-opacity-10 text-success toggle-activo" data-id="' + id + '" style="cursor:pointer;">' +
                            '<i class="bi bi-check-circle-fill me-1"></i> Activo</span>'
                        : '<span class="status-badge bg-secondary bg-opacity-10 text-secondary toggle-activo" data-id="' + id + '" style="cursor:pointer;">' +
                            '<i class="bi bi-x-circle-fill me-1"></i> Inactivo</span>';
                }
            },
            {
                data: null,
                className: 'text-end pe-4',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return renderAcciones(data.id, {
                        show: '{{ url("proveedores") }}/' + data.id,
                        edit: '{{ url("proveedores") }}/' + data.id + '/edit',
                        delete: '{{ url("proveedores") }}/' + data.id,
                        csrf: csrfToken,
                        nombre: data.nombre
                    });
                }
            }
        ],
        language: {
            search: '',
            lengthMenu: '_MENU_',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ proveedores',
            infoEmpty: 'No hay proveedores',
            infoFiltered: '(de _MAX_ totales)',
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>'
            },
            zeroRecords: '<div class="text-center py-5">' +
                '<i class="bi bi-truck d-block mb-2" style="font-size:2.5rem;color:#cbd5e1;"></i>' +
                '<p class="fw-semibold mb-1" style="color:#475569;">No se encontraron proveedores</p>' +
                '<p class="text-muted small mb-0">Intenta ajustar los filtros de búsqueda.</p></div>'
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

    // Toggle activo via SweetAlert2
    $('#proveedores-table').on('click', '.toggle-activo', function() {
        const id = $(this).data('id');
        const badge = $(this);
        const isActive = badge.hasClass('text-success');

        Swal.fire({
            title: isActive ? '¿Desactivar proveedor?' : '¿Activar proveedor?',
            text: isActive ? 'El proveedor quedará inactivo en el sistema.' : 'El proveedor volverá a estar activo.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: isActive ? '#dc2626' : '#22c55e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, ' + (isActive ? 'desactivar' : 'activar'),
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("proveedores") }}/' + id + '/toggle',
                    method: 'PUT',
                    data: { _token: csrfToken },
                    success: function(res) {
                        if (res.success) {
                            const row = table.row($(badge).closest('tr'));
                            row.data().activo = res.activo;
                            row.invalidate();
                            table.draw(false);
                            Swal.fire({
                                icon: 'success',
                                title: res.activo ? 'Proveedor activado' : 'Proveedor desactivado',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2500
                            });
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo cambiar el estado.', 'error');
                    }
                });
            }
        });
    });

    // Delete via AJAX
    $(document).on('click', '.btn-delete-proveedor', function() {
        const btn = $(this);
        const id = btn.data('id');
        const nombre = btn.data('nombre');

        Swal.fire({
            title: '¿Eliminar proveedor?',
            text: 'Se eliminará: "' + nombre + '"',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                deleteProveedor(id, btn);
            }
        });
    });

    function deleteProveedor(id, btn) {
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', csrfToken);

        const row = btn.closest('tr');
        if (row) row.style.opacity = '0.5';

        fetch('/proveedores/' + id, {
            method: 'POST',
            body: formData
        })
        .then(function(r) {
            if (!r.ok) throw new Error('El servidor respondió con estado ' + r.status);
            const ct = r.headers.get('content-type') || '';
            if (ct.indexOf('application/json') === -1) throw new Error('Respuesta inesperada del servidor.');
            return r.json();
        })
        .then(function(data) {
            if (data.success) {
                if (row && row.closest('tbody')) {
                    table.row(row).remove().draw();
                }
                Swal.fire({ icon: 'success', title: 'Eliminado', text: data.message, timer: 1500, showConfirmButton: false });
            } else {
                if (row) row.style.opacity = '1';
                Swal.fire({ icon: 'error', title: 'No se pudo eliminar', text: data.message });
            }
        })
        .catch(function(err) {
            if (row) row.style.opacity = '1';
            Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'No se pudo conectar con el servidor.' });
        });
    }

    function renderAcciones(id, opts) {
        let html = '<div class="d-flex justify-content-end gap-1">';
        if (opts.show) {
            html += '<a href="' + opts.show + '" class="ui-action ui-action-edit" title="Ver"><i class="bi bi-eye"></i></a>';
        }
        html += '<a href="' + opts.edit + '" class="ui-action ui-action-edit" title="Editar"><i class="bi bi-pencil"></i></a>';
        html += '<button type="button" class="ui-action ui-action-delete border-0 btn-delete-proveedor" data-id="' + id + '" data-nombre="' + escapeHtml(opts.nombre || '') + '" title="Eliminar"><i class="bi bi-trash"></i></button>';
        html += '</div>';
        return html;
    }

    function escapeHtml(str) {
        return String(str || '').replace(/[&<>"']/g, function(c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }

    function crc32(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            const char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }
        return Math.abs(hash);
    }
});
</script>
@endpush
