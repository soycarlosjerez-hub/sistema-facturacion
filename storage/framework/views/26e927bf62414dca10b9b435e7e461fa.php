<?php $__env->startSection('title', 'Catálogo de Diseños'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-images me-2"></i>Catálogo de Diseños</h2>
            <p class="text-muted mb-0">Galería de diseños y obras del estudio</p>
        </div>
        <a href="<?php echo e(route('tattoo.disenos.create')); ?>" class="btn btn-primary rounded-pill px-4 fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Diseño
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm"><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm"><?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-3">
                    <form method="GET" class="row g-2 align-items-center">
                        <div class="col-lg-4">
                            <input type="text" name="busqueda" class="form-control rounded-3" placeholder="Buscar por título..." value="<?php echo e(request('busqueda')); ?>">
                        </div>
                        <div class="col-lg-2">
                            <select name="estilo" class="form-select rounded-3">
                                <option value="">Todos los estilos</option>
                                <?php $__currentLoopData = $estilos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($e); ?>" <?php echo e(request('estilo') == $e ? 'selected' : ''); ?>><?php echo e($e); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <select name="artista_id" class="form-select rounded-3">
                                <option value="">Todos los artistas</option>
                                <?php $__currentLoopData = $artistas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($a->id); ?>" <?php echo e(request('artista_id') == $a->id ? 'selected' : ''); ?>><?php echo e($a->nombre_completo); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <button class="btn btn-primary rounded-pill w-100"><i class="bi bi-funnel me-1"></i> Filtrar</button>
                        </div>
                        <div class="col-lg-2">
                            <a href="<?php echo e(route('tattoo.disenos.index')); ?>" class="btn btn-light rounded-pill w-100"><i class="bi bi-x-lg"></i></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
        $query = \App\Models\TattooDesign::with('artist');
        if ($busqueda = request('busqueda')) $query->where('titulo', 'like', "%{$busqueda}%");
        if ($estilo = request('estilo')) $query->where('estilo', $estilo);
        if ($artistaId = request('artista_id')) $query->where('artist_id', $artistaId);
        $disenos = $query->orderBy('created_at', 'desc')->get();
    ?>

    <div class="row g-3">
        <?php $__empty_0 = true; $__currentLoopData = $disenos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
            <div class="col-lg-3 col-md-4 col-6">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div style="position:relative;padding-top:75%;overflow:hidden;border-radius:16px 16px 0 0;">
                        <?php if($d->imagen_portada): ?>
                            <img src="<?php echo e($d->imagen_portada); ?>" class="position-absolute top-0 start-0 w-100 h-100" style="object-fit:cover;" alt="<?php echo e($d->titulo); ?>">
                        <?php else: ?>
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background:linear-gradient(135deg,#2d1b69,#7c3aed);">
                                <i class="bi bi-brush" style="font-size:3rem;opacity:0.3;"></i>
                            </div>
                        <?php endif; ?>
                        <?php if($d->popular): ?>
                            <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2 rounded-pill px-2">
                                <i class="bi bi-fire"></i> Popular
                            </span>
                        <?php endif; ?>
                        <?php if($d->estilo): ?>
                            <span class="position-absolute bottom-0 start-0 badge bg-dark bg-opacity-75 m-2 rounded-pill">
                                <?php echo e($d->estilo); ?>

                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-1 text-truncate"><?php echo e($d->titulo); ?></h6>
                        <?php if($d->artist): ?>
                            <small class="text-muted d-block"><i class="bi bi-person-badge me-1"></i><?php echo e($d->artist->nombre_completo); ?></small>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="fw-bold" style="color:#a855f7;">
                                RD$<?php echo e(number_format($d->precio_minimo, 0)); ?>

                                <?php if($d->precio_maximo > $d->precio_minimo): ?>
                                    - RD$<?php echo e(number_format($d->precio_maximo, 0)); ?>

                                <?php endif; ?>
                            </span>
                            <div class="btn-group">
                                <a href="<?php echo e(route('tattoo.disenos.edit', $d)); ?>" class="btn btn-sm btn-outline-warning rounded-pill">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="<?php echo e(route('tattoo.disenos.destroy', $d)); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este diseño?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill ms-1"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-images display-1 text-muted opacity-25 d-block mb-3"></i>
                    <p class="text-muted">No hay diseños registrados</p>
                    <a href="<?php echo e(route('tattoo.disenos.create')); ?>" class="btn btn-primary rounded-pill">
                        <i class="bi bi-plus-lg me-1"></i> Primer diseño
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/tattoo/disenos/index.blade.php ENDPATH**/ ?>