<div class="ui-card h-100" style="--delay:.3s">
    <div class="ui-card-body">
        <h5 class="fw-bold mb-3">
            <i class="bi bi-list-check me-2" style="color:var(--accent,#8b5cf6)"></i>
            Acciones Requeridas
        </h5>

        <?php
            $hasActions = $pendingApprovals > 0 || $bloqueadas > 0 || $churnRiskCount > 0 || $erroresCriticos24h > 0 || $instanciasConAtraso->count() > 0;
        ?>

        <?php if(!$hasActions): ?>
            <div class="ui-empty-state">
                <i class="bi bi-emoji-smile" style="color:#10b981;"></i>
                <p>¡Todo en orden!</p>
                <small class="text-muted">No hay acciones pendientes</small>
            </div>
        <?php else: ?>
            <?php if($pendingApprovals > 0): ?>
                <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-2" style="background:rgba(245,158,11,.06);border:1px solid rgba(245,158,11,.12);">
                    <div style="width:36px;height:36px;border-radius:8px;background:rgba(245,158,11,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-file-earmark-check text-warning"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width:0;">
                        <strong class="small"><?php echo e($pendingApprovals); ?> solicitud(ones) de registro</strong>
                        <div class="text-muted" style="font-size:.72rem;">Esperando aprobación del owner</div>
                    </div>
                    <a href="<?php echo e(route('owner.solicitudes.index')); ?>" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill flex-shrink-0" style="background:linear-gradient(135deg,#f59e0b,#d97706);border-color:#d97706;">
                        <i class="bi bi-check2 me-1"></i>Aprobar
                    </a>
                </div>
            <?php endif; ?>

            <?php if($bloqueadas > 0): ?>
                <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-2" style="background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.12);">
                    <div style="width:36px;height:36px;border-radius:8px;background:rgba(239,68,68,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-lock-fill text-danger"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width:0;">
                        <strong class="small"><?php echo e($bloqueadas); ?> instancia(s) bloqueada(s)</strong>
                        <div class="text-muted" style="font-size:.72rem;">Por impago o motivo manual</div>
                    </div>
                    <a href="<?php echo e(route('owner.instances.index')); ?>" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill flex-shrink-0" style="background:linear-gradient(135deg,#ef4444,#dc2626);border-color:#dc2626;">
                        <i class="bi bi-eye me-1"></i>Revisar
                    </a>
                </div>
            <?php endif; ?>

            <?php if($churnRiskCount > 0): ?>
                <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-2" style="background:rgba(245,158,11,.06);border:1px solid rgba(245,158,11,.12);">
                    <div style="width:36px;height:36px;border-radius:8px;background:rgba(245,158,11,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-person-x text-warning"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width:0;">
                        <strong class="small"><?php echo e($churnRiskCount); ?> en riesgo de churn</strong>
                        <div class="text-muted" style="font-size:.72rem;">Prueba perdida, sin pago</div>
                    </div>
                    <a href="<?php echo e(route('owner.instances.index')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill flex-shrink-0">
                        Contactar
                    </a>
                </div>
            <?php endif; ?>

            <?php if($erroresCriticos24h > 0): ?>
                <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-2" style="background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.12);">
                    <div style="width:36px;height:36px;border-radius:8px;background:rgba(239,68,68,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-bug-fill text-danger"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width:0;">
                        <strong class="small"><?php echo e($erroresCriticos24h); ?> error(es) crítico(s)</strong>
                        <div class="text-muted" style="font-size:.72rem;">Últimas 24 horas</div>
                    </div>
                    <a href="<?php echo e(route('owner.errors.index')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill flex-shrink-0">
                        Ver
                    </a>
                </div>
            <?php endif; ?>

            <?php if($instanciasConAtraso->count() > 0): ?>
                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.12);">
                    <div style="width:36px;height:36px;border-radius:8px;background:rgba(239,68,68,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-cash-coin text-danger"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width:0;">
                        <strong class="small"><?php echo e($instanciasConAtraso->count()); ?> con pago atrasado</strong>
                        <div class="text-muted" style="font-size:.72rem;">Deuda total: <?php echo e($systemMoneda ?? 'RD$'); ?> <?php echo e(number_format($deudaTotal, 2)); ?></div>
                    </div>
                    <a href="<?php echo e(route('owner.instances.index')); ?>" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill flex-shrink-0" style="background:linear-gradient(135deg,#10b981,#059669);border-color:#059669;color:#fff;">
                        <i class="bi bi-cash me-1"></i>Cobrar
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/partials/_action_items.blade.php ENDPATH**/ ?>