<?php $__env->startSection('title', $almacen->nombre); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; margin-bottom: .15rem; }
    .detail-value { font-size: .9rem; color: #1e293b; font-weight: 500; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#14b8a6;--accent-rgb:20,184,166;--accent-hover:#0d9488;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h4 class="ui-header-title"><?php echo e($almacen->nombre); ?></h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-geo-alt me-1"></i>
                        <?php echo e($almacen->ubicacion ?? 'Sin ubicación'); ?>

                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('almacenes.edit', $almacen)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" style="background:rgba(20,184,166,.2);border-color:rgba(20,184,166,.35);">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <a href="<?php echo e(route('almacenes.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-4"><i class="bi bi-info-circle me-2" style="color:#14b8a6;"></i> Información del Almacén</h5>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="detail-label">Nombre</div>
                            <div class="detail-value fw-semibold"><?php echo e($almacen->nombre); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Ubicación</div>
                            <div class="detail-value"><?php echo e($almacen->ubicacion ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Sucursal</div>
                            <div class="detail-value"><?php echo e($almacen->sucursal?->nombre ?? 'Sin asignar'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Estado</div>
                            <div class="detail-value">
                                <?php if($almacen->activo ?? true): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill"><i class="bi bi-x-circle-fill me-1"></i>Inactivo</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Fecha de Creación</div>
                            <div class="detail-value"><?php echo e($almacen->created_at->format('d/m/Y h:i A')); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if(isset($almacen->productos) && $almacen->productos->count()): ?>
            <div class="ui-card mt-4" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-box-seam me-2" style="color:#14b8a6;"></i> Productos en Stock (<?php echo e($almacen->productos->count()); ?>)</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-muted small text-uppercase">
                                    <th class="ps-3">Producto</th>
                                    <th class="text-end">Stock</th>
                                    <th class="text-end">Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $almacen->productos->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="ps-3"><?php echo e($producto->nombre); ?></td>
                                    <td class="text-end">
                                        <span class="badge <?php echo e(($producto->pivot->stock ?? 0) > 0 ? 'bg-success' : 'bg-danger'); ?> bg-opacity-10 text-<?php echo e(($producto->pivot->stock ?? 0) > 0 ? 'success' : 'danger'); ?> rounded-pill">
                                            <?php echo e($producto->pivot->stock ?? 0); ?>

                                        </span>
                                    </td>
                                    <td class="text-end">RD$ <?php echo e(number_format($producto->precio, 2)); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="ui-card mb-3" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body text-center">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:64px;height:64px;background:rgba(20,184,166,.1);">
                        <i class="bi bi-building fs-2" style="color:#14b8a6;"></i>
                    </div>
                    <h5 class="fw-bold mb-1"><?php echo e($almacen->nombre); ?></h5>
                    <small class="text-muted"><?php echo e($almacen->ubicacion ?? 'Sin ubicación definida'); ?></small>
                </div>
            </div>

            <?php if(isset($movimientos) && count($movimientos)): ?>
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-arrow-left-right me-2"></i>Movimientos Recientes</h6>
                    <?php $__currentLoopData = $movimientos->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom border-light">
                        <div>
                            <span class="fw-semibold small"><?php echo e($mov->tipo); ?></span>
                            <small class="text-muted d-block"><?php echo e($mov->created_at->format('d/m/Y')); ?></small>
                        </div>
                        <span class="badge <?php echo e($mov->tipo === 'entrada' ? 'bg-success' : 'bg-danger'); ?> bg-opacity-10 text-<?php echo e($mov->tipo === 'entrada' ? 'success' : 'danger'); ?> rounded-pill">
                            <?php echo e($mov->tipo === 'entrada' ? '+' : '-'); ?><?php echo e($mov->cantidad); ?>

                        </span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/almacenes/show.blade.php ENDPATH**/ ?>