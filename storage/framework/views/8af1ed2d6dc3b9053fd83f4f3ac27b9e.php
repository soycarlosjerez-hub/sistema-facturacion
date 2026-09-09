<?php $__env->startSection('title', 'Ver Marca Tecnológica'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tag"></i>
                </div>
                <div>
                    <h4 class="ui-header-title"><?php echo e($marcaTecnologica->nombre); ?></h4>
                    <div class="ui-header-meta">Detalles de la marca tecnológica</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('marca-tecnologicas.edit')): ?>
                <a href="<?php echo e(route('marcas-tecnologicas.edit', $marcaTecnologica)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('marcas-tecnologicas.index')); ?>" class="ui-btn ui-btn-sm rounded-pill" style="background:rgba(255,255,255,.15);">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Información General</h6>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Nombre</small>
                        <strong><?php echo e($marcaTecnologica->nombre); ?></strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Website</small>
                        <?php if($marcaTecnologica->website): ?>
                        <a href="<?php echo e($marcaTecnologica->website); ?>" target="_blank" class="text-decoration-none">
                            <i class="bi bi-globe me-1"></i><?php echo e($marcaTecnologica->website); ?>

                        </a>
                        <?php else: ?>
                        <span class="text-muted">-</span>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">País</small>
                        <strong><?php echo e($marcaTecnologica->pais ?? '-'); ?></strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Email de Contacto</small>
                        <?php if($marcaTecnologica->contacto_email): ?>
                        <a href="mailto:<?php echo e($marcaTecnologica->contacto_email); ?>" class="text-decoration-none">
                            <i class="bi bi-envelope me-1"></i><?php echo e($marcaTecnologica->contacto_email); ?>

                        </a>
                        <?php else: ?>
                        <span class="text-muted">-</span>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Estado</small>
                        <span class="badge <?php echo e($marcaTecnologica->activo ? 'bg-success' : 'bg-secondary'); ?>">
                            <?php echo e($marcaTecnologica->activo_label); ?>

                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Orden</small>
                        <strong><?php echo e($marcaTecnologica->orden); ?></strong>
                    </div>

                    <?php if($marcaTecnologica->logo_url): ?>
                    <div class="text-center mb-3">
                        <img src="<?php echo e($marcaTecnologica->logo_url); ?>" alt="<?php echo e($marcaTecnologica->nombre); ?>" class="img-fluid rounded" style="max-height: 150px;" onerror="this.style.display='none'">
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <small class="text-muted d-block">Productos Registrados</small>
                        <span class="badge bg-info fs-6"><?php echo e($marcaTecnologica->productos_count ?? 0); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-bold mb-0">Productos de esta Marca</h6>
                </div>
                <div class="card-body">
                    <?php if($marcaTecnologica->productos && $marcaTecnologica->productos->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Código</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $marcaTecnologica->productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo e(route('productos.show', $producto)); ?>" class="text-decoration-none">
                                            <?php echo e($producto->nombre); ?>

                                        </a>
                                    </td>
                                    <td><code><?php echo e($producto->codigo_barras ?? '-'); ?></code></td>
                                    <td>RD$ <?php echo e(number_format($producto->precio, 2)); ?></td>
                                    <td>
                                        <span class="badge <?php echo e($producto->stock > 10 ? 'bg-success' : ($producto->stock > 0 ? 'bg-warning' : 'bg-danger')); ?>">
                                            <?php echo e($producto->stock); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo e($producto->activo ? 'bg-success' : 'bg-secondary'); ?>">
                                            <?php echo e($producto->activo_label); ?>

                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox d-block fs-1 mb-2"></i>
                        No hay productos registrados para esta marca
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/marcas-tecnologicas/show.blade.php ENDPATH**/ ?>