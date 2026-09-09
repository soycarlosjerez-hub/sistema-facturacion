<?php $__env->startSection('title', 'Evaluaciones Periódicas de Proveedores'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.datatable-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .dt-table thead th {
        background: rgba(241,245,249,.8);
        color: #64748b;
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        font-weight: 700;
        padding: .75rem 1rem;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .dt-table tbody td {
        padding: .75rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: .85rem;
    }
    .dt-table tbody tr:last-child td { border-bottom: none; }
    .dt-table tbody tr { transition: background .15s; }
    .dt-table tbody tr:hover { background: rgba(139,92,246,.03); }
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-pendiente { background: #fef3c7; color: #d97706; }
    .badge-completada { background: #dcfce7; color: #16a34a; }
    .badge-en_curso { background: #dbeafe; color: #2563eb; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Evaluaciones Periódicas</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-arrow-left me-1"></i>
                        <a href="<?php echo e(route('sgc.evaluaciones-proveedores.index')); ?>" class="text-white text-decoration-none">Evaluaciones</a>
                        <span class="mx-1">/</span> Periódicas
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('sgc.evaluaciones-proveedores.periodico.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Evaluación
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <table class="dt-table datatable" id="periodico-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Proveedor</th>
                        <th>Período</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Puntuación</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $evaluacionesPeriodicas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><code><?php echo e($eval->codigo); ?></code></td>
                        <td class="fw-semibold"><?php echo e($eval->proveedor?->nombre ?? '—'); ?></td>
                        <td><?php echo e($eval->periodo ?? '—'); ?></td>
                        <td><?php echo e($eval->fecha_inicio ? $eval->fecha_inicio->format('d/m/Y') : '—'); ?></td>
                        <td><?php echo e($eval->fecha_fin ? $eval->fecha_fin->format('d/m/Y') : '—'); ?></td>
                        <td>
                            <?php if($eval->puntuacion): ?>
                                <span class="fw-bold" style="color:<?php echo e($eval->puntuacion >= 70 ? '#16a34a' : ($eval->puntuacion >= 50 ? '#d97706' : '#dc2626')); ?>;">
                                    <?php echo e($eval->puntuacion); ?>/100
                                </span>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td><span class="badge-status badge-<?php echo e($eval->estado ?? 'pendiente'); ?>"><?php echo e(ucfirst($eval->estado ?? 'pendiente')); ?></span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?php echo e(route('sgc.evaluaciones-proveedores.periodico.show', $eval)); ?>" class="btn btn-sm btn-outline-primary rounded-pill" title="Ver">
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
    const table = document.getElementById('periodico-table');
    if (table && typeof $.fn.DataTable === 'function') {
        $(table).DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                zeroRecords: 'No se encontraron evaluaciones periódicas',
                infoEmpty: 'Sin registros',
                infoFiltered: '(filtrado de _MAX_ total)'
            },
            columnDefs: [
                { orderable: false, targets: [7] }
            ],
            order: [[0, 'desc']],
            dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip'
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/sgc/evaluaciones_proveedores/periodico/index.blade.php ENDPATH**/ ?>