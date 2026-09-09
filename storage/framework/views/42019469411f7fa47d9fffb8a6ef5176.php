<div class="col-xl col-md-6 col-6">
    <div class="ui-stat">
        <div class="ui-card-accent" style="background:#3b82f6"></div>
        <div class="ui-stat-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="ui-stat-label">Collection Rate</div>
                <span class="badge <?php echo e($collectionRate >= 80 ? 'bg-success' : 'bg-warning'); ?> bg-opacity-10 text-<?php echo e($collectionRate >= 80 ? 'success' : 'warning'); ?> rounded-pill px-2 py-1">
                    <?php echo e($collectionRate >= 80 ? 'Ok' : 'Bajo'); ?>

                </span>
            </div>
            <div class="ui-stat-value" style="font-size:1.45rem;color:#3b82f6;"><?php echo e($collectionRate); ?>%</div>
            <div class="ui-stat-sub"><?php echo e($systemMoneda ?? 'RD$'); ?> <?php echo e(number_format($currentMonthCollected, 2)); ?> cobrado</div>
        </div>
    </div>
</div>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/partials/_kpi_collection.blade.php ENDPATH**/ ?>