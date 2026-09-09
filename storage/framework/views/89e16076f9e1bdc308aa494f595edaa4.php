<div class="ui-card h-100" style="--delay:.25s">
    <div class="ui-card-body">
        <h5 class="fw-bold mb-3"><i class="bi bi-pie-chart me-2" style="color:#3b82f6"></i>Salud de Instancias</h5>
        <div style="height:200px;">
            <canvas id="instanceHealthChart"></canvas>
        </div>
        <div class="row g-2 mt-3">
            <div class="col-6">
                <div class="d-flex align-items-center gap-2">
                    <span style="width:10px;height:10px;border-radius:50%;background:#10b981;display:inline-block;"></span>
                    <div>
                        <span class="fw-bold small"><?php echo e($activas); ?></span>
                        <small class="text-muted d-block">Activas</small>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="d-flex align-items-center gap-2">
                    <span style="width:10px;height:10px;border-radius:50%;background:#3b82f6;display:inline-block;"></span>
                    <div>
                        <span class="fw-bold small"><?php echo e($enPrueba); ?></span>
                        <small class="text-muted d-block">En prueba</small>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="d-flex align-items-center gap-2">
                    <span style="width:10px;height:10px;border-radius:50%;background:#ef4444;display:inline-block;"></span>
                    <div>
                        <span class="fw-bold small"><?php echo e($bloqueadas); ?></span>
                        <small class="text-muted d-block">Bloqueadas</small>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="d-flex align-items-center gap-2">
                    <span style="width:10px;height:10px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>
                    <div>
                        <span class="fw-bold small"><?php echo e($churnRiskCount); ?></span>
                        <small class="text-muted d-block">Churn risk</small>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="d-flex align-items-center gap-2">
                    <span style="width:10px;height:10px;border-radius:50%;background:#94a3b8;display:inline-block;"></span>
                    <div>
                        <span class="fw-bold small"><?php echo e($pendingApprovals); ?></span>
                        <small class="text-muted d-block">Pendientes</small>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="d-flex align-items-center gap-2">
                    <span style="width:10px;height:10px;border-radius:50%;background:#64748b;display:inline-block;"></span>
                    <div>
                        <span class="fw-bold small"><?php echo e($archivadas); ?></span>
                        <small class="text-muted d-block">Archivadas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/partials/_chart_instance_health.blade.php ENDPATH**/ ?>