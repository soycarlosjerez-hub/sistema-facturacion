<?php $__env->startSection('title', 'Gestión de Almacenes'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#14b8a6;--accent-rgb:20,184,166;--accent-hover:#0d9488;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Gestión de Almacenes</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i>
                        <span><?php echo e($almacenes->total()); ?> registro(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('almacenes.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Almacén
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <div class="row g-2 align-items-end">
                <div class="col-lg-6">
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="buscar-local" class="ui-input" placeholder="Buscar almacén por nombre..." autocomplete="off">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Almacenes -->
    <div class="row g-4">
        <?php $__empty_1 = true; $__currentLoopData = $almacenes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-md-6 col-lg-4">
            <div class="ui-card h-100" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-4" style="width:50px;height:50px;background:rgba(var(--accent-rgb),0.1);color:var(--accent);">
                            <i class="bi bi-building fs-4"></i>
                        </div>
                        <div class="d-flex gap-1">
                            <a href="<?php echo e(route('almacenes.edit', $a->id)); ?>" class="ui-action ui-action-edit" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="<?php echo e(route('almacenes.destroy', $a->id)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="ui-action ui-action-delete" onclick="event.preventDefault();UI.confirm.delete('<?php echo e(route('almacenes.destroy', $a->id)); ?>', '<?php echo e(addslashes($a->nombre)); ?>')" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark mb-1"><?php echo e($a->nombre); ?></h5>
                    <div class="d-flex align-items-center text-muted small mb-1">
                        <i class="bi bi-geo-alt me-1"></i> <?php echo e($a->ubicacion ?? 'Ubicación no especificada'); ?>

                    </div>
                    <?php if($a->sucursal): ?>
                    <div class="d-flex align-items-center text-muted small mb-3">
                        <i class="bi bi-building me-1"></i> <?php echo e($a->sucursal->nombre); ?>

                    </div>
                    <?php endif; ?>

                    <div class="ui-stat" style="--delay:0s">
                        <div class="ui-stat-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="ui-stat-label">Productos</div>
                                <div class="ui-stat-sub"><i class="bi bi-box-seam me-1"></i>con stock</div>
                            </div>
                            <div class="ui-stat-value fs-4"><?php echo e(\App\Models\AlmacenMovimiento::where('almacen_id', $a->id)->selectRaw('COUNT(DISTINCT producto_id) as total')->value('total')); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
            <div class="ui-empty-state">
                <i class="bi bi-building-x"></i>
                <p>No hay almacenes configurados en el sistema.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('buscar-local').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const cards = document.querySelectorAll('.col-md-6.col-lg-4');

        cards.forEach(card => {
            const name = card.querySelector('h5').innerText.toLowerCase();
            const location = card.querySelector('.text-muted').innerText.toLowerCase();
            if (name.includes(query) || location.includes(query)) {
                card.classList.remove('d-none');
            } else {
                card.classList.add('d-none');
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/almacenes/index.blade.php ENDPATH**/ ?>