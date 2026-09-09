<?php $__env->startSection('title', $categoria->nombre); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#ec4899;--accent-rgb:236,72,153;--accent-hover:#db2777;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tags"></i>
                </div>
                <div>
                    <h4 class="ui-header-title"><?php echo e($categoria->nombre); ?></h4>
                    <div class="ui-header-meta"><?php echo e($productos->count()); ?> producto(s)</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('categorias.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4">
        <div class="ui-card-accent"></div>
        <div class="card-body p-4">
            <div class="ui-detail-row">
                <span class="ui-detail-label">Descripción</span>
                <span class="ui-detail-value"><?php echo e($categoria->descripcion ?? 'Sin descripción'); ?></span>
            </div>
            <div class="ui-detail-row">
                <span class="ui-detail-label">Estado</span>
                <span class="ui-detail-value">
                    <?php if($categoria->activa): ?>
                        <span class="ui-badge-success"><i class="bi bi-check-circle-fill"></i> Activa</span>
                    <?php else: ?>
                        <span class="ui-badge-danger"><i class="bi bi-x-circle-fill"></i> Inactiva</span>
                    <?php endif; ?>
                </span>
            </div>
            <div class="ui-detail-row">
                <span class="ui-detail-label">Productos</span>
                <span class="ui-detail-value fw-bold fs-4"><?php echo e($productos->count()); ?></span>
            </div>
            <?php if($categoria->icono): ?>
            <div class="ui-detail-row">
                <span class="ui-detail-label">Icono</span>
                <span class="ui-detail-value"><i class="<?php echo e($categoria->icono); ?>"></i> <?php echo e($categoria->icono); ?></span>
            </div>
            <?php endif; ?>
            <?php if($categoria->color): ?>
            <div class="ui-detail-row">
                <span class="ui-detail-label">Color</span>
                <span class="ui-detail-value">
                    <span style="display:inline-block;width:20px;height:20px;background:<?php echo e($categoria->color); ?>;border-radius:4px;vertical-align:middle;"></span>
                    <?php echo e($categoria->color); ?>

                </span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="ui-card">
        <div class="ui-card-title"><i class="bi bi-box-seam me-2"></i> Productos en esta categoría</div>
        <div class="card-body">
                <?php if($productos->count()): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-muted small text-uppercase">
                                    <th class="ps-3">Producto</th>
                                    <th class="text-end">Precio</th>
                                    <th class="text-end">Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="ps-3"><?php echo e($p->nombre); ?></td>
                                        <td class="text-end">RD$ <?php echo e(number_format($p->precio, 2)); ?></td>
                                        <td class="text-end"><?php echo e($p->stock); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No hay productos en esta categoría.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/categorias/show.blade.php ENDPATH**/ ?>