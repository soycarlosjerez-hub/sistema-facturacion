<?php $__env->startSection('title', 'Plantilla de Gastos'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.status-dot {
    width: 8px; height: 8px; border-radius: 50%; display: inline-block;
}
.status-dot.active { background: #10b981; }
.status-dot.inactive { background: #94a3b8; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-file-earmark-richtext"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Plantilla de Gastos</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-bookmark me-1"></i>
                        <span>Gestiona plantillas para registrar gastos recurrentes rápidamente</span>
                        <span class="divider">·</span>
                        <i class="bi bi-list-ul me-1"></i>
                        <span><?php echo e($plantillas->total()); ?> registro(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('plantilla-gastos.create')): ?>
                <a href="<?php echo e(route('plantilla-gastos.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Plantilla
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent green"></div>
        <div class="ui-card-body p-3">
            <form method="GET" action="<?php echo e(route('plantilla-gastos.index')); ?>" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="ui-input" placeholder="Buscar por nombre, descripción o comprobante..." value="<?php echo e(request('search')); ?>" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <select name="categoria" class="ui-select">
                        <option value="">Todas las categorías</option>
                        <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e(request('categoria') === $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-2">
                    <select name="activo" class="ui-select">
                        <option value="">Estado</option>
                        <option value="1" <?php echo e(request('activo') === '1' ? 'selected' : ''); ?>>Activa</option>
                        <option value="0" <?php echo e(request('activo') === '0' ? 'selected' : ''); ?>>Inactiva</option>
                    </select>
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="<?php echo e(route('plantilla-gastos.index')); ?>" class="ui-btn ui-btn-ghost"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent green"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Nombre</th>
                            <th>Categoría</th>
                            <th>Método Pago</th>
                            <th>Comprobante</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_0 = true; $__currentLoopData = $plantillas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plantilla): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-semibold"><?php echo e($plantilla->nombre); ?></span>
                                    <?php if($plantilla->descripcion): ?>
                                        <br><small class="text-muted"><?php echo e(Str::limit($plantilla->descripcion, 60)); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($plantilla->categoria): ?>
                                        <span class="badge rounded-pill" style="background:rgba(16,185,129,.1);color:#059669;font-weight:600;"><?php echo e($categorias[$plantilla->categoria] ?? $plantilla->categoria); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($plantilla->metodo_pago): ?>
                                        <span class="text-muted small"><?php echo e(ucfirst(str_replace('_', ' ', $plantilla->metodo_pago))); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($plantilla->comprobante): ?>
                                        <span class="badge rounded-pill" style="background:rgba(99,102,241,.1);color:#4f46e5;font-weight:600;"><?php echo e($plantilla->comprobante); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="status-dot <?php echo e($plantilla->activo ? 'active' : 'inactive'); ?>"></span>
                                    <small class="ms-1 text-muted"><?php echo e($plantilla->activo ? 'Activa' : 'Inactiva'); ?></small>
                                </td>
                                <td class="text-end pe-4">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('plantilla-gastos.edit')): ?>
                                    <a href="<?php echo e(route('plantilla-gastos.edit', $plantilla)); ?>" class="ui-action ui-action-edit" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if($plantilla->activo): ?>
                                        <form action="<?php echo e(route('plantilla-gastos.desactivar', $plantilla)); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="premium-btn-warning ms-1" title="Desactivar">
                                                <i class="bi bi-pause-circle"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form action="<?php echo e(route('plantilla-gastos.activar', $plantilla)); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="premium-btn-success ms-1" title="Activar">
                                                <i class="bi bi-play-circle"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('plantilla-gastos.delete')): ?>
                                    <button type="button" class="ui-action ui-action-delete ms-1" 
                                            onclick="confirmDelete('<?php echo e(route('plantilla-gastos.destroy', $plantilla)); ?>', '<?php echo e(addslashes($plantilla->nombre)); ?>')"
                                            title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="ui-empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>No hay plantillas registradas</p>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('plantilla-gastos.create')): ?>
                                        <a href="<?php echo e(route('plantilla-gastos.create')); ?>" class="ui-btn ui-btn-solid rounded-pill mt-2">Crear primera plantilla</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if($plantillas->hasPages()): ?>
    <div class="d-flex justify-content-center mt-3">
        <?php echo e($plantillas->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function confirmDelete(url, name) {
    Swal.fire({
        title: '¿Eliminar plantilla?',
        text: `Se eliminará: "${name}"`,
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
            form.innerHTML = '<?php echo csrf_field(); ?> <?php echo method_field("DELETE"); ?>';
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/plantilla-gastos/index.blade.php ENDPATH**/ ?>