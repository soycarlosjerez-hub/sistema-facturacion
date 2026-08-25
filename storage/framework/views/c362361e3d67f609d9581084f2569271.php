<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.ui-page { --accent: #0ea5e9; --accent-rgb: 14,165,233; --accent-hover: #0284c7; }
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
                    <i class="bi bi-speedometer2"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">¡Hola, <?php echo e(Auth::user()->name); ?>!</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-calendar3 me-1"></i><?php echo e(now()->translatedFormat('l, d \d\e F \d\e Y')); ?>

                        <span class="mx-2">·</span>
                        <i class="bi bi-clock me-1"></i><span id="live-clock" class="fw-medium"><?php echo e(now()->format('h:i A')); ?></span>
                        <span class="mx-2">·</span>
                        <a href="<?php echo e(route('dashboard.pdf')); ?>" class="text-white-50 text-decoration-none small">
                            <i class="bi bi-download"></i> PDF
                        </a>
                        <a href="<?php echo e(route('dashboard.exportar')); ?>" class="text-white-50 text-decoration-none small ms-2">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </a>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if($cajaActual['abierta']): ?>
                    <div class="d-flex align-items-center gap-2 me-2">
                        <span class="badge bg-success bg-opacity-25 text-white rounded-pill px-3 py-1">
                            <i class="bi bi-circle-fill text-success me-1" style="font-size:.5rem;"></i>
                            <?php echo e($cajaActual['caja'] ?? 'Caja'); ?>

                        </span>
                    </div>
                <?php else: ?>
                    <a href="<?php echo e(route('cajas.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                        <i class="bi bi-cash-coin me-1"></i> Abrir caja
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="mt-3">
            <form method="GET" action="<?php echo e(route('dashboard')); ?>" class="d-flex gap-2 align-items-center flex-wrap">
                <div class="d-flex align-items-center gap-1 bg-white bg-opacity-10 rounded-pill px-3 py-1">
                    <i class="bi bi-calendar-event text-white-50 small"></i>
                    <input type="date" name="desde" value="<?php echo e(request('desde')); ?>" class="border-0 bg-transparent text-white small" style="outline:none;width:130px;">
                </div>
                <span class="text-white-50 small">—</span>
                <div class="d-flex align-items-center gap-1 bg-white bg-opacity-10 rounded-pill px-3 py-1">
                    <i class="bi bi-calendar-event text-white-50 small"></i>
                    <input type="date" name="hasta" value="<?php echo e(request('hasta')); ?>" class="border-0 bg-transparent text-white small" style="outline:none;width:130px;">
                </div>
                <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-3">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                <?php if(request('desde') || request('hasta')): ?>
                    <a href="<?php echo e(route('dashboard')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-3 text-white border-white border-opacity-25">
                        <i class="bi bi-x-lg"></i> Limpiar
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="ui-card" style="--delay:0s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <span class="ui-badge ui-badge-primary rounded-pill px-3 py-2">
                                <i class="bi bi-lightning-charge-fill me-1"></i> Acceso rápido
                            </span>
                        </div>
                        <div class="col d-flex gap-2 flex-wrap">
                            <a href="<?php echo e(route('ventas.create')); ?>" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                                <i class="bi bi-cart-plus"></i> Nueva Venta
                            </a>
                            <a href="<?php echo e(route('compras.create')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                                <i class="bi bi-bag-plus"></i> Nueva Compra
                            </a>
                            <a href="<?php echo e(route('productos.create')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                                <i class="bi bi-box-seam"></i> Nuevo Producto
                            </a>
                            <a href="<?php echo e(route('clientes.cuentas')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                                <i class="bi bi-cash-coin"></i> Cobrar Deudas
                            </a>
                            <a href="<?php echo e(route('dashboard.pdf')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                                <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
                            </a>
                            <a href="<?php echo e(route('dashboard.exportar')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                                <i class="bi bi-file-earmark-excel"></i> Exportar Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <?php echo $__env->make('dashboard._kpis', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('dashboard._content', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('dashboard._activity', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<?php echo $__env->make('dashboard._scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/dashboard.blade.php ENDPATH**/ ?>