<div class="ui-card h-100" style="--delay:.2s">
    <div class="ui-card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-bug me-2" style="color:#ef4444"></i>Errores</h5>
                <small class="text-muted">Últimos 30 días</small>
            </div>
            <div class="text-end">
                <div class="fw-bold" style="color:#ef4444"><?php echo e($erroresNoResueltos7d); ?></div>
                <small class="text-muted">Sin resolver</small>
            </div>
        </div>
        <div style="height:180px;">
            <canvas id="errorsChart"></canvas>
        </div>
    </div>
</div>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/partials/_chart_errors.blade.php ENDPATH**/ ?>