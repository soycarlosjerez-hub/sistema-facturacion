<?php $__env->startSection('title', 'Objetivos de Calidad'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.datatable-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-en_curso { background: #dbeafe; color: #2563eb; }
    .badge-cumplido { background: #dcfce7; color: #16a34a; }
    .badge-no_cumplido { background: #fee2e2; color: #dc2626; }
    .badge-atrasado { background: #fef3c7; color: #d97706; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-bullseye"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Objetivos de Calidad</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i> Seguimiento de objetivos e indicadores de calidad
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('sgc.objetivos.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Objetivo
                </a>
            </div>
        </div>
        <div class="mt-3">
            <form method="GET" action="<?php echo e(route('sgc.objetivos.index')); ?>" class="d-flex gap-2 align-items-center flex-wrap">
                <div class="d-flex align-items-center gap-1 bg-white bg-opacity-10 rounded-pill px-3 py-1">
                    <i class="bi bi-search text-white-50 small"></i>
                    <input type="text" name="buscar" value="<?php echo e(request('buscar')); ?>" placeholder="Buscar..." class="border-0 bg-transparent text-white small" style="outline:none;width:200px;">
                </div>
                <select name="estado" class="bg-white bg-opacity-10 border-0 text-white small rounded-pill px-3 py-1" style="outline:none;cursor:pointer;">
                    <option value="">Todos los estados</option>
                    <option value="en_curso" <?php echo e(request('estado')=='en_curso' ? 'selected' : ''); ?>>En Curso</option>
                    <option value="cumplido" <?php echo e(request('estado')=='cumplido' ? 'selected' : ''); ?>>Cumplido</option>
                    <option value="no_cumplido" <?php echo e(request('estado')=='no_cumplido' ? 'selected' : ''); ?>>No Cumplido</option>
                    <option value="atrasado" <?php echo e(request('estado')=='atrasado' ? 'selected' : ''); ?>>Atrasado</option>
                </select>
                <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-3">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                <?php if(request('buscar') || request('estado')): ?>
                    <a href="<?php echo e(route('sgc.objetivos.index')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-3 text-white border-white border-opacity-25">
                        <i class="bi bi-x-lg"></i> Limpiar
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <table class="dt-table datatable" id="objetivos-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Título</th>
                        <th>Indicador</th>
                        <th>Meta</th>
                        <th>Valor Actual</th>
                        <th>Cumplimiento</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $objetivos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $obj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><code><?php echo e($obj->codigo); ?></code></td>
                        <td><?php echo e(Str::limit($obj->titulo, 40)); ?></td>
                        <td><?php echo e($obj->indicador ?? '-'); ?></td>
                        <td><?php echo e($obj->meta); ?></td>
                        <td><?php echo e($obj->valor_actual ?? '-'); ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:6px;max-width:80px;">
                                    <div class="progress-bar <?php echo e($obj->cumplimiento_bar); ?>" style="width:<?php echo e(min($obj->cumplimiento ?? 0, 100)); ?>%"></div>
                                </div>
                                <small class="text-muted"><?php echo e($obj->cumplimiento ?? 0); ?>%</small>
                            </div>
                        </td>
                        <td><span class="badge-status badge-<?php echo e($obj->estado); ?>"><?php echo e($obj->estado_label); ?></span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?php echo e(route('sgc.objetivos.show', $obj)); ?>" class="btn btn-sm btn-outline-primary rounded-pill" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo e(route('sgc.objetivos.edit', $obj)); ?>" class="btn btn-sm btn-outline-warning rounded-pill" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('objetivos-table');
    if (table && typeof $.fn.DataTable === 'function') {
        $(table).DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                zeroRecords: 'No se encontraron objetivos',
                infoEmpty: 'Sin registros',
                infoFiltered: '(filtrado de _MAX_ total)'
            },
            columnDefs: [
                { orderable: false, targets: [7] }
            ],
            order: [[0, 'asc']],
            dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip'
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/sgc/objetivos/index.blade.php ENDPATH**/ ?>