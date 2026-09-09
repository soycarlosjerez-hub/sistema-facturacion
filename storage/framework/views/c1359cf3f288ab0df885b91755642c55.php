<div class="ui-card" style="--delay:.35s">
    <div class="ui-card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-building me-2" style="color:var(--accent,#8b5cf6)"></i>
                Instancias Recientes
            </h5>
            <a href="<?php echo e(route('owner.instances.index')); ?>" class="text-decoration-none small fw-bold">Ver todas →</a>
        </div>
        <div class="table-responsive">
            <table class="ui-table mb-0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Vencimiento</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_0 = true; $__currentLoopData = $instancias->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <tr>
                        <td class="fw-bold">
                            <a href="<?php echo e(route('owner.instances.show', $instance)); ?>" class="text-decoration-none"><?php echo e($instance->nombre); ?></a>
                        </td>
                        <td><span class="ui-badge ui-badge-<?php echo e($instance->businessType?->color ?? 'secondary'); ?> rounded-pill"><?php echo e($instance->businessType?->nombre ?? '—'); ?></span></td>
                        <td>
                            <?php if(!$instance->activo): ?>
                                <span class="ui-badge ui-badge-neutral rounded-pill">Inactiva</span>
                            <?php elseif($instance->bloqueado): ?>
                                <span class="ui-badge ui-badge-danger rounded-pill">Bloqueada</span>
                            <?php elseif($instance->estaAlDia()): ?>
                                <span class="ui-badge ui-badge-success rounded-pill">Al día</span>
                            <?php else: ?>
                                <span class="ui-badge ui-badge-warning rounded-pill"><?php echo e($instance->mesesAtrasados()); ?> mes(es)</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($instance->fecha_vencimiento): ?>
                                <?php echo e($instance->fecha_vencimiento->format('d/m/Y')); ?>

                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="<?php echo e(route('owner.instances.show', $instance)); ?>" class="ui-action ui-action-view" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?php echo e(route('owner.instances.edit', $instance)); ?>" class="ui-action ui-action-edit" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No hay instancias registradas.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/partials/_recent_instances_table.blade.php ENDPATH**/ ?>