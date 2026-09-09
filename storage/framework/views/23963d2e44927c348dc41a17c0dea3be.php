<?php $__env->startSection('title', $listaPrecio->nombre); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .margin-positive { color: #198754; font-weight: 600; }
    .margin-negative { color: #dc3545; font-weight: 600; }
    .margin-zero { color: #6c757d; font-weight: 600; }
    .precios-show-table thead th {
        background: rgba(241,245,249,.8);
        color: #64748b;
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        font-weight: 700;
        padding: .85rem 1rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .precios-show-table tbody td {
        padding: .85rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: .9rem;
    }
    .precios-show-table tbody tr:last-child td { border-bottom: none; }
    body.dark-mode .precios-show-table thead th {
        background: rgba(15,23,42,.5);
        color: #94a3b8;
        border-color: #1e293b;
    }
    body.dark-mode .precios-show-table tbody td {
        border-bottom-color: #1e293b;
        color: #cbd5e1;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">

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
                    <h4 class="ui-header-title"><?php echo e($listaPrecio->nombre); ?></h4>
                    <div class="ui-header-meta"><?php echo e($listaPrecio->codigo); ?> &middot; <?php echo e($listaPrecio->items->count()); ?> productos</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <div class="d-flex gap-2">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('listas-precio.edit')): ?>
                    <a href="<?php echo e(route('listas-precio.edit', $listaPrecio)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('listas-precio.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                        <i class="bi bi-arrow-left me-1"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php
        $itemsList = $listaPrecio->items;
        $precioPromedio = $itemsList->avg('precio');
        $precioMin = $itemsList->min('precio');
        $precioMax = $itemsList->max('precio');
    ?>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="ui-stat p-3" style="--delay:.05s">
                <div class="ui-card-accent"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(139,92,246,0.1);color:#8b5cf6;font-size:1.4rem;">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <div class="ui-stat-value" style="color:#8b5cf6;"><?php echo e($itemsList->count()); ?></div>
                        <div class="ui-stat-label">Total Productos</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="ui-stat p-3" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(13,202,240,0.1);color:#0dcaf0;font-size:1.4rem;">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div>
                        <div class="ui-stat-value text-info">RD$ <?php echo e(number_format($precioPromedio ?? 0, 0)); ?></div>
                        <div class="ui-stat-label">Precio Promedio</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="ui-stat p-3" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(25,135,84,0.1);color:#198754;font-size:1.4rem;">
                        <i class="bi bi-arrow-down-circle"></i>
                    </div>
                    <div>
                        <div class="ui-stat-value text-success">RD$ <?php echo e(number_format($precioMin ?? 0, 0)); ?></div>
                        <div class="ui-stat-label">Precio M&iacute;nimo</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="ui-stat p-3" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(220,53,69,0.1);color:#dc3545;font-size:1.4rem;">
                        <i class="bi bi-arrow-up-circle"></i>
                    </div>
                    <div>
                        <div class="ui-stat-value text-danger">RD$ <?php echo e(number_format($precioMax ?? 0, 0)); ?></div>
                        <div class="ui-stat-label">Precio M&aacute;ximo</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-box-seam me-2"></i>
                    Productos en esta lista
                </div>
                <div class="ui-card-subtitle"><?php echo e($itemsList->count()); ?> productos configurados</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 precios-show-table" id="tablaPrecios">
                        <thead class="table-light">
                            <tr class="text-muted text-uppercase small">
                                <th class="ps-4">C&oacute;digo</th>
                                <th>Producto</th>
                                <th class="text-end">Costo</th>
                                <th class="text-end">Margen %</th>
                                <th class="text-end">Precio Actual</th>
                                <th class="text-end pe-4">Precio Lista</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_0 = true; $__currentLoopData = $itemsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                            <?php
                                $producto = $item->producto;
                                $costo = $producto ? (float) ($producto->precio_compra ?? 0) : 0;
                                $precioLista = (float) $item->precio;
                                $margen = $costo > 0 ? ((($precioLista - $costo) / $costo) * 100) : 0;
                                $marginClass = $margen > 0 ? 'margin-positive' : ($margen < 0 ? 'margin-negative' : 'margin-zero');
                                $marginSign = $margen > 0 ? '+' : '';
                            ?>
                            <tr>
                                <td class="ps-4"><span class="badge bg-light text-muted rounded-pill"><?php echo e($producto?->codigo ?? $producto?->codigo_barras ?? '&mdash;'); ?></span></td>
                                <td class="fw-bold small"><?php echo e($producto?->nombre ?? 'Producto Eliminado'); ?></td>
                                <td class="text-end text-muted">RD$ <?php echo e(number_format($costo, 2)); ?></td>
                                <td class="text-end <?php echo e($marginClass); ?>">
                                    <?php if($costo > 0): ?>
                                        <?php echo e($marginSign); ?><?php echo e(number_format($margen, 1)); ?>%
                                    <?php else: ?>
                                        &mdash;
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-muted">RD$ <?php echo e(number_format($producto?->precio ?? 0, 2)); ?></td>
                                <td class="text-end pe-4 fw-bold text-success">
                                    RD$ <?php echo e(number_format($precioLista, 2)); ?>

                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                            <tr>
                                <td colspan="6">
                                    <div class="ui-empty-state py-4">
                                        <i class="bi bi-box-seam ui-empty-state-icon"></i>
                                        <p class="ui-empty-state-text">Esta lista a&uacute;n no tiene productos configurados.</p>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('listas-precio.edit')): ?>
                                        <a href="<?php echo e(route('listas-precio.edit', $listaPrecio)); ?>" class="ui-btn ui-btn-solid rounded-pill">Agregar productos</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-card mb-3" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-gear me-2"></i>
                    Informaci&oacute;n
                </div>
                <div class="card-body">
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Estado</span>
                        <span class="ui-detail-value">
                            <?php if($listaPrecio->activa): ?>
                                <span class="ui-badge-success rounded-pill">Activa</span>
                            <?php else: ?>
                                <span class="ui-badge-danger rounded-pill">Inactiva</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <?php if($listaPrecio->vigencia_desde): ?>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Vigencia</span>
                        <span class="ui-detail-value"><?php echo e($listaPrecio->vigencia_desde->format('d/m/Y')); ?> - <?php echo e($listaPrecio->vigencia_hasta?->format('d/m/Y') ?? 'Indefinida'); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($listaPrecio->descripcion): ?>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Descripci&oacute;n</span>
                        <span class="ui-detail-value"><?php echo e($listaPrecio->descripcion); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-lightning me-2"></i>
                    Herramientas
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('listas-precio.edit')): ?>
                        <a href="<?php echo e(route('listas-precio.impacto', $listaPrecio)); ?>" class="ui-btn ui-btn-ghost rounded-pill w-100">
                            <i class="bi bi-graph-up me-1"></i> Impacto de Precios
                        </a>
                        <a href="<?php echo e(route('listas-precio.logs', $listaPrecio)); ?>" class="ui-btn ui-btn-ghost rounded-pill w-100">
                            <i class="bi bi-clock-history me-1"></i> Historial de Cambios
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/listas-precio/show.blade.php ENDPATH**/ ?>