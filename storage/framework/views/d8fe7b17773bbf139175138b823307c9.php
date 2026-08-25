<?php $__env->startSection('title', 'Instancias de Negocio'); ?>

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
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Instancias de Negocio</h2>
                    <p class="mb-0 opacity-75">Gestión de todas las instancias multi-tenant.</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('owner.instances.create')); ?>" class="ui-btn ui-btn-solid">
                    <i class="bi bi-plus-lg me-2"></i>Nueva Instancia
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#3b82f6"></div>
        <div class="card-body p-3">
            <form method="GET" action="<?php echo e(route('owner.instances.index')); ?>" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="ui-input border-0 bg-white" placeholder="Buscar por nombre, slug, RNC..." value="<?php echo e(request('search')); ?>" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-3">
                    <select name="business_type" class="ui-select border-0 bg-white">
                        <option value="">Todos los tipos</option>
                        <?php $__currentLoopData = $businessTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($type->id); ?>" <?php echo e(request('business_type') == $type->id ? 'selected' : ''); ?>><?php echo e($type->nombre); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-3">
                    <select name="status" class="ui-select border-0 bg-white">
                        <option value="">Todos los estados</option>
                        <option value="al-dia" <?php echo e(request('status') === 'al-dia' ? 'selected' : ''); ?>>Al día</option>
                        <option value="atrasado" <?php echo e(request('status') === 'atrasado' ? 'selected' : ''); ?>>Atrasado</option>
                        <option value="bloqueado" <?php echo e(request('status') === 'bloqueado' ? 'selected' : ''); ?>>Bloqueado</option>
                        <option value="vencido" <?php echo e(request('status') === 'vencido' ? 'selected' : ''); ?>>Vencido</option>
                        <option value="inactivo" <?php echo e(request('status') === 'inactivo' ? 'selected' : ''); ?>>Inactivo</option>
                    </select>
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="<?php echo e(route('owner.instances.index')); ?>" class="ui-btn ui-btn-primary"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.2s">
        <div class="ui-card-accent" style="background:#3b82f6"></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nombre</th>
                        <th>Plan</th>
                        <th>Tipo</th>
                        <th>Propietario</th>
                        <th class="text-center">Estado Pago</th>
                        <th class="text-center">Bloqueo</th>
                        <th>Costo Mensual</th>
                        <th>Vencimiento</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $instances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="ps-4 fw-bold">
                            <a href="<?php echo e(route('owner.instances.show', $instance)); ?>" class="text-decoration-none"><?php echo e($instance->nombre); ?></a>
                        </td>
                        <td>
                            <?php if($instance->plan): ?>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2" title="Plan <?php echo e($instance->plan->nombre); ?>">Plan <?php echo e($instance->plan->nombre); ?></span>
                            <?php else: ?>
                                <span class="badge bg-light text-muted rounded-pill px-2">Personalizado</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-<?php echo e($instance->businessType?->color ?? 'secondary'); ?> bg-opacity-10 text-<?php echo e($instance->businessType?->color ?? 'secondary'); ?> rounded-pill"><?php echo e($instance->businessType?->nombre ?? '—'); ?></span></td>                        <td>
                            <?php if($instance->owner_nombre): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:rgba(139,92,246,.15);color:#8b5cf6;font-size:13px;font-weight:600;">
                                        <?php echo e(strtoupper(substr($instance->owner_nombre, 0, 1))); ?>

                                    </div>
                                    <div>
                                        <div class="small fw-semibold"><?php echo e($instance->owner_nombre); ?></div>
                                        <div class="text-muted" style="font-size:11px;"><?php echo e($instance->owner_email); ?></div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php $subEstado = $instance->estadoSuscripcion(); ?>
                            <?php if(!$instance->activo): ?>
                                <span class="badge bg-secondary rounded-pill px-2">Inactiva</span>
                            <?php elseif($subEstado === 'suspendida'): ?>
                                <span class="badge bg-danger rounded-pill px-2">Suspendida</span>
                            <?php elseif($subEstado === 'prueba'): ?>
                                <span class="badge bg-primary rounded-pill px-2" title="Prueba gratuita — termina el <?php echo e(optional($instance->trial_ends_at)->format('d/m/Y')); ?>">
                                    <i class="bi bi-rocket-takeoff me-1"></i>Prueba (<?php echo e($instance->diasPruebaRestantes()); ?>d)
                                </span>
                            <?php elseif($subEstado === 'activa'): ?>
                                <span class="badge bg-success rounded-pill px-2"><i class="bi bi-check-circle me-1"></i>Al día</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark rounded-pill px-2"><i class="bi bi-exclamation-triangle me-1"></i><?php echo e($instance->mesesAtrasados()); ?> mes(es)</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if($instance->bloqueado): ?>
                                <span class="badge bg-danger rounded-pill px-2"><i class="bi bi-lock-fill me-1"></i>Bloqueado</span>
                            <?php else: ?>
                                <span class="badge bg-success rounded-pill px-2"><i class="bi bi-unlock me-1"></i>Normal</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($systemMoneda ?? 'RD$'); ?> <?php echo e(number_format($instance->costo_mensual ?? 0, 2)); ?></td>
                        <td>
                            <?php if($instance->fecha_vencimiento): ?>
                                <?php echo e($instance->fecha_vencimiento->format('d/m/Y')); ?>

                                <?php if($instance->activo && $instance->fecha_vencimiento < now()): ?>
                                    <span class="text-danger fw-bold small">(vencida)</span>
                                <?php elseif($instance->activo && $instance->fecha_vencimiento->diffInDays(now()) <= 30): ?>
                                    <span class="text-warning fw-bold small">(<?php echo e($instance->fecha_vencimiento->diffForHumans()); ?>)</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <a href="<?php echo e(route('owner.instances.show', $instance)); ?>" class="ui-btn ui-btn-view rounded-pill me-1" title="Ver detalles"><i class="bi bi-eye"></i></a>
                            <a href="<?php echo e(route('owner.instances.edit', $instance)); ?>" class="ui-btn ui-btn-edit rounded-pill me-1" title="Editar"><i class="bi bi-pencil"></i></a>
                            <a href="<?php echo e(route('owner.instances.config', $instance)); ?>" class="ui-btn ui-btn-solid rounded-pill me-1" style="background:#f59e0b;border-color:#f59e0b;color:#000;" title="Configuración"><i class="bi bi-gear"></i></a>
                            <a href="<?php echo e(route('owner.instances.pagos.create', $instance)); ?>" class="ui-btn ui-btn-solid rounded-pill me-1" style="background:#10b981;border-color:#10b981;color:#fff;" title="Registrar Pago"><i class="bi bi-cash-coin"></i></a>
                            <?php if($instance->activo): ?>
                            <form action="<?php echo e(route('owner.instances.destroy', $instance)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Desactivar la instancia <?php echo e($instance->nombre); ?>?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="ui-action ui-action-delete" title="Desactivar"><i class="bi bi-power"></i></button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No hay instancias registradas.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($instances->hasPages()): ?>
        <div class="card-footer bg-transparent border-0 py-3 px-4">
            <?php echo e($instances->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/instances/index.blade.php ENDPATH**/ ?>