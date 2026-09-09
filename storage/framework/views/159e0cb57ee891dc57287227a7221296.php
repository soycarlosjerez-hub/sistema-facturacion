<?php $__env->startSection('title', 'Reservaciones'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.ui-table .dropdown-menu { z-index: 1050 !important; position: absolute !important; }
.ui-table tbody td .dropdown { position: relative; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Reservaciones</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-calendar-check me-1"></i>Gestión de reservaciones de mesas
                        <span class="divider">·</span>
                        <i class="bi bi-list-ul me-1"></i>
                        <span><?php echo e($reservaciones->total()); ?> registro(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <button type="button" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#reservaModal">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Reservación
                </button>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible rounded-4 border-0 shadow-sm fade show mb-4" role="alert" style="border-left:4px solid #10b981 !important;">
        <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible rounded-4 border-0 shadow-sm fade show mb-4" role="alert" style="border-left:4px solid #dc3545 !important;">
        <i class="bi bi-exclamation-triangle me-2"></i><?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="ui-card mb-4">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form method="GET" action="<?php echo e(route('restaurante.reservaciones.index')); ?>" id="filtros-form" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="busqueda" class="ui-input"
                               placeholder="Buscar por cliente o mesa..." value="<?php echo e(request('busqueda')); ?>" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <select name="estado" class="ui-select">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" <?php echo e(request('estado') == 'pendiente' ? 'selected' : ''); ?>>Pendiente</option>
                        <option value="confirmada" <?php echo e(request('estado') == 'confirmada' ? 'selected' : ''); ?>>Confirmada</option>
                        <option value="cumplida" <?php echo e(request('estado') == 'cumplida' ? 'selected' : ''); ?>>Cumplida</option>
                        <option value="cancelada" <?php echo e(request('estado') == 'cancelada' ? 'selected' : ''); ?>>Cancelada</option>
                    </select>
                </div>
                <div class="col-lg-4 d-flex gap-2">
                    <button class="ui-btn ui-btn-solid rounded-pill px-3 flex-grow-1"><i class="bi bi-funnel"></i> Filtrar</button>
                    <a href="<?php echo e(route('restaurante.reservaciones.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill px-3"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="overflow:visible;">
        <div class="ui-card-accent"></div>
        <div class="table-responsive">
            <table class="ui-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Mesa</th>
                        <th>Cliente</th>
                        <th>Teléfono</th>
                        <th>Personas</th>
                        <th>Fecha / Hora</th>
                        <th>Estado</th>
                        <th>Notas</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_0 = true; $__currentLoopData = $reservaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold"><?php echo e($r->mesa->nombre ?? 'Mesa '.$r->mesa->numero); ?></span>
                        </td>
                        <td>
                            <div class="fw-semibold"><?php echo e($r->cliente_nombre); ?></div>
                            <?php $email = $r->cliente_email ?: $r->cliente?->email ?>
                            <?php if($email): ?>
                                <small class="text-muted"><?php echo e($email); ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($r->cliente_telefono ?: $r->cliente?->telefono ?? '—'); ?></td>
                        <td><?php echo e($r->personas); ?></td>
                        <td><?php echo e($r->fecha_hora->format('d/m/Y h:i A')); ?></td>
                        <td>
                            <span class="ui-badge
                                <?php echo e($r->estado === 'pendiente' ? 'ui-badge-warning' : ''); ?>

                                <?php echo e($r->estado === 'confirmada' ? 'ui-badge-success' : ''); ?>

                                <?php echo e($r->estado === 'cancelada' ? 'ui-badge-neutral' : ''); ?>

                                <?php echo e($r->estado === 'cumplida' ? 'ui-badge-info' : ''); ?>

                            "><?php echo e(ucfirst($r->estado)); ?></span>
                        </td>
                        <td><small class="text-muted"><?php echo e(Str::limit($r->notas, 30) ?: '—'); ?></small></td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1 align-items-center">
                                 <a class="ui-action ui-action-view" href="#" title="Ver detalles"
                                    onclick="editarReservacion(<?php echo e($r->id); ?>, <?php echo e($r->mesa_id); ?>, <?php echo \Illuminate\Support\Js::from($r->cliente_nombre)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($r->cliente_telefono ?: $r->cliente?->telefono)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($r->cliente_email ?: $r->cliente?->email)->toHtml() ?>, <?php echo e($r->personas); ?>, <?php echo \Illuminate\Support\Js::from($r->fecha_hora->format('Y-m-d\TH:i'))->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($r->notas)->toHtml() ?>); return false;">
                                      <i class="bi bi-eye"></i>
                                  </a>
                                  <a class="ui-action ui-action-edit" href="#" title="Editar"
                                     onclick="editarReservacion(<?php echo e($r->id); ?>, <?php echo e($r->mesa_id); ?>, <?php echo \Illuminate\Support\Js::from($r->cliente_nombre)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($r->cliente_telefono ?: $r->cliente?->telefono)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($r->cliente_email ?: $r->cliente?->email)->toHtml() ?>, <?php echo e($r->personas); ?>, <?php echo \Illuminate\Support\Js::from($r->fecha_hora->format('Y-m-d\TH:i'))->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($r->notas)->toHtml() ?>); return false;">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <div class="dropdown">
                                    <button class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill dropdown-toggle px-2" type="button" data-bs-toggle="dropdown" title="Cambiar estado">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0">
                                        <li><button class="dropdown-item small" type="button" onclick="cambiarEstado(<?php echo e($r->id); ?>, 'confirmada')"><i class="bi bi-check-circle text-success me-2"></i>Confirmar</button></li>
                                        <li><button class="dropdown-item small" type="button" onclick="cambiarEstado(<?php echo e($r->id); ?>, 'cumplida')"><i class="bi bi-check-all text-info me-2"></i>Marcar cumplida</button></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><button class="dropdown-item small" type="button" onclick="cambiarEstado(<?php echo e($r->id); ?>, 'cancelada')"><i class="bi bi-x-circle text-danger me-2"></i>Cancelar</button></li>
                                    </ul>
                                </div>
                                <form action="<?php echo e(route('restaurante.reservaciones.destroy', $r)); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar la reservación de ' + <?php echo \Illuminate\Support\Js::from($r->cliente_nombre)->toHtml() ?> + '? Esta acción no se puede deshacer.')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="ui-action ui-action-delete" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <tr>
                        <td colspan="8">
                            <div class="ui-empty-state">
                                <i class="bi bi-calendar-x"></i>
                                <p>No se encontraron reservaciones.</p>
                                <button type="button" class="ui-btn ui-btn-solid rounded-pill mt-2" data-bs-toggle="modal" data-bs-target="#reservaModal">
                                    <i class="bi bi-plus-lg me-1"></i> Crear primera reservación
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <?php echo e($reservaciones->links()); ?>

    </div>
</div>


<div class="modal fade" id="reservaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="<?php echo e(route('restaurante.reservaciones.store')); ?>" class="modal-content rounded-4 border-0 shadow">
            <?php echo csrf_field(); ?>
            <div class="modal-header border-0" style="background:linear-gradient(135deg,#10b981,#059669);border-radius:1rem 1rem 0 0;">
                <h5 class="fw-bold text-white"><i class="bi bi-plus-circle me-2"></i>Nueva Reservación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="ui-label">Mesa <span class="text-danger">*</span></label>
                    <select name="mesa_id" class="ui-select" required>
                        <option value="">Seleccionar mesa</option>
                        <?php $__currentLoopData = $mesas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m->id); ?>"><?php echo e($m->nombre ?? 'Mesa '.$m->numero); ?> (Cap. <?php echo e($m->capacidad); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="ui-label">Nombre del cliente <span class="text-danger">*</span></label>
                    <input type="text" name="cliente_nombre" class="ui-input" required maxlength="200">
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="ui-label">Teléfono</label>
                        <input type="text" name="cliente_telefono" class="ui-input" maxlength="30">
                    </div>
                    <div class="col-6">
                        <label class="ui-label">Email</label>
                        <input type="email" name="cliente_email" class="ui-input" maxlength="200">
                    </div>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-6">
                        <label class="ui-label">Personas <span class="text-danger">*</span></label>
                        <input type="number" name="personas" class="ui-input" value="2" min="1" required>
                    </div>
                    <div class="col-6">
                        <label class="ui-label">Fecha y Hora <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="fecha_hora" class="ui-input" required>
                    </div>
                </div>
                <div class="mb-3 mt-2">
                    <label class="ui-label">Notas</label>
                    <textarea name="notas" class="ui-textarea" rows="2" maxlength="500"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill">Crear Reservación</button>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="editarReservaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content rounded-4 border-0 shadow" id="form-editar-reserva">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="modal-header border-0" style="background:linear-gradient(135deg,#10b981,#059669);border-radius:1rem 1rem 0 0;">
                <h5 class="fw-bold text-white"><i class="bi bi-pencil me-2"></i>Editar Reservación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="ui-label" for="edit-mesa-id">Mesa <span class="text-danger">*</span></label>
                    <select name="mesa_id" id="edit-mesa-id" class="ui-select" required>
                        <option value="">Seleccionar mesa</option>
                        <?php $__currentLoopData = $mesas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m->id); ?>"><?php echo e($m->nombre ?? 'Mesa '.$m->numero); ?> (Cap. <?php echo e($m->capacidad); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="ui-label" for="edit-cliente-nombre">Nombre del cliente <span class="text-danger">*</span></label>
                    <input type="text" name="cliente_nombre" id="edit-cliente-nombre" class="ui-input" required maxlength="200">
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="ui-label" for="edit-cliente-telefono">Teléfono</label>
                        <input type="text" name="cliente_telefono" id="edit-cliente-telefono" class="ui-input" maxlength="30">
                    </div>
                    <div class="col-6">
                        <label class="ui-label" for="edit-cliente-email">Email</label>
                        <input type="email" name="cliente_email" id="edit-cliente-email" class="ui-input" maxlength="200">
                    </div>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-6">
                        <label class="ui-label" for="edit-personas">Personas <span class="text-danger">*</span></label>
                        <input type="number" name="personas" id="edit-personas" class="ui-input" min="1" required>
                    </div>
                    <div class="col-6">
                        <label class="ui-label" for="edit-fecha-hora">Fecha y Hora <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="fecha_hora" id="edit-fecha-hora" class="ui-input" required>
                    </div>
                </div>
                <div class="mb-3 mt-2">
                    <label class="ui-label" for="edit-notas">Notas</label>
                    <textarea name="notas" id="edit-notas" class="ui-textarea" rows="2" maxlength="500"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function editarReservacion(id, mesaId, clienteNombre, clienteTelefono, clienteEmail, personas, fechaHora, notas) {
    var form = document.getElementById('form-editar-reserva');
    form.action = '/restaurante/reservaciones/' + id;
    document.getElementById('edit-mesa-id').value = mesaId;
    document.getElementById('edit-cliente-nombre').value = clienteNombre || '';
    document.getElementById('edit-cliente-telefono').value = clienteTelefono || '';
    document.getElementById('edit-cliente-email').value = clienteEmail || '';
    document.getElementById('edit-personas').value = personas;
    document.getElementById('edit-fecha-hora').value = fechaHora || '';
    document.getElementById('edit-notas').value = notas || '';
    new bootstrap.Modal(document.getElementById('editarReservaModal')).show();
}

function cambiarEstado(id, estado) {
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '/restaurante/reservaciones/' + id + '/estado';
    form.style.display = 'none';
    var h1 = document.createElement('input'); h1.type = 'hidden'; h1.name = '_token'; h1.value = document.querySelector('meta[name=\"csrf-token\"]')?.content || '';
    var h2 = document.createElement('input'); h2.type = 'hidden'; h2.name = '_method'; h2.value = 'PATCH';
    var h3 = document.createElement('input'); h3.type = 'hidden'; h3.name = 'estado'; h3.value = estado;
    form.append(h1, h2, h3);
    document.body.appendChild(form);
    form.submit();
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/restaurante/reservaciones.blade.php ENDPATH**/ ?>