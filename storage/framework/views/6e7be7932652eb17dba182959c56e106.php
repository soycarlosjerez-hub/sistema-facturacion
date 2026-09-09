<?php $__env->startSection('title', 'Ver Red de Configuración'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-hdd-network"></i>
                </div>
                <div>
                    <h4 class="ui-header-title"><?php echo e($redConfig->nombre_red); ?></h4>
                    <div class="ui-header-meta">Detalles de la configuración de red</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('redes-config.edit')): ?>
                <a href="<?php echo e(route('redes-config.edit', $redConfig)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('redes-config.index')); ?>" class="ui-btn ui-btn-sm rounded-pill" style="background:rgba(255,255,255,.15);">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Información de Red</h6>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Nombre de la Red</small>
                        <strong><?php echo e($redConfig->nombre_red); ?></strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Cliente</small>
                        <strong><?php echo e($redConfig->cliente->nombre ?? '-'); ?></strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">SSID WiFi</small>
                        <span class="badge bg-info"><?php echo e($redConfig->ssid_wifi ?? 'N/A'); ?></span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Canal WiFi</small>
                        <strong><?php echo e($redConfig->canal_wifi ?? '-'); ?></strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">VLAN ID</small>
                        <?php if($redConfig->vlan_id): ?>
                        <span class="badge bg-success fs-6">VLAN <?php echo e($redConfig->vlan_id); ?></span>
                        <?php else: ?>
                        <span class="text-muted">Sin VLAN</span>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Dirección de Red</small>
                        <code><?php echo e($redConfig->direccion_red ?? '-'); ?></code>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Configuración DHCP</h6>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">DHCP</small>
                        <span class="badge <?php echo e($redConfig->dhcp_activado ? 'bg-success' : 'bg-secondary'); ?> fs-6">
                            <?php echo e($redConfig->dhcp_activado ? 'Activado' : 'Desactivado'); ?>

                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Rango DHCP</small>
                        <code><?php echo e($redConfig->dhcp_rango ?? 'N/A'); ?></code>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Cobertura</small>
                        <p class="text-muted"><?php echo e($redConfig->cobertura ?? '-'); ?></p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Notas</small>
                        <p class="text-muted"><?php echo e($redConfig->notas ?? 'Sin notas'); ?></p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Estado</small>
                        <span class="badge <?php echo e($redConfig->activo ? 'bg-success' : 'bg-secondary'); ?> fs-6">
                            <?php echo e($redConfig->activo_label); ?>

                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/redes-config/show.blade.php ENDPATH**/ ?>