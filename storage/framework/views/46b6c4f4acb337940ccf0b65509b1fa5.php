<?php $__env->startSection('title', 'Detalle del Gasto'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-eye"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Detalle del Gasto</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-receipt me-1"></i>
                        <?php echo e($gasto->descripcion); ?>

                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('gastos.edit')): ?>
                <a href="<?php echo e(route('gastos.edit', $gasto)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" style="background:rgba(245,158,11,.2);border-color:rgba(245,158,11,.35);">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('gastos.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-info-circle"></i>
                    Información del Gasto
                </div>
                <div class="ui-card-subtitle">Datos completos del registro</div>
                <div class="ui-card-body">
                    <div class="ui-detail-row">
                        <div class="ui-detail-label">Descripción</div>
                        <div class="ui-detail-value fw-semibold"><?php echo e($gasto->descripcion); ?></div>
                    </div>
                    <div class="ui-detail-row">
                        <div class="ui-detail-label">Monto</div>
                        <div class="ui-detail-value fw-bold" style="color:#059669;font-size:1.2rem;">RD$ <?php echo e(number_format($gasto->monto, 2)); ?></div>
                    </div>
                    <div class="ui-detail-row">
                        <div class="ui-detail-label">Categoría</div>
                        <div class="ui-detail-value">
                            <?php if($gasto->categoria): ?>
                                <span class="ui-badge ui-badge-success"><?php echo e(\App\Models\Gasto::categorias()[$gasto->categoria] ?? $gasto->categoria); ?></span>
                            <?php else: ?>
                                <span class="text-muted">Sin categoría</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="ui-detail-row">
                        <div class="ui-detail-label">Método de Pago</div>
                        <div class="ui-detail-value"><?php echo e($gasto->metodo_pago ? ucfirst($gasto->metodo_pago) : '—'); ?></div>
                    </div>
                    <div class="ui-detail-row">
                        <div class="ui-detail-label">Proveedor</div>
                        <div class="ui-detail-value"><?php echo e($gasto->proveedor?->nombre ?? '—'); ?></div>
                    </div>
                    <div class="ui-detail-row">
                        <div class="ui-detail-label">N° Comprobante</div>
                        <div class="ui-detail-value">
                            <?php if($gasto->comprobante): ?>
                                <span class="ui-badge ui-badge-info"><?php echo e($gasto->comprobante); ?></span>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="ui-detail-row">
                        <div class="ui-detail-label">Fecha del Gasto</div>
                        <div class="ui-detail-value"><?php echo e($gasto->fecha_gasto->format('d/m/Y')); ?></div>
                    </div>
                    <div class="ui-detail-row">
                        <div class="ui-detail-label">Notas</div>
                        <div class="ui-detail-value"><?php echo e($gasto->notas ?: '—'); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-person-badge"></i>
                    Registrado por
                </div>
                <div class="ui-card-subtitle">Información del usuario que creó el gasto</div>
                <div class="ui-card-body text-center">
                    <div class="ui-user-avatar ui-user-avatar-amber mx-auto mb-3">
                        <i class="bi bi-person-circle fs-2" style="color:#d97706;"></i>
                    </div>
                    <h6 class="fw-bold mb-1"><?php echo e($gasto->user?->name ?? '—'); ?></h6>
                    <small class="text-muted"><?php echo e($gasto->created_at->format('d/m/Y h:i A')); ?></small>
                    <?php if($gasto->caja): ?>
                        <hr class="my-3">
                        <div class="text-start">
                            <small class="text-muted d-block">Caja: <span class="fw-semibold" style="color:#059669;"><?php echo e($gasto->caja->nombre); ?></span></small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/gastos/show.blade.php ENDPATH**/ ?>