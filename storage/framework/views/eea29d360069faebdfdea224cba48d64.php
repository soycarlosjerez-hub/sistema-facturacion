<?php $__env->startSection('title', 'Artistas / Tatuadores'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-person-badge me-2"></i>Artistas / Tatuadores</h2>
            <p class="text-muted mb-0">Gestiona los artistas del estudio</p>
        </div>
        <a href="<?php echo e(route('tattoo.artistas.create')); ?>" class="btn btn-primary rounded-pill px-4 fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Artista
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm"><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-muted small text-uppercase">
                            <th class="ps-4">Artista</th>
                            <th>Especialidad</th>
                            <th>Tipo</th>
                            <th>Comisión</th>
                            <th>Experiencia</th>
                            <th>Instagram</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_0 = true; $__currentLoopData = $artistas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <?php if($a->foto_perfil): ?>
                                        <img src="<?php echo e($a->foto_perfil); ?>" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;" alt="">
                                    <?php else: ?>
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:rgba(168,85,247,0.15);color:#a855f7;font-weight:700;">
                                            <?php echo e(strtoupper(substr($a->nombre_completo, 0, 1))); ?>

                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <span class="fw-semibold"><?php echo e($a->nombre_completo); ?></span>
                                        <?php if($a->user_id): ?>
                                            <small class="text-muted d-block" style="font-size:.7rem;">
                                                <i class="bi bi-person-fill-gear"></i> <?php echo e($a->user->name ?? '—'); ?>

                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo e($a->especialidad ?: '—'); ?></td>
                            <td>
                                <span class="badge rounded-pill <?php echo e($a->tipo === 'empleado' ? 'bg-primary' : 'bg-warning text-dark'); ?>">
                                    <?php echo e($a->tipo === 'empleado' ? 'Empleado' : 'Externo'); ?>

                                </span>
                            </td>
                            <td><span class="fw-bold"><?php echo e(number_format($a->comision_pct, 0)); ?>%</span></td>
                            <td><?php echo e($a->experiencia_anos); ?> años</td>
                            <td>
                                <?php if($a->instagram): ?>
                                    <a href="https://instagram.com/<?php echo e($a->instagram); ?>" target="_blank" class="text-decoration-none small">
                                        <i class="bi bi-instagram"></i> {{ $a->instagram }}
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-3 <?php echo e($a->activo ? 'bg-success' : 'bg-secondary'); ?>">
                                    <?php echo e($a->activo ? 'Activo' : 'Inactivo'); ?>

                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="<?php echo e(route('tattoo.artistas.show', $a)); ?>" class="btn btn-sm btn-outline-info rounded-pill" title="Ver perfil">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo e(route('tattoo.artistas.edit', $a)); ?>" class="btn btn-sm btn-outline-warning rounded-pill" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="<?php echo e(route('tattoo.artistas.toggle-status', $a)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill" title="<?php echo e($a->activo ? 'Desactivar' : 'Activar'); ?>">
                                        <i class="bi <?php echo e($a->activo ? 'bi-pause-circle' : 'bi-play-circle'); ?>"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-person-badge display-4 text-muted opacity-25 d-block mb-3"></i>
                                <p class="text-muted">No hay artistas registrados</p>
                                <a href="<?php echo e(route('tattoo.artistas.create')); ?>" class="btn btn-primary rounded-pill">
                                    <i class="bi bi-plus-lg me-1"></i> Crear primer artista
                                </a>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/tattoo/artistas/index.blade.php ENDPATH**/ ?>