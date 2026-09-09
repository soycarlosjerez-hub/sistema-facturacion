<?php $__env->startSection('title', 'Contrato: '.$contrato->codigo); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.ui-page { --accent: #06b6d4; --accent-rgb: 6,182,212; --accent-hover: #0891b2; }
body.dark-mode .ui-page { --accent: #22d3ee; --accent-rgb: 34,211,238; --accent-hover: #06b6d4; }
body.dark-mode .ui-user-avatar-green { background: rgba(34,211,238,.15); border-color: rgba(34,211,238,.3); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <h4 class="ui-header-title"><?php echo e($contrato->codigo); ?></h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-wind me-1"></i>Climatización
                        <span class="mx-2">·</span>
                        <i class="bi bi-person me-1"></i><?php echo e($contrato->cliente?->nombre ?? 'Sin cliente'); ?>

                        <span class="mx-2">·</span>
                        <a href="<?php echo e(route('climatizacion.contratos.index')); ?>" class="text-white-50 text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if($contrato->estado === 'borrador'): ?>
                    <form action="<?php echo e(route('climatizacion.contratos.activar', $contrato)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                        <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill"
                                onclick="return confirm('¿Activar este contrato?')">
                            <i class="bi bi-play-circle me-1"></i> Activar
                        </button>
                    </form>
                <?php endif; ?>
                <?php if(in_array($contrato->estado, ['activo','borrador'])): ?>
                    <form action="<?php echo e(route('climatizacion.contratos.cancelar', $contrato)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                        <button type="submit" class="ui-btn ui-btn-danger ui-btn-sm rounded-pill"
                                onclick="return confirm('¿Cancelar este contrato?')">
                            <i class="bi bi-x-circle me-1"></i> Cancelar
                        </button>
                    </form>
                <?php endif; ?>
                <?php if($contrato->estado !== 'cancelado'): ?>
                    <a href="<?php echo e(route('climatizacion.contratos.edit', $contrato)); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4">

        
        <div class="col-lg-5">
            <div class="ui-card" style="--delay:.1s">
                <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title" style="padding:0;margin-bottom:1rem;">
                        <i class="bi bi-info-circle"></i> Información del Contrato
                    </h5>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Estado</span>
                        <span class="ui-detail-value">
                            <?php
                                $estadoBadge = match($contrato->estado) {
                                    'borrador' => 'neutral',
                                    'activo' => 'success',
                                    'vencido' => 'danger',
                                    'cancelado' => 'neutral',
                                    default => 'neutral',
                                };
                                $proximo = $contrato->estaActivo() && $contrato->vigencia_hasta <= now()->addDays(30);
                                if ($proximo) $estadoBadge = 'warning';
                            ?>
                            <?php if($proximo): ?>
                                <span class="ui-badge ui-badge-warning">
                                    <i class="bi bi-exclamation-circle"></i>
                                    <?php echo e(\App\Models\ContratoMantenimiento::ESTADOS[$contrato->estado] ?? $contrato->estado); ?>

                                </span>
                            <?php else: ?>
                                <span class="ui-badge ui-badge-<?php echo e($estadoBadge); ?>">
                                    <?php echo e(\App\Models\ContratoMantenimiento::ESTADOS[$contrato->estado] ?? $contrato->estado); ?>

                                </span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Cliente</span>
                        <span class="ui-detail-value">
                            <?php echo e($contrato->cliente?->nombre ?? '-'); ?>

                            <?php if($contrato->cliente): ?>
                                <br><small class="text-muted"><?php echo e($contrato->cliente->identificacion ?? ''); ?></small>
                            <?php endif; ?>
                        </span>
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Periodicidad</span>
                        <span class="ui-detail-value"><?php echo e(\App\Models\ContratoMantenimiento::PERIODICIDADES[$contrato->tipo_periodicidad] ?? $contrato->tipo_periodicidad); ?></span>
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Vigencia</span>
                        <span class="ui-detail-value">
                            <?php echo e($contrato->vigencia_desde?->format('d/m/Y') ?? '-'); ?>

                            <i class="bi bi-arrow-right mx-1 text-muted"></i>
                            <?php echo e($contrato->vigencia_hasta?->format('d/m/Y') ?? '-'); ?>

                            <?php if($proximo): ?>
                                <span class="ui-badge ui-badge-warning ms-2">Próximo a vencer</span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Valor Mensual</span>
                        <span class="ui-detail-value fw-bold" style="color:var(--accent,#06b6d4);">
                            RD$ <?php echo e(number_format($contrato->valor_mensual ?? 0, 2)); ?>

                        </span>
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Creado por</span>
                        <span class="ui-detail-value">
                            <?php echo e($contrato->creadoPor?->name ?? 'Sistema'); ?>

                            <br><small class="text-muted"><?php echo e($contrato->created_at?->format('d/m/Y h:i A')); ?></small>
                        </span>
                    </div>
                </div>
            </div>

            
            <div class="ui-card" style="--delay:.15s">
                <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title" style="padding:0;margin-bottom:1rem;">
                        <i class="bi bi-shield-check"></i> Cobertura
                    </h5>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Incluye Visitas</span>
                        <span class="ui-detail-value">
                            <?php if($contrato->incluye_visitas): ?>
                                <span class="ui-badge ui-badge-success"><i class="bi bi-check-lg"></i> Sí</span>
                            <?php else: ?>
                                <span class="ui-badge ui-badge-neutral"><i class="bi bi-x-lg"></i> No</span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <?php if($contrato->incluye_visitas): ?>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Visitas Realizadas</span>
                        <span class="ui-detail-value">
                            <span class="ui-badge ui-badge-primary"><?php echo e($contrato->visitas_realizadas ?? 0); ?>/<?php echo e($contrato->num_visitas_anuales ?? 0); ?></span>
                        </span>
                    </div>
                    <?php endif; ?>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Deducible</span>
                        <span class="ui-detail-value">RD$ <?php echo e(number_format($contrato->deducible ?? 0, 2)); ?></span>
                    </div>

                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Cobertura Máxima</span>
                        <span class="ui-detail-value">RD$ <?php echo e(number_format($contrato->cobertura_maxima ?? 0, 2)); ?></span>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-lg-7">

            
            <div class="ui-card" style="--delay:.1s">
                <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title" style="padding:0;margin-bottom:1rem;">
                        <i class="bi bi-cpu"></i> Equipos Cubiertos
                    </h5>
                    <?php if($contrato->equipos_cubiertos): ?>
                        <?php if(is_array($contrato->equipos_cubiertos)): ?>
                            <ul class="mb-0">
                                <?php $__currentLoopData = $contrato->equipos_cubiertos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($equipo); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php else: ?>
                            <p class="mb-0 text-muted"><?php echo e($contrato->equipos_cubiertos); ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="mb-0 text-muted"><i class="bi bi-dash-circle me-1"></i> No se especificaron equipos.</p>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="ui-card" style="--delay:.2s">
                <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title" style="padding:0;margin-bottom:1rem;">
                        <i class="bi bi-calendar-check"></i> Visitas Programadas
                        <span class="ui-badge ui-badge-primary ms-2"><?php echo e($contrato->visitas->count()); ?></span>
                    </h5>
                    <?php if($contrato->visitas->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="ui-table dt-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Técnico</th>
                                        <th>Estado</th>
                                        <th>Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $contrato->visitas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $visita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($visita->fecha_programada?->format('d/m/Y') ?? '-'); ?></td>
                                            <td><?php echo e($visita->tecnico ?? '-'); ?></td>
                                            <td>
                                                <?php
                                                    $vBadge = match($visita->estado ?? 'pendiente') {
                                                        'realizada' => 'success',
                                                        'pendiente' => 'warning',
                                                        'cancelada' => 'danger',
                                                        default => 'neutral',
                                                    };
                                                ?>
                                                <span class="ui-badge ui-badge-<?php echo e($vBadge); ?>">
                                                    <?php echo e(ucfirst($visita->estado ?? 'pendiente')); ?>

                                                </span>
                                            </td>
                                            <td><?php echo e(Str::limit($visita->observaciones ?? '-', 50)); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="ui-empty-state">
                            <i class="bi bi-calendar-x"></i>
                            <p>No hay visitas programadas</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="ui-card" style="--delay:.25s">
                <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title" style="padding:0;margin-bottom:1rem;">
                        <i class="bi bi-wrench-adjustable"></i> Mantenimientos Realizados
                        <span class="ui-badge ui-badge-primary ms-2"><?php echo e($contrato->mantenimientos->count()); ?></span>
                    </h5>
                    <?php if($contrato->mantenimientos->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="ui-table dt-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Descripción</th>
                                        <th>Técnico</th>
                                        <th>Costo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $contrato->mantenimientos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($m->fecha?->format('d/m/Y') ?? $m->created_at?->format('d/m/Y') ?? '-'); ?></td>
                                            <td><?php echo e($m->descripcion ?? $m->observaciones ?? '-'); ?></td>
                                            <td><?php echo e($m->tecnico ?? '-'); ?></td>
                                            <td class="fw-semibold">RD$ <?php echo e(number_format($m->costo ?? 0, 2)); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="ui-empty-state">
                            <i class="bi bi-tools"></i>
                            <p>No hay mantenimientos registrados</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    
    <div class="d-flex gap-2 mt-4 mb-3">
        <a href="<?php echo e(route('climatizacion.contratos.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
        <?php if($contrato->estado !== 'cancelado'): ?>
            <a href="<?php echo e(route('climatizacion.contratos.edit', $contrato)); ?>" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-pencil"></i> Editar Contrato
            </a>
        <?php endif; ?>

        <?php if($contrato->estado === 'activo'): ?>
            <?php
                $yaFacturado = \App\Models\ClimatizacionFactura::where('origen', 'contrato_cuota')
                    ->where('origen_id', $contrato->id)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->exists();
            ?>
            <?php if($yaFacturado): ?>
                <?php
                    $factura = \App\Models\ClimatizacionFactura::where('origen', 'contrato_cuota')
                        ->where('origen_id', $contrato->id)
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->first();
                ?>
                <a href="<?php echo e(route('climatizacion.facturas.show', $factura)); ?>" class="ui-btn ui-btn-primary rounded-pill">
                    <i class="bi bi-receipt me-1"></i> Ver Factura del Mes
                </a>
            <?php else: ?>
                <form action="<?php echo e(route('climatizacion.facturas.desde.contrato', $contrato)); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="ui-btn ui-btn-primary rounded-pill"
                            onclick="return confirm('¿Generar factura de cuota mensual por RD$ <?php echo e(number_format($contrato->valor_mensual, 2)); ?>?');">
                        <i class="bi bi-receipt-cutoff me-1"></i> Facturar Cuota
                    </button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/climatizacion/contratos/show.blade.php ENDPATH**/ ?>