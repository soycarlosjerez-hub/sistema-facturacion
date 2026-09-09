<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
                    <i class="bi bi-bag-check"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Órdenes</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i>
                        <span>Gestión de órdenes de mostrador, delivery y pickup</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('ordenes.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Orden
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Pendientes</div>
                    <div class="ui-stat-value"><?php echo e($totales['pendientes']); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">En Proceso</div>
                    <div class="ui-stat-value"><?php echo e($totales['en_proceso']); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Total Hoy</div>
                    <div class="ui-stat-value">RD$ <?php echo e(number_format($totales['hoy'], 2)); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.25s">
        <div class="ui-card-accent amber"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $ordenes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orden): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($orden->id); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($orden->tipo_orden === 'delivery' ? 'info' : ($orden->tipo_orden === 'pickup' ? 'warning' : 'secondary')); ?>">
                                    <?php echo e(ucfirst($orden->tipo_orden)); ?>

                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($orden->estado === 'pendiente' ? 'danger' : ($orden->estado === 'completada' ? 'success' : ($orden->estado === 'anulada' ? 'dark' : 'primary'))); ?>">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $orden->estado))); ?>

                                </span>
                            </td>
                            <td><?php echo e($orden->cliente?->nombre ?? '—'); ?></td>
                            <td>RD$ <?php echo e(number_format($orden->subtotal + $orden->impuestos, 2)); ?></td>
                            <td><?php echo e($orden->created_at->format('d/m/Y h:i A')); ?></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ordenes.view')): ?>
                                    <a href="<?php echo e(route('ordenes.show', $orden)); ?>" class="ui-action ui-action-view" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php endif; ?>

                                    <?php if(!in_array($orden->estado, ['completada', 'anulada'])): ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ordenes.update')): ?>
                                        <a href="<?php echo e(route('ordenes.show', $orden)); ?>" class="ui-action ui-action-edit" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php endif; ?>

                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ordenes.pay')): ?>
                                        <a href="<?php echo e(route('ordenes.show', $orden)); ?>" class="ui-action" style="background:rgba(34,197,94,.1);color:#16a34a;border-color:rgba(34,197,94,.2);" title="Cobrar">
                                            <i class="bi bi-cash-coin"></i>
                                        </a>
                                        <?php endif; ?>

                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ordenes.cancel')): ?>
                                        <form action="<?php echo e(route('ordenes.destroy', $orden)); ?>" method="POST" class="d-inline form-anular">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <input type="hidden" name="motivo" value="Anulada por usuario">
                                            <button type="button" class="ui-action ui-action-delete btn-trigger-anular" title="Anular">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ordenes.view')): ?>
                                    <a href="<?php echo e(route('ordenes.ticket', $orden)); ?>" class="ui-action ui-action-print" title="Imprimir ticket" target="_blank">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    <?php endif; ?>

                                    <?php if($orden->estado === 'anulada'): ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ordenes.cancel')): ?>
                                        <form action="<?php echo e(route('ordenes.forceDestroy', $orden)); ?>" method="POST" class="d-inline form-borrar">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="button" class="ui-action" style="background:rgba(100,116,139,.1);color:#64748b;border-color:rgba(100,116,139,.2);" title="Eliminar permanentemente">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php if($ordenes->hasPages()): ?>
            <div class="p-3 border-top border-light">
                <?php echo e($ordenes->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('click', function(e) {
    const btnAnular = e.target.closest('.btn-trigger-anular');
    if (btnAnular) {
        const form = btnAnular.closest('.form-anular');
        if (!form) return;
        Swal.fire({
            title: 'Anular Orden',
            text: '¿Estás seguro de anular esta orden?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, anular',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#ef4444',
        }).then(function(result) {
            if (result.isConfirmed) form.submit();
        });
        return;
    }

    const btnBorrar = e.target.closest('.btn-trigger-borrar');
    if (btnBorrar) {
        const form = btnBorrar.closest('.form-borrar');
        if (!form) return;
        Swal.fire({
            title: 'Eliminar Orden Permanentemente',
            html: '¿Estás seguro? Esta acción <strong>no se puede deshacer</strong> y eliminará la orden y todos sus registros asociados.',
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar permanentemente',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc2626',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise(resolve => {
                    Swal.fire({
                        title: 'Confirma la eliminación',
                        text: 'Escribe "ELIMINAR" para confirmar',
                        input: 'text',
                        inputPlaceholder: 'Escribe ELIMINAR',
                        showCancelButton: true,
                        confirmButtonText: 'Eliminar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#dc2626',
                        preConfirm: (input) => {
                            if (input !== 'ELIMINAR') {
                                Swal.showValidationMessage('Debes escribir ELIMINAR');
                                return false;
                            }
                            return true;
                        }
                    }).then(r => {
                        if (r.isConfirmed) resolve();
                    });
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then(function(result) {
            if (result.isConfirmed) form.submit();
        });
        return;
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/ordenes/index.blade.php ENDPATH**/ ?>