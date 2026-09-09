<?php $__env->startSection('title', 'Garantía #' . $garantia->id); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.info-item {
    background: #f8fafc;
    border-radius: 0.75rem;
    padding: 1rem;
    border-left: 3px solid #10b981;
}
.info-item .label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    font-weight: 700;
    margin-bottom: 4px;
}
.info-item .value {
    font-weight: 600;
    color: #1e293b;
}
body.dark-mode .info-item { background: rgba(30,41,59,.8); }
body.dark-mode .info-item .label { color: #94a3b8; }
body.dark-mode .info-item .value { color: #f1f5f9; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Garantía #<?php echo e($garantia->id); ?></h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-box me-1"></i>
                        <?php echo e($garantia->equipo ? $garantia->equipo->serial_imei . ' - ' . $garantia->equipo->modelo : 'Sin equipo'); ?>

                        <span class="divider">·</span>
                        <i class="bi bi-receipt me-1"></i>
                        <?php echo e($garantia->ordenReparacion ? $garantia->ordenReparacion->numero_orden : 'Sin orden'); ?>

                        <span class="divider">·</span>
                        <span class="ui-badge ui-badge-<?php echo e(match($garantia->estado) {
                            'vigente' => 'success',
                            'expirada' => 'danger',
                            'reclamada' => 'warning',
                            'rechazada' => 'danger',
                            'cancelada' => 'secondary',
                            default => 'secondary'); ?>"><?php echo e($garantia->estado_label ?? ucfirst($garantia->estado)); ?></span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('garantias.edit')): ?>
                <?php if(in_array($garantia->estado, ['vigente', 'activa'])): ?>
                <a href="<?php echo e(route('garantias.edit', $garantia)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" style="background:rgba(245,158,11,.2);border-color:rgba(245,158,11,.35);">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <?php endif; ?>
                <?php endif; ?>
                <a href="<?php echo e(route('garantias.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.05s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Cobertura</div>
                    <div class="ui-stat-value">RD$ <?php echo e(number_format($garantia->cobertura ?? 0, 2)); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Inicio</div>
                    <div class="ui-stat-value" style="font-size:1.1rem;"><?php echo e($garantia->fecha_inicio ? $garantia->fecha_inicio->format('d/m/Y') : '-'); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Vence</div>
                    <div class="ui-stat-value" style="font-size:1.1rem;"><?php echo e($garantia->fecha_fin ? $garantia->fecha_fin->format('d/m/Y') : '-'); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Días Restantes</div>
                    <div class="ui-stat-value <?php echo e($garantia->dias_restantes <= 7 ? 'text-danger' : 'text-success'); ?>"><?php echo e($garantia->dias_restantes); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title"><i class="bi bi-info-circle"></i> Información</div>
        <div class="ui-card-body">
            <div class="row g-3">
                <div class="col-md-3"><div class="info-item"><div class="label">Tipo</div><div class="value"><?php echo e($garantia->tipo_label ?? ucfirst($garantia->tipo)); ?></div></div></div>
                <div class="col-md-3"><div class="info-item"><div class="label">Equipo</div><div class="value"><?php echo e($garantia->equipo ? $garantia->equipo->serial_imei : '-'); ?></div></div></div>
                <div class="col-md-3"><div class="info-item"><div class="label">Modelo</div><div class="value"><?php echo e($garantia->equipo ? $garantia->equipo->modelo : '-'); ?></div></div></div>
                <div class="col-md-3"><div class="info-item"><div class="label">Cliente</div><div class="value"><?php echo e($garantia->ordenReparacion?->cliente?->nombre ?? '-'); ?></div></div></div>
                <div class="col-12">
                    <div class="info-item"><div class="label">Términos y Condiciones</div><div class="value" style="white-space:pre-line;"><?php echo e($garantia->terminos_condiciones ?? 'Sin condiciones registradas.'); ?></div></div>
                </div>
            </div>
        </div>
    </div>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('garantias.edit')): ?>
    <?php if($garantia->esta_vigente && in_array($garantia->estado, ['vigente', 'activa'])): ?>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="ui-card mb-4" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title"><i class="bi bi-calendar-plus"></i> Extender Garantía</div>
                <div class="ui-card-body">
                    <form method="POST" action="<?php echo e(route('garantias.extender', $garantia)); ?>" class="row g-2">
                        <?php echo csrf_field(); ?>
                        <div class="col-lg-6">
                            <input type="number" name="meses_adicionales" class="ui-input" placeholder="Meses adicionales" min="1" max="60" required>
                        </div>
                        <div class="col-lg-6">
                            <input type="text" name="motivo" class="ui-input" placeholder="Motivo (opcional)">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="ui-btn ui-btn-success btn-sm">
                                <i class="bi bi-calendar-plus me-1"></i> Extender
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="ui-card mb-4" style="--delay:.3s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title"><i class="bi bi-exclamation-triangle"></i> Procesar Reclamo</div>
                <div class="ui-card-body">
                    <form method="POST" action="<?php echo e(route('garantias.reclamar', $garantia)); ?>" class="row g-2">
                        <?php echo csrf_field(); ?>
                        <div class="col-12">
                            <textarea name="descripcion_reclamo" class="ui-input" rows="2" placeholder="Describe el reclamo (mín. 10 caracteres)" required></textarea>
                        </div>
                        <div class="col-12">
                            <input type="text" name="accion_tomada" class="ui-input" placeholder="Acción tomada (opcional)">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="ui-btn btn-outline-danger btn-sm">
                                <i class="bi bi-exclamation-triangle me-1"></i> Registrar Reclamo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/garantias/show.blade.php ENDPATH**/ ?>