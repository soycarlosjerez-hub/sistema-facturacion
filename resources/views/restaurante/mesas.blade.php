@extends('layouts.app')
@section('title', 'Gestión de Mesas')
@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
:root {
    --dt-accent: #f97316;
    --dt-accent-gradient: linear-gradient(135deg, #f97316, #f59e0b);
    --dt-accent-rgb: 249,115,22;
}
#mesas-table { width: 100% !important; margin: 0; }
#mesas-table thead th {
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
#mesas-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
#mesas-table tbody tr:last-child td { border-bottom: none; }
#mesas-table tbody tr { transition: background .15s; }
#mesas-table tbody tr:hover { background: rgba(249,115,22,.03); }
.mesa-badge-cat {
    padding: .35em .7em;
    border-radius: 2rem;
    font-weight: 600;
    font-size: .75rem;
    display: inline-flex;
    align-items: center;
    gap: .3rem;
}
.form-switch-mesa {
    width: 2.5em !important;
    height: 1.25em !important;
    cursor: pointer;
}
.form-switch-mesa:checked {
    background-color: var(--dt-accent, #f97316) !important;
    border-color: var(--dt-accent, #f97316) !important;
}
.premium-btn-edit {
    background: rgba(249,115,22,.1);
    color: #f97316;
    border: 1.5px solid rgba(249,115,22,.2);
}
.premium-btn-edit:hover {
    background: #f97316;
    color: #fff;
    border-color: #f97316;
}
body.dark-mode #mesas-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-bottom-color: #1e293b;
}
body.dark-mode #mesas-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
body.dark-mode #mesas-table tbody tr:hover {
    background: rgba(249,115,22,.05);
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
                    <i class="bi bi-cup-straw"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Gestión de Mesas</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-grid me-1"></i>
                        Administra las mesas del restaurante
                    </small>
                </div>
            </div>
            <div>
                <button class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);" data-bs-toggle="modal" data-bs-target="#mesaModal" onclick="abrirModalCrear()">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Mesa
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 shadow-sm d-flex align-items-center mb-4">
            <i class="bi bi-check-circle-fill me-2"></i><div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 rounded-3 shadow-sm d-flex align-items-center mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><div>{{ session('error') }}</div>
        </div>
    @endif

    <div class="premium-card mb-4" style="animation-delay:.1s;">
        <div class="card-accent" style="background:var(--dt-accent-gradient);"></div>
        <div class="card-body p-3">
            <form id="filtros-form" class="row g-2 align-items-end">
                <div class="col-lg-4">
                    <label class="form-label small fw-bold text-muted">Buscar mesa</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="busqueda-mesa" class="form-control bg-light border-0" placeholder="Número, nombre..." autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-3">
                    <label class="form-label small fw-bold text-muted">Estado</label>
                    <select id="filter-estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="disponible">Disponible</option>
                        <option value="ocupada">Ocupada</option>
                        <option value="reservada">Reservada</option>
                        <option value="inactiva">Inactiva</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label small fw-bold text-muted">Ubicación</label>
                    <select id="filter-ubicacion" class="form-select">
                        <option value="">Todas</option>
                        @foreach($ubicaciones as $ubi)
                            <option value="{{ $ubi->nombre }}">{{ $ubi->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary flex-grow-1" style="background:var(--dt-accent-gradient);border:none;"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('restaurante.mesas.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card" style="animation-delay:.15s;">
        <div class="card-accent" style="background:var(--dt-accent-gradient);"></div>
        <div class="card-body p-0">
            <table id="mesas-table" class="table dt-table nowrap no-footer" style="width:100%">
                <thead>
                    <tr>
                        <th class="ps-4" style="width:50px;">#</th>
                        <th>Número</th>
                        <th>Nombre</th>
                        <th>Capacidad</th>
                        <th>Ubicación</th>
                        <th>Categoría</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Activa</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal crear/editar --}}
<div class="modal fade" id="mesaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="mesa-form" method="POST" action="{{ route('restaurante.mesa.store') }}">
                @csrf
                <input type="hidden" name="_method" id="mesa-method" value="POST">
                <div class="modal-header border-0">
                    <h6 class="modal-title fw-bold" id="mesa-modal-title">Nueva Mesa</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Número <span class="text-danger">*</span></label>
                        <input type="text" name="numero" id="mesa-numero" class="form-control rounded-3" required placeholder="01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nombre</label>
                        <input type="text" name="nombre" id="mesa-nombre" class="form-control rounded-3" placeholder="Ej. Terraza, VIP">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Capacidad <span class="text-danger">*</span></label>
                            <input type="number" name="capacidad" id="mesa-capacidad" class="form-control rounded-3" value="4" min="1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Ubicación</label>
                            <select name="ubicacion_id" id="mesa-ubicacion" class="form-select rounded-3">
                                <option value="">Sin ubicación</option>
                                @foreach($ubicaciones as $ubi)
                                    <option value="{{ $ubi->id }}">{{ $ubi->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Categoría</label>
                        <select name="categoria_id" id="mesa-categoria" class="form-select rounded-3">
                            <option value="">Sin categoría</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="mesa-estado-group">
                        <label class="form-label small fw-bold">Estado</label>
                        <select name="estado" id="mesa-estado" class="form-select rounded-3">
                            <option value="disponible">Disponible</option>
                            <option value="ocupada">Ocupada</option>
                            <option value="reservada">Reservada</option>
                            <option value="inactiva">Inactiva</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" style="background:var(--dt-accent-gradient);border:none;">
                        <i class="bi bi-check-lg me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function abrirModalCrear() {
    document.getElementById('mesa-modal-title').textContent = 'Nueva Mesa';
    document.getElementById('mesa-method').value = 'POST';
    document.getElementById('mesa-form').action = '{{ route("restaurante.mesa.store") }}';
    document.getElementById('mesa-estado-group').classList.add('d-none');
    document.getElementById('mesa-form').reset();
    document.getElementById('mesa-capacidad').value = '4';
}

function editarMesa(id) {
    fetch('/restaurante/mesas/' + id)
        .then(r => r.json())
        .then(mesa => {
            document.getElementById('mesa-modal-title').textContent = 'Editar Mesa';
            document.getElementById('mesa-method').value = 'PUT';
            document.getElementById('mesa-form').action = '/restaurante/mesa/' + id + '/update';
            document.getElementById('mesa-numero').value = mesa.numero;
            document.getElementById('mesa-nombre').value = mesa.nombre || '';
            document.getElementById('mesa-capacidad').value = mesa.capacidad;
            document.getElementById('mesa-ubicacion').value = mesa.ubicacion_id || '';
            document.getElementById('mesa-categoria').value = mesa.categoria_id || '';
            document.getElementById('mesa-estado').value = mesa.estado;
            document.getElementById('mesa-estado-group').classList.remove('d-none');
            new bootstrap.Modal(document.getElementById('mesaModal')).show();
        });
}

document.getElementById('mesaModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('mesa-modal-title').textContent = 'Nueva Mesa';
    document.getElementById('mesa-method').value = 'POST';
    document.getElementById('mesa-form').action = '{{ route("restaurante.mesa.store") }}';
    document.getElementById('mesa-estado-group').classList.add('d-none');
});

$(function() {
    const mesas = @json($mesasAll);
    const csrfToken = '{{ csrf_token() }}';

    const table = $('#mesas-table').DataTable({
        data: mesas,
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
                data: 'numero',
                orderable: true,
                searchable: true,
                render: function(data) {
                    return '<span class="fw-semibold">' + escapeHtml(data) + '</span>';
                }
            },
            {
                data: 'nombre',
                defaultContent: '<span class="text-muted">—</span>',
                render: function(data) {
                    return data ? escapeHtml(data) : '<span class="text-muted">—</span>';
                }
            },
            {
                data: 'capacidad',
                className: 'text-center',
                render: function(data) {
                    return '<i class="bi bi-people me-1 text-muted"></i>' + data;
                }
            },
            {
                data: null,
                render: function(data) {
                    if (data.ubicacion && data.ubicacion.nombre) {
                        return '<span class="fw-medium">' + escapeHtml(data.ubicacion.nombre) + '</span>';
                    }
                    return '<span class="text-muted">—</span>';
                }
            },
            {
                data: null,
                render: function(data) {
                    if (data.categoria) {
                        const bg = (data.categoria.color || '#6366f1') + '20';
                        const color = data.categoria.color || '#6366f1';
                        const icono = data.categoria.icono || 'bi-tag';
                        return '<span class="mesa-badge-cat" style="background:' + bg + ';color:' + color + ';">' +
                            '<i class="bi ' + icono + '"></i>' + escapeHtml(data.categoria.nombre) + '</span>';
                    }
                    return '<span class="text-muted">—</span>';
                }
            },
            {
                data: 'estado',
                className: 'text-center',
                render: function(data) {
                    const estados = {
                        disponible: { label: 'Disponible', cls: 'bg-success' },
                        ocupada:    { label: 'Ocupada', cls: 'bg-danger' },
                        reservada:  { label: 'Reservada', cls: 'bg-warning text-dark' },
                        inactiva:   { label: 'Inactiva', cls: 'bg-secondary' },
                    };
                    const e = estados[data] || estados.disponible;
                    return '<span class="badge ' + e.cls + ' rounded-pill">' + e.label + '</span>';
                }
            },
            {
                data: 'activa',
                className: 'text-center',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    const checked = data ? 'checked' : '';
                    return '<div class="form-check form-switch d-flex justify-content-center">' +
                        '<input class="form-check-input form-switch-mesa toggle-activa" type="checkbox" ' + checked +
                        ' data-id="' + row.id + '"></div>';
                }
            },
            {
                data: null,
                className: 'text-end pe-4',
                orderable: false,
                searchable: false,
                render: function(data) {
                    const id = data.id;
                    let html = '<div class="d-flex justify-content-end gap-1">';
                    html += '<button class="premium-btn-edit btn-editar-mesa" data-id="' + id + '" title="Editar">' +
                        '<i class="bi bi-pencil"></i></button>';
                    if (data.estado !== 'ocupada') {
                        html += '<form action="/restaurante/mesa/' + id + '" method="POST" class="d-inline form-eliminar-mesa" data-numero="' + escapeHtml(data.numero) + '">' +
                            '@csrf<input type="hidden" name="_method" value="DELETE">' +
                            '<button type="submit" class="premium-btn-delete border-0" title="Eliminar">' +
                            '<i class="bi bi-trash"></i></button></form>';
                    } else {
                        html += '<button class="premium-btn-delete border-0" disabled title="No se puede eliminar una mesa ocupada">' +
                            '<i class="bi bi-trash"></i></button>';
                    }
                    html += '</div>';
                    return html;
                }
            }
        ],
        language: {
            search: '',
            lengthMenu: '_MENU_',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ mesas',
            infoEmpty: 'No hay mesas',
            infoFiltered: '(de _MAX_ totales)',
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>'
            },
            zeroRecords: '<div class="text-center py-5">' +
                '<i class="bi bi-grid-3x3-gap d-block mb-2" style="font-size:2.5rem;color:#cbd5e1;"></i>' +
                '<p class="fw-semibold mb-1" style="color:#475569;">No se encontraron mesas</p>' +
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
             '<"row px-3 pb-2"<"col-sm-5"i><"col-sm-7"p>>',
        createdRow: function(row, data, dataIndex) {
            $(row).attr('id', 'row-' + data.id);
        }
    });

    // Filtros
    $('#filtros-form').on('submit', function(e) {
        e.preventDefault();
        const valBusqueda = $('#busqueda-mesa').val();
        const valEstado = $('#filter-estado').val();
        const valUbicacion = $('#filter-ubicacion').val();

        table.search(valBusqueda).draw();

        $.fn.dataTable.ext.search.push(function(settings, data) {
            const estado = data[6] || '';
            const ubicacion = data[4] || '';

            if (valEstado) {
                const badge = $('<span>' + estado + '</span>');
                const texto = badge.text().trim().toLowerCase();
                if (texto !== valEstado) return false;
            }
            if (valUbicacion && ubicacion.trim() !== valUbicacion) return false;

            return true;
        });

        table.draw();
        $.fn.dataTable.ext.search.pop();
    });

    // Búsqueda en tiempo real
    let searchTimeout;
    $('#busqueda-mesa').on('input', function() {
        clearTimeout(searchTimeout);
        const val = $(this).val();
        searchTimeout = setTimeout(function() {
            table.search(val).draw();
        }, 300);
    });

    // Editar mesa
    $(document).on('click', '.btn-editar-mesa', function() {
        editarMesa($(this).data('id'));
    });

    // Toggle activa
    $(document).on('change', '.toggle-activa', function() {
        const chk = $(this);
        const id = chk.data('id');
        const activa = chk.prop('checked');

        fetch('/restaurante/mesa/' + id + '/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ activa: activa, _method: 'PUT' })
        }).then(r => {
            if (!r.ok) {
                chk.prop('checked', !activa);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo actualizar' });
                }
            }
        }).catch(() => {
            chk.prop('checked', !activa);
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo actualizar' });
            }
        });
    });

    // Confirmar eliminación
    $(document).on('submit', '.form-eliminar-mesa', function(e) {
        e.preventDefault();
        const form = $(this);
        const numero = form.data('numero');
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '¿Eliminar mesa ' + numero + '?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#ef4444'
            }).then(function(result) {
                if (result.isConfirmed) {
                    form.off('submit').submit();
                }
            });
        } else {
            if (confirm('¿Eliminar mesa ' + numero + '? Esta acción no se puede deshacer.')) {
                form.off('submit').submit();
            }
        }
    });

    function escapeHtml(str) {
        return String(str || '').replace(/[&<>"']/g, function(c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }
});
</script>
@endpush
@endsection
