@extends('layouts.app')
@section('title', 'Categorías')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
:root {
    --dt-accent: #8b5cf6;
    --dt-accent-gradient: linear-gradient(135deg, #8b5cf6, #a855f7);
    --dt-accent-rgb: 139,92,246;
}
.premium-header {
    background: linear-gradient(135deg, #8b5cf6, #a855f7, #7c3aed, #8b5cf6);
    background-size: 300% 300%;
    box-shadow: 0 8px 32px rgba(139,92,246,.25);
}
.premium-header::before {
    background:
        radial-gradient(circle at 30% 40%, rgba(255,255,255,.12) 0%, transparent 50%),
        radial-gradient(circle at 70% 60%, rgba(255,255,255,.08) 0%, transparent 50%);
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
<div class="container-fluid px-4 py-3 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-tags"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Gestión de Categorías</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-info-circle me-1"></i>Clasifica y organiza tus productos e inventario
                    </small>
                </div>
            </div>
            <div class="d-flex gap-2">
                @can('productos.view')
                <a href="{{ route('categorias.exportar') }}" class="btn btn-light rounded-pill px-3 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.15);border:1.5px solid rgba(255,255,255,.25);">
                    <i class="bi bi-download me-1"></i> Exportar
                </a>
                <a href="{{ route('categorias.importar') }}" class="btn btn-light rounded-pill px-3 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.15);border:1.5px solid rgba(255,255,255,.25);">
                    <i class="bi bi-upload me-1"></i> Importar
                </a>
                @endcan
                <a href="{{ route('categorias.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Categoría
                </a>
            </div>
        </div>
    </div>

    <div class="premium-card" style="animation-delay:.15s;">
        <div class="card-accent purple"></div>
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

    // Delete con SweetAlert2
    $('#categorias-table').on('click', '.btn-delete-categoria', function() {
        const btn = $(this);
        const url = btn.data('url');
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
        }).then(result => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = '<input type="hidden" name="_token" value="' + csrfToken + '"><input type="hidden" name="_method" value="DELETE">';
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    function renderAcciones(id, opts) {
        let html = '<div class="d-flex justify-content-end gap-1">';
        html += '<a href="' + opts.edit + '" class="premium-btn-edit" title="Editar">' +
            '<i class="bi bi-pencil"></i></a>';
        if (opts.delete) {
            html += '<button type="button" class="premium-btn-delete border-0 btn-delete-categoria" data-url="' + opts.delete + '" data-nombre="' + escapeHtml(opts.nombre || '') + '" title="Eliminar">' +
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