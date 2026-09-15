@extends('layouts.app')
@section('title', 'Impresoras')

@push('styles')
@include('partials.premium-ui')
<style>
:root {
    --dt-accent: #f59e0b;
    --dt-accent-gradient: linear-gradient(135deg, #f59e0b, #fbbf24);
    --dt-accent-rgb: 245,158,11;
    --dt-violet: #6366f1;
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
.impresoras-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(245,158,11,.04);
    width: 100%;
    margin: 0;
    table-layout: auto;
}
.impresoras-table thead th {
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
.impresoras-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid var(--dt-gray-100);
    vertical-align: middle;
    font-size: .9rem;
    overflow: hidden;
    max-width: none;
}
.impresoras-table td.text-center,
.impresoras-table td.text-end {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.impresoras-table tbody tr:last-child td { border-bottom: none; }
.impresoras-table tbody tr { transition: background var(--dt-transition); }
.impresoras-table tbody tr:hover { background: rgba(245,158,11,.03); }

.text-brand { color: var(--dt-violet); }

.conn-badge {
    padding: 0.25em 0.65em;
    border-radius: 1rem;
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.3px;
}
.conn-local { background: rgba(100,116,139,0.12); color: #475569; }
.conn-usb { background: rgba(99,102,241,0.12); color: #6366f1; }
.conn-red { background: rgba(6,182,212,0.12); color: #0891b2; }
.conn-pdf { background: rgba(239,68,68,0.12); color: #dc2626; }

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

.manual-card {
    background: linear-gradient(135deg, rgba(251,191,36,0.05), rgba(245,158,11,0.08));
    border: 1px solid rgba(245,158,11,0.2);
}
.manual-step {
    display: flex;
    gap: 12px;
    padding: 10px 0;
}
.manual-step-number {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--dt-accent);
    color: white;
    font-size: 0.8rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-printer"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Impresoras</h4>
                    <div class="ui-header-meta">Configura tus impresoras para tickets y comprobantes</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('impresoras.create')
                <a href="{{ route('impresoras.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Impresora
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('impresoras.index') }}" id="filtros-form" class="row g-2 align-items-end">
                <div class="col-lg-3">
                    <label class="ui-label small fw-bold text-muted">Buscar</label>
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="nombre" id="busqueda-impresora" class="ui-input" placeholder="Nombre de impresora..." value="{{ request('nombre') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-3">
                    <label class="ui-label small fw-bold text-muted">Sucursal</label>
                    <select name="sucursal_id" id="filter-sucursal" class="ui-select">
                        <option value="">Todas</option>
                        @foreach($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}" {{ request('sucursal_id') == $sucursal->id ? 'selected' : '' }}>
                            {{ $sucursal->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="ui-label small fw-bold text-muted">Estado</label>
                    <select name="activo" id="filter-activo" class="ui-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activas</option>
                        <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivas</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="ui-label small fw-bold text-muted">Auto-imprimir</label>
                    <select name="auto_imprimir" id="filter-auto" class="ui-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('auto_imprimir') === '1' ? 'selected' : '' }}>Con auto-imprimir</option>
                        <option value="0" {{ request('auto_imprimir') === '0' ? 'selected' : '' }}>Sin auto-imprimir</option>
                    </select>
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('impresoras.index') }}" class="ui-btn ui-btn-ghost"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-0">
            <div id="impresoras-table_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                <table id="impresoras-table" class="table impresoras-table no-footer" style="min-width:900px;">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width:50px;" data-label="#">#</th>
                            <th style="min-width:180px;" data-label="impresora">Impresora</th>
                            <th style="min-width:140px;" data-label="sucursal">Sucursal</th>
                            <th style="width:90px;" class="text-center" data-label="conexion">Conexion</th>
                            <th style="width:70px;" class="text-center" data-label="papel">Papel</th>
                            <th style="width:90px;" class="text-center" data-label="auto-imprimir">Auto</th>
                            <th style="width:90px;" class="text-center" data-label="estado">Estado</th>
                            <th style="width:100px;" class="text-end pe-3" data-label="acciones">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="ui-card mt-4 manual-card" style="--delay:.2s">
        <div class="ui-card-accent" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);"></div>
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                <div>
                    <h5 class="fw-bold mb-1"><i class="bi bi-book me-2" style="color:#f59e0b;"></i>Manual de Configuracion</h5>
                    <p class="text-muted mb-0 small">Configura tu impresora termica en 4 pasos simples</p>
                </div>
                <a href="{{ route('impresoras.manual') }}" target="_blank" class="ui-btn ui-btn-primary btn-sm rounded-pill">
                    <i class="bi bi-fullscreen me-1"></i> Ver Manual Completo
                </a>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="manual-step">
                        <div class="manual-step-number">1</div>
                        <div>
                            <strong>Conecta la impresora</strong>
                            <p class="text-muted small mb-0 mt-1">USB conecta al PC de la caja o Red/anota la IP (ej: 192.168.1.50). Chrome usa impresoras del sistema.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="manual-step">
                        <div class="manual-step-number">2</div>
                        <div>
                            <strong>Registra en el sistema</strong>
                            <p class="text-muted small mb-0 mt-1">Clic en "Nueva Impresora" arriba. Nombre, sucursal, tipo, tamano de papel. Activa auto-imprimir si deseas impresion automatica.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="manual-step">
                        <div class="manual-step-number">3</div>
                        <div>
                            <strong>Configura Chrome</strong>
                            <p class="text-muted small mb-0 mt-1">Chrome > 3 puntos > Configuracion > Impresiones. Selecciona tu impresora y marca "Predeterminada". Configura tamano personalizado (80mm x infinito).</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="manual-step">
                        <div class="manual-step-number">4</div>
                        <div>
                            <strong>Prueba con una venta</strong>
                            <p class="text-muted small mb-0 mt-1">Crea una venta de prueba de RD$100. Clic en "Imprimir Ticket". Verifica que el ticket sale con formato correcto en tu impresora termica.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-link text-decoration-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#manualResumen" style="color:#f59e0b; font-weight:600;">
                    <i class="bi bi-chevron-expand me-1"></i> Expandir resumen de problemas frecuentes <i class="bi bi-chevron-expand me-1"></i>
                </button>
                <div class="collapse mt-3" id="manualResumen">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <strong style="color:#ef4444;">Q: No aparece mi impresora en Chrome</strong>
                                <p class="text-muted small mb-0 mt-1">Chrome usa las impresoras del sistema. Verifica que aparezca en Windows > Dispositivos > Impresoras. Reinstala el driver si es necesario.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <strong style="color:#ef4444;">Q: Se imprime en papel doble</strong>
                                <p class="text-muted small mb-0 mt-1">En el dialogo de impresion > Mas configuracion > Tamaño: Personalizado. Ancho: 80mm (o 58mm), Alto: Sin limite. Margenes: Sin margenes.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <strong style="color:#ef4444;">Q: La impresion sale cortada</strong>
                                <p class="text-muted small mb-0 mt-1">Al crear la impresora, selecciona el tamano correcto (58mm o 80mm) y las caracteras por linea adecuados. 58mm=42 caracteres, 80mm=48-80 caracteres.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <strong style="color:#ef4444;">Q: Quiero impresion sin dialogo</strong>
                                <p class="text-muted small mb-0 mt-1">Chrome no permite impresion 100% automatica. Setea tu impresora como "Predeterminada" y activa "Auto-imprimir" en el sistema. El dialogo se abrira con tu impresora pre-seleccionada.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    const API_BASE = '/impresoras';
    const csrfToken = '{{ csrf_token() }}';
    const canEdit = {{ auth()->user()->can('impresoras.edit') ? 'true' : 'false' }};
    const canDelete = {{ auth()->user()->can('impresoras.delete') ? 'true' : 'false' }};
    const canCreate = {{ auth()->user()->can('impresoras.create') ? 'true' : 'false' }};

    let lastDataHash = '';

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
                toast.fire({ icon: 'info', title: 'Datos actualizados' });
            } else {
                toast.fire({ title: 'Datos sincronizados' });
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

    function renderConnBadge(conn) {
        const cls = {
            'local': 'conn-local',
            'usb': 'conn-usb',
            'red': 'conn-red',
            'pdf': 'conn-pdf'
        }[conn] || 'conn-local';
        const labels = { local: 'Local', usb: 'USB', red: 'Red', pdf: 'PDF' };
        return '<span class="conn-badge ' + cls + '">' + (labels[conn] || conn) + '</span>';
    }

    function renderEstado(activo) {
        const cls = activo ? 'success' : 'secondary';
        const icon = activo ? 'check-circle-fill' : 'x-circle-fill';
        const label = activo ? 'Activa' : 'Inactiva';
        return '<span class="badge rounded-pill bg-' + cls + ' bg-opacity-10 text-' + cls + ' fw-semibold">' +
            '<i class="bi bi-' + icon + ' me-1"></i>' + label + '</span>';
    }

    function renderAcciones(data) {
        let html = '<div class="d-flex justify-content-end gap-1">';
        html += '<a href="' + API_BASE + '/' + data.id + '" class="ui-action" title="Ver"><i class="bi bi-eye"></i></a>';
        if (canEdit) {
            html += '<a href="' + API_BASE + '/' + data.id + '/edit" class="ui-action" title="Editar"><i class="bi bi-pencil"></i></a>';
        }
        if (canDelete) {
            html += '<button type="button" class="ui-action btn-delete-impresora" title="Eliminar" data-id="' + data.id + '" data-nombre="' + escapeHtml(data.nombre || '') + '"><i class="bi bi-trash"></i></button>';
        }
        html += '</div>';
        return html;
    }

    const table = $('#impresoras-table').DataTable({
        ajax: {
            url: '{{ route("impresoras.ajax") }}',
            type: 'GET',
            dataSrc: function(json) {
                const newHash = generateHash(json.data);
                if (lastDataHash && newHash !== lastDataHash) {
                    showRefreshToast(true);
                }
                lastDataHash = newHash;
                return json.data;
            }
        },
        columns: [
            {
                data: null,
                className: 'text-center ps-4',
                orderable: true,
                searchable: false,
                width: '50px',
                render: function(data, type) {
                    if (type === 'display') return '<span class="text-muted fw-bold">' + data.id + '</span>';
                    return data.id;
                }
            },
            {
                data: 'nombre',
                orderable: true,
                searchable: true,
                render: function(data, type, row) {
                    return '<div class="fw-bold">' + escapeHtml(data || '') + '</div>';
                }
            },
            {
                data: 'sucursal_raw',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return row.sucursal;
                }
            },
            {
                data: 'tipo_conexion',
                className: 'text-center',
                orderable: false,
                render: function(data) {
                    return renderConnBadge(data);
                }
            },
            {
                data: 'papel',
                className: 'text-center',
                orderable: false,
                render: function(data) {
                    return '<span class="badge bg-light text-secondary border rounded-pill">' + escapeHtml(data) + '</span>';
                }
            },
            {
                data: 'auto_imprimir',
                className: 'text-center',
                orderable: false,
                render: function(data) {
                    if (data === 'Si') {
                        return '<span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Sí</span>';
                    }
                    return '<span class="text-muted small"><i class="bi bi-x-circle me-1"></i>No</span>';
                }
            },
            {
                data: 'activo',
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    return '<span class="status-badge">' + renderEstado(data) + '</span>';
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
            info: 'Mostrando _START_ a _END_ de _TOTAL_ impresoras',
            infoEmpty: 'No hay impresoras',
            infoFiltered: '(de _MAX_ totales)',
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>'
            },
            zeroRecords: '<div class="text-center py-5"><i class="bi bi-printer d-block mb-2" style="font-size:2.5rem;color:#cbd5e1;"></i><p class="fw-semibold mb-1" style="color:#475569;">No se encontraron impresoras</p><p class="text-muted small mb-0">Clic en "Nueva Impresora" para agregar una.</p></div>'
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
        const params = new URLSearchParams();
        const nombre = $('#busqueda-impresora').val();
        const sucursal = $('#filter-sucursal').val();
        const activo = $('#filter-activo').val();
        const auto = $('#filter-auto').val();
        if (nombre) params.set('nombre', nombre);
        if (sucursal) params.set('sucursal_id', sucursal);
        if (activo) params.set('activo', activo);
        if (auto) params.set('auto_imprimir', auto);
        table.ajax.url('{{ route("impresoras.ajax") }}?' + params.toString()).load();
    }

    $('#filtros-form').on('submit', function(e) {
        e.preventDefault();
        reloadWithFilters();
    });

    let searchTimeout = null;
    $('#busqueda-impresora').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            reloadWithFilters();
        }, 300);
    });

    $('#filter-sucursal, #filter-activo, #filter-auto').on('change', function() {
        reloadWithFilters();
    });

    // Toggle activo
    $(document).on('click', '.status-badge', function() {
        const badge = $(this);
        const isActive = badge.find('i.check-circle-fill').length > 0;

        Swal.fire({
            title: isActive ? '¿Desactivar impresora?' : '¿Activar impresora?',
            text: isActive ? 'La impresora quedará inactiva y no se usará para imprimir.' : 'La impresora volverá a estar activa.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: isActive ? '#dc2626' : '#22c55e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, ' + (isActive ? 'desactivar' : 'activar'),
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                const id = badge.closest('tr').find('.ui-action[href*="/edit"]').attr('href').split('/').pop();
                fetch(API_BASE + '/' + id + '/toggle', {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res.success) {
                        table.ajax.reload();
                        Swal.fire({ icon: 'success', title: res.activa ? 'Impresora activada' : 'Impresora desactivada', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                    }
                })
                .catch(function() {
                    Swal.fire('Error', 'No se pudo cambiar el estado.', 'error');
                });
            }
        });
    });

    // Delete
    $(document).on('click', '.btn-delete-impresora', function() {
        const btn = $(this);
        const row = btn.closest('tr')[0];
        const id = btn.data('id');
        const nombre = btn.data('nombre');

        Swal.fire({
            title: '¿Eliminar impresora?',
            text: 'Se eliminará: "' + escapeHtml(nombre) + '". Las ventas impresas con esta impresora mantendran su registro.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
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
                        table.row(row).remove().draw();
                        Swal.fire({ icon: 'success', title: 'Eliminado', text: data.message, timer: 1500, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: 'No se pudo eliminar', text: data.message });
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
