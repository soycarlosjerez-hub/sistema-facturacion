<?php $__env->startSection('title', 'Conduces (Notas de Entrega)'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
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
                    <h4 class="ui-header-title">Conduces</h4>
                    <div class="ui-header-meta">Notas de entrega y transporte de mercancía</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('conduces.create')): ?>
                <a href="<?php echo e(route('conduces.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i>Nuevo Conduce
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-stat-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="ui-stat-label">Total</div>
                            <div class="ui-stat-value"><?php echo e($stats['total']); ?></div>
                        </div>
                        <i class="bi bi-files fs-1 text-secondary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-stat-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="ui-stat-label">En tránsito</div>
                            <div class="ui-stat-value text-info"><?php echo e($stats['en_transito']); ?></div>
                        </div>
                        <i class="bi bi-truck fs-1 text-info opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-stat-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="ui-stat-label">Entregados hoy</div>
                            <div class="ui-stat-value text-success"><?php echo e($stats['entregados_hoy']); ?></div>
                        </div>
                        <i class="bi bi-check-circle-fill fs-1 text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="ui-stat" style="--delay:.25s">
                <div class="ui-stat-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="ui-stat-label">Vencidos</div>
                            <div class="ui-stat-value text-danger"><?php echo e($stats['vencidos']); ?></div>
                        </div>
                        <i class="bi bi-exclamation-triangle-fill fs-1 text-danger opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="ui-card mb-3" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form method="GET" action="<?php echo e(route('conduces.index')); ?>" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="buscar" class="ui-label small">Buscar</label>
                    <input type="text" name="buscar" id="buscar" class="ui-input"
                           placeholder="Número, cliente, transportista..."
                           value="<?php echo e(request('buscar')); ?>">
                </div>
                <div class="col-md-2">
                    <label for="estado" class="ui-label small">Estado</label>
                    <select name="estado" id="estado" class="ui-select">
                        <option value="todos">Todos</option>
                        <?php $__currentLoopData = \App\Models\Conduce::ESTADOS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $estado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e(request('estado') == $key ? 'selected' : ''); ?>>
                                <?php echo e($estado['label']); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="cliente_id" class="ui-label small">Cliente</label>
                    <select name="cliente_id" id="cliente_id" class="ui-select">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cli): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cli->id); ?>" <?php echo e(request('cliente_id') == $cli->id ? 'selected' : ''); ?>>
                                <?php echo e($cli->nombre); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="desde" class="ui-label small">Desde</label>
                    <input type="date" name="desde" id="desde" class="ui-input" value="<?php echo e(request('desde')); ?>">
                </div>
                <div class="col-md-2">
                    <label for="hasta" class="ui-label small">Hasta</label>
                    <input type="date" name="hasta" id="hasta" class="ui-input" value="<?php echo e(request('hasta')); ?>">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="ui-btn ui-btn-solid w-100" aria-label="Filtrar">
                        <i class="bi bi-funnel"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    
    <div class="ui-card" style="--delay:.2s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table table-hover align-middle mb-0" role="table" aria-label="Lista de conduces">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Transportista</th>
                            <th class="text-center">Items</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_0 = true; $__currentLoopData = $conduces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conduce): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(route('conduces.show', $conduce)); ?>" class="fw-bold text-decoration-none">
                                        <?php echo e($conduce->numero); ?>

                                    </a>
                                    <?php if($conduce->esta_vencido): ?>
                                        <i class="bi bi-exclamation-circle-fill text-danger ms-1"
                                           title="Vencido" aria-label="Vencido"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted"><?php echo e($conduce->fecha->format('d/m/Y')); ?></small>
                                </td>
                                <td>
                                    <div><?php echo e($conduce->cliente?->nombre ?? 'N/A'); ?></div>
                                    <small class="text-muted"><?php echo e($conduce->cliente?->rnc_cedula); ?></small>
                                </td>
                                <td>
                                    <?php if($conduce->transportista): ?>
                                        <div><?php echo e($conduce->transportista); ?></div>
                                        <?php if($conduce->chofer): ?>
                                            <small class="text-muted"><?php echo e($conduce->chofer); ?></small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?php echo e($conduce->total_items); ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?php echo e($conduce->estado_color); ?>">
                                        <i class="bi bi-<?php echo e($conduce->estado_icon); ?> me-1" aria-hidden="true"></i>
                                        <?php echo e($conduce->estado_label); ?>

                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="<?php echo e(route('conduces.show', $conduce)); ?>"
                                       class="btn btn-sm btn-outline-primary rounded-pill"
                                       aria-label="Ver conduce <?php echo e($conduce->numero); ?>">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if($conduce->puede_entregarse): ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('conduces.edit')): ?>
                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEntregar<?php echo e($conduce->id); ?>"
                                            aria-label="Marcar como entregado">
                                        <i class="bi bi-check2"></i>
                                    </button>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('conduces.ticket', $conduce)); ?>"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-secondary rounded-pill"
                                       aria-label="Imprimir conduce">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                    No se encontraron conduces
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($conduces->hasPages()): ?>
        <div class="card-footer bg-white">
            <?php echo e($conduces->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>


<?php $__currentLoopData = $conduces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conduce): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php if($conduce->puede_entregarse): ?>
<div class="modal fade" id="modalEntregar<?php echo e($conduce->id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('conduces.entregar', $conduce)); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-check-circle me-2"></i>Marcar como Entregado
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Conduce: <strong><?php echo e($conduce->numero); ?></strong></p>

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

                    <div class="mb-3">
                        <label for="recibido_por<?php echo e($conduce->id); ?>" class="form-label">Recibido por <span class="required-indicator">*</span></label>
                        <input type="text" id="recibido_por<?php echo e($conduce->id); ?>"
                               name="recibido_por" class="form-control" required
                               placeholder="Nombre completo">
                    </div>
                    <div class="mb-3">
                        <label for="recibido_cedula<?php echo e($conduce->id); ?>" class="form-label">Cédula</label>
                        <input type="text" id="recibido_cedula<?php echo e($conduce->id); ?>"
                               name="recibido_cedula" class="form-control"
                               placeholder="000-0000000-0">
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
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/conduces/index.blade.php ENDPATH**/ ?>