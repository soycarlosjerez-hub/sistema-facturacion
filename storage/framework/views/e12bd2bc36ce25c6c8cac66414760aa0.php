<div class="col-xl col-md-6 col-6">
    <div class="ui-stat">
        <div class="ui-card-accent" style="background:#f59e0b"></div>
        <div class="ui-stat-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="ui-stat-label">Churn Risk</div>
                <span class="badge <?php echo e($churnRiskCount > 0 ? 'bg-warning' : 'bg-success'); ?> bg-opacity-10 text-<?php echo e($churnRiskCount > 0 ? 'warning' : 'success'); ?> rounded-pill px-2 py-1">
                    <?php echo e($churnRiskCount > 0 ? 'Atención' : 'Ok'); ?>

                </span>
            </div>
            <div class="ui-stat-value" style="font-size:1.45rem;color:#f59e0b;"><?php echo e($churnRiskCount); ?></div>
            <div class="ui-stat-sub">Instancias en riesgo de fuga</div>
        </div>
    </div>
</div>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/partials/_kpi_churn.blade.php ENDPATH**/ ?>