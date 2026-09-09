<?php $__env->startSection('title', 'e-CF ' . $ecf->encf); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
body.dark-mode .ui-header { background: linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e); }
body.dark-mode .ui-card { background: rgba(15,23,42,.8); }
body.dark-mode .ui-table { color: #cbd5e1; }
body.dark-mode .ui-table thead th { background: rgba(30,41,59,.6); color: #94a3b8; border-color: #334155; }
body.dark-mode .ui-table tbody td { border-color: #1e293b; }
body.dark-mode .bg-light { background: rgba(30,41,59,.6) !important; }
body.dark-mode .text-dark { color: #f1f5f9 !important; }
body.dark-mode .alert-success { background: rgba(16,185,129,.15); color: #6ee7b7; }
body.dark-mode .alert-warning { background: rgba(245,158,11,.15); color: #fcd34d; }
body.dark-mode .alert-danger { background: rgba(239,68,68,.15); color: #fca5a5; }
body.dark-mode .alert-info { background: rgba(59,130,246,.15); color: #93c5fd; }
body.dark-mode .ui-card .ui-btn-solid { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 14px rgba(245,158,11,.3); }
body.dark-mode .ui-card .ui-btn-solid:hover { box-shadow: 0 6px 20px rgba(245,158,11,.45); }
body.dark-mode .ui-card .ui-btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 4px 14px rgba(239,68,68,.3); }
body.dark-mode .ui-card .ui-btn-ghost { background: rgba(255,255,255,.05); border-color: #334155; color: #94a3b8; }
body.dark-mode .ui-card .ui-input:focus,
body.dark-mode .ui-card .ui-select:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.15); }
body.dark-mode code { background: rgba(30,41,59,.8); color: #93c5fd; padding: .2rem .4rem; border-radius: .25rem; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $estadoInfo = $ecf->estado_info;
    $venta = $ecf->venta;
    $cliente = $venta?->cliente;
?>

<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">
    <div class="ui-header d-flex justify-content-between align-items-center mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('ecf.index')); ?>" class="text-decoration-none text-white-50">e-CF</a></li>
                            <li class="breadcrumb-item active text-white"><?php echo e($ecf->encf); ?></li>
                        </ol>
                    </nav>
                    <h4 class="ui-header-title">Detalle del Comprobante Electrónico</h4>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('ventas.show', $ecf->venta_id)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-receipt me-1"></i>Ver Venta
                </a>
                <a href="<?php echo e(route('ecf.pdf', $ecf)); ?>" target="_blank" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-file-pdf me-1"></i>PDF
                </a>
                <?php if($ecf->xml_content): ?>
                <a href="<?php echo e(route('ecf.xml', $ecf)); ?>" target="_blank" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-filetype-xml me-1"></i>XML
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success border-0 rounded-3 mb-3"><i class="bi bi-check-circle me-1"></i><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('warning')): ?>
        <div class="alert alert-warning border-0 rounded-3 mb-3"><i class="bi bi-exclamation-triangle me-1"></i><?php echo e(session('warning')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger border-0 rounded-3 mb-3"><i class="bi bi-x-circle me-1"></i><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <small class="text-muted text-uppercase fw-bold" style="font-size:0.7rem;">Comprobante</small>
                        <span class="ui-badge ui-badge-<?php echo e($estadoInfo['color']); ?> rounded-pill px-3">
                            <i class="bi <?php echo e($estadoInfo['icon']); ?> me-1"></i><?php echo e($estadoInfo['label']); ?>

                        </span>
                    </div>
                    <h2 class="fw-bold text-primary mb-0" style="letter-spacing:2px;"><?php echo e($ecf->encf); ?></h2>
                    <small class="text-muted"><?php echo e($ecf->tipo_nombre); ?></small>

                    <hr class="my-3">

                    <div class="row g-2 small">
                        <div class="col-6">
                            <span class="text-muted d-block">Tipo e-CF</span>
                            <span class="fw-bold"><?php echo e($ecf->tipo_ecf); ?></span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Venta</span>
                            <span class="fw-bold">#<?php echo e(str_pad($ecf->venta_id, 5, '0', STR_PAD_LEFT)); ?></span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Fecha Emisión</span>
                            <span class="fw-bold"><?php echo e($ecf->fecha_emision->format('d/m/Y h:i A')); ?></span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Vencimiento Secuencia</span>
                            <span class="fw-bold"><?php echo e($ecf->secuencia->fecha_vencimiento->format('d/m/Y')); ?></span>
                        </div>
                        <?php if($ecf->fecha_firma): ?>
                        <div class="col-6">
                            <span class="text-muted d-block">Firmado</span>
                            <span class="fw-bold"><?php echo e($ecf->fecha_firma->format('d/m/Y h:i A')); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($ecf->fecha_envio): ?>
                        <div class="col-6">
                            <span class="text-muted d-block">Enviado DGII</span>
                            <span class="fw-bold"><?php echo e($ecf->fecha_envio->format('d/m/Y h:i A')); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($ecf->fecha_aprobacion): ?>
                        <div class="col-12">
                            <span class="text-muted d-block">Aprobado por DGII</span>
                            <span class="fw-bold text-success"><?php echo e($ecf->fecha_aprobacion->format('d/m/Y h:i A')); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($ecf->track_id_dgii): ?>
                        <div class="col-12">
                            <span class="text-muted d-block">Track ID DGII</span>
                            <code class="small"><?php echo e($ecf->track_id_dgii); ?></code>
                        </div>
                        <?php endif; ?>
                    </div>

                    <hr class="my-3">

                    <div class="text-center p-3 bg-light rounded-3">
                        <img src="<?php echo e($qrUrl); ?>" alt="QR DGII" class="img-fluid" style="max-width:180px;">
                        <p class="small text-muted mb-0 mt-2">
                            <i class="bi bi-qr-code me-1"></i>QR de consulta DGII
                        </p>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <?php if(in_array($ecf->estado, ['borrador', 'generado', 'rechazado'], true)): ?>
                            <form action="<?php echo e(route('ecf.firmar', $ecf)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button class="ui-btn ui-btn-solid w-100 rounded-pill">
                                    <i class="bi bi-pen me-1"></i>Firmar Documento
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if(in_array($ecf->estado, ['firmado', 'generado', 'rechazado'], true)): ?>
                            <form action="<?php echo e(route('ecf.enviar', $ecf)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button class="ui-btn ui-btn-solid w-100 rounded-pill" style="--accent:#10b981;--accent-hover:#059669;">
                                    <i class="bi bi-cloud-upload me-1"></i>Enviar a DGII
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if($ecf->track_id_dgii && $ecf->estado !== 'aprobado'): ?>
                            <form action="<?php echo e(route('ecf.consultar', $ecf)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button class="ui-btn ui-btn-solid w-100 rounded-pill" style="--accent:#f59e0b;--accent-hover:#d97706;">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Consultar Estado DGII
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if($ecf->notaCredito): ?>
                            <a href="<?php echo e(route('ecf.show', $ecf->notaCredito)); ?>" class="ui-btn ui-btn-ghost w-100 rounded-pill">
                                <i class="bi bi-file-earmark-minus me-1"></i>Nota de Crédito E34: <?php echo e($ecf->notaCredito->encf); ?>

                                <span class="ui-badge ui-badge-<?php echo e($ecf->notaCredito->estado_info['color']); ?> ms-1"><?php echo e($ecf->notaCredito->estado_info['label']); ?></span>
                            </a>
                        <?php endif; ?>
                        <?php if($ecf->documentoOriginal): ?>
                            <a href="<?php echo e(route('ecf.show', $ecf->documentoOriginal)); ?>" class="ui-btn ui-btn-ghost w-100 rounded-pill">
                                <i class="bi bi-file-earmark-arrow-up me-1"></i>Doc. Original: <?php echo e($ecf->documentoOriginal->encf); ?>

                            </a>
                        <?php endif; ?>
                        <?php if($ecf->puedeAnular()): ?>
                            <button class="ui-btn ui-btn-danger w-100 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalAnular">
                                <i class="bi bi-slash-circle me-1"></i>Anular e-CF (genera E34)
                            </button>
                        <?php endif; ?>
                        <?php if($ecf->estado === 'aprobado' && $ecf->tipo_ecf !== 'E33'): ?>
                            <form action="<?php echo e(route('ecf.nota-debito', $ecf)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button class="ui-btn ui-btn-ghost w-100 rounded-pill" onclick="confirmAction({title:'Nota de Débito E33', text:'¿Generar Nota de Débito (E33) para corregir al alza este comprobante?', icon:'warning', color:'#f59e0b', confirmText:'Generar E33', onSubmit:function(){ this.closest('form').submit(); }})">
                                    <i class="bi bi-file-earmark-plus me-1"></i>Nota de Débito E33
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="ui-card mb-3" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title"><i class="bi bi-person"></i> Datos del Cliente</div>
                <div class="ui-card-body">
                    <?php if($cliente): ?>
                    <div class="row g-2 small">
                        <div class="col-md-6"><span class="text-muted d-block">Nombre</span><span class="fw-bold"><?php echo e($cliente->nombre); ?></span></div>
                        <div class="col-md-3"><span class="text-muted d-block">Documento</span><span class="fw-bold"><?php echo e(ucfirst($cliente->tipo_documento ?? 'N/A')); ?></span></div>
                        <div class="col-md-3"><span class="text-muted d-block">RNC/Cédula</span><span class="fw-bold"><?php echo e($cliente->rnc_cedula ?: 'N/A'); ?></span></div>
                        <div class="col-md-6"><span class="text-muted d-block">Email</span><span><?php echo e($cliente->email ?: 'N/A'); ?></span></div>
                        <div class="col-md-6"><span class="text-muted d-block">Tipo Cliente</span><span class="ui-badge ui-badge-info"><?php echo e(ucfirst(str_replace('_',' ', $cliente->tipo_cliente))); ?></span></div>
                    </div>
                    <?php else: ?>
                    <p class="text-muted mb-0">Sin cliente asociado</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="ui-card mb-3" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title"><i class="bi bi-cash-stack"></i> Totales</div>
                <div class="ui-card-body">
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">Monto Gravado</span><span>RD$ <?php echo e(number_format($ecf->monto_gravado_total, 2)); ?></span></div>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">Monto Exento</span><span>RD$ <?php echo e(number_format($ecf->monto_exento_total, 2)); ?></span></div>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted">ITBIS</span><span>RD$ <?php echo e(number_format($ecf->itbis_total, 2)); ?></span></div>
                    <div class="d-flex justify-content-between mt-3 pt-3 border-top"><span class="fw-bold h5 mb-0">TOTAL</span><span class="fw-bold h5 mb-0 text-primary">RD$ <?php echo e(number_format($ecf->monto_total, 2)); ?></span></div>
                </div>
            </div>

            <div class="ui-card mb-3" style="--delay:.3s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title"><i class="bi bi-list-ul"></i> Items del Comprobante</div>
                <div class="table-responsive">
                    <table class="ui-table table-sm align-middle mb-0">
                        <thead>
                            <tr style="font-size:0.7rem; text-transform:uppercase;">
                                <th class="ps-3">#</th>
                                <th>Descripción</th>
                                <th class="text-end">Cant.</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end pe-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $venta->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="ps-3 small"><?php echo e($i + 1); ?></td>
                                <td>
                                    <span class="fw-bold small"><?php echo e($d->producto->nombre ?? $d->obra->titulo ?? 'Obra de Arte'); ?></span>
                                    <br><small class="text-muted"><?php echo e($d->producto->codigo_barras ?? ($d->obra_id ? 'OBRA-' . $d->obra_id : 'N/A')); ?></small>
                                </td>
                                <td class="text-end small"><?php echo e($d->cantidad); ?></td>
                                <td class="text-end small">RD$ <?php echo e(number_format($d->precio_unitario, 2)); ?></td>
                                <td class="text-end pe-3 small fw-bold">RD$ <?php echo e(number_format($d->subtotal, 2)); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if($ecf->mensaje_dgii): ?>
            <div class="alert alert-<?php echo e($ecf->estado === 'aprobado' ? 'success' : 'danger'); ?> border-0 rounded-3">
                <strong>DGII:</strong> <?php echo e($ecf->mensaje_dgii); ?>

            </div>
            <?php endif; ?>

            <?php if($ecf->logs->count()): ?>
            <div class="ui-card" style="--delay:.4s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title"><i class="bi bi-list-task"></i> Historial de Operaciones</div>
                <div class="table-responsive">
                    <table class="ui-table table-sm mb-0">
                        <thead>
                            <tr style="font-size:0.7rem; text-transform:uppercase;">
                                <th class="ps-3">Fecha</th>
                                <th>Acción</th>
                                <th>Resultado</th>
                                <th class="text-end pe-3">Duración</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $ecf->logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="small">
                                <td class="ps-3"><?php echo e($log->created_at->format('d/m/Y h:i:s')); ?></td>
                                <td><?php echo e(ucfirst($log->accion)); ?></td>
                                <td>
                                    <span class="ui-badge ui-badge-<?php echo e($log->estado_resultado === 'exito' ? 'success' : 'danger'); ?> rounded-pill">
                                        <?php echo e(ucfirst($log->estado_resultado)); ?>

                                    </span>
                                </td>
                                <td class="text-end pe-3"><?php echo e($log->duracion_ms ? $log->duracion_ms . ' ms' : '-'); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if($ecf->puedeAnular()): ?>
<div class="modal fade" id="modalAnular" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="<?php echo e(route('ecf.anular', $ecf)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header border-0 pb-0 text-white" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                    <h5 class="fw-bold mb-0"><i class="bi bi-slash-circle me-2"></i>Anular e-CF</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 rounded-3 small mb-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Proceso fiscal:</strong> Al anular este e-CF se generará automáticamente una
                        <strong>Nota de Crédito (E34)</strong> con los mismos montos, que será firmada y enviada a DGII.
                        Esta Nota de Crédito anula fiscalmente el comprobante original.
                    </div>
                    <div class="alert alert-info border-0 rounded-3 small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong><?php echo e($ecf->encf); ?></strong> — Monto: RD$ <?php echo e(number_format($ecf->monto_total, 2)); ?>

                    </div>
                    <label class="ui-label fw-bold small text-uppercase">Motivo <span class="text-danger">*</span></label>
                    <textarea name="motivo" class="ui-input" rows="3" required minlength="5"></textarea>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="ui-btn ui-btn-danger rounded-pill px-4">Anular</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/ecf/show.blade.php ENDPATH**/ ?>