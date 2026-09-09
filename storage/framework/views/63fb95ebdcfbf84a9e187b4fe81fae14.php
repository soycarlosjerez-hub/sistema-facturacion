<?php $__env->startSection('title', 'Detalle de Factura #' . str_pad($venta->id, 5, '0', STR_PAD_LEFT)); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.invoice-card {
    background: white;
    color: #1e293b;
}
.pagos-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(59,130,246,.04);
    margin: 0;
}
.pagos-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.pagos-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
body.dark-mode .invoice-card {
    background: rgba(15,23,42,.9);
    color: #f1f5f9;
}
body.dark-mode .pagos-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .pagos-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
@media print {
    .breadcrumb, .btn, .nav-section-title, .nav, header, .ui-header { display: none !important; }
    .invoice-card { box-shadow: none !important; border: 1px solid #eee !important; }
    .container-fluid { padding: 0 !important; }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">

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
                    <h4 class="ui-header-title">Comprobante de Venta</h4>
                    <div class="ui-header-meta">
                        Factura #<?php echo e(str_pad($venta->id, 5, '0', STR_PAD_LEFT)); ?>

                        <span class="mx-2">·</span>
                        <?php echo e($venta->created_at->format('d/m/Y h:i A')); ?>

                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('ventas.pdf', $venta->id)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                        <i class="bi bi-file-pdf me-1"></i>PDF
                    </a>
                    <button onclick="window.print()" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                        <i class="bi bi-printer me-1"></i>Imprimir
                    </button>
                    <a href="<?php echo e(route('ventas.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden invoice-card">
            <div class="p-4 text-center border-bottom border-dashed">
                <?php if($pdfLogoUrl): ?>
                <img src="<?php echo e($pdfLogoUrl); ?>" style="max-width: 80px; max-height: 60px; object-fit: contain; margin-bottom: 8px;" alt="Logo">
                <?php endif; ?>
                <h4 class="fw-bold mb-0"><?php echo e(\App\Models\SystemSetting::nombreEmpresaActual()); ?></h4>
                <small class="text-muted text-uppercase fw-bold" style="letter-spacing: 2px;">Venta Realizada</small>
            </div>
                
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-6">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Folio</small>
                            <span class="fw-bold text-primary">#<?php echo e(str_pad($venta->id, 5, '0', STR_PAD_LEFT)); ?></span>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Fecha</small>
                            <span class="fw-bold small"><?php echo e($venta->created_at->format('d/m/Y h:i A')); ?></span>
                        </div>
                    </div>

                    <div class="mb-4 p-3 rounded-3 bg-light">
                        <div class="mb-2">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Cliente</small>
                            <span class="fw-bold"><?php echo e($venta->cliente->nombre ?? 'Consumidor Final'); ?></span>
                        </div>
                        <div>
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Atendido por</small>
                            <span class="small"><?php echo e($venta->usuario->name ?? 'Sistema'); ?></span>
                        </div>
                    </div>

                    <div class="text-center mb-4">
                        <?php if($venta->estado == 'completada'): ?>
                            <span class="ui-badge-success"><i class="bi bi-check-circle-fill me-1"></i>PAGADA COMPLETAMENTE</span>
                        <?php elseif($venta->estado == 'cuenta_abierta'): ?>
                            <span class="ui-badge-info"><i class="bi bi-door-open-fill me-1"></i>CUENTA ABIERTA</span>
                        <?php else: ?>
                            <span class="ui-badge-warning"><i class="bi bi-exclamation-circle-fill me-1"></i>FIAO PENDIENTE</span>
                        <?php endif; ?>
                    </div>

                    <div class="border-top border-bottom border-dashed py-3 mb-3">
                        <?php $__currentLoopData = $venta->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="d-flex justify-content-between mb-2">
                            <div class="small pe-3" style="flex: 1;">
                                <div class="fw-bold"><?php echo e($d->producto->nombre ?? $d->obra->titulo ?? 'Obra de Arte'); ?></div>
                                <small class="text-muted"><?php echo e($d->cantidad); ?> x RD$<?php echo e(number_format($d->precio_unitario, 2)); ?></small>
                                <?php if($d->notas): ?>
                                <div class="small mt-1" style="color: var(--bs-warning); font-style: italic;"><i class="bi bi-journal-text me-1"></i><?php echo e($d->notas); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="fw-bold text-end small">RD$<?php echo e(number_format($d->subtotal, 2)); ?></div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Subtotal</span>
                            <span class="small">RD$<?php echo e(number_format($venta->subtotal, 2)); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">ITBIS (Impuestos)</span>
                            <span class="small">RD$<?php echo e(number_format($venta->impuestos, 2)); ?></span>
                        </div>
                        <?php if($venta->detalles->contains(fn($d) => $d->sin_itbis)): ?>
                        <div class="small text-danger fw-bold mb-1"><i class="bi bi-slash-circle me-1"></i>Venta incluye líneas sin ITBIS</div>
                        <?php endif; ?>
                        <?php if($venta->descuento > 0): ?>
                        <div class="d-flex justify-content-between mb-1 text-danger">
                            <span class="small">Descuento</span>
                            <span class="small">-RD$<?php echo e(number_format($venta->descuento, 2)); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between mt-3 pt-3 border-top">
                            <span class="fw-bold h5">TOTAL</span>
                            <span class="fw-bold h5 text-primary">RD$<?php echo e(number_format($venta->total, 2)); ?></span>
                        </div>
                    </div>

                    <?php if($venta->ncf): ?>
                    <div class="alert alert-secondary border-0 rounded-3 text-center py-2 mb-2">
                        <small class="text-uppercase fw-bold opacity-50 d-block" style="font-size: 0.6rem;">NCF</small>
                        <span class="fw-bold" style="letter-spacing: 1px;"><?php echo e($venta->ncf); ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if($venta->ecf): ?>
                    <?php
                        $ecfEstado = $venta->ecf->estado_info;
                    ?>
                    <div class="alert alert-<?php echo e($ecfEstado['color']); ?> border-0 rounded-3 py-2 mb-0">
                        <small class="text-uppercase fw-bold opacity-75 d-block" style="font-size: 0.6rem;">
                            <i class="bi bi-shield-check me-1"></i>e-CF - <?php echo e($ecfEstado['label']); ?>

                        </small>
                        <div class="d-flex align-items-center justify-content-between mt-1">
                            <span class="fw-bold" style="letter-spacing: 1px;"><?php echo e($venta->ecf->encf); ?></span>
                            <a href="<?php echo e(route('ecf.show', $venta->ecf)); ?>" class="btn btn-sm btn-light rounded-pill ms-2">
                                Ver <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="p-3 text-center opacity-50 bg-light">
                    <small>Gracias por su compra</small>
                </div>
            </div>

            <?php if($venta->estado != 'completada'): ?>
            <a href="<?php echo e(route('pagos.realizar', $venta->id)); ?>" class="btn btn-success rounded-pill px-4 shadow w-100 mt-3">
                <i class="bi bi-cash-coin me-2"></i>Registrar Pago
            </a>
            <?php endif; ?>
        </div>

        <div class="col-lg-8">
            <div class="ui-card mb-4" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-cash-coin me-2"></i>
                    Seguimiento de Pagos
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table pagos-table">
                            <thead>
                                <tr>
                                    <th class="ps-4">Fecha de Pago</th>
                                    <th>Monto Pagado</th>
                                    <th>Método / Nota</th>
                                    <th class="text-end pe-4">Referencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_0 = true; $__currentLoopData = $venta->pagos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                                <tr>
                                    <td class="ps-4 small"><?php echo e(\Carbon\Carbon::parse($p->fecha_pago)->format('d/m/Y h:i A')); ?></td>
                                    <td><span class="fw-bold text-success">RD$<?php echo e(number_format($p->monto, 2)); ?></span></td>
                                    <td class="small text-muted"><?php echo e($p->nota ?? 'Pago registrado'); ?></td>
                                    <td class="text-end pe-4"><small class="ui-badge-neutral">ID-<?php echo e($p->id); ?></small></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">
                                        No se han registrado pagos para esta venta todavía.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                            <?php if($venta->pagos->count()): ?>
                            <tfoot class="fw-bold" style="background:rgba(241,245,249,.5);">
                                <tr>
                                    <td class="ps-4">Resumen de Cobros</td>
                                    <td class="text-success">RD$<?php echo e(number_format($venta->pagos->sum('monto'), 2)); ?></td>
                                    <td class="text-danger">Pendiente: RD$<?php echo e(number_format($venta->total - $venta->pagos->sum('monto'), 2)); ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>

            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-archive me-2"></i>
                    Movimientos de Inventario (Kardex)
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm pagos-table">
                            <thead>
                                <tr>
                                    <th class="ps-4">Producto</th>
                                    <th>Almacén</th>
                                    <th>Tipo</th>
                                    <th class="text-center">Cantidad</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $venta->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $det): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php 
                                    $mov = \App\Models\AlmacenMovimiento::where('producto_id', $det->producto_id)
                                            ->where('nota', 'like', '%' . $venta->id . '%')
                                            ->first();
                                ?>
                                <tr class="small">
                                    <td class="ps-4"><?php echo e($det->producto->nombre); ?></td>
                                    <td><?php echo e($det->almacen->nombre ?? 'N/A'); ?></td>
                                    <td><span class="ui-badge-danger">Salida</span></td>
                                    <td class="text-center fw-bold"><?php echo e($det->cantidad); ?></td>
                                    <td class="text-muted"><?php echo e($venta->usuario->name); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php if($venta->equipos->count() || $venta->garantias->count()): ?>
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"></div>
                <div class="ui-card-title">
                    <i class="bi bi-shield-check me-2"></i>
                    Garantías del Equipo
                </div>
                <div class="ui-card-subtitle">
                    <?php if($venta->garantias->count()): ?>
                        <?php echo e($venta->garantias->count()); ?> garantía(s) registrada(s)
                    <?php else: ?>
                        Sin garantía activa
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm pagos-table">
                            <thead>
                                <tr>
                                    <th class="ps-4">Equipo</th>
                                    <th>Tipo</th>
                                    <th>Inicio</th>
                                    <th>Vencimiento</th>
                                    <th>Días Rest.</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_0 = true; $__currentLoopData = $venta->garantias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                                <tr>
                                    <td class="ps-4">
                                        <?php if($g->equipo): ?>
                                        <div class="fw-bold"><?php echo e($g->equipo->serial_imei); ?></div>
                                        <small class="text-muted"><?php echo e($g->equipo->marca); ?> <?php echo e($g->equipo->modelo); ?></small>
                                        <?php else: ?>
                                        <small class="text-muted">No vinculado</small>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="ui-badge-neutral"><?php echo e($g->tipo_label ?? 'N/A'); ?></span></td>
                                    <td><?php echo e($g->fecha_inicio->format('d/m/Y')); ?></td>
                                    <td><?php echo e($g->fecha_fin->format('d/m/Y')); ?></td>
                                    <td class="text-center">
                                        <?php if($g->dias_restantes > 30): ?>
                                            <span class="ui-badge-success"><?php echo e($g->dias_restantes); ?> días</span>
                                        <?php elseif($g->dias_restantes >= 7): ?>
                                            <span class="ui-badge-warning"><?php echo e($g->dias_restantes); ?> días</span>
                                        <?php elseif($g->dias_restantes > 0): ?>
                                            <span class="ui-badge-danger"><?php echo e($g->dias_restantes); ?> días</span>
                                        <?php else: ?>
                                            <span class="ui-badge-secondary">Expirada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                            $estadoClass = match($g->estado) {
                                                'vigente' => 'success',
                                                'expirada' => 'secondary',
                                                'reclamada', 'en_reclamo' => 'warning',
                                                'rechazada' => 'danger',
                                                'cancelada' => 'secondary',
                                                default => 'neutral',
                                            };
                                        ?>
                                        <span class="ui-badge-<?php echo e($estadoClass); ?>"><?php echo e($g->estado_label ?? $g->estado); ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                                <?php $__currentLoopData = $venta->equipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold"><?php echo e($eq->equipo->serial_imei ?? 'N/A'); ?></div>
                                        <small class="text-muted"><?php echo e($eq->equipo->marca ?? ''); ?> <?php echo e($eq->equipo->modelo ?? ''); ?></small>
                                    </td>
                                    <td colspan="5" class="text-center text-muted">
                                        <i class="bi bi-info-circle me-1"></i>Sin garantía registrada
                                        <?php if(auth()->user()->can('garantias.create')): ?>
                                        &middot;
                                        <a href="<?php echo e(route('garantias.create', ['venta_id' => $venta->id])); ?>" class="text-success fw-bold">Crear garantía</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if($venta->splitBillPersons->count()): ?>
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-people me-2"></i>
                    División de Cuenta (<?php echo e($venta->splitBillPersons->count()); ?> personas)
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php $__currentLoopData = $venta->splitBillPersons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;">
                                            <i class="bi bi-person-fill text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0"><?php echo e($person->persona_nombre ?? "Persona " . $person->persona_num); ?></h6>
                                            <small class="text-muted">Parte <?php echo e($person->persona_num); ?></small>
                                        </div>
                                        <div class="ms-auto text-end">
                                            <span class="fs-5 fw-bold text-success">RD$<?php echo e(number_format($person->subtotal, 2)); ?></span>
                                        </div>
                                    </div>
                                    <?php if(is_array($person->items) && count($person->items)): ?>
                                    <ul class="list-unstyled mb-0">
                                        <?php $__currentLoopData = $person->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="d-flex justify-content-between small py-1 border-bottom">
                                            <span><?php echo e($item["nombre"] ?? $item["name"] ?? "Item"); ?> × <?php echo e($item["cantidad"] ?? $item["qty"] ?? 1); ?></span>
                                            <span class="fw-semibold">RD$<?php echo e(number_format($item["subtotal"] ?? $item["price"] ?? 0, 2)); ?></span>
                                        </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                    <?php else: ?>
                                    <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1"></i>Sin items detallados.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if(auth()->user()->role === 'admin'): ?>
<div class="modal fade" id="modalAnularVenta" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="<?php echo e(route('ventas.destroy', $venta->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <div class="modal-header border-0 pb-0 text-white" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                    <h5 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Anular Venta #<?php echo e(str_pad($venta->id, 5, '0', STR_PAD_LEFT)); ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 rounded-3 d-flex align-items-start gap-2 mb-3">
                        <i class="bi bi-info-circle-fill mt-1"></i>
                        <div class="small">
                            Esta acción es <strong>irreversible</strong>. Se revertirá el stock al inventario
                            <?php if($venta->cliente_id && ($venta->estado == 'pendiente' || $venta->estado == 'cuenta_abierta')): ?>
                                y se ajustará el balance pendiente del cliente
                            <?php endif; ?>.
                        </div>
                    </div>
                    <div class="row g-2 small mb-3">
                        <div class="col-6"><strong>Total:</strong> RD$ <?php echo e(number_format($venta->total, 2)); ?></div>
                        <div class="col-6"><strong>Cliente:</strong> <?php echo e($venta->cliente->nombre ?? 'N/A'); ?></div>
                        <div class="col-6"><strong>Pagado:</strong> RD$ <?php echo e(number_format($venta->pagos->sum('monto'), 2)); ?></div>
                        <div class="col-6"><strong>Estado:</strong> <?php echo e(ucfirst($venta->estado)); ?></div>
                    </div>
                    <label class="form-label fw-bold small text-uppercase">Motivo de anulación <span class="text-danger">*</span></label>
                    <textarea name="motivo" class="form-control border-0 bg-light" rows="3" required minlength="5" placeholder="Ej: Error en productos, cliente devolvió, etc."></textarea>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="confirmar" value="1" id="confirmAnular" required>
                        <label class="form-check-label small fw-bold" for="confirmAnular">Confirmo que deseo anular esta venta</label>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                        <i class="bi bi-x-circle me-1"></i>Anular Venta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/ventas/show.blade.php ENDPATH**/ ?>