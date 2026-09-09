<?php $__env->startSection('title', $sucursal->nombre); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
body.dark-mode .sucursales-detail-card {
    background: rgba(15,23,42,.8);
    border-color: rgba(255,255,255,.08);
}
body.dark-mode .sucursales-detail-card .form-label,
body.dark-mode .sucursales-detail-card .text-muted { color: #94a3b8; }
body.dark-mode .sucursales-detail-card .text-dark { color: #f1f5f9 !important; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 py-3 ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>    <div class="bubble"></div>    <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-geo-alt"></i></div>
                <div>
                    <h4 class="ui-header-title">
                        <?php echo e($sucursal->nombre); ?>

                        <?php if($sucursal->es_matriz): ?>
                            <span class="ui-badge ui-badge-success" style="font-size:.6rem;"><i class="bi bi-star-fill me-1"></i>Matriz</span>
                        <?php endif; ?>
                        <?php if($sucursal->activa): ?>
                            <span class="ui-badge ui-badge-success" style="font-size:.6rem;"><i class="bi bi-check-circle me-1"></i>Activa</span>
                        <?php else: ?>
                            <span class="ui-badge ui-badge-neutral" style="font-size:.6rem;"><i class="bi bi-x-circle me-1"></i>Inactiva</span>
                        <?php endif; ?>
                    </h4>
                    <div class="ui-header-meta"><?php echo e($sucursal->codigo); ?> &middot; <?php echo e($sucursal->direccion ?? 'Sin dirección'); ?></div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sucursales.edit')): ?>
                <a href="<?php echo e(route('sucursales.edit', $sucursal)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill"><i class="bi bi-pencil me-2"></i>Editar</a>
                <?php endif; ?>
                <a href="<?php echo e(route('sucursales.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill"><i class="bi bi-arrow-left me-2"></i>Volver</a>
            </div>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.1s">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="bi bi-graph-up text-primary fs-5"></i>
                        </div>
                        <div>
                            <div class="ui-stat-label">Ventas del mes</div>
                            <div class="ui-stat-value"><?php echo e($stats['ventas_mes']); ?></div>
                            <small class="text-muted"><?php echo e($stats['ingresos_mes'] > 0 ? ($systemMoneda ?? '$') . ' ' . number_format($stats['ingresos_mes'], 0) : 'Sin ingresos'); ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.15s">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="bi bi-building text-success fs-5"></i>
                        </div>
                        <div>
                            <div class="ui-stat-label">Almacenes</div>
                            <div class="ui-stat-value"><?php echo e($sucursal->almacenes_count ?? $stats['almacenes']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.2s">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="bi bi-cash-stack text-warning fs-5"></i>
                        </div>
                        <div>
                            <div class="ui-stat-label">Cajas activas</div>
                            <div class="ui-stat-value"><?php echo e($stats['cajas_activas']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.25s">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                            <i class="bi bi-people text-info fs-5"></i>
                        </div>
                        <div>
                            <div class="ui-stat-label">Empleados</div>
                            <div class="ui-stat-value"><?php echo e($stats['empleados']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            <div class="ui-card sucursales-detail-card h-100" style="--delay:.3s">
                <div class="ui-card-accent"></div>
                <div class="premium-card-title">
                    <i class="bi bi-info-circle icon-purple"></i>
                    Información
                </div>
                <div class="card-body pt-0">
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Código</div>
                        <div class="premium-detail-value"><?php echo e($sucursal->codigo); ?></div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Nombre</div>
                        <div class="premium-detail-value"><?php echo e($sucursal->nombre); ?></div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Dirección</div>
                        <div class="premium-detail-value"><?php echo e($sucursal->direccion ?? '—'); ?></div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Teléfono</div>
                        <div class="premium-detail-value"><?php echo e($sucursal->telefono ?? '—'); ?></div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Email</div>
                        <div class="premium-detail-value"><?php echo e($sucursal->email ?? '—'); ?></div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">RNC</div>
                        <div class="premium-detail-value"><?php echo e($sucursal->rnc ?? '—'); ?></div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Creado</div>
                        <div class="premium-detail-value"><?php echo e($sucursal->created_at->format('d/m/Y h:i A')); ?></div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Actualizado</div>
                        <div class="premium-detail-value"><?php echo e($sucursal->updated_at->format('d/m/Y h:i A')); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="ui-card h-100" style="--delay:.35s">
                <div class="ui-card-accent"></div>
                <div class="premium-card-title">
                    <i class="bi bi-clock-history icon-purple"></i>
                    Ventas recientes
                </div>
                <div class="card-body pt-0">
                    <?php $__empty_0 = true; $__currentLoopData = $activity['ultimas_ventas']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom border-light">
                        <div>
                            <span class="fw-bold text-primary">#<?php echo e(str_pad($venta->id, 5, '0', STR_PAD_LEFT)); ?></span>
                            <small class="text-muted ms-2"><?php echo e($venta->cliente?->nombre ?? 'Consumidor Final'); ?></small>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold"><?php echo e($systemMoneda ?? '$'); ?><?php echo e(number_format($venta->total, 2)); ?></span>
                            <small class="d-block text-muted"><?php echo e($venta->created_at->diffForHumans()); ?></small>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-1"></i>
                        <p class="mt-2 mb-0">Sin ventas registradas</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="ui-card" style="--delay:.4s">
                <div class="ui-card-accent"></div>
                <div class="premium-card-title">
                    <i class="bi bi-box-seam icon-purple"></i>
                    Almacenes
                </div>
                <div class="card-body pt-0">
                    <?php $almacenes = $sucursal->almacenes()->orderBy('nombre')->get(); ?>
                    <?php $__empty_0 = true; $__currentLoopData = $almacenes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $almacen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span><i class="bi bi-building me-2 text-muted"></i><?php echo e($almacen->nombre); ?></span>
                        <a href="<?php echo e(route('almacenes.show', $almacen)); ?>" class="btn btn-sm btn-outline-primary rounded-pill">Ver</a>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <div class="text-muted small">Sin almacenes registrados.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="ui-card" style="--delay:.45s">
                <div class="ui-card-accent"></div>
                <div class="premium-card-title">
                    <i class="bi bi-cash-coin icon-purple"></i>
                    Cajas
                </div>
                <div class="card-body pt-0">
                    <?php $cajas = $sucursal->cajas()->orderBy('nombre')->get(); ?>
                    <?php $__empty_0 = true; $__currentLoopData = $cajas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $caja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <span><?php echo e($caja->nombre); ?></span>
                            <?php if($caja->activa): ?>
                                <span class="ui-badge ui-badge-success" style="font-size:.6rem;">Activa</span>
                            <?php endif; ?>
                        </div>
                        <a href="<?php echo e(route('cajas.show', $caja)); ?>" class="btn btn-sm btn-outline-primary rounded-pill">Ver</a>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <div class="text-muted small">Sin cajas registradas.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/sucursales/show.blade.php ENDPATH**/ ?>