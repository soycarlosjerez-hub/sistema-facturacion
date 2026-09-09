<?php $__env->startSection('title', 'Repartidores'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
/* Delivery Drivers Module Styles */
.driver-avatar {
    width: 40px; height: 40px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: .85rem;
    background: rgba(14,165,233,.1); color: #0ea5e9;
    flex-shrink: 0;
}
.drivers-active-count {
    background: rgba(34,197,94,.12); color: #16a34a;
    padding: .2rem .6rem; border-radius: 9999px; font-size: .75rem; font-weight: 600;
}
@media (max-width: 767.98px) {
    .drivers-stats .ui-stat { margin-bottom: .75rem; }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#0ea5e9;--accent-rgb:14,165,233;--accent-hover:#0284c7;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Repartidores</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-people me-1"></i>
                        <span><?php echo e($drivers->total()); ?> repartidor(es)</span>
                        <?php if(isset($activeCount)): ?>
                        <span class="divider">·</span>
                        <span class="drivers-active-count"><i class="bi bi-check-circle me-1"></i><?php echo e($activeCount); ?> activos</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delivery-drivers.create')): ?>
                <a href="<?php echo e(route('delivery-drivers.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Repartidor
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4">
            <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    <?php if(isset($stats)): ?>
    <div class="row g-3 mb-4 drivers-stats">
        <div class="col-6 col-md-3">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Total Repartidores</div>
                    <div class="ui-stat-value"><?php echo e($stats['total']); ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Activos</div>
                    <div class="ui-stat-value" style="color:#16a34a;"><?php echo e($stats['activos']); ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Inactivos</div>
                    <div class="ui-stat-value" style="color:#64748b;"><?php echo e($stats['inactivos']); ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-stat" style="--delay:.25s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Órdenes en Curso</div>
                    <div class="ui-stat-value" style="color:#0ea5e9;"><?php echo e($stats['ordenes_en_curso']); ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="ui-card" style="--delay:.3s">
        <div class="ui-card-accent"></div>
        <div class="table-responsive">
            <table class="ui-table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Repartidor</th>
                        <th>Usuario</th>
                        <th>Cédula</th>
                        <th>Contacto</th>
                        <th>Licencia</th>
                        <th class="text-center">Órdenes</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_0 = true; $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2">
                                <div class="driver-avatar">
                                    <?php echo e(strtoupper(substr($driver->nombre, 0, 1) . substr($driver->apellido, 0, 1))); ?>

                                </div>
                                <div>
                                    <div class="fw-semibold"><?php echo e($driver->nombre); ?> <?php echo e($driver->apellido); ?></div>
                                    <small class="text-muted" style="font-size:.75rem;">
                                        <i class="bi bi-clock me-1"></i>Registrado <?php echo e($driver->created_at->diffForHumans()); ?>

                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if($driver->user): ?>
                                <div>
                                    <a href="<?php echo e(route('usuarios.edit', $driver->user)); ?>" class="text-decoration-none fw-semibold">
                                        <?php echo e($driver->user->name); ?>

                                    </a>
                                    <div class="text-muted" style="font-size:.75rem;"><?php echo e($driver->user->email); ?></div>
                                </div>
                                <?php if($driver->user->hasRole('delivery')): ?>
                                    <span class="badge bg-success" style="font-size:.65rem;">delivery</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted" style="font-size:.8rem;">—</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="ui-badge ui-badge-neutral"><?php echo e($driver->cedula); ?></span></td>
                        <td>
                            <div class="small">
                                <div><i class="bi bi-telephone me-1 text-muted"></i><?php echo e($driver->telefono); ?></div>
                                <?php if($driver->whatsapp): ?>
                                <div>
                                    <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $driver->whatsapp)); ?>" target="_blank" class="text-decoration-none" title="Enviar WhatsApp">
                                        <i class="bi bi-whatsapp me-1" style="color:#25D366;"></i><?php echo e($driver->whatsapp); ?>

                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><span class="ui-badge ui-badge-neutral"><?php echo e($driver->licencia_conducir ?? '—'); ?></span></td>
                        <td class="text-center">
                            <?php if($driver->ordenes_activas > 0): ?>
                                <span class="ui-badge ui-badge-info"><?php echo e($driver->ordenes_activas); ?></span>
                            <?php else: ?>
                                <span class="text-muted">0</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($driver->activo): ?>
                                <span class="ui-badge ui-badge-success"><i class="bi bi-check-circle me-1"></i>Activo</span>
                            <?php else: ?>
                                <span class="ui-badge ui-badge-neutral"><i class="bi bi-x-circle me-1"></i>Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-1 justify-content-end">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delivery-drivers.edit')): ?>
                                <a href="<?php echo e(route('delivery-drivers.edit', $driver)); ?>" class="ui-action ui-action-edit" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delivery-drivers.delete')): ?>
                                <form action="<?php echo e(route('delivery-drivers.destroy', $driver)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="ui-action ui-action-delete" type="submit" onclick="event.preventDefault(); UI.confirm.action({title:'¿Eliminar este repartidor?', icon:'error', callback: () => this.closest('form').submit()})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-truck fs-1 d-block mb-2"></i>
                            No hay repartidores registrados
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($drivers->hasPages()): ?>
        <div class="card-footer bg-transparent border-0 p-3">
            <?php echo e($drivers->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/delivery-drivers/index.blade.php ENDPATH**/ ?>