<?php $__env->startSection('title', 'Colecciones'); ?>
<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-collection"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-collection me-1"></i>AGRUPACIONES
                    </span>
                    <h2 class="fw-bold mb-0 text-white">Colecciones</h2>
                    <p class="mb-0 opacity-75">Agrupaciones temáticas de las obras</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <button class="ui-btn ui-btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#coleccionModal">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Colección
                </button>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-card-body">
            <form method="GET" action="<?php echo e(route('arte.colecciones.index')); ?>">
                <div class="ui-input-group">
                    <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="ui-input" value="<?php echo e(request('q')); ?>" placeholder="Buscar por nombre o descripción...">
                    <button class="ui-btn ui-btn-solid rounded-pill ms-2 px-4" type="submit">Buscar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.2s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Colección</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th class="text-center">Obras</th>
                            <th class="text-center">Activo</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_0 = true; $__currentLoopData = $colecciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr>
                            <td class="ps-4"><?php echo e($c->id); ?></td>
                            <td class="fw-semibold"><?php echo e($c->nombre); ?></td>
                            <td><?php echo e($c->tipo ? ucfirst($c->tipo) : '—'); ?></td>
                            <td class="text-muted small"><?php echo e(\Illuminate\Support\Str::limit($c->descripcion, 50) ?? '—'); ?></td>
                            <td class="text-center"><span class="badge bg-info bg-opacity-10 text-info rounded-pill"><?php echo e($c->obras_count); ?></span></td>
                            <td class="text-center">
                                <span class="badge <?php echo e($c->activo ? 'bg-success' : 'bg-secondary'); ?> rounded-pill"><?php echo e($c->activo ? 'Sí' : 'No'); ?></span>
                            </td>
                            <td class="text-end text-nowrap pe-4">
                                <a href="#" class="ui-action ui-action-edit" data-bs-toggle="modal" data-bs-target="#editModal<?php echo e($c->id); ?>" title="Editar"><i class="bi bi-pencil"></i></a>
                                <form action="<?php echo e(route('arte.colecciones.destroy', $c)); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar la colección <?php echo e(addslashes($c->nombre)); ?>?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="ui-action ui-action-delete" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">No hay colecciones registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="p-3"><?php echo e($colecciones->links()); ?></div>
    </div>
</div>


<div class="modal fade" id="coleccionModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="<?php echo e(route('arte.colecciones.store')); ?>" class="modal-content rounded-4 border-0 shadow overflow-hidden">
            <?php echo csrf_field(); ?>
            <div class="ui-card-accent" style="background:#8b5cf6"></div>
            <div class="modal-header border-0 pb-0">
                <h6 class="fw-bold"><i class="bi bi-plus-circle me-2" style="color:var(--accent,#8b5cf6)"></i>Nueva Colección</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="ui-label">Nombre *</label>
                    <input type="text" name="nombre" class="ui-input" required>
                </div>
                <div class="mb-3">
                    <label class="ui-label">Tipo</label>
                    <select name="tipo" class="ui-select">
                        <option value="">Seleccionar...</option>
                        <option value="reunion">Reunión</option>
                        <option value="tematica">Temática</option>
                        <option value="temporal">Temporal</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="ui-label">Descripción</label>
                    <textarea name="descripcion" class="ui-textarea" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-4"><i class="bi bi-check-lg me-1"></i>Guardar</button>
            </div>
        </form>
    </div>
</div>


<?php $__currentLoopData = $colecciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="editModal<?php echo e($c->id); ?>" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="<?php echo e(route('arte.colecciones.update', $c)); ?>" class="modal-content rounded-4 border-0 shadow overflow-hidden">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <div class="ui-card-accent" style="background:#e1306c"></div>
            <div class="modal-header border-0 pb-0">
                <h6 class="fw-bold"><i class="bi bi-pencil me-2" style="color:var(--accent,#8b5cf6)"></i>Editar Colección</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="ui-label">Nombre *</label>
                    <input type="text" name="nombre" class="ui-input" value="<?php echo e($c->nombre); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="ui-label">Tipo</label>
                    <select name="tipo" class="ui-select">
                        <option value="">Seleccionar...</option>
                        <?php $__currentLoopData = ['reunion' => 'Reunión', 'tematica' => 'Temática', 'temporal' => 'Temporal']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($k); ?>" <?php echo e($c->tipo == $k ? 'selected' : ''); ?>><?php echo e($v); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="ui-label">Descripción</label>
                    <textarea name="descripcion" class="ui-textarea" rows="3"><?php echo e($c->descripcion); ?></textarea>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="activo" class="form-check-input" value="1" id="cactivo<?php echo e($c->id); ?>" <?php echo e($c->activo ? 'checked' : ''); ?>>
                    <label class="form-check-label fw-semibold" for="cactivo<?php echo e($c->id); ?>">Activo</label>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-4"><i class="bi bi-check-lg me-1"></i>Guardar</button>
            </div>
        </form>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/arte/colecciones/index.blade.php ENDPATH**/ ?>