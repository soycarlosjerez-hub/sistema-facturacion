<?php $__env->startSection('title', 'Servicio ' . ($servicio->numero_proyecto ?? 'Domótica')); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.info-item {
    background: #f8fafc;
    border-radius: 0.75rem;
    padding: 1rem;
    border-left: 3px solid #06b6d4;
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
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-hdd-network"></i>
                </div>
                <div>
                    <h4 class="ui-header-title"><?php echo e($servicio->numero_proyecto); ?></h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-person me-1"></i>
                        <?php echo e($servicio->cliente->nombre ?? 'Sin cliente'); ?>

                        <span class="divider">·</span>
                        <i class="bi bi-tag me-1"></i>
                        <?php echo e($servicio->tipo_servicio_label ?? ucfirst($servicio->tipo_servicio)); ?>

                        <span class="divider">·</span>
                        <span class="ui-badge ui-badge-<?php echo e(match($servicio->estado) {
                            'pendiente' => 'warning',
                            'programado' => 'info',
                            'en_curso' => 'primary',
                            'completado' => 'success',
                            'cancelado' => 'danger',
                            default => 'secondary'); ?>"><?php echo e($servicio->estado_label); ?></span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('domotica.edit')): ?>
                <?php if(!in_array($servicio->estado, ['completado', 'cancelado'])): ?>
                <a href="<?php echo e(route('domotica.edit', $servicio)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" style="background:rgba(245,158,11,.2);border-color:rgba(245,158,11,.35);">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <?php endif; ?>
                <?php endif; ?>
                <a href="<?php echo e(route('domotica.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
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
                    <div class="ui-stat-label">Subtotal</div>
                    <div class="ui-stat-value">RD$ <?php echo e(number_format($servicio->subtotal ?? 0, 2)); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">ITBIS</div>
                    <div class="ui-stat-value">RD$ <?php echo e(number_format($servicio->itbis ?? 0, 2)); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Descuento</div>
                    <div class="ui-stat-value text-danger">-RD$ <?php echo e(number_format($servicio->descuento ?? 0, 2)); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Total</div>
                    <div class="ui-stat-value text-success">RD$ <?php echo e(number_format($servicio->total ?? 0, 2)); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title"><i class="bi bi-info-circle"></i> Información del Servicio</div>
        <div class="ui-card-body">
            <div class="row g-3">
                <div class="col-md-3"><div class="info-item"><div class="label">Título</div><div class="value"><?php echo e($servicio->titulo); ?></div></div></div>
                <div class="col-md-3"><div class="info-item"><div class="label">Tipo de Servicio</div><div class="value"><?php echo e($servicio->tipo_servicio_label ?? ucfirst($servicio->tipo_servicio)); ?></div></div></div>
                <div class="col-md-3"><div class="info-item"><div class="label">Técnico</div><div class="value"><?php echo e($servicio->tecnico->nombre ?? 'Sin asignar'); ?></div></div></div>
                <div class="col-md-3"><div class="info-item"><div class="label">Presupuesto</div><div class="value">RD$ <?php echo e(number_format($servicio->presupuesto ?? 0, 2)); ?></div></div></div>
                <div class="col-md-4"><div class="info-item"><div class="label">Dirección de Instalación</div><div class="value"><?php echo e($servicio->direccion_instalacion ?? '-'); ?></div></div></div>
                <div class="col-md-4"><div class="info-item"><div class="label">Fecha Programada</div><div class="value"><?php echo e($servicio->fecha_programada ? $servicio->fecha_programada->format('d/m/Y') : '-'); ?></div></div></div>
                <div class="col-md-4"><div class="info-item"><div class="label">Fecha Completada</div><div class="value"><?php echo e($servicio->fecha_completada ? $servicio->fecha_completada->format('d/m/Y') : '-'); ?></div></div></div>
                <div class="col-12"><div class="info-item"><div class="label">Descripción</div><div class="value"><?php echo e($servicio->descripcion ?? '-'); ?></div></div></div>
                <?php if($servicio->notas): ?>
                <div class="col-12"><div class="info-item"><div class="label">Notas</div><div class="value"><?php echo e($servicio->notas); ?></div></div></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('domotica.edit')): ?>
    <?php if(!in_array($servicio->estado, ['completado', 'cancelado'])): ?>
    <div class="ui-card mb-4" style="--delay:.2s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title"><i class="bi bi-sliders"></i> Gestión del Servicio</div>
        <div class="ui-card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="ui-label">Cambiar Estado</label>
                    <form method="POST" action="<?php echo e(route('domotica.cambiar-estado', $servicio)); ?>" class="d-flex gap-2">
                        <?php echo csrf_field(); ?>
                        <select name="estado" class="ui-select">
                            <option value="pendiente" <?php echo e($servicio->estado == 'pendiente' ? 'selected' : ''); ?>>Pendiente</option>
                            <option value="programado" <?php echo e($servicio->estado == 'programado' ? 'selected' : ''); ?>>Programado</option>
                            <option value="en_curso" <?php echo e($servicio->estado == 'en_curso' ? 'selected' : ''); ?>>En Curso</option>
                            <option value="completado" <?php echo e($servicio->estado == 'completado' ? 'selected' : ''); ?>>Completado</option>
                            <option value="cancelado" <?php echo e($servicio->estado == 'cancelado' ? 'selected' : ''); ?>>Cancelado</option>
                        </select>
                        <button type="submit" class="ui-btn ui-btn-primary btn-sm">Actualizar</button>
                    </form>
                </div>
                <div class="col-md-4">
                    <form method="POST" action="<?php echo e(route('domotica.completar', $servicio)); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="ui-btn ui-btn-success btn-sm w-100">
                            <i class="bi bi-check2-circle me-1"></i> Marcar como Completado
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.3s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title"><i class="bi bi-plus-square"></i> Agregar Equipo/Producto</div>
        <div class="ui-card-body">
            <form method="POST" action="<?php echo e(route('domotica.agregar-equipo', $servicio)); ?>" class="row g-2">
                <?php echo csrf_field(); ?>
                <div class="col-lg-3">
                    <select name="producto_id" class="ui-select" required>
                        <option value="">Seleccionar producto...</option>
                        <?php $__currentLoopData = $productos ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($producto->id); ?>"><?php echo e($producto->nombre); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-2">
                    <input type="number" name="cantidad" class="ui-input" placeholder="Cantidad" min="1" value="1" required>
                </div>
                <div class="col-lg-2">
                    <input type="number" name="precio_unitario" class="ui-input" placeholder="Precio unitario" step="0.01" min="0" required>
                </div>
                <div class="col-lg-3">
                    <input type="text" name="ubicacion_instalacion" class="ui-input" placeholder="Ubicación de instalación">
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="ui-btn ui-btn-primary w-100"><i class="bi bi-plus-lg"></i> Agregar</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <div class="ui-card" style="--delay:.4s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title"><i class="bi bi-box-seam"></i> Equipos Instalados</div>
        <div class="ui-card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                            <th>Ubicación</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_0 = true; $__currentLoopData = $servicio->instalaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr>
                            <td><?php echo e($inst->producto->nombre ?? 'Producto eliminado'); ?></td>
                            <td><?php echo e($inst->cantidad); ?></td>
                            <td>RD$ <?php echo e(number_format($inst->precio_unitario ?? 0, 2)); ?></td>
                            <td>RD$ <?php echo e(number_format(($inst->cantidad ?? 0) * ($inst->precio_unitario ?? 0), 2)); ?></td>
                            <td><?php echo e($inst->ubicacion_instalacion ?? '-'); ?></td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary"><?php echo e($inst->estado_label ?? ucfirst($inst->estado ?? 'pendiente')); ?></span>
                            </td>
                            <td class="text-end">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('domotica.edit')): ?>
                                <form action="<?php echo e(route('domotica.eliminar-equipo', [$servicio, $inst])); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Retirar este equipo del servicio?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Retirar"><i class="bi bi-x-lg"></i></button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">No hay equipos registrados en este servicio.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/domotica/show.blade.php ENDPATH**/ ?>