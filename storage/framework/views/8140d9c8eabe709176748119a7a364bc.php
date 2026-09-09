<div class="col-xl col-md-6 col-6">
    <div class="ui-stat">
        <div class="ui-card-accent" style="background:#ef4444"></div>
        <div class="ui-stat-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="ui-stat-label">Errores (7d)</div>
                <span class="badge <?php echo e($erroresNoResueltos7d > 0 ? 'bg-danger' : 'bg-success'); ?> bg-opacity-10 text-<?php echo e($erroresNoResueltos7d > 0 ? 'danger' : 'success'); ?> rounded-pill px-2 py-1">
                    <?php echo e($erroresNoResueltos7d > 0 ? 'Activos' : 'Sin errores'); ?>

                </span>
            </div>
            <div class="ui-stat-value" style="font-size:1.45rem;color:#ef4444;"><?php echo e($erroresNoResueltos7d); ?></div>
            <div class="ui-stat-sub"><?php echo e($totalSinResolver); ?> sin resolver total</div>
        </div>
    </div>
</div>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/partials/_kpi_errors.blade.php ENDPATH**/ ?>