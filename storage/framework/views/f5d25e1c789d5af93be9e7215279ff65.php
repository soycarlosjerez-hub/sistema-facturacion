<div class="col-xl col-md-6 col-6">
    <div class="ui-stat">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-stat-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="ui-stat-label">MRR</div>
                <span class="badge <?php echo e($mrrGrowthRate >= 0 ? 'bg-success' : 'bg-danger'); ?> bg-opacity-10 text-<?php echo e($mrrGrowthRate >= 0 ? 'success' : 'danger'); ?> rounded-pill px-2 py-1">
                    <i class="bi bi-arrow-<?php echo e($mrrGrowthRate >= 0 ? 'up' : 'down'); ?>-short"></i><?php echo e(abs($mrrGrowthRate)); ?>%
                </span>
            </div>
            <div class="ui-stat-value" style="font-size:1.45rem;color:#8b5cf6;"><?php echo e($systemMoneda ?? 'RD$'); ?> <?php echo e(number_format($mrr, 2)); ?></div>
            <div class="ui-stat-sub">Facturación recurrente mensual</div>
        </div>
    </div>
</div>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/partials/_kpi_mrr.blade.php ENDPATH**/ ?>