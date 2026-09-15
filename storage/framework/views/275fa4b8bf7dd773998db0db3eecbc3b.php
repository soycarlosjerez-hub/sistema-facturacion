<div class="ui-card h-100" style="--delay:.2s">
    <div class="ui-card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-activity me-2" style="color:var(--accent,#8b5cf6)"></i>
                Actividad Reciente
            </h5>
            <a href="<?php echo e(route('owner.activity.history')); ?>" class="text-decoration-none small fw-bold">Ver toda</a>
        </div>

        <div class="activity-timeline">
            <?php $__empty_1 = true; $__currentLoopData = $ownerActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $actionIcons = [
                        'INSTANCE_CREATE' => ['bi-plus-circle', '#10b981', 'rgba(16,185,129,.12)'],
                        'INSTANCE_UPDATE' => ['bi-pencil', '#3b82f6', 'rgba(59,130,246,.12)'],
                        'INSTANCE_BLOCK' => ['bi-lock', '#ef4444', 'rgba(239,68,68,.12)'],
                        'INSTANCE_UNBLOCK' => ['bi-unlock', '#10b981', 'rgba(16,185,129,.12)'],
                        'INSTANCE_DELETE' => ['bi-trash', '#64748b', 'rgba(100,116,139,.12)'],
                        'USER_CREATE' => ['bi-person-plus', '#8b5cf6', 'rgba(139,92,246,.12)'],
                        'USER_DELETE' => ['bi-person-x', '#ef4444', 'rgba(239,68,68,.12)'],
                        'PAYMENT_CONFIRM' => ['bi-cash-coin', '#10b981', 'rgba(16,185,129,.12)'],
                        'PLAN_*' => ['bi-card-checklist', '#06b6d4', 'rgba(6,182,212,.12)'],
                    ];
                    $iconKey = $log->action;
                    foreach ($actionIcons as $key => $val) {
                        if ($iconKey === $key || fnmatch($key, $iconKey)) {
                            $iconData = $val;
                            break;
                        }
                    }
                    $iconData ??= ['bi-circle', '#8b5cf6', 'rgba(139,92,246,.12)'];
                ?>
                <div class="d-flex gap-3 <?php echo e($loop->last ? '' : 'pb-3'); ?>" style="<?php echo e($loop->last ? '' : 'border-bottom:1px solid rgba(0,0,0,.04);'); ?>">
                    <div class="flex-shrink-0">
                        <div style="width:32px;height:32px;border-radius:50%;background:<?php echo e($iconData[2]); ?>;display:flex;align-items:center;justify-content:center;">
                            <i class="bi <?php echo e($iconData[0]); ?>" style="font-size:.8rem;color:<?php echo e($iconData[1]); ?>;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1" style="min-width:0;">
                        <div class="small fw-semibold text-truncate"><?php echo e($log->description); ?></div>
                        <div class="text-muted" style="font-size:.72rem;">
                            <?php echo e($log->user?->name ?? 'Sistema'); ?> · <?php echo e($log->created_at->diffForHumans()); ?>

                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="ui-empty-state">
                    <i class="bi bi-clock-history"></i>
                    <p>Sin actividad reciente</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/partials/_activity_feed.blade.php ENDPATH**/ ?>