<?php $__env->startSection('title', $artista->nombre_completo); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <a href="<?php echo e(route('tattoo.artistas.index')); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-3">
        <i class="bi bi-arrow-left me-1"></i> Volver a Artistas
    </a>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body text-center p-4">
                    <?php if($artista->foto_perfil): ?>
                        <img src="<?php echo e($artista->foto_perfil); ?>" class="rounded-circle mb-3" style="width:120px;height:120px;object-fit:cover;" alt="">
                    <?php else: ?>
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:120px;height:120px;background:rgba(168,85,247,0.15);color:#a855f7;font-size:2.5rem;font-weight:700;">
                            <?php echo e(strtoupper(substr($artista->nombre_completo, 0, 1))); ?>

                        </div>
                    <?php endif; ?>
                    <h4 class="fw-bold"><?php echo e($artista->nombre_completo); ?></h4>
                    <p class="text-muted"><?php echo e($artista->especialidad ?: 'Sin especialidad'); ?></p>
                    <span class="badge rounded-pill px-3 <?php echo e($artista->tipo === 'empleado' ? 'bg-primary' : 'bg-warning text-dark'); ?>">
                        <?php echo e($artista->tipo === 'empleado' ? 'Empleado' : 'Externo'); ?>

                    </span>

                    <hr class="my-3">
                    <div class="text-start small">
                        <?php if($artista->experiencia_anos > 0): ?>
                            <div class="mb-2"><i class="bi bi-clock me-2 text-muted"></i><?php echo e($artista->experiencia_anos); ?> años de experiencia</div>
                        <?php endif; ?>
                        <?php if($artista->telefono): ?>
                            <div class="mb-2"><i class="bi bi-telephone me-2 text-muted"></i><?php echo e($artista->telefono); ?></div>
                        <?php endif; ?>
                        <?php if($artista->whatsapp): ?>
                            <div class="mb-2"><i class="bi bi-whatsapp me-2 text-muted"></i><?php echo e($artista->whatsapp); ?></div>
                        <?php endif; ?>
                        <?php if($artista->instagram): ?>
                            <div class="mb-2">
                                <i class="bi bi-instagram me-2 text-muted"></i>
                                <a href="https://instagram.com/<?php echo e($artista->instagram); ?>" target="_blank">{{ $artista->instagram }}</a>
                            </div>
                        <?php endif; ?>
                        <div class="mb-2"><i class="bi bi-percent me-2 text-muted"></i>Comisión: <strong><?php echo e(number_format($artista->comision_pct, 0)); ?>%</strong></div>
                    </div>

                    <?php if($artista->biografia): ?>
                        <hr class="my-3">
                        <p class="small text-muted"><?php echo e($artista->biografia); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart me-2"></i>Resumen</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-4">
                            <div class="border rounded-3 p-3 text-center">
                                <div class="fw-bold fs-4" style="color:#a855f7;"><?php echo e($artista->citas_completadas); ?></div>
                                <small class="text-muted">Citas Completadas</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded-3 p-3 text-center">
                                <div class="fw-bold fs-4" style="color:#10b981;">RD$<?php echo e(number_format($artista->ganancia_total, 0)); ?></div>
                                <small class="text-muted">Ganancias Generadas</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded-3 p-3 text-center">
                                <div class="fw-bold fs-4" style="color:#3b82f6;"><?php echo e($artista->designs->count()); ?></div>
                                <small class="text-muted">Diseños</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Últimas Citas</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <?php $__empty_0 = true; $__currentLoopData = $artista->appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <span class="fw-semibold"><?php echo e($cita->cliente->nombre); ?></span>
                                <small class="text-muted d-block"><?php echo e($cita->fecha_hora_inicio->format('d/m/Y h:i A')); ?></small>
                            </div>
                            <div class="text-end">
                                <span class="badge rounded-pill
                                    <?php echo e($cita->estado === 'completada' ? 'bg-success' : ''); ?>

                                    <?php echo e($cita->estado === 'cancelada' ? 'bg-secondary' : ''); ?>

                                    <?php echo e(in_array($cita->estado, ['pendiente','confirmada']) ? 'bg-warning text-dark' : ''); ?>

                                "><?php echo e(ucfirst(str_replace('_', ' ', $cita->estado))); ?></span>
                                <small class="d-block text-muted">RD$<?php echo e(number_format($cita->total_final, 0)); ?></small>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <p class="text-muted text-center py-3 mb-0">Sin citas registradas</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/tattoo/artistas/show.blade.php ENDPATH**/ ?>