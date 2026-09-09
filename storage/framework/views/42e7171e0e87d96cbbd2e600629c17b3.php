<?php $__env->startSection('title', 'Actividad de la Instancia'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h2 class="mb-1"><i class="bi bi-activity me-2"></i>Actividad de la Instancia</h2>
                    <p class="text-muted mb-0">Todo lo que está sucediendo en tu negocio, en tiempo real</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="markAllRead">
                        <i class="bi bi-check2-all me-1"></i>Marcar todo leído
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="cleanOld">
                        <i class="bi bi-trash me-1"></i>Limpiar antiguas
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-3">
                            <select class="form-select form-select-sm" id="statusFilter">
                                <option value="all" <?php echo e($status === 'all' ? 'selected' : ''); ?>>Todas</option>
                                <option value="unread" <?php echo e($status === 'unread' ? 'selected' : ''); ?>>No leídas</option>
                                <option value="read" <?php echo e($status === 'read' ? 'selected' : ''); ?>>Leídas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select form-select-sm" id="categoryFilter">
                                <option value="">Todas las categorías</option>
                                <option value="sale" <?php echo e($filter === 'sale' ? 'selected' : ''); ?>>Ventas</option>
                                <option value="order" <?php echo e($filter === 'order' ? 'selected' : ''); ?>>Órdenes</option>
                                <option value="payment" <?php echo e($filter === 'payment' ? 'selected' : ''); ?>>Pagos</option>
                                <option value="inventory" <?php echo e($filter === 'inventory' ? 'selected' : ''); ?>>Inventario</option>
                                <option value="cash" <?php echo e($filter === 'cash' ? 'selected' : ''); ?>>Caja</option>
                                <option value="fiscal" <?php echo e($filter === 'fiscal' ? 'selected' : ''); ?>>Fiscal</option>
                                <option value="cliente" <?php echo e($filter === 'cliente' ? 'selected' : ''); ?>>Clientes</option>
                                <option value="gasto" <?php echo e($filter === 'gasto' ? 'selected' : ''); ?>>Gastos</option>
                                <option value="system" <?php echo e($filter === 'system' ? 'selected' : ''); ?>>Sistema</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-sm btn-primary w-100" id="applyFilters">
                                <i class="bi bi-funnel me-1"></i>Filtrar
                            </button>
                        </div>
                        <div class="col-md-3 text-md-end">
                            <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">
                                <i class="bi bi-bell me-1"></i><?php echo e($unreadCount); ?> sin leer
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Feed -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div id="notificationsContainer">
                <?php $lastDayLabel = null; ?>
                <?php $__empty_0 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <?php
                        $dayLabel = $notification->created_at->isToday() ? 'Hoy' : ($notification->created_at->isYesterday() ? 'Ayer' : $notification->created_at->format('D, d M Y'));
                        $color = $notification->data['color'] ?? '#3b82f6';
                        $icon = $notification->data['icon'] ?? 'bi-bell';
                        $actorName = $notification->actor_name ?? 'Sistema';
                        $verb = $notification->action ?? $notification->data['verb'] ?? null;
                        $initials = collect(preg_split('/\s+/', trim($actorName)))->take(2)->map(fn($w) => mb_substr($w, 0, 1))->implode('');
                    ?>

                    <?php if($dayLabel !== $lastDayLabel): ?>
                        <div class="feed-day-divider px-4 pt-3 pb-1">
                            <small class="fw-bold text-uppercase text-muted" style="letter-spacing: 0.06em;"><?php echo e($dayLabel); ?></small>
                            <hr class="my-1 opacity-25">
                        </div>
                        <?php $lastDayLabel = $dayLabel; ?>
                    <?php endif; ?>

                    <div class="notification-item d-flex align-items-start p-3 border-bottom <?php echo e($notification->read_at ? '' : 'bg-light-subtle'); ?>"
                         data-id="<?php echo e($notification->id); ?>"
                         data-category="<?php echo e($notification->category); ?>"
                         data-read="<?php echo e($notification->read_at ? 'true' : 'false'); ?>">
                        <div class="flex-shrink-0 me-3 feed-avatar" style="background: <?php echo e($color); ?>22; color: <?php echo e($color); ?>;">
                            <?php if($notification->actor_avatar): ?>
                                <img src="<?php echo e($notification->actor_avatar); ?>" alt="<?php echo e($actorName); ?>" class="w-100 h-100 rounded-circle object-fit-cover">
                            <?php else: ?>
                                <i class="<?php echo e($icon); ?>"></i>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div class="min-w-0">
                                    <span class="fw-semibold"><?php echo e($actorName); ?></span>
                                    <?php if($verb): ?>
                                        <span class="text-muted"> <?php echo e($verb); ?></span>
                                    <?php endif; ?>
                                    <span class="text-dark fw-medium"> <?php echo e($notification->title); ?></span>
                                </div>
                                <small class="text-muted text-nowrap flex-shrink-0"><?php echo e($notification->created_at->diffForHumans()); ?></small>
                            </div>
                            <?php if($notification->body): ?>
                                <p class="mb-1 text-muted small text-break"><?php echo e($notification->body); ?></p>
                            <?php endif; ?>
                            <div class="d-flex gap-2 mt-1 align-items-center">
                                <span class="badge rounded-pill bg-light text-dark border">
                                    <i class="<?php echo e($notification->data['category_icon'] ?? 'bi-bell'); ?> me-1"></i>
                                    <?php echo e($notification->data['category_label'] ?? 'Sistema'); ?>

                                </span>
                                <span class="badge rounded-pill <?php echo e($notification->read_at ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'); ?>">
                                    <?php echo e($notification->read_at ? 'Leída' : 'No leída'); ?>

                                </span>
                            </div>
                        </div>
                        <div class="flex-shrink-0 ms-2 d-flex flex-column gap-1">
                            <?php if($notification->data['action_url'] ?? null): ?>
                                <a href="<?php echo e($notification->data['action_url']); ?>" class="btn btn-sm btn-link text-decoration-none text-primary" title="Ver detalle">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            <?php endif; ?>
                            <?php if(!$notification->read_at): ?>
                                <button class="btn btn-sm btn-link text-decoration-none mark-read-btn" data-id="<?php echo e($notification->id); ?>" title="Marcar como leída">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            <?php endif; ?>
                            <button class="btn btn-sm btn-link text-danger delete-notification-btn" data-id="<?php echo e($notification->id); ?>" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-activity display-1 text-muted"></i>
                        <p class="mt-3 text-muted">Aún no hay actividad registrada</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-footer bg-white border-top-0">
            <?php echo e($notifications->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/notifications/index.blade.php ENDPATH**/ ?>