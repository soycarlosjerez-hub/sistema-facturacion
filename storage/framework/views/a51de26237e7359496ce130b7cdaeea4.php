<?php $__env->startSection('title', 'Terminal Tattoo Studio'); ?>

<?php $__env->startSection('fullbleed'); ?>
<style>
:root {
    --tattoo-accent: #a855f7;
    --tattoo-accent2: #d946ef;
    --tattoo-bg: #0f0a1a;
    --tattoo-card: rgba(255,255,255,0.03);
}
body.dark-mode { --tattoo-bg: #0f0a1a; }
.tattoo-app {
    background: var(--tattoo-bg);
    color: #f1f5f9;
    min-height: calc(100vh - 70px);
    padding: 20px;
}
.tattoo-header {
    background: linear-gradient(135deg, #2d1b69, #7c3aed, #a855f7);
    border-radius: 20px;
    padding: 24px 28px;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}
.tattoo-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.3;
}
.stat-card {
    background: var(--tattoo-card);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 16px;
    padding: 20px;
    backdrop-filter: blur(8px);
}
.stat-card .stat-icon {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
}
.stat-card .stat-value { font-size: 1.8rem; font-weight: 800; font-variant-numeric: tabular-nums; }
.stat-card .stat-label { font-size: 0.75rem; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 0.5px; }
.cita-timeline { position: relative; }
.cita-timeline::before {
    content: '';
    position: absolute;
    left: 16px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: rgba(168,85,247,0.2);
}
.cita-item {
    position: relative;
    padding-left: 44px;
    padding-bottom: 16px;
}
.cita-item .cita-dot {
    position: absolute;
    left: 10px;
    top: 4px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2px solid var(--tattoo-accent);
    background: var(--tattoo-bg);
}
.cita-item .cita-dot.completada { background: #10b981; border-color: #10b981; }
.cita-item .cita-dot.en_progreso { background: var(--tattoo-accent); border-color: var(--tattoo-accent); animation: pulse 1.5s infinite; }
.cita-item .cita-dot.cancelada { background: #ef4444; border-color: #ef4444; }
.cita-item .cita-dot.no_show { background: #64748b; border-color: #64748b; }
@keyframes pulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(168,85,247,0.5); } 50% { box-shadow: 0 0 0 8px rgba(168,85,247,0); } }
</style>

<div class="tattoo-app">
    <div class="tattoo-header">
        <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2;">
            <div style="width:56px;height:56px;border-radius:16px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">
                <i class="bi bi-brush"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-0 text-white">Tattoo Studio</h2>
                <p class="text-white text-opacity-75 mb-0"><?php echo e(now()->format('l, d \\d\\e F \\d\\e Y')); ?></p>
            </div>
            <div class="ms-auto d-flex gap-2">
                <a href="<?php echo e(route('tattoo.citas.create')); ?>" class="btn btn-light rounded-pill fw-bold px-4">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Cita
                </a>
                <a href="<?php echo e(route('tattoo.artistas.create')); ?>" class="btn btn-outline-light rounded-pill fw-bold px-4">
                    <i class="bi bi-person-plus me-1"></i> Nuevo Artista
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="stat-icon" style="background:rgba(168,85,247,0.15);color:var(--tattoo-accent);">
                        <i class="bi bi-clock"></i>
                    </div>
                    <div>
                        <div class="stat-value" style="color:var(--tattoo-accent);"><?php echo e($stats['hoy_pendientes']); ?></div>
                        <div class="stat-label">Pendientes Hoy</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="stat-icon" style="background:rgba(16,185,129,0.15);color:#10b981;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="stat-value" style="color:#10b981;"><?php echo e($stats['hoy_completadas']); ?></div>
                        <div class="stat-label">Completadas Hoy</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="stat-icon" style="background:rgba(250,204,21,0.15);color:#facc15;">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <div class="stat-value" style="color:#facc15;">RD$<?php echo e(number_format($stats['hoy_ingresos'], 0)); ?></div>
                        <div class="stat-label">Ingresos Hoy</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="stat-icon" style="background:rgba(59,130,246,0.15);color:#3b82f6;">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="stat-value" style="color:#3b82f6;"><?php echo e($stats['artistas_activos']); ?></div>
                        <div class="stat-label">Artistas Activos</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="stat-card">
                <h5 class="fw-bold mb-3"><i class="bi bi-calendar-day me-2"></i>Citas de Hoy</h5>
                <div class="cita-timeline">
                    <?php $__empty_0 = true; $__currentLoopData = $citasHoy; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <div class="cita-item">
                            <div class="cita-dot <?php echo e($cita->estado); ?>"></div>
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="fw-bold"><?php echo e($cita->fecha_hora_inicio->format('h:i A')); ?></span>
                                    <span class="ms-2"><?php echo e($cita->cliente->nombre); ?></span>
                                </div>
                                <span class="badge rounded-pill px-3 py-1
                                    <?php echo e($cita->estado === 'pendiente' ? 'bg-warning text-dark' : ''); ?>

                                    <?php echo e($cita->estado === 'confirmada' ? 'bg-info' : ''); ?>

                                    <?php echo e($cita->estado === 'en_progreso' ? 'bg-primary' : ''); ?>

                                    <?php echo e($cita->estado === 'completada' ? 'bg-success' : ''); ?>

                                    <?php echo e($cita->estado === 'cancelada' ? 'bg-secondary' : ''); ?>

                                    <?php echo e($cita->estado === 'no_show' ? 'bg-dark' : ''); ?>

                                "><?php echo e(str_replace('_', ' ', ucfirst($cita->estado))); ?></span>
                            </div>
                            <?php if($cita->artista): ?>
                                <small class="text-muted">
                                    <i class="bi bi-person-badge me-1"></i><?php echo e($cita->artista->nombre_completo); ?>

                                    <?php if($cita->lugar_tatuaje): ?> · <?php echo e($cita->lugar_tatuaje); ?> <?php endif; ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-check display-6 d-block mb-2 opacity-50"></i>
                            <p class="mb-0">No hay citas programadas para hoy</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="stat-card">
                <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Próximas Citas</h5>
                <?php $__empty_0 = true; $__currentLoopData = $proximas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-white border-opacity-10">
                        <div>
                            <span class="fw-bold small"><?php echo e($cita->fecha_hora_inicio->format('d/m h:i A')); ?></span>
                            <div class="small text-muted"><?php echo e($cita->cliente->nombre); ?></div>
                        </div>
                        <span class="badge bg-opacity-10 text-capitalize
                            <?php echo e($cita->estado === 'pendiente' ? 'bg-warning text-warning' : ''); ?>

                            <?php echo e($cita->estado === 'confirmada' ? 'bg-info text-info' : ''); ?>

                        "><?php echo e($cita->estado); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <div class="text-center py-3 text-muted"><small>Sin próximas citas</small></div>
                <?php endif; ?>
                <?php if(count($proximas) > 0): ?>
                    <a href="<?php echo e(route('tattoo.citas.index')); ?>" class="btn btn-sm btn-outline-light w-100 mt-2 rounded-pill">Ver todas</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="stat-card">
                <h5 class="fw-bold mb-3"><i class="bi bi-images me-2"></i>Diseños Destacados</h5>
                <div class="row g-2">
                    <?php $__empty_0 = true; $__currentLoopData = $disenos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <div class="col-6">
                            <div class="border border-white border-opacity-10 rounded-3 p-2 text-center" style="background:rgba(255,255,255,0.03);">
                                <?php if($d->imagen_portada): ?>
                                    <img src="<?php echo e($d->imagen_portada); ?>" class="rounded-2 mb-1" style="width:100%;height:60px;object-fit:cover;" alt="">
                                <?php else: ?>
                                    <div class="rounded-2 mb-1 d-flex align-items-center justify-content-center" style="width:100%;height:60px;background:rgba(168,85,247,0.1);">
                                        <i class="bi bi-brush text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <small class="d-block text-truncate fw-semibold"><?php echo e($d->titulo); ?></small>
                                <small class="text-muted"><?php echo e($d->estilo); ?></small>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <div class="col-12 text-center py-3 text-muted"><small>Sin diseños aún</small></div>
                    <?php endif; ?>
                </div>
                <?php if(count($disenos) > 0): ?>
                    <a href="<?php echo e(route('tattoo.disenos.index')); ?>" class="btn btn-sm btn-outline-light w-100 mt-2 rounded-pill">Catálogo completo</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/tattoo/index.blade.php ENDPATH**/ ?>