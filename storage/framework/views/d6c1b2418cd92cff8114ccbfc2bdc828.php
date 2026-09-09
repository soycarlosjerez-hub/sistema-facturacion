<?php $__env->startSection('title', 'Instalaciones'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.ui-page { --accent: #06b6d4; --accent-rgb: 6,182,212; --accent-hover: #0891b2; }
.filter-row .form-select,
.filter-row .form-control { font-size: .85rem; border-radius: .65rem; }
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
                    <h1 class="ui-header-title">Instalaciones</h1>
                    <div class="ui-header-meta">
                        <span>Gestión de instalaciones de equipos de climatización</span>
                        <span class="divider">·</span>
                        <span><?php echo e($instalaciones->total()); ?> registro(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('climatizacion.instalaciones.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Instalación
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s;">
        <div style="height:4px;background:linear-gradient(90deg, #06b6d4, rgba(255,255,255,.3));"></div>
        <div style="padding:1.25rem 1.75rem 0;">
            <div class="ui-card-title" style="padding:0;margin-bottom:.15rem;">
                <i class="bi bi-list-check"></i> Listado de Instalaciones
            </div>
            <div class="ui-card-subtitle" style="padding:0;">Filtra y administra las instalaciones</div>
        </div>

        <form method="GET" class="filter-row" style="padding:1rem 1.75rem;">
            <div class="row g-2 align-items-end">
                <div class="col-lg-4">
                    <label class="form-label small text-muted fw-semibold mb-1">Buscar</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Número, cliente o dirección..." value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-lg-2">
                    <label class="form-label small text-muted fw-semibold mb-1">Estado</label>
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = \App\Models\Instalacion::ESTADOS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e(request('estado') === $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label small text-muted fw-semibold mb-1">Tipo Inmueble</label>
                    <select name="tipo_inmueble" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = \App\Models\Instalacion::TIPOS_INMUEBLE; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e(request('tipo_inmueble') === $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid ui-btn-sm flex-fill" style="border-radius:.65rem;">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="<?php echo e(route('climatizacion.instalaciones.index')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm" style="border-radius:.65rem;">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </div>
        </form>

        <div style="overflow-x:auto;">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Dirección</th>
                        <th>Tipo Inmueble</th>
                        <th>Instalador</th>
                        <th>Programada</th>
                        <th>Estado</th>
                        <th class="text-end">Total</th>
                        <th style="width:140px;" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_0 = true; $__currentLoopData = $instalaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <?php
                        $badgeColor = match ($inst->estado) {
                            'pendiente' => 'neutral',
                            'programada' => 'info',
                            'en_progreso' => 'warning',
                            'completada' => 'success',
                            'cancelada' => 'danger',
                            default => 'neutral',
                        };
                    ?>
                    <tr>
                        <td class="fw-semibold"><?php echo e($inst->numero); ?></td>
                        <td><?php echo e($inst->cliente?->nombre ?? '-'); ?></td>
                        <td class="text-truncate" style="max-width:180px;"><?php echo e($inst->direccion_instalacion ?? '-'); ?></td>
                        <td><?php echo e(\App\Models\Instalacion::TIPOS_INMUEBLE[$inst->tipo_inmueble] ?? $inst->tipo_inmueble); ?></td>
                        <td><?php echo e($inst->instalador?->name ?? '-'); ?></td>
                        <td><?php echo e($inst->programada_para ? $inst->programada_para->format('d/m/Y H:i') : '-'); ?></td>
                        <td>
                            <span class="ui-badge ui-badge-<?php echo e($badgeColor); ?>">
                                <?php echo e(\App\Models\Instalacion::ESTADOS[$inst->estado] ?? $inst->estado); ?>

                            </span>
                        </td>
                        <td class="text-end fw-semibold"><?php echo e(number_format($inst->total ?? 0, 2)); ?></td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="<?php echo e(route('climatizacion.instalaciones.show', $inst)); ?>" class="ui-action ui-action-view" title="Ver"><i class="bi bi-eye"></i></a>
                                <?php if(!in_array($inst->estado, ['completada', 'cancelada'])): ?>
                                    <a href="<?php echo e(route('climatizacion.instalaciones.edit', $inst)); ?>" class="ui-action ui-action-edit" title="Editar"><i class="bi bi-pencil"></i></a>
                                    <?php
                                        $nextState = match ($inst->estado) {
                                            'pendiente' => 'programada',
                                            'programada' => 'en_progreso',
                                            default => null,
                                        };
                                    ?>
                                    <?php if($nextState): ?>
                                        <form action="<?php echo e(route('climatizacion.instalaciones.advance', $inst)); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <input type="hidden" name="next_state" value="<?php echo e($nextState); ?>">
                                            <button type="submit" class="ui-action" style="background:rgba(6,182,212,.1);color:#06b6d4;border-color:rgba(6,182,212,.2);" title="Avanzar a <?php echo e(\App\Models\Instalacion::ESTADOS[$nextState]); ?>"><i class="bi bi-forward"></i></button>
                                        </form>
                                    <?php endif; ?>
                                    <button type="button" class="ui-action ui-action-delete" title="Eliminar"
                                            onclick="UI._fire({title:'¿Eliminar instalación?',text:'<?php echo e($inst->numero); ?>',icon:'error',color:'#dc2626',confirmText:'Sí, eliminar',form:document.getElementById('del-inst-<?php echo e($inst->id); ?>')})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form id="del-inst-<?php echo e($inst->id); ?>" action="<?php echo e(route('climatizacion.instalaciones.destroy', $inst)); ?>" method="POST" class="d-none">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="bi bi-inbox" style="font-size:2.5rem;display:block;margin-bottom:.75rem;color:#cbd5e1;"></i>
                            <p class="fw-semibold mb-1" style="color:#64748b;">No hay instalaciones</p>
                            <span style="font-size:.85rem;">Crea la primera instalación para comenzar</span>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($instalaciones->hasPages()): ?>
        <div style="padding:1rem 1.75rem;border-top:1px solid #f1f5f9;">
            <div class="d-flex justify-content-end">
                <?php echo e($instalaciones->links()); ?>

            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/climatizacion/instalaciones/index.blade.php ENDPATH**/ ?>