<?php $__env->startSection('title', 'Factura #' . $climatizacionFactura->id); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .detail-row { display: flex; justify-content: space-between; padding: .5rem 0; border-bottom: 1px solid #f1f5f9; }
    .detail-row:last-child { border-bottom: none; }
    body.dark-mode .detail-row { border-color: #334155; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">
    
    <div class="ui-header">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-receipt"></i></div>
                <div>
                    <h1 class="ui-header-title">Factura #<?php echo e($climatizacionFactura->id); ?></h1>
                    <div class="ui-header-meta">
                        <span>Creada <?php echo e(optional($climatizacionFactura->created_at)->format('d/m/Y h:i A')); ?></span>
                        <span class="divider">|</span>
                        <span>
                            <span class="ui-badge ui-badge-<?php echo e(match($climatizacionFactura->estado) {
                                'borrador' => 'secondary',
                                'generada' => 'info',
                                'enviada' => 'success',
                                'anulada' => 'danger',
                                default => 'secondary'
                            }); ?>">
                                <?php echo e(\App\Models\ClimatizacionFactura::ESTADOS[$climatizacionFactura->estado] ?? $climatizacionFactura->estado); ?>

                            </span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('climatizacion.facturas.index')); ?>" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        
        <div class="col-lg-8">
            
            <div class="ui-card mb-3">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title"><i class="bi bi-person"></i> Datos del Cliente</h5>
                    <div class="detail-row">
                        <span class="text-muted">Nombre</span>
                        <strong><?php echo e($climatizacionFactura->cliente->nombre ?? 'Consumidor Final'); ?></strong>
                    </div>
                    <div class="detail-row">
                        <span class="text-muted">Origen</span>
                        <span class="ui-badge ui-badge-<?php echo e(match($climatizacionFactura->origen) {
                            'mantenimiento' => 'info',
                            'contrato_cuota' => 'success',
                            'instalacion' => 'warning',
                            'emergencia' => 'danger',
                            default => 'secondary'
                        }); ?>"><?php echo e(\App\Models\ClimatizacionFactura::ORIGENES[$climatizacionFactura->origen] ?? $climatizacionFactura->origen); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="text-muted">Referencia</span>
                        <strong><?php echo e($climatizacionFactura->referencia ?? '-'); ?></strong>
                    </div>
                    <div class="detail-row">
                        <span class="text-muted">Creada por</span>
                        <span><?php echo e($climatizacionFactura->creadoPor?->name ?? '-'); ?></span>
                    </div>
                </div>
            </div>

            
            <div class="ui-card mb-3">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body p-0">
                    <h5 class="ui-card-title px-3 pt-3 pb-2"><i class="bi bi-list-ul"></i> Detalle de Conceptos</h5>
                    <div class="table-responsive">
                        <table class="ui-table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Concepto</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($climatizacionFactura->detalle && count($climatizacionFactura->detalle) > 0): ?>
                                    <?php $__currentLoopData = $climatizacionFactura->detalle; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $linea): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($idx + 1); ?></td>
                                        <td class="fw-medium"><?php echo e($linea['descripcion'] ?? '-'); ?></td>
                                        <td class="text-center"><?php echo e($linea['cantidad'] ?? 1); ?></td>
                                        <td class="text-end">RD$ <?php echo e(number_format($linea['precio_unitario'] ?? 0, 2)); ?></td>
                                        <td class="text-end fw-semibold" style="color:var(--accent);">
                                            RD$ <?php echo e(number_format($linea['subtotal'] ?? 0, 2)); ?>

                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center text-muted py-3">Sin detalle</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-lg-4">
            
            <div class="ui-card mb-3">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title"><i class="bi bi-calculator"></i> Totales</h5>
                    <div class="detail-row">
                        <span class="text-muted">Subtotal</span>
                        <strong>RD$ <?php echo e(number_format($climatizacionFactura->subtotal, 2)); ?></strong>
                    </div>
                    <div class="detail-row">
                        <span class="text-muted">ITBIS (18%)</span>
                        <strong>RD$ <?php echo e(number_format($climatizacionFactura->itbis, 2)); ?></strong>
                    </div>
                    <div class="detail-row">
                        <span class="text-muted">Descuento</span>
                        <strong>RD$ <?php echo e(number_format($climatizacionFactura->descuento, 2)); ?></strong>
                    </div>
                    <div class="detail-row" style="border-top:2px solid var(--accent);padding-top:.75rem;margin-top:.5rem;">
                        <span class="fw-bold">TOTAL</span>
                        <span class="fw-bold" style="font-size:1.3rem;color:var(--accent);">
                            RD$ <?php echo e(number_format($climatizacionFactura->total, 2)); ?>

                        </span>
                    </div>
                </div>
            </div>

            
            <div class="ui-card">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title"><i class="bi bi-gear"></i> Acciones</h5>

                    <?php if($climatizacionFactura->estado === 'borrador'): ?>
                        <div class="alert alert-info small mb-3">
                            <i class="bi bi-info-circle"></i> Borrador listo para generar factura DGII.
                        </div>
                        <div class="mb-2">
                            <a href="#" id="btnGenerar" class="ui-btn ui-btn-solid w-100">
                                <i class="bi bi-send"></i> Generar Factura DGII
                            </a>
                        </div>
                        <form action="<?php echo e(route('climatizacion.facturas.anular', $climatizacionFactura)); ?>"
                              method="POST" class="d-inline w-100"
                              onsubmit="return confirm('¿Anular esta factura? Esta acción no se puede deshacer.');">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <button type="submit" class="ui-btn ui-btn-danger w-100">
                                <i class="bi bi-x-circle"></i> Anular
                            </button>
                        </form>
                    <?php elseif($climatizacionFactura->estado === 'anulada'): ?>
                        <div class="text-center py-3">
                            <span class="ui-badge ui-badge-danger"><i class="bi bi-x-circle-fill"></i> ANULADA</span>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <span class="ui-badge ui-badge-success"><i class="bi bi-check-circle-fill"></i> GENERADA</span>
                            <?php if($climatizacionFactura->referencia): ?>
                                <div class="mt-2 small text-muted">NCF: <?php echo e($climatizacionFactura->referencia); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <?php
        $origenModelo = null;
        if ($climatizacionFactura->origen === 'mantenimiento') {
            $origenModelo = \App\Models\Mantenimiento::find($climatizacionFactura->origen_id);
        } elseif ($climatizacionFactura->origen === 'contrato_cuota') {
            $origenModelo = \App\Models\ContratoMantenimiento::find($climatizacionFactura->origen_id);
        } elseif ($climatizacionFactura->origen === 'emergencia') {
            $origenModelo = \App\Models\OrdenEmergencia::find($climatizacionFactura->origen_id);
        } elseif ($climatizacionFactura->origen === 'instalacion') {
            $origenModelo = \App\Models\Instalacion::find($climatizacionFactura->origen_id);
        }
    ?>

    <?php if($origenModelo): ?>
    <div class="ui-card">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <h5 class="ui-card-title"><i class="bi bi-link-45deg"></i> Registro Origen</h5>
            <div class="detail-row">
                <span class="text-muted">Tipo</span>
                <strong><?php echo e(\App\Models\ClimatizacionFactura::ORIGENES[$climatizacionFactura->origen]); ?></strong>
            </div>
            <div class="detail-row">
                <span class="text-muted">Identificador</span>
                <strong><?php echo e($origenModelo->numero ?? $origenModelo->codigo ?? '#' . $origenModelo->id); ?></strong>
            </div>
            <div class="detail-row">
                <span class="text-muted">Estado Original</span>
                <span>
                    <?php if(isset($origenModelo->estado)): ?>
                        <?php
                            $constName = $climatizacionFactura->origen === 'mantenimiento' ? '\App\Models\Mantenimiento::ESTADOS' :
                                         ($climatizacionFactura->origen === 'contrato_cuota' ? '\App\Models\ContratoMantenimiento::ESTADOS' :
                                         ($climatizacionFactura->origen === 'emergencia' ? '\App\Models\OrdenEmergencia::ESTADOS' :
                                         '\App\Models\Instalacion::ESTADOS'));
                        ?>
                        <?php echo e(($constName[$origenModelo->estado] ?? $origenModelo->estado)); ?>

                    <?php else: ?>
                        -
                    <?php endif; ?>
                </span>
            </div>
            <div class="mt-2">
                <a href="<?php echo e(match($climatizacionFactura->origen) {
                    'mantenimiento' => route('climatizacion.mantenimientos.show', $origenModelo),
                    'contrato_cuota' => route('climatizacion.contratos.show', $origenModelo),
                    'emergencia' => route('climatizacion.ordenes-emergencia.show', $origenModelo),
                    'instalacion' => route('climatizacion.instalaciones.show', $origenModelo),
                    default => '#'
                }); ?>" class="ui-btn ui-btn-ghost">
                    <i class="bi bi-box-arrow-up-right"></i> Ir al Registro
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('btnGenerar')?.addEventListener('click', function(e) {
    e.preventDefault();
    if (!confirm('¿Confirmar generación de factura DGII para esta factura?')) return;

    fetch('<?php echo e(route("climatizacion.facturas.generar", $climatizacionFactura)); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error al generar factura.');
        }
    })
    .catch(err => alert('Error de conexión.'));
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/climatizacion/facturas/show.blade.php ENDPATH**/ ?>