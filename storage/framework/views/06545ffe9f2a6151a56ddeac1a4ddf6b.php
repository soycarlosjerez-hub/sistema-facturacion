<?php $__env->startSection('title', 'Historial de Compras'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.compras-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(245,158,11,.04);
    margin: 0;
}
.compras-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.compras-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
.compras-table tbody tr:last-child td { border-bottom: none; }
.compras-table tbody tr { transition: background .15s; }
.compras-table tbody tr:hover { background: rgba(245,158,11,.03); }
.status-badge {
    padding: 0.4em 0.8em;
    border-radius: 2rem;
    font-weight: 500;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}
body.dark-mode .compras-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .compras-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Gestión de Compras</h4>
                    <div class="ui-header-meta">
                        Administra tus compras, proveedores y retenciones
                        <span class="mx-2">·</span>
                        <?php echo e($compras->total()); ?> registro(s)
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('compras.create')): ?>
                <a href="<?php echo e(route('compras.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Registrar Compra
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-3">
            <form method="GET" id="filtros-form" action="<?php echo e(route('compras.index')); ?>" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="proveedor" id="busqueda-proveedor" class="ui-input" placeholder="Proveedor o RNC..." value="<?php echo e(request('proveedor')); ?>" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <input type="date" name="desde" class="ui-input" value="<?php echo e(request('desde')); ?>">
                </div>
                <div class="col-lg-2">
                    <input type="date" name="hasta" class="ui-input" value="<?php echo e(request('hasta')); ?>">
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="<?php echo e(route('compras.index')); ?>" class="ui-btn ui-btn-ghost"><i class="bi bi-x-lg"></i></a>
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <a href="<?php echo e(route('compras.exportar', request()->all())); ?>" class="ui-btn ui-btn-ghost flex-grow-1">
                        <i class="bi bi-file-excel me-1"></i> Excel
                    </a>
                    <a href="<?php echo e(route('compras.pdf', request()->all())); ?>" class="ui-btn ui-btn-ghost flex-grow-1">
                        <i class="bi bi-file-pdf me-1"></i> PDF
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table compras-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Comprobante</th>
                            <th>Proveedor</th>
                            <th>Fecha &amp; Hora</th>
                            <th>Detalles</th>
                            <th class="text-end">Montos</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="compras-tbody">
                        <?php $__empty_0 = true; $__currentLoopData = $compras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark fs-6"><?php echo e($c->folio); ?></span>
                            <span class="ui-badge-neutral" style="font-size:.7rem;">
                                <?php echo e($c->tipoCompra?->nombre ?? 'N/A'); ?>

                            </span>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                        $firstLetter = strtoupper(substr($c->proveedor->nombre ?? 'D', 0, 1));
                                        $colors = ['#f87171', '#60a5fa', '#34d399', '#fbbf24', '#a78bfa', '#f472b6'];
                                        $color = $colors[crc32($c->proveedor->nombre ?? '') % count($colors)];
                                    ?>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle text-white me-3 shadow-sm" style="background-color: <?php echo e($color); ?>; width:40px;height:40px;font-size:1.1rem;">
                                            <?php echo e($firstLetter); ?>

                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-6"><?php echo e($c->proveedor->nombre ?? 'Desconocido'); ?></div>
                                            <div class="text-muted small">RNC: <?php echo e($c->proveedor->rnc ?? $c->proveedor->rnc_cedula ?? '—'); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark"><?php echo e($c->fecha ? $c->fecha->format('d/m/Y') : $c->created_at->format('d/m/Y')); ?></div>
                                    <div class="text-muted small"><i class="bi bi-clock me-1"></i><?php echo e($c->created_at->format('h:i A')); ?></div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <?php if($c->almacen): ?>
                                            <span class="ui-badge-info">
                                                <i class="bi bi-building me-1"></i><?php echo e($c->almacen->nombre); ?>

                                            </span>
                                        <?php endif; ?>
                                        <?php if($c->aplica_retencion_isr || $c->aplica_retencion_itbis): ?>
                                            <span class="ui-badge-warning">
                                                <i class="bi bi-shield-exclamation me-1"></i>Retenciones
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="text-muted small mb-1">Sub: RD$ <?php echo e(number_format($c->subtotal ?? 0, 2)); ?></div>
                                    <div class="fw-bold text-primary fs-5">RD$ <?php echo e(number_format($c->total, 2)); ?></div>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="ui-action ui-action-edit" type="button" data-bs-toggle="collapse" data-bs-target="#details-<?php echo e($c->id); ?>" title="Ver productos">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                    <a href="<?php echo e(route('compras.show', $c)); ?>" class="ui-action ui-action-edit" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('compras.edit', $c)); ?>" class="ui-action ui-action-edit" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?php echo e(route('compras.destroy', $c)); ?>" method="POST" class="d-inline" onsubmit="return UI.confirm.delete('¿Eliminar la compra <?php echo e($c->folio); ?>? Se revertirá el stock.')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="ui-action ui-action-delete" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="collapse" id="details-<?php echo e($c->id); ?>">
                                <td colspan="6" class="p-4 border-0" style="background:rgba(241,245,249,.5);">
                                    <div class="rounded p-3 bg-white border">
                                        <h6 class="text-muted fw-bold mb-3 small text-uppercase"><i class="bi bi-box-seam me-2"></i>Productos Adquiridos (<?php echo e($c->detalles->count()); ?>)</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-borderless mb-0">
                                                <thead class="border-bottom border-light">
                                                    <tr class="text-muted small">
                                                        <th>Producto</th>
                                                        <th class="text-center">Cant.</th>
                                                        <th class="text-end">Precio</th>
                                                        <th class="text-end">ITBIS</th>
                                                        <th class="text-end">Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $c->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td class="fw-medium text-dark"><?php echo e($d->producto->nombre ?? '—'); ?></td>
                                                        <td class="text-center"><?php echo e($d->cantidad); ?></td>
                                                        <td class="text-end text-muted">RD$ <?php echo e(number_format($d->precio_unitario, 2)); ?></td>
                                                        <td class="text-end text-muted"><?php echo e(number_format($d->itbis_porcentaje ?? $systemItbis ?? 18, 2)); ?>%</td>
                                                        <td class="text-end fw-bold text-dark">RD$ <?php echo e(number_format($d->subtotal, 2)); ?></td>
                                                    </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-cart-x fs-1" style="color:#cbd5e1;"></i>
                                    <p class="mt-2 mb-0 fw-semibold">No hay compras registradas</p>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('compras.create')): ?>
                                    <a href="<?php echo e(route('compras.create')); ?>" class="ui-btn ui-btn-solid rounded-pill mt-2">Registrar primera compra</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if($compras->hasPages()): ?>
    <div class="d-flex justify-content-center mt-3">
        <?php echo e($compras->withQueryString()->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('busqueda-proveedor');
    const tbody = document.getElementById('compras-tbody');
    const form = document.getElementById('filtros-form');
    let timeout = null;

    if (!searchInput) return;

    searchInput.addEventListener('input', function () {
        clearTimeout(timeout);
        const termino = this.value;
        timeout = setTimeout(() => {
            const url = new URL(form.action);
            const fd = new FormData(form);
            fd.set('proveedor', termino);
            url.search = new URLSearchParams(fd).toString();

            tbody.style.opacity = '0.5';
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newTbody = doc.getElementById('compras-tbody');
                    if (newTbody && tbody) {
                        tbody.innerHTML = newTbody.innerHTML;
                        tbody.style.opacity = '1';
                    }
                })
                .catch(() => { tbody.style.opacity = '1'; });
        }, 400);
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/compras/index.blade.php ENDPATH**/ ?>