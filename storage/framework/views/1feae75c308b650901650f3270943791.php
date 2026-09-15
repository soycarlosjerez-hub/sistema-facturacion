<div class="ui-card h-100" style="--delay:.3s">
    <div class="ui-card-body">
        <h5 class="fw-bold mb-3"><i class="bi bi-card-list me-2" style="color:#06b6d4"></i>Distribución por Plan</h5>
        <div style="height:200px;">
            <canvas id="planDistributionChart"></canvas>
        </div>
        <div class="mt-3">
            <?php $__empty_1 = true; $__currentLoopData = $planDistribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $planName => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $total = array_sum($planDistribution);
                    $pct = $total > 0 ? round($count / $total * 100) : 0;
                    $colors = ['#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#06b6d4', '#ec4899', '#84cc16'];
                ?>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width:10px;height:10px;border-radius:3px;background:<?php echo e($colors[$loop->index % count($colors)]); ?>;display:inline-block;"></span>
                        <span class="small fw-semibold"><?php echo e($planName); ?></span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress" style="width:80px;height:5px;">
                            <div class="progress-bar" style="width:<?php echo e($pct); ?>%;background:<?php echo e($colors[$loop->index % count($colors)]); ?>;"></div>
                        </div>
                        <span class="small fw-bold"><?php echo e($count); ?></span>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-3 text-muted">
                    <i class="bi bi-inbox"></i>
                    <small>Sin planes configurados</small>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/partials/_chart_plan_distribution.blade.php ENDPATH**/ ?>