<?php $__env->startSection('title', 'Inventario por Almacén'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
                    <h4 class="ui-header-title">Inventario por Almacén</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-box-seam me-1"></i>
                        <span>Consulta el stock y valor del inventario en cada almacén</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions d-flex gap-3">
                <?php
                    $totalProductos = 0;
                    $totalValorGeneral = 0;
                    foreach ($almacenes as $alm) {
                        if ($almacenId && $almacenId != $alm->id) continue;
                        foreach ($stocks->get($alm->id, collect()) as $it) {
                            if ((int)$it->stock <= 0) continue;
                            $p = $productos->firstWhere('id', $it->producto_id);
                            if (!$p) continue;
                            $totalProductos++;
                            $totalValorGeneral += $it->stock * ($p->precio_compra ?? 0);
                        }
                    }
                ?>
                <div class="text-end">
                    <small class="opacity-75 d-block">Productos</small>
                    <span class="fw-bold fs-5"><?php echo e($totalProductos); ?></span>
                </div>
                <div class="text-end">
                    <small class="opacity-75 d-block">Valor Total</small>
                    <span class="fw-bold fs-5">RD$ <?php echo e(number_format($totalValorGeneral, 2)); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-lg-4">
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" id="buscar-instant" class="ui-input" placeholder="Buscar producto por nombre o código..." value="<?php echo e($buscar); ?>" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-3">
                    <select name="almacen_id" class="ui-select" onchange="this.form.submit()">
                        <option value="">Todos los almacenes</option>
                        <?php $__currentLoopData = $almacenes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($a->id); ?>" <?php echo e($almacenId == $a->id ? 'selected' : ''); ?>><?php echo e($a->nombre); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-2">
                    <button class="ui-btn ui-btn-solid rounded-pill w-100"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                </div>
                <div class="col-lg-2">
                    <a href="<?php echo e(route('almacenes.inventario')); ?>" class="ui-btn ui-btn-ghost rounded-pill w-100">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <?php $__currentLoopData = $almacenes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $almacen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($almacenId && $almacenId != $almacen->id): ?> <?php continue; ?> <?php endif; ?>
        <?php
            $items = $stocks->get($almacen->id, collect());
            $totalValor = 0;
            $totalUnidades = 0;
        ?>
        <div class="ui-card mb-4 overflow-hidden" style="--delay:.2s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-building me-2 text-primary"></i><?php echo e($almacen->nombre); ?>

                        </h5>
                        <?php if($almacen->sucursal): ?>
                            <small class="text-muted">
                                <i class="bi bi-geo-alt me-1"></i><?php echo e($almacen->sucursal->nombre); ?>

                                <?php if($almacen->ubicacion): ?> · <?php echo e($almacen->ubicacion); ?> <?php endif; ?>
                            </small>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-3 mt-2 mt-sm-0">
                        <span class="badge rounded-pill fs-6 px-3 py-2" style="background:rgba(var(--accent-rgb),0.1);color:var(--accent);">
                            <i class="bi bi-box me-1"></i><?php echo e($items->count()); ?> productos
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="ui-table mb-0">
                        <thead class="small text-uppercase text-muted">
                            <tr>
                                <th class="py-3 ps-4">Código</th>
                                <th class="py-3">Producto</th>
                                <th class="py-3 text-end">Stock</th>
                                <th class="py-3 text-end d-none d-md-table-cell">Costo Prom.</th>
                                <th class="py-3 text-end pe-4">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $rowCount = 0; ?>
                            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    if ((int)$item->stock <= 0) continue;
                                    $producto = $productos->firstWhere('id', $item->producto_id);
                                    if (!$producto) continue;
                                    $rowCount++;
                                    $totalValor += $item->stock * ($producto->precio_compra ?? 0);
                                    $totalUnidades += $item->stock;
                                    $stock = (int)$item->stock;
                                    if ($stock <= 5) $badgeClass = 'bg-danger';
                                    elseif ($stock <= 20) $badgeClass = 'bg-warning text-dark';
                                    else $badgeClass = 'bg-success';
                                    $pct = min($stock, 100) / 100;
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <code class="text-muted small"><?php echo e($producto->codigo_barras ?? '—'); ?></code>
                                    </td>
                                    <td>
                                        <span class="fw-semibold"><?php echo e($producto->nombre); ?></span>
                                        <?php if($producto->categoria): ?>
                                            <br><small class="text-muted"><?php echo e($producto->categoria->nombre); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge <?php echo e($badgeClass); ?> rounded-pill px-3 py-1 fs-6"><?php echo e($stock); ?></span>
                                    </td>
                                    <td class="text-end text-muted d-none d-md-table-cell">
                                        <small>RD$ <?php echo e(number_format($producto->precio_compra ?? 0, 2)); ?></small>
                                    </td>
                                    <td class="text-end pe-4 fw-semibold">
                                        RD$ <?php echo e(number_format($stock * ($producto->precio_compra ?? 0), 2)); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($rowCount === 0): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 text-muted opacity-50"></i>
                                        Sin productos en este almacén
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if($rowCount > 0): ?>
                        <tfoot class="border-top" style="background:rgba(var(--accent-rgb),0.05);">
                            <tr>
                                <td colspan="2" class="ps-4 py-3 fw-bold">Totales</td>
                                <td class="text-end py-3">
                                    <span class="fw-bold"><?php echo e($totalUnidades); ?> unidades</span>
                                </td>
                                <td class="text-end d-none d-md-table-cell py-3"></td>
                                <td class="text-end pe-4 py-3 fw-bold fs-6" style="color:var(--accent);">
                                    RD$ <?php echo e(number_format($totalValor, 2)); ?>

                                </td>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('buscar-instant');
    if (!input) return;
    input.addEventListener('keydown', e => { if (e.key === 'Enter') e.preventDefault(); });
    function filtrar() {
        const q = input.value.toLowerCase();
        document.querySelectorAll('.table tbody tr').forEach(tr => {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }
    input.addEventListener('input', filtrar);
    filtrar();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/almacenes/inventario.blade.php ENDPATH**/ ?>