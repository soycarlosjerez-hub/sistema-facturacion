<?php $__env->startSection('title', 'Cotizaciones'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
body.dark-mode .card-footer { background: rgba(15,23,42,.8); border-color: #334155; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#6366f1;--accent-rgb:99,102,241;--accent-hover:#4f46e5;">
    <!-- Header -->
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
                <div>
                    <h2 class="ui-header-title">Cotizaciones</h2>
                    <div class="ui-header-meta">Gestión de cotizaciones y presupuestos</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('cotizaciones.create')): ?>
                <a href="<?php echo e(route('cotizaciones.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Cotización
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 col-lg-2">
            <div class="ui-stat h-100" style="--delay:.05s">
                <div class="ui-stat-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2">
                                <i class="bi bi-file-earmark-text fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="ui-stat-label">Total</div>
                            <div class="ui-stat-value"><?php echo e(number_format($stats['total'])); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
            <div class="ui-stat h-100" style="--delay:.1s">
                <div class="ui-stat-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 text-info rounded-3 p-2">
                                <i class="bi bi-clock fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="ui-stat-label">Pendientes</div>
                            <div class="ui-stat-value"><?php echo e(number_format($stats['pendientes'])); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
            <div class="ui-stat h-100" style="--delay:.15s">
                <div class="ui-stat-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 text-success rounded-3 p-2">
                                <i class="bi bi-check-circle fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="ui-stat-label">Aprobadas</div>
                            <div class="ui-stat-value"><?php echo e(number_format($stats['aprobadas'])); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
            <div class="ui-stat h-100" style="--delay:.2s">
                <div class="ui-stat-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2">
                                <i class="bi bi-exclamation-triangle fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="ui-stat-label">Vencidas</div>
                            <div class="ui-stat-value"><?php echo e(number_format($stats['vencidas'])); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
            <div class="ui-stat h-100" style="--delay:.25s">
                <div class="ui-stat-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-secondary bg-opacity-10 text-secondary rounded-3 p-2">
                                <i class="bi bi-arrow-right-circle fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="ui-stat-label">Convertidas</div>
                            <div class="ui-stat-value"><?php echo e(number_format($stats['convertidas'])); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-2">
            <div class="ui-stat h-100 bg-primary bg-gradient" style="--delay:.3s">
                <div class="ui-stat-body p-3 text-white">
                    <div class="ui-stat-label opacity-75">Monto Activo</div>
                    <div class="ui-stat-value">RD$<?php echo e(number_format($stats['monto_total'], 0)); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="ui-card mb-3" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form method="GET" action="<?php echo e(route('cotizaciones.index')); ?>" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="ui-label small">Buscar</label>
                    <input type="text" name="buscar" class="ui-input" placeholder="Número, cliente..." value="<?php echo e(request('buscar')); ?>">
                </div>
                <div class="col-md-2">
                    <label class="ui-label small">Estado</label>
                    <select name="estado" class="ui-select">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = \App\Models\Cotizacion::ESTADOS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $estado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e(request('estado') == $key ? 'selected' : ''); ?>>
                                <?php echo e($estado['label']); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="ui-label small">Desde</label>
                    <input type="date" name="fecha_desde" class="ui-input" value="<?php echo e(request('fecha_desde')); ?>">
                </div>
                <div class="col-md-2">
                    <label class="ui-label small">Hasta</label>
                    <input type="date" name="fecha_hasta" class="ui-input" value="<?php echo e(request('fecha_hasta')); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="ui-btn ui-btn-solid w-100">
                        <i class="bi bi-search me-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de cotizaciones -->
    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent"></div>
        <div class="table-responsive">
            <table class="ui-table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Número</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Validez</th>
                        <th>Estado</th>
                        <th class="text-end">Total</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_0 = true; $__currentLoopData = $cotizaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr>
                            <td class="ps-4">
                                <a href="<?php echo e(route('cotizaciones.show', $cot)); ?>" class="text-decoration-none fw-bold">
                                    <?php echo e($cot->numero); ?>

                                </a>
                                <div class="small text-muted"><?php echo e($cot->items->count()); ?> items</div>
                            </td>
                            <td>
                                <div class="fw-semibold"><?php echo e($cot->cliente?->nombre ?? 'Consumidor Final'); ?></div>
                                <?php if($cot->user): ?>
                                    <div class="small text-muted">por <?php echo e($cot->user->name); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><?php echo e($cot->fecha->format('d/m/Y')); ?></div>
                                <div class="small text-muted"><?php echo e($cot->created_at->diffForHumans()); ?></div>
                            </td>
                            <td>
                                <div><?php echo e($cot->fecha_validez->format('d/m/Y')); ?></div>
                                <?php if($cot->esta_vencida): ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger small">
                                        <i class="bi bi-exclamation-circle me-1"></i>Vencida
                                    </span>
                                <?php else: ?>
                                    <div class="small text-muted"><?php echo e($cot->fecha_validez->diffForHumans()); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($cot->estado_color); ?> bg-opacity-10 text-<?php echo e($cot->estado_color); ?> rounded-pill px-3">
                                    <i class="bi bi-<?php echo e($cot->estado_icon); ?> me-1"></i>
                                    <?php echo e($cot->estado_label); ?>

                                </span>
                            </td>
                            <td class="text-end fw-bold">RD$<?php echo e(number_format($cot->total, 2)); ?></td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo e(route('cotizaciones.show', $cot)); ?>" class="btn btn-outline-primary" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('cotizaciones.pdf', $cot)); ?>" class="btn btn-outline-secondary" title="PDF" target="_blank">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                    <?php if($cot->puede_convertirse && auth()->user()->can('cotizaciones.convertir')): ?>
                                        <button type="button" class="btn btn-outline-success" 
                                                onclick="confirmarConvertir(<?php echo e($cot->id); ?>, '<?php echo e($cot->numero); ?>')" 
                                                title="Convertir a venta">
                                            <i class="bi bi-arrow-right-circle"></i>
                                        </button>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('cotizaciones.edit')): ?>
                                        <?php if(!in_array($cot->estado, ['convertida', 'anulada'])): ?>
                                        <a href="<?php echo e(route('cotizaciones.edit', $cot)); ?>" class="ui-action ui-action-edit" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('cotizaciones.delete')): ?>
                                        <?php if($cot->estado !== 'convertida'): ?>
                                        <button type="button" class="ui-action ui-action-delete" 
                                                onclick="confirmarEliminar(<?php echo e($cot->id); ?>, '<?php echo e($cot->numero); ?>')" 
                                                title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 opacity-50"></i>
                                    <p class="mt-2">No hay cotizaciones registradas</p>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('cotizaciones.create')): ?>
                                        <a href="<?php echo e(route('cotizaciones.create')); ?>" class="btn btn-primary rounded-pill">
                                            <i class="bi bi-plus-lg me-1"></i> Crear la primera
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($cotizaciones->hasPages()): ?>
            <div class="card-footer bg-white border-0">
                <?php echo e($cotizaciones->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Form para eliminar -->
<form id="form-eliminar" method="POST" style="display:none;">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
</form>

<?php $__env->startPush('scripts'); ?>
<script>
function confirmarEliminar(id, numero) {
    Swal.fire({
        title: '¿Eliminar cotización?',
        text: `La cotización ${numero} será eliminada`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('form-eliminar');
            form.action = `<?php echo e(url('cotizaciones')); ?>/${id}`;
            form.submit();
        }
    });
}

function confirmarConvertir(id, numero) {
    Swal.fire({
        title: '¿Convertir a venta?',
        html: `La cotización <strong>${numero}</strong> se convertirá en una venta.<br>Esta acción no se puede deshacer.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, convertir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `<?php echo e(url('cotizaciones')); ?>/${id}/convertir`;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '<?php echo e(csrf_token()); ?>';
            form.appendChild(csrf);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/cotizaciones/index.blade.php ENDPATH**/ ?>