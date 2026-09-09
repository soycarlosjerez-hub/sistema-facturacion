<?php $__env->startSection('title', 'Obras de Arte'); ?>
<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-images"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-collection me-1"></i>CATÁLOGO
                    </span>
                    <h2 class="fw-bold mb-0 text-white">Obras de Arte</h2>
                    <p class="mb-0 opacity-75">Catálogo de obras, esculturas y piezas de la galería</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('arte.obras.create')); ?>" class="ui-btn ui-btn-primary rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Obra
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-card-body">
            <form method="GET" action="<?php echo e(route('arte.obras.index')); ?>" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="ui-label" for="q">Buscar</label>
                    <input type="text" name="q" id="q" class="ui-input" value="<?php echo e(request('q')); ?>" placeholder="Título, técnica, artista...">
                </div>
                <div class="col-md-3">
                    <label class="ui-label" for="estado">Estado</label>
                    <select name="estado" id="estado" class="ui-select">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = ['vendida' => 'Vendida', 'disponible' => 'Disponible', 'en_exhibicion' => 'En Exhibición', 'en_consulta' => 'En Consulta']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($k); ?>" <?php echo e(request('estado') == $k ? 'selected' : ''); ?>><?php echo e($v); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="ui-label" for="artista">Artista</label>
                    <select name="artista" id="artista" class="ui-select">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $artistas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($a->id); ?>" <?php echo e(request('artista') == $a->id ? 'selected' : ''); ?>><?php echo e($a->nombre); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="ui-label" for="coleccion">Colección</label>
                    <select name="coleccion" id="coleccion" class="ui-select">
                        <option value="">Todas</option>
                        <?php $__currentLoopData = $colecciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->id); ?>" <?php echo e(request('coleccion') == $c->id ? 'selected' : ''); ?>><?php echo e($c->nombre); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="ui-btn ui-btn-solid rounded-pill w-100" type="submit" title="Filtrar"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.2s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Obra</th>
                            <th>Artista</th>
                            <th>Colección</th>
                            <th>Técnica</th>
                            <th>Estado</th>
                            <th class="text-end">Precio Venta</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_0 = true; $__currentLoopData = $obras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $obra): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr>
                            <td class="ps-4"><?php echo e($obra->id); ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if($obra->imagen): ?>
                                        <img src="<?php echo e(asset('storage/' . $obra->imagen)); ?>" width="36" height="36" class="rounded-2 object-fit-cover" alt="<?php echo e($obra->titulo); ?>">
                                    <?php else: ?>
                                        <div class="bg-light rounded-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;"><i class="bi bi-image text-muted"></i></div>
                                    <?php endif; ?>
                                    <span class="fw-semibold"><?php echo e($obra->titulo); ?></span>
                                </div>
                            </td>
                            <td><?php echo e($obra->artista?->nombre ?? '—'); ?></td>
                            <td><?php echo e($obra->coleccion?->nombre ?? '—'); ?></td>
                            <td><?php echo e($obra->tecnica ?? '—'); ?></td>
                            <td><span class="badge bg-<?php echo e($obra->estado_badge_class); ?> rounded-pill"><?php echo e($obra->estado_label); ?></span></td>
                            <td class="text-end fw-bold">RD$ <?php echo e(number_format($obra->precio_venta, 2)); ?></td>
                            <td class="text-end text-nowrap pe-4">
                                <a href="<?php echo e(route('arte.obras.show', $obra)); ?>" class="ui-action ui-action-view" title="Ver"><i class="bi bi-eye"></i></a>
                                <a href="<?php echo e(route('arte.obras.edit', $obra)); ?>" class="ui-action ui-action-edit" title="Editar"><i class="bi bi-pencil"></i></a>
                                <form action="<?php echo e(route('arte.obras.destroy', $obra)); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar la obra "<?php echo e(addslashes($obra->titulo)); ?>"?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="ui-action ui-action-delete" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">No hay obras registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="p-3">
            <?php echo e($obras->links()); ?>

        </div>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/arte/obras/index.blade.php ENDPATH**/ ?>