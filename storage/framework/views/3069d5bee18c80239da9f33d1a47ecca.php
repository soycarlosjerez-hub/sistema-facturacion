<?php $__env->startSection('title', 'Cotización ' . $cotizacion->numero); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
body.dark-mode .modal-content { background: #1e293b; color: #e2e8f0; }
body.dark-mode .modal-header { border-color: #334155; }
body.dark-mode .modal-footer { border-color: #334155; }
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
                    <h2 class="ui-header-title"><?php echo e($cotizacion->numero); ?></h2>
                    <div class="ui-header-meta">
                        Creada <?php echo e($cotizacion->created_at->diffForHumans()); ?>

                        <?php if($cotizacion->user): ?>
                            por <strong><?php echo e($cotizacion->user->name); ?></strong>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('cotizaciones.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
                <a href="<?php echo e(route('cotizaciones.pdf', $cotizacion)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" target="_blank">
                    <i class="bi bi-file-pdf me-1"></i> PDF
                </a>
                <?php if($cotizacion->puede_convertirse && auth()->user()->can('cotizaciones.convertir')): ?>
                    <button type="button" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill" onclick="confirmarConvertir()">
                        <i class="bi bi-arrow-right-circle me-1"></i> Convertir a Venta
                    </button>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('cotizaciones.edit')): ?>
                    <?php if(!in_array($cotizacion->estado, ['convertida', 'anulada'])): ?>
                    <a href="<?php echo e(route('cotizaciones.edit', $cotizacion)); ?>" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill" style="--accent:#f59e0b;--accent-hover:#d97706;">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Columna principal -->
        <div class="col-lg-8">
            <!-- Estado y datos -->
            <div class="ui-card mb-3" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <small class="text-muted d-block">Estado</small>
                            <span class="badge bg-<?php echo e($cotizacion->estado_color); ?> bg-opacity-10 text-<?php echo e($cotizacion->estado_color); ?> rounded-pill px-3 py-2 mt-1">
                                <i class="bi bi-<?php echo e($cotizacion->estado_icon); ?> me-1"></i>
                                <?php echo e($cotizacion->estado_label); ?>

                            </span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Fecha</small>
                            <div class="fw-semibold"><?php echo e($cotizacion->fecha->format('d/m/Y')); ?></div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Válida hasta</small>
                            <div class="fw-semibold <?php echo e($cotizacion->esta_vencida ? 'text-danger' : ''); ?>">
                                <?php echo e($cotizacion->fecha_validez->format('d/m/Y')); ?>

                                <?php if($cotizacion->esta_vencida): ?>
                                    <i class="bi bi-exclamation-circle"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Cliente</small>
                            <div class="fw-semibold"><?php echo e($cotizacion->cliente?->nombre ?? 'Consumidor Final'); ?></div>
                            <?php if($cotizacion->cliente?->documento): ?>
                                <small class="text-muted"><?php echo e($cotizacion->cliente->documento); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if($cotizacion->venta): ?>
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Esta cotización fue convertida en 
                        <a href="<?php echo e(route('ventas.show', $cotizacion->venta)); ?>" class="alert-link">
                            Venta #<?php echo e($cotizacion->venta->id); ?>

                        </a>
                        el <?php echo e($cotizacion->convertida_en->format('d/m/Y H:i')); ?>

                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Items -->
            <div class="ui-card mb-3" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-box-seam"></i>
                    Items de la Cotización
                </div>
                <div class="table-responsive">
                    <table class="ui-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end">Desc.</th>
                                <th class="text-end">ITBIS</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $cotizacion->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?php echo e($item->nombre); ?></div>
                                        <small class="text-muted"><?php echo e($item->codigo ?? ''); ?> · <?php echo e($item->unidad); ?></small>
                                    </td>
                                    <td class="text-center"><?php echo e(number_format($item->cantidad, 2)); ?></td>
                                    <td class="text-end">RD$<?php echo e(number_format($item->precio_unitario, 2)); ?></td>
                                    <td class="text-end">RD$<?php echo e(number_format($item->descuento, 2)); ?></td>
                                    <td class="text-end">RD$<?php echo e(number_format($item->itbis, 2)); ?></td>
                                    <td class="text-end fw-bold">RD$<?php echo e(number_format($item->total, 2)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notas y condiciones -->
            <?php if($cotizacion->notas || $cotizacion->condiciones): ?>
            <div class="row g-3">
                <?php if($cotizacion->notas): ?>
                <div class="col-md-6">
                    <div class="ui-card h-100" style="--delay:.2s">
                        <div class="ui-card-accent"></div>
                        <div class="ui-card-body">
                            <h6 class="fw-bold">
                                <i class="bi bi-sticky me-1"></i> Notas
                            </h6>
                            <p class="mb-0"><?php echo e($cotizacion->notas); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if($cotizacion->condiciones): ?>
                <div class="col-md-6">
                    <div class="ui-card h-100" style="--delay:.2s">
                        <div class="ui-card-accent"></div>
                        <div class="ui-card-body">
                            <h6 class="fw-bold">
                                <i class="bi bi-file-text me-1"></i> Términos y Condiciones
                            </h6>
                            <p class="mb-0"><?php echo e($cotizacion->condiciones); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Columna derecha: resumen y acciones -->
        <div class="col-lg-4">
            <?php if($cotizacion->cliente && $cotizacion->cliente->email && !in_array($cotizacion->estado, ['convertida', 'anulada'])): ?>
            <div class="ui-card mb-3" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-envelope"></i>
                    Enviar por Email
                </div>
                <div class="ui-card-body">
                    <p class="text-muted small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Enviar esta cotización al cliente con un PDF adjunto
                    </p>
                    <button type="button" 
                            class="ui-btn ui-btn-solid w-100 mb-2" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalEnviarEmail"
                            aria-label="Abrir formulario para enviar cotización por email">
                        <i class="bi bi-send me-1"></i>
                        Enviar a <?php echo e($cotizacion->cliente->email); ?>

                    </button>
                    <div class="btn-group w-100" role="group" aria-label="Opciones de impresión">
                        <a href="<?php echo e(route('cotizaciones.ticket', [$cotizacion, 'paper' => 80])); ?>" 
                           target="_blank"
                           class="btn btn-outline-secondary"
                           aria-label="Imprimir ticket en 80mm">
                            <i class="bi bi-printer me-1"></i>Ticket 80mm
                        </a>
                        <a href="<?php echo e(route('cotizaciones.ticket', [$cotizacion, 'paper' => 58])); ?>" 
                           target="_blank"
                           class="btn btn-outline-secondary"
                           aria-label="Imprimir ticket en 58mm">
                            <i class="bi bi-printer me-1"></i>58mm
                        </a>
                    </div>
                    <a href="<?php echo e(route('cotizaciones.ticketText', $cotizacion)); ?>" 
                       class="btn btn-outline-secondary w-100 mt-2"
                       aria-label="Descargar ticket como texto">
                        <i class="bi bi-download me-1"></i>Descargar .txt
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <div class="ui-card mb-3" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-calculator"></i>
                    Totales
                </div>
                <div class="ui-card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal:</span>
                        <span class="fw-semibold">RD$<?php echo e(number_format($cotizacion->subtotal, 2)); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">ITBIS:</span>
                        <span class="fw-semibold">RD$<?php echo e(number_format($cotizacion->itbis, 2)); ?></span>
                    </div>
                    <?php if($cotizacion->descuento > 0): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Descuento:</span>
                        <span class="fw-semibold text-danger">-RD$<?php echo e(number_format($cotizacion->descuento, 2)); ?></span>
                    </div>
                    <?php endif; ?>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-5">Total:</span>
                        <span class="fw-bold fs-4 text-primary">RD$<?php echo e(number_format($cotizacion->total, 2)); ?></span>
                    </div>
                </div>
            </div>

            <!-- Cambiar estado -->
            <?php if(!in_array($cotizacion->estado, ['convertida', 'anulada'])): ?>
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-arrow-left-right"></i>
                    Cambiar Estado
                </div>
                <div class="ui-card-body">
                    <form method="POST" action="<?php echo e(route('cotizaciones.cambiarEstado', $cotizacion)); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="d-grid gap-2">
                            <?php $__currentLoopData = \App\Models\Cotizacion::ESTADOS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $estado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($key !== $cotizacion->estado && !in_array($key, ['convertida'])): ?>
                                    <button type="submit" name="estado" value="<?php echo e($key); ?>" 
                                            class="btn btn-outline-<?php echo e($estado['color']); ?> text-start">
                                        <i class="bi bi-<?php echo e($estado['icon']); ?> me-1"></i>
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
    </div>
</div>

<!-- Modal: Enviar por Email -->
<?php if($cotizacion->cliente && $cotizacion->cliente->email && !in_array($cotizacion->estado, ['convertida', 'anulada'])): ?>
<div class="modal fade" id="modalEnviarEmail" tabindex="-1" aria-labelledby="modalEnviarEmailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('cotizaciones.enviar', $cotizacion)); ?>" aria-label="Formulario de envío de cotización por email">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEnviarEmailLabel">
                        <i class="bi bi-envelope me-2" aria-hidden="true"></i>Enviar Cotización por Email
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email_destino" class="form-label">Email del destinatario</label>
                        <input type="email" 
                               class="form-control" 
                               id="email_destino" 
                               name="email_destino"
                               value="<?php echo e($cotizacion->cliente->email); ?>" 
                               required
                               aria-describedby="emailHelp">
                        <small id="emailHelp" class="form-text text-muted">
                            Si lo deja vacío, se usará el email del cliente
                        </small>
                    </div>
                    <div class="mb-3">
                        <label for="mensaje_email" class="form-label">Mensaje adicional (opcional)</label>
                        <textarea class="form-control" 
                                  id="mensaje_email" 
                                  name="mensaje" 
                                  rows="3" 
                                  maxlength="1000"
                                  placeholder="Ej: Esta cotización tiene un 10% de descuento por pronto pago..."
                                  aria-describedby="mensajeHelp"></textarea>
                        <small id="mensajeHelp" class="form-text text-muted">Se incluirá en el cuerpo del email</small>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="incluir_pdf" 
                               name="incluir_pdf" 
                               value="1" 
                               checked
                               aria-describedby="pdfHelp">
                        <label class="form-check-label" for="incluir_pdf">
                            Adjuntar PDF de la cotización
                        </label>
                        <small id="pdfHelp" class="form-text text-muted d-block">
                            Genera un PDF con todos los detalles y lo adjunta al email
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1" aria-hidden="true"></i>Enviar Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<form id="form-convertir" method="POST" style="display:none;" action="<?php echo e(route('cotizaciones.convertir', $cotizacion)); ?>">
    <?php echo csrf_field(); ?>
</form>

<?php $__env->startPush('scripts'); ?>
<script>
function confirmarConvertir() {
    Swal.fire({
        title: '¿Convertir a venta?',
        html: `La cotización <strong><?php echo e($cotizacion->numero); ?></strong> se convertirá en una venta.<br>Esta acción no se puede deshacer.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, convertir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-convertir').submit();
        }
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/cotizaciones/show.blade.php ENDPATH**/ ?>