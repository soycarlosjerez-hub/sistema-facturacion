<?php $__env->startSection('title', 'Contratos de Mantenimiento'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.datatable-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.ui-page { --accent: #06b6d4; --accent-rgb: 6,182,212; --accent-hover: #0891b2; --dt-accent: #06b6d4; --dt-accent-rgb: 6,182,212; --dt-accent-gradient: linear-gradient(135deg,#06b6d4,#0ea5e9); }
body.dark-mode .ui-page { --accent: #22d3ee; --accent-rgb: 34,211,238; --accent-hover: #06b6d4; }
body.dark-mode .ui-stat-value { color: #22d3ee; }
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
                    <h4 class="ui-header-title">Contratos de Mantenimiento</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-wind me-1"></i>Climatización
                        <span class="mx-2">·</span>
                        <i class="bi bi-calendar3 me-1"></i><?php echo e(now()->format('d/m/Y')); ?>

                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('climatizacion.contratos.create')); ?>" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg"></i> Nuevo Contrato
                </a>
            </div>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="ui-stat" style="--delay:.05s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Próximos a Vencer</div>
                    <div class="ui-stat-value"><?php echo e($proximosVencer); ?></div>
                    <div class="ui-stat-sub">Contratos activos por vencer en 30 días</div>
                </div>
            </div>
        </div>
        <div class="col-xl-9 col-lg-8 col-md-6">
            <div class="ui-card" style="--delay:.1s">
                <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
                <div class="ui-card-body">
                    <form method="GET" action="<?php echo e(route('climatizacion.contratos.index')); ?>" class="row g-2 align-items-end">
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <label class="ui-label small">Estado</label>
                            <select name="estado" class="ui-select form-select form-select-sm">
                                <option value="">Todos</option>
                                <?php $__currentLoopData = \App\Models\ContratoMantenimiento::ESTADOS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($val); ?>" <?php echo e(request('estado') == $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <label class="ui-label small">Periodicidad</label>
                            <select name="periodicidad" class="ui-select form-select form-select-sm">
                                <option value="">Todas</option>
                                <?php $__currentLoopData = \App\Models\ContratoMantenimiento::PERIODICIDADES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($val); ?>" <?php echo e(request('periodicidad') == $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-8">
                            <label class="ui-label small">Buscar</label>
                            <input type="search" name="search" class="ui-input form-control form-control-sm" placeholder="Código o cliente..." value="<?php echo e(request('search')); ?>">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-4 d-flex gap-1">
                            <button type="submit" class="ui-btn ui-btn-solid ui-btn-sm flex-fill">
                                <i class="bi bi-funnel"></i> Filtrar
                            </button>
                            <?php if(request()->anyFilled(['estado','periodicidad','search'])): ?>
                                <a href="<?php echo e(route('climatizacion.contratos.index')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <div class="ui-card" style="--delay:.15s">
        <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table dt-table mb-0" id="contratos-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Cliente</th>
                            <th>Periodicidad</th>
                            <th>Vigencia Desde</th>
                            <th>Vigencia Hasta</th>
                            <th>Valor Mensual</th>
                            <th>Visitas</th>
                            <th>Estado</th>
                            <th style="width:140px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_0 = true; $__currentLoopData = $contratos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                            <?php
                                $proximosVencerItem = $c->estaActivo() && $c->vigencia_hasta <= now()->addDays(30);
                                $badgeColor = match($c->estado) {
                                    'borrador' => 'neutral',
                                    'activo' => 'success',
                                    'vencido' => 'danger',
                                    'cancelado' => 'neutral',
                                    default => 'neutral',
                                };
                                if ($proximosVencerItem) $badgeColor = 'warning';
                            ?>
                            <tr>
                                <td><strong><?php echo e($c->codigo); ?></strong></td>
                                <td><?php echo e($c->cliente?->nombre ?? '-'); ?></td>
                                <td><?php echo e(\App\Models\ContratoMantenimiento::PERIODICIDADES[$c->tipo_periodicidad] ?? $c->tipo_periodicidad); ?></td>
                                <td><?php echo e($c->vigencia_desde?->format('d/m/Y') ?? '-'); ?></td>
                                <td>
                                    <?php echo e($c->vigencia_hasta?->format('d/m/Y') ?? '-'); ?>

                                    <?php if($proximosVencerItem): ?>
                                        <i class="bi bi-exclamation-triangle-fill text-warning ms-1" title="Próximo a vencer"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-semibold">RD$ <?php echo e(number_format($c->valor_mensual ?? 0, 2)); ?></td>
                                <td>
                                    <?php if($c->incluye_visitas): ?>
                                        <span class="ui-badge ui-badge-primary">
                                            <?php echo e($c->visitas_realizadas ?? 0); ?>/<?php echo e($c->num_visitas_anuales ?? 0); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="ui-badge ui-badge-neutral">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($proximosVencerItem): ?>
                                        <span class="ui-badge ui-badge-warning">
                                            <i class="bi bi-exclamation-circle"></i>
                                            <?php echo e(\App\Models\ContratoMantenimiento::ESTADOS[$c->estado] ?? $c->estado); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="ui-badge ui-badge-<?php echo e($badgeColor); ?>">
                                            <?php echo e(\App\Models\ContratoMantenimiento::ESTADOS[$c->estado] ?? $c->estado); ?>

                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo e(route('climatizacion.contratos.show', $c)); ?>" class="ui-action ui-action-view" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if($c->estado !== 'cancelado'): ?>
                                            <a href="<?php echo e(route('climatizacion.contratos.edit', $c)); ?>" class="ui-action ui-action-edit" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if($c->estado === 'borrador'): ?>
                                            <form action="<?php echo e(route('climatizacion.contratos.activar', $c)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="ui-action" style="background:rgba(34,197,94,.1);color:#16a34a;border-color:rgba(34,197,94,.2);" title="Activar"
                                                        onclick="return confirm('¿Activar este contrato?')">
                                                    <i class="bi bi-play-circle"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if(in_array($c->estado, ['activo','borrador'])): ?>
                                            <form action="<?php echo e(route('climatizacion.contratos.cancelar', $c)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="ui-action ui-action-delete" title="Cancelar"
                                                        onclick="return confirm('¿Cancelar este contrato?')">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if(!in_array($c->estado, ['activo','cancelado'])): ?>
                                            <form action="<?php echo e(route('climatizacion.contratos.destroy', $c)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="ui-action ui-action-delete" title="Eliminar"
                                                        onclick="return confirm('¿Eliminar este contrato?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    No hay contratos registrados.
                                    <a href="<?php echo e(route('climatizacion.contratos.create')); ?>" class="d-block mt-1">Crear el primer contrato</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small">
            Mostrando <?php echo e($contratos->firstItem() ?? 0); ?> - <?php echo e($contratos->lastItem() ?? 0); ?> de <?php echo e($contratos->total()); ?> contratos
        </div>
        <div>
            <?php echo e($contratos->links()); ?>

        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Badge color mapping for responsive child rows
    window.renderEstadoContrato = function(estado, proximo) {
        const labels = <?php echo json_encode(\App\Models\ContratoMantenimiento::ESTADOS, 15, 512) ?>;
        const label = labels[estado] || estado;
        if (proximo) {
            return '<span class="ui-badge ui-badge-warning"><i class="bi bi-exclamation-circle"></i> ' + label + '</span>';
        }
        const map = { borrador: 'neutral', activo: 'success', vencido: 'danger', cancelado: 'neutral' };
        const cls = map[estado] || 'neutral';
        return '<span class="ui-badge ui-badge-' + cls + '">' + label + '</span>';
    };
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/climatizacion/contratos/index.blade.php ENDPATH**/ ?>