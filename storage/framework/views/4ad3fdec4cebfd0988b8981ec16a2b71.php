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
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Orden #<?php echo e($orden->id); ?>

                        <span class="badge bg-<?php echo e($orden->estado === 'pendiente' ? 'danger' : ($orden->estado === 'completada' ? 'success' : ($orden->estado === 'anulada' ? 'dark' : 'primary'))); ?> fs-6 ms-2">
                            <?php echo e(ucfirst(str_replace('_', ' ', $orden->estado))); ?>

                        </span>
                        <span class="badge bg-<?php echo e($orden->tipo_orden === 'delivery' ? 'info' : ($orden->tipo_orden === 'pickup' ? 'warning' : 'secondary')); ?> fs-6 ms-1">
                            <?php echo e(ucfirst($orden->tipo_orden)); ?>

                        </span>
                    </h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-clock me-1"></i>
                        <span><?php echo e($orden->created_at->format('d/m/Y h:i A')); ?></span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if(!in_array($orden->estado, ['completada', 'anulada'])): ?>
                <form action="<?php echo e(route('ordenes.destroy', $orden)); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <input type="hidden" name="motivo" value="Anulada por usuario">
                    <button type="button" class="ui-btn ui-btn-danger ui-btn-sm rounded-pill" onclick="event.preventDefault();UI.confirm.delete('<?php echo e(route('ordenes.destroy', $orden)); ?>', '<?php echo e(addslashes('Orden #'.$orden->id)); ?>')">Anular</button>
                </form>
                <?php endif; ?>
                <?php if($orden->estado === 'anulada'): ?>
                <form action="<?php echo e(route('ordenes.forceDestroy', $orden)); ?>" method="POST" class="d-inline form-borrar-show">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="button" class="ui-btn ui-btn-danger ui-btn-sm rounded-pill btn-trigger-borrar-show">Eliminar</button>
                </form>
                <?php endif; ?>
                <a href="<?php echo e(route('ordenes.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva
                </a>
                <a href="<?php echo e(route('ordenes.index')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="ui-card mb-3" style="--delay:.1s">
                <div class="ui-card-accent amber"></div>
                <div class="ui-card-title d-flex justify-content-between">
                    <span><i class="bi bi-box-seam me-2"></i>Productos</span>
                    <?php if(!in_array($orden->estado, ['completada', 'anulada'])): ?>
                    <button type="button" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#addProductModal">+ Agregar</button>
                    <?php endif; ?>
                </div>
                <div class="ui-card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cant</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                    <th>Curso</th>
                                    <?php if(!in_array($orden->estado, ['completada', 'anulada'])): ?>
                                    <th></th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $orden->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($detalle->producto?->nombre ?? '—'); ?>

                                        <?php if($detalle->notas): ?><br><small class="text-muted"><?php echo e($detalle->notas); ?></small><?php endif; ?>
                                    </td>
                                    <td><?php echo e($detalle->cantidad); ?></td>
                                    <td>RD$ <?php echo e(number_format($detalle->precio_unitario, 2)); ?></td>
                                    <td>RD$ <?php echo e(number_format($detalle->subtotal, 2)); ?></td>
                                    <td><span class="badge bg-info"><?php echo e($detalle->curso); ?></span></td>
                                    <?php if(!in_array($orden->estado, ['completada', 'anulada'])): ?>
                                    <td>
                                        <form action="<?php echo e(route('ordenes.quitarItem', [$orden, $detalle->id])); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar producto?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button class="btn btn-sm btn-danger">×</button>
                                        </form>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Subtotal</th>
                                    <th>RD$ <?php echo e(number_format($orden->subtotal, 2)); ?></th>
                                    <th colspan="<?php echo e(!in_array($orden->estado, ['completada', 'anulada']) ? 2 : 1); ?>"></th>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">Impuestos</td>
                                    <td>RD$ <?php echo e(number_format($orden->impuestos, 2)); ?></td>
                                    <td colspan="<?php echo e(!in_array($orden->estado, ['completada', 'anulada']) ? 2 : 1); ?>"></td>
                                </tr>
                                <?php if($orden->descuento > 0): ?>
                                <tr>
                                    <td colspan="3" class="text-end">Descuento (<?php echo e($orden->descuento_tipo); ?>)</td>
                                    <td class="text-danger">-RD$ <?php echo e(number_format($orden->descuento, 2)); ?></td>
                                    <td colspan="<?php echo e(!in_array($orden->estado, ['completada', 'anulada']) ? 2 : 1); ?>"></td>
                                </tr>
                                <?php endif; ?>
                                <tr class="table-active">
                                    <th colspan="3" class="text-end">Total</th>
                                    <th>RD$ <?php echo e(number_format($orden->subtotal + $orden->impuestos - $orden->descuento, 2)); ?></th>
                                    <th colspan="<?php echo e(!in_array($orden->estado, ['completada', 'anulada']) ? 2 : 1); ?>"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <?php if($orden->estado === 'completada' && $orden->pagos->count() > 0): ?>
            <div class="ui-card mb-3" style="--delay:.15s">
                <div class="ui-card-accent amber"></div>
                <div class="ui-card-title"><i class="bi bi-credit-card me-2"></i>Pagos</div>
                <div class="ui-card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr><th>Método</th><th>Monto</th><th>Fecha</th></tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $orden->pagos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pago): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e(ucfirst($pago->metodo_pago)); ?></td>
                                <td>RD$ <?php echo e(number_format($pago->monto, 2)); ?></td>
                                <td><?php echo e($pago->fecha_pago->format('h:i A d/m/Y')); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-5">
            <?php if($orden->cliente): ?>
            <div class="ui-card mb-3" style="--delay:.2s">
                <div class="ui-card-accent amber"></div>
                <div class="ui-card-title"><i class="bi bi-person me-2"></i>Cliente</div>
                <div class="ui-card-body">
                    <p><strong><?php echo e($orden->cliente->nombre); ?></strong></p>
                    <?php if($orden->cliente->telefono): ?><p>Tel: <?php echo e($orden->cliente->telefono); ?></p><?php endif; ?>
                    <?php if($orden->cliente->email): ?><p>Email: <?php echo e($orden->cliente->email); ?></p><?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if($orden->tipo_orden === 'delivery'): ?>
            <div class="ui-card mb-3" style="--delay:.25s">
                <div class="ui-card-accent amber"></div>
                <div class="ui-card-title"><i class="bi bi-truck me-2"></i>Entrega</div>
                <div class="ui-card-body">
                    <p><strong>Dirección:</strong> <?php echo e($orden->direccion_entrega ?? '—'); ?></p>
                    <p><strong>Empresa:</strong> <?php echo e($orden->entregaEmpresa?->nombre ?? '—'); ?></p>
                    <p><strong>Contacto:</strong> <?php echo e($orden->telefono_contacto ?? '—'); ?></p>
                </div>
            </div>
            <?php elseif($orden->tipo_orden === 'pickup'): ?>
            <div class="ui-card mb-3" style="--delay:.25s">
                <div class="ui-card-accent amber"></div>
                <div class="ui-card-title"><i class="bi bi-shop me-2"></i>Retiro</div>
                <div class="ui-card-body">
                    <p><strong>Hora de retiro:</strong> <?php echo e($orden->hora_retiro?->format('h:i A d/m/Y') ?? '—'); ?></p>
                    <p><strong>Contacto:</strong> <?php echo e($orden->telefono_contacto ?? '—'); ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if($orden->notas): ?>
            <div class="ui-card mb-3" style="--delay:.3s">
                <div class="ui-card-accent amber"></div>
                <div class="ui-card-title"><i class="bi bi-sticky me-2"></i>Notas</div>
                <div class="ui-card-body"><?php echo e($orden->notas); ?></div>
            </div>
            <?php endif; ?>

            <?php if(!in_array($orden->estado, ['completada', 'anulada'])): ?>
                <div class="ui-card mb-3" style="--delay:.35s">
                    <div class="ui-card-accent amber"></div>
                    <div class="ui-card-title"><i class="bi bi-cash-coin me-2"></i>Cobrar</div>
                    <div class="ui-card-body">
                        <form action="<?php echo e(route('ordenes.cobrar', $orden)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="mb-3">
                                <label class="ui-label">Método de Pago</label>
                                <select name="metodo_pago" class="ui-select" required id="metodo_pago">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="mixto">Mixto</option>
                                </select>
                            </div>
                            <div id="payment_efectivo">
                                <div class="mb-3">
                                    <label class="ui-label">Monto Recibido</label>
                                    <input type="number" step="0.01" name="monto_recibido" class="ui-input" placeholder="0.00">
                                </div>
                            </div>
                            <div id="payment_tarjeta" style="display:none;">
                                <div class="mb-3">
                                    <label class="ui-label">Monto Tarjeta</label>
                                    <input type="number" step="0.01" name="monto_tarjeta" class="ui-input" placeholder="0.00">
                                </div>
                            </div>
                            <div id="payment_transferencia" style="display:none;">
                                <div class="mb-3">
                                    <label class="ui-label">Monto Transferencia</label>
                                    <input type="number" step="0.01" name="monto_transferencia" class="ui-input" placeholder="0.00">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="ui-label">Propina</label>
                                <input type="number" step="0.01" name="propina" class="ui-input" value="0">
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="cargo_servicio" class="form-check-input" value="1" id="cargo_servicio">
                                <label class="form-check-label" for="cargo_servicio">Cargo por Servicio (10%)</label>
                            </div>
                            <button type="submit" class="ui-btn ui-btn-solid w-100" style="padding:.75rem 1.5rem;font-size:1.1rem;">
                                <i class="bi bi-cash-coin me-2"></i> Cobrar — RD$ <?php echo e(number_format($orden->subtotal + $orden->impuestos - $orden->descuento, 2)); ?>

                            </button>
                        </form>
                    </div>
                </div>

                <div class="ui-card mb-3" style="--delay:.4s">
                    <div class="ui-card-accent amber"></div>
                    <div class="ui-card-title"><i class="bi bi-arrow-left-right me-2"></i>Cambiar Estado</div>
                    <div class="ui-card-body">
                        <form action="<?php echo e(route('ordenes.cambiarEstado', $orden)); ?>" method="POST" class="row g-2">
                            <?php echo csrf_field(); ?>
                            <div class="col-8">
                                <select name="estado" class="ui-select">
                                    <option value="confirmada">Confirmada</option>
                                    <option value="en_proceso">En Proceso</option>
                                    <option value="lista">Lista</option>
                                    <?php if($orden->tipo_orden === 'delivery'): ?>
                                    <option value="en_camino">En Camino</option>
                                    <option value="entregado">Entregado</option>
                                    <?php elseif($orden->tipo_orden === 'pickup'): ?>
                                    <option value="recogida">Recogida</option>
                                    <?php endif; ?>
                                    <option value="completada">Completada</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <button type="submit" class="ui-btn ui-btn-solid w-100">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <div class="d-grid gap-2">
                <a href="<?php echo e(route('ordenes.ticket', $orden)); ?>" class="ui-btn ui-btn-ghost" target="_blank"><i class="bi bi-printer me-2"></i>Ver Ticket</a>
            </div>
        </div>
    </div>
</div>

<?php if(!in_array($orden->estado, ['completada', 'anulada'])): ?>
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo e(route('ordenes.agregarItem', $orden)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="ui-label">Producto</label>
                        <select name="producto_id" class="ui-select" required id="modal_producto_select">
                            <option value="">Buscar y seleccionar...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="ui-label">Cantidad</label>
                        <input type="number" name="cantidad" class="ui-input" value="1" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="ui-label">Curso</label>
                        <select name="curso" class="ui-select">
                            <option value="entrada">Entrada</option>
                            <option value="fuerte" selected>Fuerte</option>
                            <option value="postre">Postre</option>
                            <option value="bebida">Bebida</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="ui-label">Notas</label>
                        <input type="text" name="notas" class="ui-input" maxlength="200">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="ui-btn ui-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="ui-btn ui-btn-solid">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('metodo_pago')?.addEventListener('change', function() {
    const v = this.value;
    document.getElementById('payment_efectivo').style.display = (v === 'efectivo' || v === 'mixto') ? 'block' : 'none';
    document.getElementById('payment_tarjeta').style.display = (v === 'tarjeta' || v === 'mixto') ? 'block' : 'none';
    document.getElementById('payment_transferencia').style.display = (v === 'transferencia' || v === 'mixto') ? 'block' : 'none';
});

// Select2-like product search in modal
let modalProductSelect = document.getElementById('modal_producto_select');
if (modalProductSelect) {
    modalProductSelect.addEventListener('focus', function() {
        if (this.options.length <= 1) {
            fetch('<?php echo e(route("ordenes.buscarProducto")); ?>?q=')
                .then(r => r.json())
                .then(data => {
                    data.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = `${p.nombre} - RD$ ${p.precio}`;
                        modalProductSelect.appendChild(opt);
                    });
                });
        }
    });
}

document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-trigger-borrar-show');
    if (!btn) return;
    const form = btn.closest('.form-borrar-show');
    if (!form) return;
    Swal.fire({
        title: 'Eliminar Orden Permanentemente',
        html: 'Esta acción <strong>no se puede deshacer</strong>. Se eliminará la orden y todos sus registros asociados.',
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626',
        preConfirm: () => {
            return Swal.fire({
                title: 'Confirma escribiendo ELIMINAR',
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
                }
            }).then(r => r.isConfirmed ? Promise.resolve() : Promise.reject());
        }
    }).then(r => { if (r.isConfirmed) form.submit(); });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/ordenes/show.blade.php ENDPATH**/ ?>