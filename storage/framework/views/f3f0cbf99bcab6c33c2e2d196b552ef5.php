<?php $__env->startSection('title', 'Reclamos de Clientes'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.datatable-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-abierto { background: #fee2e2; color: #dc2626; }
    .badge-en_tramite { background: #fef3c7; color: #d97706; }
    .badge-resuelto { background: #dcfce7; color: #16a34a; }
    .badge-rechazado { background: #f1f5f9; color: #64748b; }
    .badge-cerrado { background: #dbeafe; color: #2563eb; }
    .badge-reclamo { background: #fee2e2; color: #dc2626; }
    .badge-queja { background: #fef3c7; color: #d97706; }
    .badge-sugerencia { background: #dbeafe; color: #2563eb; }
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
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Reclamos de Clientes</h4>
                    <div class="ui-header-meta">
                        <a href="<?php echo e(route('sgc.satisfaccion.index')); ?>" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Gestión de reclamos, quejas y sugerencias
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('sgc.satisfaccion.reclamos.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Reclamo
                </a>
            </div>
        </div>
        <div class="mt-3">
            <form method="GET" action="<?php echo e(route('sgc.satisfaccion.reclamos')); ?>" class="d-flex gap-2 align-items-center flex-wrap">
                <div class="d-flex align-items-center gap-1 bg-white bg-opacity-10 rounded-pill px-3 py-1">
                    <i class="bi bi-search text-white-50 small"></i>
                    <input type="text" name="buscar" value="<?php echo e(request('buscar')); ?>" placeholder="Buscar..." class="border-0 bg-transparent text-white small" style="outline:none;width:200px;">
                </div>
                <select name="estado" class="bg-white bg-opacity-10 border-0 text-white small rounded-pill px-3 py-1" style="outline:none;cursor:pointer;">
                    <option value="">Todos los estados</option>
                    <option value="abierto" <?php echo e(request('estado')=='abierto' ? 'selected' : ''); ?>>Abierto</option>
                    <option value="en_tramite" <?php echo e(request('estado')=='en_tramite' ? 'selected' : ''); ?>>En Trámite</option>
                    <option value="resuelto" <?php echo e(request('estado')=='resuelto' ? 'selected' : ''); ?>>Resuelto</option>
                    <option value="cerrado" <?php echo e(request('estado')=='cerrado' ? 'selected' : ''); ?>>Cerrado</option>
                </select>
                <select name="tipo" class="bg-white bg-opacity-10 border-0 text-white small rounded-pill px-3 py-1" style="outline:none;cursor:pointer;">
                    <option value="">Todos los tipos</option>
                    <option value="reclamo" <?php echo e(request('tipo')=='reclamo' ? 'selected' : ''); ?>>Reclamo</option>
                    <option value="queja" <?php echo e(request('tipo')=='queja' ? 'selected' : ''); ?>>Queja</option>
                    <option value="sugerencia" <?php echo e(request('tipo')=='sugerencia' ? 'selected' : ''); ?>>Sugerencia</option>
                </select>
                <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-3">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                <?php if(request('buscar') || request('estado') || request('tipo')): ?>
                    <a href="<?php echo e(route('sgc.satisfaccion.reclamos')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-3 text-white border-white border-opacity-25">
                        <i class="bi bi-x-lg"></i> Limpiar
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <table class="dt-table datatable" id="reclamos-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Tipo</th>
                        <th>Canal</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $reclamos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><code><?php echo e($rec->codigo); ?></code></td>
                        <td><span class="badge-status badge-<?php echo e($rec->tipo); ?>"><?php echo e($rec->tipo_label); ?></span></td>
                        <td><?php echo e($rec->canal_label); ?></td>
                        <td><?php echo e($rec->cliente ? $rec->cliente->nombre : '-'); ?></td>
                        <td><?php echo e($rec->created_at ? $rec->created_at->format('d/m/Y') : '-'); ?></td>
                        <td><span class="badge-status badge-<?php echo e($rec->estado); ?>"><?php echo e($rec->estado_label); ?></span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?php echo e(route('sgc.satisfaccion.reclamos.show', $rec)); ?>" class="btn btn-sm btn-outline-primary rounded-pill" title="Ver">
                                    <i class="bi bi-eye"></i>
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
    const table = document.getElementById('reclamos-table');
    if (table && typeof $.fn.DataTable === 'function') {
        $(table).DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                zeroRecords: 'No se encontraron reclamos',
                infoEmpty: 'Sin registros',
                infoFiltered: '(filtrado de _MAX_ total)'
            },
            columnDefs: [
                { orderable: false, targets: [6] }
            ],
            order: [[0, 'desc']],
            dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip'
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/sgc/satisfaccion/reclamos/index.blade.php ENDPATH**/ ?>