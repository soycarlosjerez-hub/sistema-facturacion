<?php $__env->startSection('title', 'Lavadores'); ?>
<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Lavadores</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-people me-1"></i>
                        <span>Gestión de empleados del lavadero</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <button class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#lavadorModal">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Lavador
                </button>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th class="text-center">% Comisión</th>
                            <th>Teléfono</th>
                            <th class="text-center">Activo</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_0 = true; $__currentLoopData = $lavadores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr>
                            <td><?php echo e($l->id); ?></td>
                            <td class="fw-medium"><?php echo e($l->nombre); ?></td>
                            <td>
                                <span class="ui-badge <?php echo e($l->tipo === 'fijo' ? 'ui-badge-primary' : 'ui-badge-warning'); ?>">
                                    <?php echo e($l->tipo === 'fijo' ? 'Fijo' : 'Temporal'); ?>

                                </span>
                            </td>
                            <td class="text-center fw-bold"><?php echo e(number_format($l->porcentaje, 1)); ?>%</td>
                            <td><?php echo e($l->telefono ?? '—'); ?></td>
                            <td class="text-center">
                                <span class="ui-badge <?php echo e($l->activo ? 'ui-badge-success' : 'ui-badge-neutral'); ?>">
                                    <?php echo e($l->activo ? 'Sí' : 'No'); ?>

                                </span>
                            </td>
                            <td class="text-end">
                                <button type="button" class="ui-action ui-action-edit me-1" data-bs-toggle="modal" data-bs-target="#editModal<?php echo e($l->id); ?>" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form id="del-lavador-<?php echo e($l->id); ?>" action="<?php echo e(route('lavadero.lavadores.destroy', $l)); ?>" method="POST" class="d-inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?></form>
                                <button type="button" class="ui-action ui-action-delete" title="Eliminar"
                                        onclick="UI.confirm.deleteWithForm('del-lavador-<?php echo e($l->id); ?>', '<?php echo e(addslashes($l->nombre)); ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr>
                            <td colspan="7">
                                <div class="ui-empty-state">
                                    <i class="bi bi-people"></i>
                                    <p>Sin lavadores registrados</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="lavadorModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="<?php echo e(route('lavadero.lavadores.store')); ?>" class="modal-content rounded-4 border-0 shadow">
            <?php echo csrf_field(); ?>
            <div class="modal-header border-0">
                <h6 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nuevo Lavador</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="ui-label">Nombre</label>
                    <input type="text" name="nombre" class="ui-input" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="ui-label">Tipo</label>
                        <select name="tipo" class="ui-select" onchange="actualizarPorcentajeDefecto(this)">
                            <option value="temporal">Temporal</option>
                            <option value="fijo">Fijo</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="ui-label">% Comisión</label>
                        <div class="ui-input-group">
                            <input type="number" name="porcentaje" class="ui-input" step="0.01" min="0" max="100" value="<?php echo e($defaultTemporal); ?>">
                            <span class="ui-input-group-text">%</span>
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="ui-label">Teléfono</label>
                        <input type="text" name="telefono" class="ui-input">
                    </div>
                    <div class="col-6">
                        <label class="ui-label">Email</label>
                        <input type="email" name="email" class="ui-input">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="ui-label">Identificación</label>
                    <input type="text" name="identificacion" class="ui-input">
                </div>
                <div class="mb-3">
                    <label class="ui-label">Notas</label>
                    <textarea name="notas" class="ui-textarea" rows="2"></textarea>
                </div>
                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(6,182,212,.05);">
                    <div class="form-check form-switch mb-0">
                        <input type="checkbox" name="activo" class="form-check-input" value="1" id="lavadorActivo" checked role="switch" style="width:3em;height:1.5em;">
                        <label class="form-check-label fw-semibold ms-2" for="lavadorActivo">Activo</label>
                    </div>
                    <small class="text-muted">Si está inactivo no aparecerá en la lista.</small>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-4">Guardar</button>
            </div>
        </form>
    </div>
</div>


<?php $__currentLoopData = $lavadores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="editModal<?php echo e($l->id); ?>" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="<?php echo e(route('lavadero.lavadores.update', $l)); ?>" class="modal-content rounded-4 border-0 shadow">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <div class="modal-header border-0">
                <h6 class="fw-bold"><i class="bi bi-pencil me-2"></i>Editar Lavador</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="ui-label">Nombre</label>
                    <input type="text" name="nombre" class="ui-input" value="<?php echo e($l->nombre); ?>" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="ui-label">Tipo</label>
                        <select name="tipo" class="ui-select">
                            <option value="temporal" <?php echo e($l->tipo === 'temporal' ? 'selected' : ''); ?>>Temporal</option>
                            <option value="fijo" <?php echo e($l->tipo === 'fijo' ? 'selected' : ''); ?>>Fijo</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="ui-label">% Comisión</label>
                        <div class="ui-input-group">
                            <input type="number" name="porcentaje" class="ui-input" step="0.01" min="0" max="100" value="<?php echo e($l->porcentaje); ?>">
                            <span class="ui-input-group-text">%</span>
                        </div>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="ui-label">Teléfono</label>
                        <input type="text" name="telefono" class="ui-input" value="<?php echo e($l->telefono); ?>">
                    </div>
                    <div class="col-6">
                        <label class="ui-label">Email</label>
                        <input type="email" name="email" class="ui-input" value="<?php echo e($l->email); ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="ui-label">Identificación</label>
                    <input type="text" name="identificacion" class="ui-input" value="<?php echo e($l->identificacion); ?>">
                </div>
                <div class="mb-3">
                    <label class="ui-label">Notas</label>
                    <textarea name="notas" class="ui-textarea" rows="2"><?php echo e($l->notas); ?></textarea>
                </div>
                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(6,182,212,.05);">
                    <div class="form-check form-switch mb-0">
                        <input type="checkbox" name="activo" class="form-check-input" value="1" id="editActivo<?php echo e($l->id); ?>" <?php echo e($l->activo ? 'checked' : ''); ?> role="switch" style="width:3em;height:1.5em;">
                        <label class="form-check-label fw-semibold ms-2" for="editActivo<?php echo e($l->id); ?>">Activo</label>
                    </div>
                    <small class="text-muted">Si está inactivo no aparecerá en la lista.</small>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-4">Guardar</button>
            </div>
        </form>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<script>
function actualizarPorcentajeDefecto(select) {
    const pctInput = select.closest('.modal-body').querySelector('input[name="porcentaje"]');
    if (select.value === 'fijo') {
        pctInput.value = '<?php echo e($defaultFijo); ?>';
    } else {
        pctInput.value = '<?php echo e($defaultTemporal); ?>';
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/lavadero/lavadores/index.blade.php ENDPATH**/ ?>