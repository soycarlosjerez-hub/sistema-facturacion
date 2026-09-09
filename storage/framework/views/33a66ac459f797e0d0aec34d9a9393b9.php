<?php $__env->startSection('title', $obra->titulo); ?>
<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-image"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-eye me-1"></i>DETALLE
                    </span>
                    <h2 class="fw-bold mb-0 text-white"><?php echo e($obra->titulo); ?></h2>
                    <p class="mb-0 opacity-75">Detalle de la obra</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('arte.obras.edit', $obra)); ?>" class="ui-btn ui-btn-primary rounded-pill px-4">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <a href="<?php echo e(route('arte.obras.index')); ?>" class="ui-btn ui-btn-primary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="ui-card text-center p-0 overflow-hidden" style="--delay:.15s">
                <div class="ui-card-accent" style="background:#8b5cf6"></div>
                <?php if($obra->imagen): ?>
                    <img src="<?php echo e(asset('storage/' . $obra->imagen)); ?>" class="w-100" style="max-height:380px;object-fit:cover;" alt="<?php echo e($obra->titulo); ?>">
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center bg-light" style="height:320px;">
                        <i class="bi bi-image text-muted" style="font-size:3rem;"></i>
                    </div>
                <?php endif; ?>
                <div class="p-4">
                    <span class="badge bg-<?php echo e($obra->estado_badge_class); ?> rounded-pill mb-2"><?php echo e($obra->estado_label); ?></span>
                    <h4 class="fw-bold mb-1"><?php echo e($obra->titulo); ?></h4>
                    <p class="text-muted mb-0">por <strong><?php echo e($obra->artista?->nombre ?? '—'); ?></strong></p>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Precio venta</span>
                        <span class="fw-bold">RD$ <?php echo e(number_format($obra->precio_venta, 2)); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span class="text-muted small">Precio compra</span>
                        <span>RD$ <?php echo e(number_format($obra->precio_compra, 2)); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span class="text-muted small">Margen</span>
                        <span class="fw-bold text-success">RD$ <?php echo e(number_format(max(0, $obra->precio_venta - $obra->precio_compra), 2)); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="background:#8b5cf6"></div>
                <h5 class="ui-card-title"><i class="bi bi-info-circle"></i>Información de la obra</h5>
                <div class="ui-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="small text-muted">Técnica</div>
                            <div class="fw-semibold"><?php echo e($obra->tecnica ?? '—'); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Año de creación</div>
                            <div class="fw-semibold"><?php echo e($obra->ano_creacion ?? '—'); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Dimensiones</div>
                            <div class="fw-semibold"><?php echo e($obra->dimensiones ? $obra->dimensiones . ' cm' : '—'); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Material</div>
                            <div class="fw-semibold"><?php echo e($obra->material ?? '—'); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Colección</div>
                            <div class="fw-semibold"><?php echo e($obra->coleccion?->nombre ?? 'Sin colección'); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Fecha de adquisición</div>
                            <div class="fw-semibold"><?php echo e(optional($obra->fecha_adquisicion)->format('d/m/Y') ?? '—'); ?></div>
                        </div>
                        <div class="col-12">
                            <div class="small text-muted">Descripción</div>
                            <div class="fw-semibold"><?php echo e($obra->descripcion ?? 'Sin descripción'); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if($obra->consignacion): ?>
            <div class="ui-card mt-3" style="--delay:.25s">
                <div class="ui-card-accent" style="background:#10b981"></div>
                <h5 class="ui-card-title"><i class="bi bi-arrow-left-right"></i>Consignación activa</h5>
                <div class="ui-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="small text-muted">Consignante</div>
                            <div class="fw-semibold"><?php echo e($obra->consignacion->consignante); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Comisión</div>
                            <div class="fw-semibold"><?php echo e($obra->consignacion->porcentaje_comision); ?>%</div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="ui-card mt-3" style="--delay:.3s">
                <div class="ui-card-accent" style="background:#e1306c"></div>
                <h5 class="ui-card-title"><i class="bi bi-easel"></i>Exhibiciones</h5>
                <div class="ui-card-body">
                    <?php $__empty_0 = true; $__currentLoopData = $obra->exhibiciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exhibicion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill me-1 mb-1"><?php echo e($exhibicion->nombre); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <span class="text-muted small">No está asignada a ninguna exhibición.</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/arte/obras/show.blade.php ENDPATH**/ ?>