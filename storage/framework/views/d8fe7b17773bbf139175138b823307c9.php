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

    
    <div class="row g-3 mb-4">
        <div class="col-xl col-md-6 col-6">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent" style="background:#3b82f6"></div>
                <div class="ui-card-body py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="ui-stat-label text-muted mb-1">Total</div>
                            <div class="fw-bold" style="font-size:1.75rem;color:#3b82f6"><?php echo e($totalInstances); ?></div>
                            <small class="text-muted">Registros</small>
                        </div>
                        <div style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(59,130,246,.1);">
                            <i class="bi bi-building" style="font-size:1.5rem;color:#3b82f6;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-6 col-6">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent" style="background:#10b981"></div>
                <div class="ui-card-body py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="ui-stat-label text-muted mb-1">Activas</div>
                            <div class="fw-bold" style="font-size:1.75rem;color:#10b981"><?php echo e($totalActivas); ?></div>
                            <small class="text-muted">En operación</small>
                        </div>
                        <div style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(16,185,129,.1);">
                            <i class="bi bi-check-circle" style="font-size:1.5rem;color:#10b981;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-6 col-6">
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="background:#ef4444"></div>
                <div class="ui-card-body py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="ui-stat-label text-muted mb-1">Bloqueadas</div>
                            <div class="fw-bold" style="font-size:1.75rem;color:#ef4444"><?php echo e($totalBloqueadas); ?></div>
                            <small class="text-muted">Restringidas</small>
                        </div>
                        <div style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(239,68,68,.1);">
                            <i class="bi bi-lock-fill" style="font-size:1.5rem;color:#ef4444;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-6 col-6">
            <div class="ui-card" style="--delay:.25s">
                <div class="ui-card-accent" style="background:#f59e0b"></div>
                <div class="ui-card-body py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="ui-stat-label text-muted mb-1">Pendientes</div>
                            <div class="fw-bold" style="font-size:1.75rem;color:#f59e0b"><?php echo e($totalPendientes); ?></div>
                            <small class="text-muted">En revisión</small>
                        </div>
                        <div style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(245,158,11,.1);">
                            <i class="bi bi-hourglass-split" style="font-size:1.5rem;color:#f59e0b;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-6 col-6">
            <div class="ui-card" style="--delay:.3s">
                <div class="ui-card-accent" style="background:#f59e0b"></div>
                <div class="ui-card-body py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="ui-stat-label text-muted mb-1">Atrasadas</div>
                            <div class="fw-bold" style="font-size:1.75rem;color:#f59e0b"><?php echo e($totalAtrasadas); ?></div>
                            <small class="text-muted">Pago pendiente</small>
                        </div>
                        <div style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(245,158,11,.1);">
                            <i class="bi bi-exclamation-triangle" style="font-size:1.5rem;color:#f59e0b;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="ui-card mb-4" style="--delay:.15s">
        <div class="ui-card-body p-3">
            <form method="GET" action="<?php echo e(route('owner.instances.index')); ?>" class="row g-2 align-items-center">
                <div class="col-lg-4 col-md-6">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="ui-input border-0 bg-white" placeholder="Buscar por nombre, slug, RNC..." value="<?php echo e(request('search')); ?>" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <select name="business_type" class="ui-select border-0 bg-white">
                        <option value="">Todos los tipos</option>
                        <?php $__currentLoopData = $businessTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($type->id); ?>" <?php echo e(request('business_type') == $type->id ? 'selected' : ''); ?>><?php echo e($type->nombre); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <select name="status" class="ui-select border-0 bg-white">
                        <option value="">Todos los estados</option>
                        <option value="al-dia" <?php echo e(request('status') === 'al-dia' ? 'selected' : ''); ?>>Al día</option>
                        <option value="atrasado" <?php echo e(request('status') === 'atrasado' ? 'selected' : ''); ?>>Atrasado</option>
                        <option value="bloqueado" <?php echo e(request('status') === 'bloqueado' ? 'selected' : ''); ?>>Bloqueado</option>
                        <option value="pendiente" <?php echo e(request('status') === 'pendiente' ? 'selected' : ''); ?>>Pendiente</option>
                        <option value="vencido" <?php echo e(request('status') === 'vencido' ? 'selected' : ''); ?>>Vencido</option>
                        <option value="inactivo" <?php echo e(request('status') === 'inactivo' ? 'selected' : ''); ?>>Inactivo</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-2 d-flex align-items-center">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="show_trashed" id="show_trashed" value="1" <?php echo e(request('show_trashed') == '1' ? 'checked' : ''); ?>>
                        <label class="form-check-label small" for="show_trashed">Borradas</label>
                    </div>
                </div>
                <div class="col-lg-auto col-md-auto d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="<?php echo e(route('owner.instances.index')); ?>" class="ui-btn ui-btn-primary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    
    <div class="ui-card d-none d-md-block" style="--delay:.2s">
        <div class="table-responsive">
            <table class="ui-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Instancia</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Aprobación</th>
                        <th>Propietario</th>
                        <th>Último Pago</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $instances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $subEstado = $instance->estadoSuscripcion(); ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <?php if($instance->logo): ?>
                                    <img src="<?php echo e(Storage::url($instance->logo)); ?>" alt="<?php echo e($instance->nombre); ?>" style="width:36px;height:36px;border-radius:8px;object-fit:cover;flex-shrink:0;">
                                <?php else: ?>
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;background:rgba(139,92,246,.1);color:#8b5cf6;font-size:13px;font-weight:600;">
                                        <?php echo e(strtoupper(substr($instance->nombre, 0, 1))); ?>

                                    </div>
                                <?php endif; ?>
                                <div style="min-width:0;">
                                    <a href="<?php echo e(route('owner.instances.show', $instance)); ?>" class="fw-bold text-truncate text-reset text-decoration-none"><?php echo e($instance->nombre); ?></a>
                                    <br>
                                    <small class="text-muted"><?php echo e($instance->slug); ?> · <?php echo e($instance->plan?->nombre ?? 'Plan personalizado'); ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="ui-badge ui-badge-info rounded-pill"><?php echo e($instance->businessType?->nombre ?? '—'); ?></span>
                        </td>
                        <td>
                            <?php if(!$instance->activo): ?>
                                <span class="ui-badge ui-badge-neutral rounded-pill"><i class="bi bi-x-circle me-1"></i>Inactiva</span>
                            <?php elseif($instance->bloqueado): ?>
                                <span class="ui-badge ui-badge-danger rounded-pill"><i class="bi bi-lock-fill me-1"></i>Bloqueada</span>
                            <?php elseif($subEstado === 'suspendida'): ?>
                                <span class="ui-badge ui-badge-danger rounded-pill"><i class="bi bi-x-circle me-1"></i>Suspendida</span>
                            <?php elseif($subEstado === 'prueba'): ?>
                                <span class="ui-badge ui-badge-info rounded-pill" title="Prueba gratuita — termina el <?php echo e(optional($instance->trial_ends_at)->format('d/m/Y')); ?>">
                                    <i class="bi bi-rocket-takeoff me-1"></i>Prueba (<?php echo e($instance->diasPruebaRestantes()); ?>d)
                                </span>
                            <?php elseif($subEstado === 'activa'): ?>
                                <span class="ui-badge ui-badge-success rounded-pill"><i class="bi bi-check-circle me-1"></i>Al día</span>
                            <?php else: ?>
                                <span class="ui-badge ui-badge-warning rounded-pill"><i class="bi bi-exclamation-triangle me-1"></i><?php echo e($instance->mesesAtrasados()); ?> mes(es)</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($instance->aprobado): ?>
                                <span class="ui-badge ui-badge-success rounded-pill"><i class="bi bi-check-circle me-1"></i>Aprobada</span>
                            <?php else: ?>
                                <span class="ui-badge ui-badge-warning rounded-pill"><i class="bi bi-hourglass-split me-1"></i>Pendiente</span>
                                <div class="d-flex gap-1 mt-1" style="font-size:11px;">
                                    <form action="<?php echo e(route('owner.instances.approve', $instance)); ?>" method="POST" style="display:inline;">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="ui-btn ui-btn-sm ui-btn-success" style="padding:2px 8px;font-size:11px;" onclick="return confirm('¿Aprobar la solicitud de <?php echo e($instance->nombre); ?>?')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="ui-btn ui-btn-sm ui-btn-danger" style="padding:2px 8px;font-size:11px;" data-bs-toggle="modal" data-bs-target="#rejectModal-<?php echo e($instance->id); ?>">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                    <div class="modal fade" id="rejectModal-<?php echo e($instance->id); ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-sm">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header border-0">
                                                    <h6 class="modal-title fw-bold">Rechazar Solicitud</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="<?php echo e(route('owner.instances.reject', $instance)); ?>" method="POST">
                                                    <?php echo csrf_field(); ?>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="text-muted small fw-semibold d-block">Motivo del Rechazo</label>
                                                            <textarea name="motivo" class="ui-input bg-white form-control" rows="3" required placeholder="Indica el motivo..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0">
                                                        <button type="button" class="ui-btn ui-btn-ghost btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="ui-btn ui-btn-danger btn-sm" onclick="return confirm('¿Rechazar esta solicitud?')">
                                                            <i class="bi bi-x-lg me-1"></i>Rechazar
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($instance->owner_nombre): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:28px;height:28px;background:rgba(139,92,246,.1);color:#8b5cf6;font-size:11px;font-weight:600;">
                                        <?php echo e(strtoupper(substr($instance->owner_nombre, 0, 1))); ?>

                                    </div>
                                    <div>
                                        <div class="small fw-semibold"><?php echo e($instance->owner_nombre); ?></div>
                                        <small class="text-muted" style="font-size:10px;"><?php echo e($instance->owner_email); ?></small>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php $ultimo = $instance->ultimoPago()->first(); ?>
                            <?php if($ultimo): ?>
                                <div class="text-muted small"><?php echo e($ultimo->mes_pagado->isoFormat('MMM YYYY')); ?></div>
                                <small class="text-muted" style="font-size:10px;"><?php echo e($ultimo->monto > 0 ? $instance->costo_mensual ? number_format($ultimo->monto, 0) . ' ' . $systemMoneda : '' : ''); ?></small>
                            <?php elseif($instance->costo_mensual > 0): ?>
                                <div class="text-muted small"><?php echo e($systemMoneda); ?> <?php echo e(number_format($instance->costo_mensual, 0)); ?></div>
                            <?php else: ?>
                                <span class="text-muted small">Sin datos</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="ui-btn ui-btn-ghost btn-sm rounded-pill" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0 py-2">
                                    <li>
                                        <a class="dropdown-item small" href="<?php echo e(route('owner.instances.show', $instance)); ?>">
                                            <i class="bi bi-eye me-2"></i>Ver detalles
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item small" href="<?php echo e(route('owner.instances.edit', $instance)); ?>">
                                            <i class="bi bi-pencil me-2"></i>Editar
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item small" href="<?php echo e(route('owner.instances.config', $instance)); ?>">
                                            <i class="bi bi-gear me-2"></i>Configuración
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item small" href="<?php echo e(route('owner.instances.pagos.create', $instance)); ?>">
                                            <i class="bi bi-cash-coin me-2"></i>Registrar Pago
                                        </a>
                                    </li>
                                    <?php if($instance->bloqueado): ?>
                                    <li>
                                        <a class="dropdown-item small text-success" href="#" onclick="document.getElementById('unblock-<?php echo e($instance->id); ?>').submit()">
                                            <i class="bi bi-unlock me-2"></i>Desbloquear
                                        </a>
                                        <form id="unblock-<?php echo e($instance->id); ?>" action="<?php echo e(route('owner.instances.toggle-block', $instance)); ?>" method="POST" style="display:none;">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="bloqueado" value="0">
                                        </form>
                                    </li>
                                    <?php else: ?>
                                    <li>
                                        <a class="dropdown-item small text-warning" href="#" onclick="document.getElementById('block-<?php echo e($instance->id); ?>').submit()">
                                            <i class="bi bi-lock me-2"></i>Bloquear
                                        </a>
                                        <form id="block-<?php echo e($instance->id); ?>" action="<?php echo e(route('owner.instances.toggle-block', $instance)); ?>" method="POST" style="display:none;">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="bloqueado" value="1">
                                            <input type="hidden" name="motivo_bloqueo" value="Bloqueo desde lista">
                                        </form>
                                    </li>
                                    <?php endif; ?>
                                    <?php if($instance->activo): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item small text-danger" href="#" onclick="UI.confirm.delete('<?php echo e(route('owner.instances.destroy', $instance)); ?>', '<?php echo e($instance->nombre); ?>')">
                                            <i class="bi bi-trash me-2"></i>Desactivar
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="text-center text-muted py-5">
                        <div class="ui-empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>No hay instancias registradas</p>
                            <small>Crea la primera instancia de negocio para comenzar</small>
                            <a href="<?php echo e(route('owner.instances.create')); ?>" class="ui-btn ui-btn-solid btn-sm mt-3">
                                <i class="bi bi-plus-lg me-1"></i>Crear Instancia
                            </a>
                        </div>
                    </td></tr>
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

    
    <div class="d-md-none">
        <?php $__empty_1 = true; $__currentLoopData = $instances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php $subEstado = $instance->estadoSuscripcion(); ?>
        <div class="ui-card mb-3" style="--delay:.<?php echo e($loop->index + 1); ?>s">
            <div class="ui-card-body p-3">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <?php if($instance->logo): ?>
                        <img src="<?php echo e(Storage::url($instance->logo)); ?>" alt="<?php echo e($instance->nombre); ?>" style="width:48px;height:48px;border-radius:12px;object-fit:cover;flex-shrink:0;">
                    <?php else: ?>
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;background:rgba(139,92,246,.1);color:#8b5cf6;font-size:18px;font-weight:600;">
                            <?php echo e(strtoupper(substr($instance->nombre, 0, 1))); ?>

                        </div>
                    <?php endif; ?>
                    <div class="flex-grow-1" style="min-width:0;">
                        <div class="fw-bold text-truncate"><?php echo e($instance->nombre); ?></div>
                        <small class="text-muted"><?php echo e($instance->slug); ?></small>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="ui-badge ui-badge-info rounded-pill"><?php echo e($instance->businessType?->nombre ?? 'Sin tipo'); ?></span>
                    <?php if(!$instance->activo): ?>
                        <span class="ui-badge ui-badge-neutral rounded-pill">Inactiva</span>
                    <?php elseif($instance->bloqueado): ?>
                        <span class="ui-badge ui-badge-danger rounded-pill">Bloqueada</span>
                    <?php elseif($subEstado === 'suspendida'): ?>
                        <span class="ui-badge ui-badge-danger rounded-pill">Suspendida</span>
                    <?php elseif($subEstado === 'prueba'): ?>
                        <span class="ui-badge ui-badge-info rounded-pill">Prueba (<?php echo e($instance->diasPruebaRestantes()); ?>d)</span>
                    <?php elseif($subEstado === 'activa'): ?>
                        <span class="ui-badge ui-badge-success rounded-pill">Al día</span>
                    <?php else: ?>
                        <span class="ui-badge ui-badge-warning rounded-pill"><?php echo e($instance->mesesAtrasados()); ?> mes(es)</span>
                    <?php endif; ?>
                    <?php if(!$instance->aprobado): ?>
                        <span class="ui-badge ui-badge-warning rounded-pill">Pendiente</span>
                    <?php endif; ?>
                </div>
                <?php if(!$instance->aprobado): ?>
                <div class="alert alert-warning border-0 rounded-3 mb-3" style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2) !important;">
                    <div class="d-flex gap-2 align-items-center mb-2">
                        <i class="bi bi-hourglass-split"></i>
                        <small class="fw-semibold">Solicitud pendiente de aprobación</small>
                    </div>
                    <form action="<?php echo e(route('owner.instances.approve', $instance)); ?>" method="POST" class="d-grid gap-2">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="ui-btn ui-btn-success btn-sm" onclick="return confirm('¿Aprobar la solicitud de <?php echo e($instance->nombre); ?>?')">
                            <i class="bi bi-check-lg me-1"></i>Aprobar
                        </button>
                        <button type="button" class="ui-btn ui-btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal-<?php echo e($instance->id); ?>">
                            <i class="bi bi-x-lg me-1"></i>Rechazar
                        </button>
                    </form>
                    <div class="modal fade" id="rejectModal-<?php echo e($instance->id); ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header border-0">
                                    <h6 class="modal-title fw-bold">Rechazar Solicitud</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="<?php echo e(route('owner.instances.reject', $instance)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="text-muted small fw-semibold d-block">Motivo del Rechazo</label>
                                            <textarea name="motivo" class="ui-input bg-white form-control" rows="3" required placeholder="Indica el motivo..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="button" class="ui-btn ui-btn-ghost btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="ui-btn ui-btn-danger btn-sm" onclick="return confirm('¿Rechazar esta solicitud?')">
                                            <i class="bi bi-x-lg me-1"></i>Rechazar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="d-grid gap-2">
                    <a href="<?php echo e(route('owner.instances.show', $instance)); ?>" class="ui-btn ui-btn-solid btn-sm">
                        <i class="bi bi-eye me-1"></i>Ver detalles
                    </a>
                    <div class="d-flex gap-2">
                        <a href="<?php echo e(route('owner.instances.edit', $instance)); ?>" class="ui-btn ui-btn-ghost btn-sm flex-grow-1">
                            <i class="bi bi-pencil me-1"></i>Editar
                        </a>
                        <a href="<?php echo e(route('owner.instances.config', $instance)); ?>" class="ui-btn ui-btn-ghost btn-sm flex-grow-1">
                            <i class="bi bi-gear me-1"></i>Config
                        </a>
                        <a href="<?php echo e(route('owner.instances.pagos.create', $instance)); ?>" class="ui-btn ui-btn-ghost btn-sm flex-grow-1">
                            <i class="bi bi-cash-coin me-1"></i>Pago
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center text-muted py-5">
            <div class="ui-empty-state">
                <i class="bi bi-inbox"></i>
                <p>No hay instancias registradas</p>
                <small>Crea la primera instancia de negocio para comenzar</small>
                <a href="<?php echo e(route('owner.instances.create')); ?>" class="ui-btn ui-btn-solid btn-sm mt-3">
                    <i class="bi bi-plus-lg me-1"></i>Crear Instancia
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/instances/index.blade.php ENDPATH**/ ?>