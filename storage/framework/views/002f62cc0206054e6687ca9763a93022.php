<?php $__env->startSection('title', 'Zonas de Cobertura'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
/* Zones Module Styles */
.zone-card {
    position: relative;
    overflow: hidden;
    transition: all .3s ease;
}
.zone-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 48px rgba(0,0,0,.1);
}
.zone-card.inactive-zone {
    opacity: .65;
}
.zone-card.inactive-zone:hover {
    opacity: .85;
}
.zone-ring {
    width: 80px; height: 80px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1rem;
    font-size: 2rem;
    border: 3px solid;
}
.zone-rate {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--accent, #0ea5e9);
}
.zone-timer {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    padding: .25rem .65rem;
    border-radius: 9999px;
    font-size: .78rem;
    font-weight: 600;
    background: rgba(14,165,233,.08);
    color: #0ea5e9;
}
.free-shipping-badge {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    padding: .2rem .6rem;
    border-radius: 9999px;
    font-size: .72rem;
    font-weight: 700;
    background: rgba(34,197,94,.1);
    color: #16a34a;
    border: 1px solid rgba(34,197,94,.2);
}
@media (max-width: 767.98px) {
    .zone-grid .col-md-6 { max-width: 100%; }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#0ea5e9;--accent-rgb:14,165,233;--accent-hover:#0284c7;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Zonas de Cobertura</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-map me-1"></i>
                        <span><?php echo e($zones->total()); ?> zona(s) configurada(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delivery-zones.create')): ?>
                <a href="<?php echo e(route('delivery-zones.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Zona
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4">
            <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="row g-4 zone-grid">
        <?php $__empty_0 = true; $__currentLoopData = $zones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
        <div class="col-md-6 col-lg-4">
            <div class="ui-card zone-card h-100 <?php echo e(!$zone->activo ? 'inactive-zone' : ''); ?>" style="--delay:<?php echo e(sprintf('%.2f', $loop->index * 0.05)); ?>s; <?php echo e($zone->activo ? '' : ''); ?>">
                <div class="ui-card-accent" style="background: <?php echo e($zone->activo ? 'linear-gradient(90deg, #0ea5e9, rgba(255,255,255,.3))' : 'linear-gradient(90deg, #94a3b8, rgba(255,255,255,.3))'); ?>;"></div>
                <div class="ui-card-body text-center">
                    <div class="zone-ring <?php echo e($zone->activo ? 'border-info' : 'border-secondary'); ?>" style="background: <?php echo e($zone->activo ? 'rgba(14,165,233,.08)' : 'rgba(148,163,184,.08)'); ?>;">
                        <i class="bi bi-geo-alt <?php echo e($zone->activo ? 'text-info' : 'text-secondary'); ?>"></i>
                    </div>

                    <h5 class="fw-bold mb-1"><?php echo e($zone->nombre); ?></h5>
                    <?php if($zone->descripcion): ?>
                    <p class="text-muted small mb-3"><?php echo e(Str::limit($zone->descripcion, 60)); ?></p>
                    <?php endif; ?>

                    <div class="row g-2 mb-3 text-start">
                        <div class="col-6">
                            <small class="text-muted d-block">Radio</small>
                            <span class="fw-bold"><?php echo e($zone->radio_km); ?> km</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Tarifa Base</small>
                            <span class="fw-bold">$<?php echo e(number_format($zone->tarifa_base, 2)); ?></span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Tarifa/Km</small>
                            <span class="fw-bold">$<?php echo e(number_format($zone->tarifa_por_km, 2)); ?></span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Tiempo Est.</small>
                            <span class="zone-timer"><i class="bi bi-clock me-1"></i><?php echo e($zone->tiempo_estimado_minutos); ?> min</span>
                        </div>
                    </div>

                    <?php if($zone->minimo_para_envio_gratis): ?>
                    <div class="mb-3">
                        <span class="free-shipping-badge">
                            <i class="bi bi-truck"></i> Envío gratis ≥ $<?php echo e(number_format($zone->minimo_para_envio_gratis ?? 0, 2)); ?>

                        </span>
                    </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-center gap-2 mt-2">
                        <?php if($zone->activo): ?>
                            <span class="ui-badge ui-badge-success"><i class="bi bi-check-circle me-1"></i>Activa</span>
                        <?php else: ?>
                            <span class="ui-badge ui-badge-neutral"><i class="bi bi-x-circle me-1"></i>Inactiva</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
        <div class="col-12">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body text-center py-5">
                    <i class="bi bi-geo-alt fs-1 d-block mb-3 text-muted"></i>
                    <h5 class="fw-bold text-muted">No hay zonas configuradas</h5>
                    <p class="text-muted mb-3">Define las zonas de cobertura para habilitar el delivery</p>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delivery-zones.create')): ?>
                    <a href="<?php echo e(route('delivery-zones.create')); ?>" class="ui-btn ui-btn-solid rounded-pill">
                        <i class="bi bi-plus-lg me-1"></i> Crear Primera Zona
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php if($zones->hasPages()): ?>
    <div class="mt-4">
        <?php echo e($zones->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/delivery-zones/index.blade.php ENDPATH**/ ?>