@extends('layouts.app')
@section('title', 'Categorías')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
:root {
    --dt-accent: #ec4899;
    --dt-accent-gradient: linear-gradient(135deg, #ec4899, #f472b6);
    --dt-accent-rgb: 236,72,153;
}
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
body.dark-mode .fw-bold.text-dark { color: #f1f5f9 !important; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#ec4899;--accent-rgb:236,72,153;--accent-hover:#db2777;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tags"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Gestión de Categorías</h4>
                    <div class="ui-header-meta">Clasifica y organiza tus productos e inventario</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('productos.create')
                <a href="{{ route('categorias.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Categoría
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('categorias.index') }}" id="filtros-form" class="row g-2 align-items-end">
                <div class="col-lg-4">
                    <label class="ui-label small fw-bold text-muted">Buscar</label>
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="nombre" id="busqueda-categoria" class="ui-input" placeholder="Nombre de categoría..." value="{{ request('nombre') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-3">
                    <label class="ui-label small fw-bold text-muted">Estado</label>
                    <select name="activo" id="filter-activo" class="ui-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activas</option>
                        <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivas</option>
                    </select>
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('categorias.index') }}" class="ui-btn ui-btn-ghost"><i class="bi bi-x-lg"></i></a>
                </div>
                <div class="col-lg-3 text-end">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('categorias.importar') }}" class="ui-btn ui-btn-primary rounded-pill shadow-sm fw-medium">
                            <i class="bi bi-upload me-1"></i> Importar CSV
                        </a>
                        <a href="{{ route('categorias.exportar', request()->all()) }}" class="ui-btn ui-btn-primary rounded-pill shadow-sm fw-medium">
                            <i class="bi bi-file-excel me-1"></i> Excel
                        </a>
                        <a href="{{ route('categorias.pdf', request()->all()) }}" class="ui-btn ui-btn-primary rounded-pill shadow-sm fw-medium">
                            <i class="bi bi-file-pdf me-1"></i> PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-0">
            <table id="categorias-table" class="table dt-table nowrap no-footer" style="width:100%">
                <thead>
                    <tr>
                        <th class="ps-4" style="width:50px;">#</th>
                        <th>Categoría</th>
                        <th>Descripción</th>
                        <th class="text-center">Productos</th>
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
    const data = @json($categorias);
    const csrfToken = '{{ csrf_token() }}';

    const table = $('#categorias-table').DataTable({
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
                    const initial = nombre.charAt(0).toUpperCase();
                    const colors = ['#f87171','#60a5fa','#34d399','#fbbf24','#a78bfa','#f472b6','#f97316','#14b8a6'];
                    const color = colors[crc32(nombre) % colors.length];
                    return '<div class="d-flex align-items-center">' +
                        '<div class="avatar-circle text-white me-3 shadow-sm" style="background:' + color + ';">' + initial + '</div>' +
                        '<div class="fw-bold text-dark fs-6">' + nombre + '</div>' +
                    '</div>';
                }
            },
            {
                data: 'descripcion',
                defaultContent: '<span class="text-muted small">Sin descripción</span>',
                render: function(data) {
                    if (!data) return '<span class="text-muted small">Sin descripción</span>';
                    return '<div class="text-muted small text-truncate" style="max-width:300px;" title="' + escapeHtml(data) + '">' + escapeHtml(data) + '</div>';
                }
            },
            {
                data: 'productos_count',
                className: 'text-center',
                render: function(data) {
                    const count = parseInt(data || 0);
                    return '<span class="badge bg-light text-secondary border rounded-pill">' +
                        '<i class="bi bi-box-seam me-1"></i> ' + count + ' prod.</span>';
                }
            },
            {
                data: 'activa',
                className: 'text-center',
                render: function(data, type, row) {
                    const id = row.id;
                    return data
                        ? '<span class="status-badge bg-success bg-opacity-10 text-success toggle-activa" data-id="' + id + '">' +
                            '<i class="bi bi-check-circle-fill me-1"></i> Activa</span>'
                        : '<span class="status-badge bg-secondary bg-opacity-10 text-secondary toggle-activa" data-id="' + id + '">' +
                            '<i class="bi bi-x-circle-fill me-1"></i> Inactiva</span>';
                }
            },
            {
                data: null,
                className: 'text-end pe-4',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return renderAcciones(data.id, {
                        show: '/categorias/' + data.id,
                        edit: '/categorias/' + data.id + '/edit',
                        delete: '/categorias/' + data.id,
                        csrf: csrfToken,
                        nombre: data.nombre
                    });
                }
            }
        ],
        language: {
            search: '',
            lengthMenu: '_MENU_',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ categorías',
            infoEmpty: 'No hay categorías',
            infoFiltered: '(de _MAX_ totales)',
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>'
            },
            zeroRecords: '<div class="text-center py-5">' +
                '<i class="bi bi-tags d-block mb-2" style="font-size:2.5rem;color:#cbd5e1;"></i>' +
                '<p class="fw-semibold mb-1" style="color:#475569;">No se encontraron categorías</p>' +
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

    // Filter form
    $('#filtros-form').on('submit', function(e) {
        e.preventDefault();
        const nombre = $('#busqueda-categoria').val();
        const activo = $('#filter-activo').val();

        table.search(nombre).draw();

        $.fn.dataTable.ext.search.push(function(settings, data) {
            const isActivo = (data[4] || '').indexOf('Activa') !== -1;
            if (activo === '1' && !isActivo) return false;
            if (activo === '0' && isActivo) return false;
            return true;
        });

        table.draw();
        $.fn.dataTable.ext.search.pop();
    });

    // Real-time search
    let searchTimeout;
    $('#busqueda-categoria').on('input', function() {
        clearTimeout(searchTimeout);
        const val = $(this).val();
        searchTimeout = setTimeout(function() {
            table.search(val).draw();
        }, 300);
    });

    // Toggle activa via SweetAlert2
    $('#categorias-table').on('click', '.toggle-activa', function() {
        const id = $(this).data('id');
        const badge = $(this);
        const isActive = badge.hasClass('text-success');

        Swal.fire({
            title: isActive ? '¿Desactivar categoría?' : '¿Activar categoría?',
            text: isActive ? 'La categoría quedará inactiva en el sistema.' : 'La categoría volverá a estar activa.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: isActive ? '#dc2626' : '#22c55e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, ' + (isActive ? 'desactivar' : 'activar'),
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/categorias/' + id + '/toggle',
                    method: 'PUT',
                    data: { _token: csrfToken },
                    success: function(res) {
                        if (res.success) {
                            const row = table.row($(badge).closest('tr'));
                            row.data().activa = res.activa;
                            row.invalidate();
                            table.draw(false);
                            Swal.fire({
                                icon: 'success',
                                title: res.activa ? 'Categoría activada' : 'Categoría desactivada',
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
    $(document).on('click', '.btn-delete-categoria', function() {
        const btn = $(this);
        const id = btn.data('id');
        const nombre = btn.data('nombre');

        Swal.fire({
            title: '¿Eliminar categoría?',
            text: 'Se eliminará: "' + nombre + '". Solo es posible si no tiene productos asociados.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                deleteCategoria(id, btn);
            }
        });
    });

    function deleteCategoria(id, btn) {
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', csrfToken);

        const row = btn.closest('tr');
        if (row) row.style.opacity = '0.5';

        fetch('/categorias/' + id, {
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
            html += '<a href="' + opts.show + '" class="ui-action ui-action-edit" title="Ver">' +
                '<i class="bi bi-eye"></i></a>';
        }
        html += '<a href="' + opts.edit + '" class="ui-action ui-action-edit" title="Editar">' +
            '<i class="bi bi-pencil"></i></a>';
        if (opts.delete) {
            html += '<button type="button" class="ui-action ui-action-delete border-0 btn-delete-categoria" data-id="' + id + '" data-nombre="' + escapeHtml(opts.nombre || '') + '" title="Eliminar">' +
                '<i class="bi bi-trash"></i></button>';
        }
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