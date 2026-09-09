<?php $__env->startSection('title', 'Conduce ' . $conduce->numero); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
body.dark-mode .modal-content { background: #1e293b; color: #e2e8f0; }
body.dark-mode .modal-header { border-color: #334155; }
body.dark-mode .modal-footer { border-color: #334155; }
body.dark-mode .card-footer { background: rgba(15,23,42,.8); border-color: #334155; }
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
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">
                        <?php echo e($conduce->numero); ?>

                        <span class="badge bg-<?php echo e($conduce->estado_color); ?> ms-2 align-middle">
                            <i class="bi bi-<?php echo e($conduce->estado_icon); ?> me-1" aria-hidden="true"></i>
                            <?php echo e($conduce->estado_label); ?>

                        </span>
                        <?php if($conduce->esta_vencido): ?>
                            <span class="badge bg-danger ms-1">
                                <i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i>Vencido
                            </span>
                        <?php endif; ?>
                    </h4>
                    <div class="ui-header-meta">Creado <?php echo e($conduce->created_at->diffForHumans()); ?></div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('conduces.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div class="btn-group">
                    <a href="<?php echo e(route('conduces.ticket', [$conduce, 'paper' => 80])); ?>" target="_blank"
                       class="ui-btn ui-btn-primary ui-btn-sm" aria-label="Imprimir ticket 80mm">
                        <i class="bi bi-printer me-1"></i>80mm
                    </a>
                    <a href="<?php echo e(route('conduces.ticket', [$conduce, 'paper' => 58])); ?>" target="_blank"
                       class="ui-btn ui-btn-primary ui-btn-sm" aria-label="Imprimir ticket 58mm">
                        <i class="bi bi-printer me-1"></i>58mm
                    </a>
                </div>
                <a href="<?php echo e(route('conduces.pdf', $conduce)); ?>" class="ui-btn ui-btn-danger ui-btn-sm"
                   aria-label="Descargar PDF">
                    <i class="bi bi-file-pdf me-1"></i>PDF
                </a>
                <?php if(in_array($conduce->estado, ['borrador', 'en_transito'])): ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('conduces.edit')): ?>
                    <a href="<?php echo e(route('conduces.edit', $conduce)); ?>" class="ui-action ui-action-edit">
                        <i class="bi bi-pencil me-1"></i>Editar
                    </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0" role="alert" style="border-left: 4px solid #22c55e !important;">
            <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0" role="alert" style="border-left: 4px solid #dc3545 !important;">
            <i class="bi bi-exclamation-triangle me-2"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <div class="row g-3">
        
        <div class="col-lg-8">
            
            <div class="ui-card mb-3" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="ui-card-title">
                        <i class="bi bi-box-seam"></i>Productos a Entregar
                        <span class="badge bg-secondary ms-2"><?php echo e($conduce->items->count()); ?> items</span>
                    </h6>
                    <div class="table-responsive">
                        <table class="ui-table table-hover align-middle mb-0" role="table">
                            <thead>
                                <tr>
                                    <th style="width:60px">#</th>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <?php if($conduce->estado === 'entregado'): ?>
                                    <th class="text-center">Recibido</th>
                                    <th class="text-center">%</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $conduce->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="text-muted"><?php echo e(str_pad($idx + 1, 3, '0', STR_PAD_LEFT)); ?></td>
                                    <td>
                                        <div class="fw-semibold"><?php echo e($item->nombre); ?></div>
                                        <small class="text-muted"><?php echo e($item->codigo ?? 'Sin código'); ?></small>
                                    </td>
                                    <td class="text-center">
                                        <strong><?php echo e(number_format($item->cantidad, 2)); ?></strong>
                                        <small class="text-muted d-block"><?php echo e($item->unidad); ?></small>
                                    </td>
                                    <?php if($conduce->estado === 'entregado'): ?>
                                    <td class="text-center">
                                        <?php echo e(number_format($item->cantidad_recibida ?? $item->cantidad, 2)); ?>

                                    </td>
                                    <td class="text-center">
                                        <?php if($item->entregado_completo): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check"></i> 100%
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">
                                                <?php echo e(number_format($item->porcentaje_entregado, 0)); ?>%
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <?php if($conduce->observaciones): ?>
            <div class="premium-card mb-3">
                <div class="card-accent purple"></div>
                <div class="card-body">
                    <h6 class="premium-card-title"><i class="bi bi-chat-left-text icon-purple"></i>Observaciones</h6>
                    <p class="mb-0"><?php echo e($conduce->observaciones); ?></p>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if(!in_array($conduce->estado, ['entregado', 'cancelado'])): ?>
            <div class="premium-card">
                <div class="card-accent purple"></div>
                <div class="card-body">
                    <h6 class="premium-card-title"><i class="bi bi-arrow-left-right icon-purple"></i>Cambiar Estado</h6>
                    <form method="POST" action="<?php echo e(route('conduces.cambiarEstado', $conduce)); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php $__currentLoopData = \App\Models\Conduce::ESTADOS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $estado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($key !== $conduce->estado && !in_array($key, ['entregado'])): ?>
                                <button type="submit" name="estado" value="<?php echo e($key); ?>"
                                        class="btn btn-outline-<?php echo e($estado['color']); ?>">
                                    <i class="bi bi-<?php echo e($estado['icon']); ?> me-1" aria-hidden="true"></i>
                                    <?php echo e($estado['label']); ?>

                                </button>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>

        
        <div class="col-lg-4">
            
            <?php if($conduce->puede_entregarse): ?>
            <div class="premium-card mb-3 border-start border-success border-4">
                <div class="card-body">
                    <h6 class="premium-card-title"><i class="bi bi-check-circle icon-green"></i>Marcar como Entregado</h6>
                    <button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                            data-bs-target="#modalEntregar" aria-label="Abrir formulario de entrega">
                        <i class="bi bi-check2 me-1"></i>Confirmar Entrega
                    </button>
                </div>
            </div>
            <?php endif; ?>

            
            <div class="premium-card mb-3">
                <div class="card-accent purple"></div>
                <div class="card-body">
                    <h6 class="premium-card-title"><i class="bi bi-info-circle icon-purple"></i>Información</h6>
                    <dl class="row mb-0 small">
                        <dt class="col-5 text-muted">Fecha emisión:</dt>
                        <dd class="col-7"><?php echo e($conduce->fecha->format('d/m/Y')); ?></dd>

                        <?php if($conduce->fecha_entrega): ?>
                        <dt class="col-5 text-muted">Fecha entrega:</dt>
                        <dd class="col-7"><?php echo e($conduce->fecha_entrega->format('d/m/Y')); ?></dd>
                        <?php endif; ?>

                        <?php if($conduce->fecha_recibido): ?>
                        <dt class="col-5 text-muted">Recibido:</dt>
                        <dd class="col-7"><?php echo e($conduce->fecha_recibido->format('d/m/Y H:i')); ?></dd>
                        <?php endif; ?>

                        <dt class="col-5 text-muted">Cliente:</dt>
                        <dd class="col-7">
                            <?php echo e($conduce->cliente?->nombre); ?>

                            <?php if($conduce->cliente?->rnc_cedula): ?>
                                <br><small class="text-muted"><?php echo e($conduce->cliente->rnc_cedula); ?></small>
                            <?php endif; ?>
                        </dd>

                        <?php if($conduce->venta): ?>
                        <dt class="col-5 text-muted">Venta:</dt>
                        <dd class="col-7">
                            <a href="<?php echo e(route('ventas.show', $conduce->venta)); ?>">
                                #<?php echo e($conduce->venta->id); ?>

                            </a>
                        </dd>
                        <?php endif; ?>

                        <dt class="col-5 text-muted">Emitido por:</dt>
                        <dd class="col-7"><?php echo e($conduce->user?->name ?? 'N/A'); ?></dd>

                        <dt class="col-5 text-muted">Total items:</dt>
                        <dd class="col-7"><strong><?php echo e($conduce->total_items); ?></strong></dd>

                        <?php if($conduce->peso_total): ?>
                        <dt class="col-5 text-muted">Peso total:</dt>
                        <dd class="col-7"><?php echo e(number_format($conduce->peso_total, 2)); ?> kg</dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>

            
            <div class="premium-card mb-3">
                <div class="card-accent purple"></div>
                <div class="card-body">
                    <h6 class="premium-card-title"><i class="bi bi-geo-alt icon-purple"></i>Entrega</h6>
                    <p class="small mb-2">
                        <i class="bi bi-house-door me-1 text-muted"></i>
                        <?php echo e($conduce->direccion_entrega); ?>

                    </p>
                    <?php if($conduce->referencia): ?>
                    <p class="small text-muted mb-2">
                        <i class="bi bi-bookmark me-1"></i><?php echo e($conduce->referencia); ?>

                    </p>
                    <?php endif; ?>
                    <?php if($conduce->contacto_entrega): ?>
                    <p class="small mb-1">
                        <i class="bi bi-person me-1 text-muted"></i><?php echo e($conduce->contacto_entrega); ?>

                    </p>
                    <?php endif; ?>
                    <?php if($conduce->telefono_entrega): ?>
                    <p class="small mb-0">
                        <i class="bi bi-telephone me-1 text-muted"></i><?php echo e($conduce->telefono_entrega); ?>

                    </p>
                    <?php endif; ?>
                </div>
            </div>

            
            <?php if($conduce->transportista || $conduce->chofer): ?>
            <div class="premium-card mb-3">
                <div class="card-accent purple"></div>
                <div class="card-body">
                    <h6 class="premium-card-title"><i class="bi bi-truck icon-purple"></i>Transporte</h6>
                    <dl class="row mb-0 small">
                        <?php if($conduce->transportista): ?>
                        <dt class="col-5 text-muted">Empresa:</dt>
                        <dd class="col-7"><?php echo e($conduce->transportista); ?></dd>
                        <?php endif; ?>
                        <?php if($conduce->vehiculo): ?>
                        <dt class="col-5 text-muted">Vehículo:</dt>
                        <dd class="col-7"><?php echo e($conduce->vehiculo); ?></dd>
                        <?php endif; ?>
                        <?php if($conduce->placa): ?>
                        <dt class="col-5 text-muted">Placa:</dt>
                        <dd class="col-7"><?php echo e($conduce->placa); ?></dd>
                        <?php endif; ?>
                        <?php if($conduce->chofer): ?>
                        <dt class="col-5 text-muted">Chofer:</dt>
                        <dd class="col-7"><?php echo e($conduce->chofer); ?></dd>
                        <?php endif; ?>
                        <?php if($conduce->chofer_cedula): ?>
                        <dt class="col-5 text-muted">Cédula:</dt>
                        <dd class="col-7"><?php echo e($conduce->chofer_cedula); ?></dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if($conduce->estado === 'entregado'): ?>
            <div class="premium-card mb-3 border-start border-success border-4">
                <div class="card-accent green"></div>
                <div class="card-body">
                    <h6 class="premium-card-title"><i class="bi bi-check-circle-fill icon-green"></i>Recibido por</h6>
                    <p class="mb-1 fw-bold"><?php echo e($conduce->recibido_por); ?></p>
                    <?php if($conduce->recibido_cedula): ?>
                    <p class="small text-muted mb-1">Cédula: <?php echo e($conduce->recibido_cedula); ?></p>
                    <?php endif; ?>
                    <?php if($conduce->fecha_recibido): ?>
                    <p class="small text-muted mb-0">
                        <i class="bi bi-clock me-1"></i><?php echo e($conduce->fecha_recibido->format('d/m/Y H:i')); ?>

                    </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if($conduce->estado !== 'entregado'): ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('conduces.delete')): ?>
            <div class="premium-card">
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('conduces.destroy', $conduce)); ?>"
                          onsubmit="return confirm('¿Eliminar el conduce <?php echo e($conduce->numero); ?>?')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="premium-btn-delete w-100">
                            <i class="bi bi-trash me-1"></i>Eliminar Conduce
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>


<?php if($conduce->puede_entregarse): ?>
<div class="modal fade" id="modalEntregar" tabindex="-1" aria-labelledby="modalEntregarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('conduces.entregar', $conduce)); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalEntregarLabel">
                        <i class="bi bi-check-circle me-2"></i>Confirmar Entrega
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        Ingresa las cantidades recibidas (pueden ser parciales) y los datos de quien recibe.
                    </p>

                    <h6 class="small fw-bold text-uppercase text-muted mb-2">Cantidades Recibidas</h6>
                    <?php $__currentLoopData = $conduce->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="mb-2 row align-items-center">
                        <div class="col-7">
                            <label class="form-label small mb-0"><?php echo e($item->nombre); ?></label>
                            <small class="text-muted d-block">Enviado: <?php echo e($item->cantidad); ?> <?php echo e($item->unidad); ?></small>
                        </div>
                        <div class="col-5">
                            <input type="number" step="0.01" min="0"
                                   class="form-control form-control-sm"
                                   name="items_recibidos[<?php echo e($item->id); ?>]"
                                   value="<?php echo e($item->cantidad); ?>"
                                   placeholder="Recibido"
                                   aria-label="Cantidad recibida de <?php echo e($item->nombre); ?>">
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <hr>

                    <h6 class="small fw-bold text-uppercase text-muted mb-2">Datos del Receptor</h6>
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label for="recibido_por" class="form-label">Recibido por <span class="required-indicator">*</span></label>
                            <input type="text" id="recibido_por" name="recibido_por" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label for="recibido_cedula" class="form-label">Cédula</label>
                            <input type="text" id="recibido_cedula" name="recibido_cedula" class="form-control" placeholder="000-0000000-0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check2 me-1"></i>Confirmar Entrega
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/conduces/show.blade.php ENDPATH**/ ?>