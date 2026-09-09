<?php $__env->startSection('title', $instalacion->numero); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.ui-page { --accent: #06b6d4; --accent-rgb: 6,182,212; --accent-hover: #0891b2; }
body.dark-mode .ui-detail-row { border-bottom-color: #1e293b; }
body.dark-mode .ui-detail-label { color: #94a3b8; }
body.dark-mode .ui-detail-value { color: #cbd5e1; }
.text-pre-wrap { white-space: pre-wrap; word-wrap: break-word; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    <div class="ui-header" style="--delay:0s;">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h1 class="ui-header-title"><?php echo e($instalacion->numero); ?></h1>
                    <div class="ui-header-meta">
                        <span>Instalación de climatización</span>
                        <span class="divider">·</span>
                        <span><?php echo e($instalacion->cliente?->nombre ?? 'Sin cliente'); ?></span>
                        <span class="divider">·</span>
                        <span>
                            <?php
                                $badgeColor = match ($instalacion->estado) {
                                    'pendiente' => 'neutral',
                                    'programada' => 'info',
                                    'en_progreso' => 'warning',
                                    'completada' => 'success',
                                    'cancelada' => 'danger',
                                    default => 'neutral',
                                };
                            ?>
                            <span class="ui-badge ui-badge-<?php echo e($badgeColor); ?>">
                                <?php echo e(\App\Models\Instalacion::ESTADOS[$instalacion->estado] ?? $instalacion->estado); ?>

                            </span>
                        </span>
                        <span class="divider">·</span>
                        <a href="<?php echo e(route('climatizacion.instalaciones.index')); ?>" style="color:rgba(255,255,255,.8);text-decoration:none;">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if(!in_array($instalacion->estado, ['completada', 'cancelada'])): ?>
                    <a href="<?php echo e(route('climatizacion.instalaciones.edit', $instalacion)); ?>" class="ui-btn ui-btn-primary ui-btn-pill">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">

            <div class="ui-card" style="--delay:.1s;">
                <div style="height:4px;background:linear-gradient(90deg, #06b6d4, rgba(255,255,255,.3));"></div>
                <div class="ui-card-body">
                    <div class="ui-card-title" style="padding:0 0 .75rem;margin:0;">
                        <i class="bi bi-info-circle"></i> Información General
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Número</span>
                        <span class="ui-detail-value fw-bold"><?php echo e($instalacion->numero); ?></span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Cliente</span>
                        <span class="ui-detail-value"><?php echo e($instalacion->cliente?->nombre ?? 'Sin cliente'); ?></span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Tipo Inmueble</span>
                        <span class="ui-detail-value"><?php echo e(\App\Models\Instalacion::TIPOS_INMUEBLE[$instalacion->tipo_inmueble] ?? $instalacion->tipo_inmueble); ?></span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Instalador</span>
                        <span class="ui-detail-value"><?php echo e($instalacion->instalador?->name ?? 'No asignado'); ?></span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Dirección</span>
                        <span class="ui-detail-value"><?php echo e($instalacion->direccion_instalacion ?? 'No especificada'); ?></span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Programada Para</span>
                        <span class="ui-detail-value"><?php echo e($instalacion->programada_para ? $instalacion->programada_para->format('d/m/Y H:i') : 'Por definir'); ?></span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Completada En</span>
                        <span class="ui-detail-value"><?php echo e($instalacion->completada_en ? $instalacion->completada_en->format('d/m/Y H:i') : '-'); ?></span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Creado por</span>
                        <span class="ui-detail-value"><?php echo e($instalacion->creadoPor?->name ?? '-'); ?></span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Creado</span>
                        <span class="ui-detail-value"><?php echo e($instalacion->created_at?->format('d/m/Y h:i A') ?? '-'); ?></span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Actualizado</span>
                        <span class="ui-detail-value"><?php echo e($instalacion->updated_at?->format('d/m/Y h:i A') ?? '-'); ?></span>
                    </div>
                </div>
            </div>

            <?php if($instalacion->nota_interna): ?>
            <div class="ui-card" style="--delay:.15s;">
                <div style="height:4px;background:linear-gradient(90deg, #06b6d4, rgba(255,255,255,.3));"></div>
                <div class="ui-card-body">
                    <div class="ui-card-title" style="padding:0 0 .75rem;margin:0;">
                        <i class="bi bi-journal-text"></i> Nota Interna
                    </div>
                    <p style="color:#475569;line-height:1.6;font-size:.9rem;white-space:pre-wrap;"><?php echo e($instalacion->nota_interna); ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-5">
            <div class="ui-card" style="--delay:.1s;">
                <div style="height:4px;background:linear-gradient(90deg, #06b6d4, rgba(255,255,255,.3));"></div>
                <div class="ui-card-body">
                    <div class="ui-card-title" style="padding:0 0 .75rem;margin:0;">
                        <i class="bi bi-bar-chart"></i> Resumen
                    </div>
                    <div class="mb-3">
                        <div class="ui-detail-label" style="width:auto;">Total de Productos</div>
                        <div class="h4 mb-0 fw-bold" style="color:var(--accent);"><?php echo e($instalacion->productos->sum('pivot.cantidad')); ?> unidades</div>
                    </div>
                    <div class="mb-3">
                        <div class="ui-detail-label" style="width:auto;">Total Instalación</div>
                        <div class="h3 mb-0 fw-bold" style="color:#16a34a;">$<?php echo e(number_format($instalacion->total ?? 0, 2)); ?></div>
                    </div>
                    <hr>
                    <div class="ui-detail-label" style="width:auto;margin-bottom:.5rem;">Transiciones de Estado</div>
                    <ul class="list-unstyled mb-0" style="font-size:.85rem;">
                        <?php $__currentLoopData = \App\Models\Instalacion::ESTADOS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $estadosArr = array_keys(\App\Models\Instalacion::ESTADOS);
                                $currentIdx = array_search($instalacion->estado, $estadosArr);
                                $keyIdx = array_search($key, $estadosArr);
                                $isCurrent = $instalacion->estado === $key;
                                $isPast = $keyIdx < $currentIdx && $instalacion->estado !== 'cancelada';
                            ?>
                            <li class="d-flex align-items-center mb-1">
                                <?php if($isCurrent): ?>
                                    <i class="bi bi-arrow-right-circle-fill me-2" style="color:var(--accent);"></i>
                                    <span class="fw-bold"><?php echo e($label); ?></span>
                                <?php elseif($isPast): ?>
                                    <i class="bi bi-check-circle text-success me-2"></i>
                                    <span class="text-muted"><?php echo e($label); ?></span>
                                <?php else: ?>
                                    <i class="bi bi-circle me-2" style="color:#cbd5e1;"></i>
                                    <span class="text-muted"><?php echo e($label); ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card mt-4" style="--delay:.2s;">
        <div style="height:4px;background:linear-gradient(90deg, #06b6d4, rgba(255,255,255,.3));"></div>
        <div class="ui-card-body">
            <div class="ui-card-title" style="padding:0 0 .75rem;margin:0;">
                <i class="bi bi-box-seam"></i> Productos de la Instalación
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Código</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">Precio Unitario</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_0 = true; $__currentLoopData = $instalacion->productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr>
                            <td class="fw-medium"><?php echo e($producto->nombre); ?></td>
                            <td><code><?php echo e($producto->codigo ?? '-'); ?></code></td>
                            <td class="text-center"><?php echo e($producto->pivot->cantidad); ?></td>
                            <td class="text-end">$<?php echo e(number_format($producto->pivot->precio_unitario, 2)); ?></td>
                            <td class="text-end fw-medium">$<?php echo e(number_format($producto->pivot->cantidad * $producto->pivot->precio_unitario, 2)); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">No hay productos asociados</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Total:</th>
                            <th class="text-end">$<?php echo e(number_format($instalacion->total ?? 0, 2)); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mt-4" style="--delay:.25s;">
        <?php if(!in_array($instalacion->estado, ['completada', 'cancelada'])): ?>
            <a href="<?php echo e(route('climatizacion.instalaciones.edit', $instalacion)); ?>" class="ui-btn ui-btn-solid">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <?php
                $nextState = match ($instalacion->estado) {
                    'pendiente' => 'programada',
                    'programada' => 'en_progreso',
                    'en_progreso' => 'completada',
                    default => null,
                };
            ?>
            <?php if($nextState): ?>
                <form action="<?php echo e(route('climatizacion.instalaciones.advance', $instalacion)); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                    <input type="hidden" name="next_state" value="<?php echo e($nextState); ?>">
                    <button type="submit" class="ui-btn ui-btn-primary" onclick="return confirm('¿Avanzar estado a <?php echo e(\App\Models\Instalacion::ESTADOS[$nextState]); ?>?');">
                        <i class="bi bi-forward"></i> Avanzar a <?php echo e(\App\Models\Instalacion::ESTADOS[$nextState]); ?>

                    </button>
                </form>
            <?php endif; ?>
        <?php endif; ?>

        <?php if($instalacion->estado === 'completada' && ($instalacion->total ?? 0) > 0): ?>
            <?php
                $yaFacturado = \App\Models\ClimatizacionFactura::where('origen', 'instalacion')
                    ->where('origen_id', $instalacion->id)->exists();
            ?>
            <?php if($yaFacturado): ?>
                <?php
                    $factura = \App\Models\ClimatizacionFactura::where('origen', 'instalacion')
                        ->where('origen_id', $instalacion->id)->first();
                ?>
                <a href="<?php echo e(route('climatizacion.facturas.show', $factura)); ?>" class="ui-btn ui-btn-primary">
                    <i class="bi bi-receipt me-1"></i> Ver Factura
                </a>
            <?php else: ?>
                <form action="<?php echo e(route('climatizacion.facturas.desde.instalacion', $instalacion)); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="ui-btn ui-btn-primary"
                            onclick="return confirm('¿Generar factura por RD$ <?php echo e(number_format($instalacion->total, 2)); ?>?');">
                        <i class="bi bi-receipt-cutoff me-1"></i> Facturar
                    </button>
                </form>
            <?php endif; ?>
        <?php endif; ?>

        <a href="<?php echo e(route('climatizacion.instalaciones.index')); ?>" class="ui-btn ui-btn-ghost">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/climatizacion/instalaciones/show.blade.php ENDPATH**/ ?>