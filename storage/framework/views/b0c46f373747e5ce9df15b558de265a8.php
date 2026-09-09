<?php $__env->startSection('title', 'Categorías'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
:root {
    --dt-accent: #ec4899;
    --dt-accent-gradient: linear-gradient(135deg, #ec4899, #f472b6);
    --dt-accent-rgb: 236,72,153;
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
    --dt-shadow: 0 2px 8px rgba(236,72,153,.25);
    --dt-transition: 0.15s;
}
.categorias-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(236,72,153,.04);
    width: 100%;
    margin: 0;
    table-layout: auto;
}
.categorias-table thead th {
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
.categorias-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid var(--dt-gray-100);
    vertical-align: middle;
    font-size: .9rem;
    overflow: hidden;
    max-width: none;
}
.categorias-table td.text-center,
.categorias-table td.text-end {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.categorias-table tbody tr:last-child td { border-bottom: none; }
.categorias-table tbody tr { transition: background var(--dt-transition); }
.categorias-table tbody tr:hover { background: rgba(236,72,153,.03); }

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

.text-brand { color: var(--dt-violet); }

@keyframes pulse-activo {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.85; transform: scale(1.02); }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
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
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('categorias.create')): ?>
                <a href="<?php echo e(route('categorias.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Categoría
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-3">
            <form method="GET" action="<?php echo e(route('categorias.index')); ?>" id="filtros-form" class="row g-2 align-items-end">
                <div class="col-lg-4">
                    <label class="ui-label small fw-bold text-muted">Buscar</label>
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="nombre" id="busqueda-categoria" class="ui-input" placeholder="Nombre de categoría..." value="<?php echo e(request('nombre')); ?>" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-3">
                    <label class="ui-label small fw-bold text-muted">Estado</label>
                    <select name="activo" id="filter-activo" class="ui-select">
                        <option value="">Todos</option>
                        <option value="1" <?php echo e(request('activo') === '1' ? 'selected' : ''); ?>>Activas</option>
                        <option value="0" <?php echo e(request('activo') === '0' ? 'selected' : ''); ?>>Inactivas</option>
                    </select>
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="<?php echo e(route('categorias.index')); ?>" class="ui-btn ui-btn-ghost"><i class="bi bi-x-lg"></i></a>
                </div>
                <div class="col-lg-3 text-end">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="<?php echo e(route('categorias.importar')); ?>" class="ui-btn ui-btn-primary rounded-pill shadow-sm fw-medium">
                            <i class="bi bi-upload me-1"></i> Importar CSV
                        </a>
                        <a href="<?php echo e(route('categorias.exportar', request()->all())); ?>" class="ui-btn ui-btn-primary rounded-pill shadow-sm fw-medium">
                            <i class="bi bi-file-excel me-1"></i> Excel
                        </a>
                        <a href="<?php echo e(route('categorias.pdf', request()->all())); ?>" class="ui-btn ui-btn-primary rounded-pill shadow-sm fw-medium">
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
            <div id="categorias-table_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                <table id="categorias-table" class="table categorias-table no-footer" style="min-width:700px;">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width:50px;" data-label="#">#</th>
                            <th style="min-width:200px;" data-label="categoría">Categoría</th>
                            <th style="min-width:250px;" data-label="descripción">Descripción</th>
                            <th style="width:100px;" class="text-center" data-label="productos">Productos</th>
                            <th style="width:100px;" class="text-center" data-label="estado">Estado</th>
                            <th style="width:100px;" class="text-end pe-3" data-label="acciones">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(function() {
    const API_BASE = '/categorias';
    const csrfToken = '<?php echo e(csrf_token()); ?>';
    const canEdit = <?php echo e(auth()->user()->can('categorias.edit') ? 'true' : 'false'); ?>;
    const canDelete = <?php echo e(auth()->user()->can('categorias.delete') ? 'true' : 'false'); ?>;

    // Auto-refresh state
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
                toast.fire({ icon: 'info', title: '⚡ Datos actualizados' });
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

    function renderEstado(activa) {
        const cls = activa ? 'success' : 'secondary';
        const icon = activa ? 'check-circle-fill' : 'x-circle-fill';
        const label = activa ? 'Activa' : 'Inactiva';
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
            html += '<button type="button" class="ui-action btn-delete-categoria" title="Eliminar" data-id="' + data.id + '" data-nombre="' + escapeHtml(data.nombre || '') + '"><i class="bi bi-trash"></i></button>';
        }
        html += '</div>';
        return html;
    }

    const table = $('#categorias-table').DataTable({
        ajax: {
            url: '<?php echo e(route("categorias.ajax")); ?>',
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
                    const initial = (data || '?').charAt(0).toUpperCase();
                    const color = row.color || '#6366f1';
                    const icono = row.icono || 'bi-grid';
                    return '<div class="d-flex align-items-center">' +
                        '<div class="avatar-circle text-white me-3 shadow-sm" style="background:' + color + ';width:36px;height:36px;font-size:1rem;">' + initial + '</div>' +
                        '<div class="fw-bold fs-6 text-brand">' + escapeHtml(data || '') + '</div>' +
                    '</div>';
                }
            },
            {
                data: 'descripcion',
                defaultContent: '<span class="text-muted small">Sin descripción</span>',
                render: function(data) {
                    if (!data) return '<span class="text-muted small">Sin descripción</span>';
                    return '<div class="text-muted small text-truncate" style="max-width:250px;" title="' + escapeHtml(data) + '">' + escapeHtml(data) + '</div>';
                }
            },
            {
                data: 'productos_count',
                className: 'text-center',
                render: function(data) {
                    const count = parseInt(data || 0);
                    return '<span class="badge bg-light text-secondary border rounded-pill">' +
                        '<i class="bi bi-box-seam me-1"></i> ' + count + '</span>';
                }
            },
            {
                data: 'activa',
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    const cls = data ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary';
                    const icon = data ? 'check-circle-fill' : 'x-circle-fill';
                    const label = data ? 'Activa' : 'Inactiva';
                    return '<span class="badge rounded-pill ' + cls + '" style="cursor:pointer;" data-id="' + row.id + '" title="Clic para cambiar estado">' +
                        '<i class="bi bi-' + icon + ' me-1"></i> <span class="badge-label">' + label + '</span></span>';
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
            info: 'Mostrando _START_ a _END_ de _TOTAL_ categorías',
            infoEmpty: 'No hay categorías',
            infoFiltered: '(de _MAX_ totales)',
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>'
            },
            zeroRecords: '<div class="text-center py-5"><i class="bi bi-tags d-block mb-2" style="font-size:2.5rem;color:#cbd5e1;"></i><p class="fw-semibold mb-1" style="color:#475569;">No se encontraron categorías</p><p class="text-muted small mb-0">Intenta ajustar los filtros de búsqueda.</p></div>'
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

    // Filter
    function reloadWithFilters() {
        const params = new URLSearchParams();
        const nombre = $('#busqueda-categoria').val();
        const activo = $('#filter-activo').val();
        if (nombre) params.set('nombre', nombre);
        if (activo) params.set('activo', activo);
        table.ajax.url('<?php echo e(route("categorias.ajax")); ?>?' + params.toString()).load();
    }

    $('#filtros-form').on('submit', function(e) {
        e.preventDefault();
        reloadWithFilters();
    });

    // Real-time search
    let searchTimeout = null;
    $('#busqueda-categoria').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            reloadWithFilters();
        }, 300);
    });

    $('#filter-activo').on('change', function() {
        reloadWithFilters();
    });

    // Toggle activa
    $(document).on('click', '.badge[data-id]', function() {
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
        }).then(function(result) {
            if (result.isConfirmed) {
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
                        badge.removeClass('bg-success bg-opacity-10 text-success bg-secondary bg-opacity-10 text-secondary');
                        if (res.activa) {
                            badge.classList.add('bg-success', 'bg-opacity-10', 'text-success');
                            badge.querySelector('i').className = 'bi bi-check-circle-fill me-1';
                        } else {
                            badge.classList.add('bg-secondary', 'bg-opacity-10', 'text-secondary');
                            badge.querySelector('i').className = 'bi bi-x-circle-fill me-1';
                        }
                        const labelEl = badge.querySelector('.badge-label');
                        if (labelEl) {
                            labelEl.textContent = res.activa ? 'Activa' : 'Inactiva';
                        }
                        Swal.fire({ icon: 'success', title: res.activa ? 'Categoría activada' : 'Categoría desactivada', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                    }
                })
                .catch(function() {
                    Swal.fire('Error', 'No se pudo cambiar el estado.', 'error');
                });
            }
        });
    });

    // Delete
    $(document).on('click', '.btn-delete-categoria', function() {
        const btn = $(this);
        const row = btn.closest('tr')[0];
        const id = btn.data('id');
        const nombre = btn.data('nombre');

        Swal.fire({
            title: '¿Eliminar categoría?',
            text: 'Se eliminará: "' + escapeHtml(nombre) + '". Los productos quedarán sin categoría.',
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/categorias/index.blade.php ENDPATH**/ ?>