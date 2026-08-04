@extends('layouts.app')
@section('title', 'Equipos')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
:root {
    --dt-accent: #38bdf8;
    --dt-accent-gradient: linear-gradient(135deg, #38bdf8, #0ea5e9);
    --dt-accent-rgb: 56,189,248;
}
.estado-badge {
    padding: 0.35em 0.8em;
    border-radius: 2rem;
    font-weight: 600;
    font-size: 0.72rem;
    letter-spacing: 0.3px;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    transition: all .2s;
}
.estado-badge:hover { filter: brightness(1.1); transform: scale(1.03); }
.garantia-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    display: inline-block;
}
@media (max-width: 767.98px) {
    .dt-table { min-width: 700px; }
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#38bdf8;--accent-rgb:56,189,248;--accent-hover:#0ea5e9;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-phone"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Gestión de Equipos</h4>
                    <div class="ui-header-meta">Administra dispositivos, inventario y estados</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('equipos.create')
                <a href="{{ route('equipos.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Equipo
                </a>
                @endcan
                <a href="{{ route('equipos.exportar') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" style="background:rgba(255,255,255,.15);">
                    <i class="bi bi-download me-1"></i> Excel
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Total Equipos</div>
                    <div class="ui-stat-value">{{ $equipos->total() ?? '-' }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Disponibles</div>
                    <div class="ui-stat-value" style="color:#22c55e;">
                        {{ $equipos->where('estado','disponible')->count() ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">En Reparación</div>
                    <div class="ui-stat-value" style="color:#f59e0b;">
                        {{ $equipos->where('estado','en_reparacion')->count() ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Dañados</div>
                    <div class="ui-stat-value" style="color:#ef4444;">
                        {{ $equipos->where('estado','dañado')->count() ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('equipos.index') }}" id="filtros-form" class="row g-2 align-items-end">
                <div class="col-lg-3">
                    <label class="ui-label small fw-bold text-muted">Buscar</label>
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="ui-input" placeholder="Serial, marca, modelo..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <label class="ui-label small fw-bold text-muted">Estado</label>
                    <select name="estado" class="ui-select">
                        <option value="">Todos</option>
                        @foreach($estados as $key => $label)
                            <option value="{{ $key }}" {{ request('estado') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="ui-label small fw-bold text-muted">Marca</label>
                    <select name="marca" class="ui-select">
                        <option value="">Todas</option>
                        @foreach($marcas as $marca)
                            <option value="{{ $marca }}" {{ request('marca') === $marca ? 'selected' : '' }}>{{ $marca }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="ui-label small fw-bold text-muted">Disponibilidad</label>
                    <select name="disponibilidad" class="ui-select">
                        <option value="">Todas</option>
                        <option value="disponible" {{ request('disponibilidad') === 'disponible' ? 'selected' : '' }}>Disponibles</option>
                        <option value="no_disponible" {{ request('disponibilidad') === 'no_disponible' ? 'selected' : '' }}>No disponibles</option>
                    </select>
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('equipos.index') }}" class="ui-btn ui-btn-ghost"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-0">
            <table id="equipos-table" class="table dt-table nowrap no-footer" style="width:100%">
                <thead>
                    <tr>
                        <th class="ps-4" style="width:45px;">#</th>
                        <th>Equipo</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Almac.</th>
                        <th>Precio</th>
                        <th class="text-center">Garantía</th>
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
    const csrfToken = '{{ csrf_token() }}';
    const table = $('#equipos-table').DataTable({
        serverSide: true,
        ajax: {
            url: '{{ route('equipos.index') }}',
            type: 'GET',
            data: function(d) {
                d.search = $('input[name="search"]').val();
                d.estado = $('select[name="estado"]').val();
                d.marca = $('select[name="marca"]').val();
                d.disponibilidad = $('select[name="disponibilidad"]').val();
            }
        },
        columns: [
            {
                data: null,
                className: 'text-center ps-4',
                orderable: false,
                searchable: false,
                width: '45px',
                render: function(data, type, row, meta) {
                    return '<span class="text-muted fw-bold">' + (meta.row + 1) + '</span>';
                }
            },
            {
                data: 'serial_imei',
                render: function(data) {
                    if (!data) return '-';
                    return '<div class="d-flex align-items-center">' +
                        '<div class="avatar-circle text-white me-3 shadow-sm" style="background:linear-gradient(135deg,#38bdf8,#0ea5e9);width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:0.8rem;"><i class="bi bi-phone"></i></div>' +
                        '<div><div class="fw-bold text-dark fs-6">' + escapeHtml(data) + '</div>' +
                        '<small class="text-muted">' + escapeHtml(row.serial_esn || '') + '</small></div></div>';
                }
            },
            {
                data: 'marca',
                render: function(data) {
                    if (!data) return '-';
                    return '<span class="fw-semibold">' + escapeHtml(data) + '</span>';
                }
            },
            {
                data: 'modelo',
                render: function(data) {
                    if (!data) return '-';
                    return '<span class="text-muted">' + escapeHtml(data) + '</span>';
                }
            },
            {
                data: 'almacenamiento',
                className: 'text-center',
                render: function(data) {
                    if (data === '-' || !data) return '-';
                    return '<span class="badge bg-light text-secondary border rounded-pill">' + escapeHtml(data) + '</span>';
                }
            },
            {
                data: 'precio_venta',
                render: function(data) {
                    if (!data || data === '0.00') return '-';
                    return '<span class="fw-bold text-success">RD$ ' + escapeHtml(data) + '</span>';
                }
            },
            {
                data: null,
                className: 'text-center',
                orderable: false,
                render: function(data) {
                    var tipo = data.garantia_tipo || '-';
                    var activa = data.garantia_activa;
                    if (tipo === '-' || !tipo) return '<span class="text-muted small">-</span>';
                    var dotColor = activa ? '#22c55e' : '#ef4444';
                    var dotLabel = activa ? 'Activa' : 'Vencida';
                    return '<div class="d-flex flex-column align-items-center gap-1">' +
                        '<span class="small fw-semibold">' + escapeHtml(tipo) + '</span>' +
                        '<span class="d-flex align-items-center gap-1"><span class="garantia-dot" style="background:' + dotColor + ';"></span><small class="text-muted" style="font-size:0.65rem;">' + dotLabel + '</small></span>' +
                    '</div>';
                }
            },
            {
                data: 'badge_color',
                className: 'text-center',
                render: function(badgeColor) {
                    var icons = {
                        'success': 'bi-check-circle-fill',
                        'primary': 'bi-cart-check-fill',
                        'warning': 'bi-tools',
                        'danger': 'bi-exclamation-triangle-fill',
                        'info': 'bi-bookmark-fill',
                        'secondary': 'bi-gear',
                        'dark': 'bi-question-circle-fill'
                    };
                    var labels = {
                        'success': 'Disponible',
                        'primary': 'Vendido',
                        'warning': 'En Reparación',
                        'danger': 'Dañado',
                        'info': 'Reservado',
                        'secondary': 'Mantenimiento',
                        'dark': 'Otro'
                    };
                    var icon = icons[badgeColor] || 'bi-circle';
                    var label = labels[badgeColor] || 'N/A';
                    return '<span class="estado-badge bg-' + badgeColor + ' bg-opacity-10 text-' + badgeColor + '">' +
                        '<i class="bi ' + icon + '"></i> ' + label + '</span>';
                }
            },
            {
                data: null,
                className: 'text-end pe-4',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return renderAcciones(data.DT_RowIndex, {
                        show: '/equipos/' + data.DT_RowIndex,
                        edit: '/equipos/' + data.DT_RowIndex + '/edit',
                        delete: '/equipos/' + data.DT_RowIndex,
                        csrf: csrfToken,
                        nombre: data.serial_imei,
                        estado: data.estado
                    });
                }
            }
        ],
        language: {
            search: '',
            lengthMenu: '_MENU_',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ equipos',
            infoEmpty: 'No hay equipos',
            infoFiltered: '(de _MAX_ totales)',
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>'
            },
            zeroRecords: '<div class="text-center py-5">' +
                '<i class="bi bi-phone d-block mb-2" style="font-size:2.5rem;color:#cbd5e1;"></i>' +
                '<p class="fw-semibold mb-1" style="color:#475569;">No se encontraron equipos</p>' +
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
                    var data = '';
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

    $('#filtros-form').on('submit', function(e) {
        e.preventDefault();
        table.ajax.reload();
    });

    $('input[name="search"], select').on('change input', function() {
        clearTimeout(window._equiposTimeout);
        window._equiposTimeout = setTimeout(function() {
            table.ajax.reload();
        }, 400);
    });

    $(document).on('click', '.btn-delete-equipo', function() {
        var btn = $(this);
        var id = btn.data('id');
        var serial = btn.data('nombre');

        Swal.fire({
            title: '¿Eliminar equipo?',
            text: 'Se eliminará: "' + (serial || 'este equipo') + '". Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                deleteEquipo(id, btn);
            }
        });
    });

    function deleteEquipo(id, btn) {
        var formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', csrfToken);

        var row = btn.closest('tr');
        if (row) row.style.opacity = '0.5';

        fetch('/equipos/' + id, {
            method: 'POST',
            body: formData
        })
        .then(function(r) {
            if (!r.ok) throw new Error('El servidor respondió con estado ' + r.status);
            var ct = r.headers.get('content-type') || '';
            if (ct.indexOf('application/json') === -1) {
                return r.text().then(function(t) { throw new Error(t.substring(0, 200)); });
            }
            return r.json();
        })
        .then(function(data) {
            if (data.success) {
                if (row && row.closest('tbody')) {
                    table.row(row).remove().draw();
                }
                Swal.fire({ icon: 'success', title: 'Eliminado', text: data.message || 'Equipo eliminado correctamente', timer: 1500, showConfirmButton: false });
            } else {
                if (row) row.style.opacity = '1';
                Swal.fire({ icon: 'error', title: 'No se pudo eliminar', text: data.message || data.error || 'Error al eliminar equipo' });
            }
        })
        .catch(function(err) {
            if (row) row.style.opacity = '1';
            Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'No se pudo conectar con el servidor.' });
        });
    }

    function renderAcciones(id, opts) {
        var html = '<div class="d-flex justify-content-end gap-1">';
        if (opts.show) {
            html += '<a href="' + opts.show + '" class="ui-action ui-action-view" title="Ver">' +
                '<i class="bi bi-eye"></i></a>';
        }
        html += '<a href="' + opts.edit + '" class="ui-action ui-action-edit" title="Editar">' +
            '<i class="bi bi-pencil"></i></a>';
        
        if (opts.estado === 'disponible' || opts.estado === 'reservado') {
            var toggleUrl = '/equipos/' + id + '/toggle-reservar';
            var toggleIcon = opts.estado === 'reservado' ? 'bi-bookmark-x' : 'bi-bookmark';
            var toggleTitle = opts.estado === 'reservado' ? 'Cancelar Reserva' : 'Reservar';
            html += '<a href="' + toggleUrl + '" class="ui-action ui-action-print" title="' + toggleTitle + '">' +
                '<i class="bi ' + toggleIcon + '"></i></a>';
        }
        
        if (opts.delete) {
            html += '<button type="button" class="ui-action ui-action-delete border-0 btn-delete-equipo" data-id="' + id + '" data-nombre="' + escapeHtml(opts.nombre || '') + '" title="Eliminar">' +
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
});
</script>
@endpush
