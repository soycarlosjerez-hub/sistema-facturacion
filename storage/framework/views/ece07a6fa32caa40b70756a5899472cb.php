<?php $__env->startSection('title', 'Mejora Continua'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.datatable-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-propuesta { background: #f1f5f9; color: #64748b; }
    .badge-evaluando { background: #dbeafe; color: #2563eb; }
    .badge-aprobada { background: #e0f2fe; color: #0284c7; }
    .badge-en_curso { background: #fef3c7; color: #d97706; }
    .badge-completada { background: #dcfce7; color: #16a34a; }
    .badge-verificada { background: #d1fae5; color: #059669; }
    .badge-cerrada { background: #f1f5f9; color: #475569; }
    .badge-baja { background: #dbeafe; color: #2563eb; }
    .badge-media { background: #fef3c7; color: #d97706; }
    .badge-alta { background: #fed7aa; color: #ea580c; }
    .badge-urgente { background: #fee2e2; color: #dc2626; }
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
                    <i class="bi bi-arrow-up-circle"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Mejora Continua</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i> Gestión de mejoras continuas del SGC
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('sgc.mejora.propuestas')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill me-2">
                    <i class="bi bi-lightbulb me-1"></i> Propuestas
                </a>
                <a href="<?php echo e(route('sgc.mejora.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Mejora
                </a>
            </div>
        </div>
        <div class="mt-3">
            <form method="GET" action="<?php echo e(route('sgc.mejora.index')); ?>" class="d-flex gap-2 align-items-center flex-wrap">
                <div class="d-flex align-items-center gap-1 bg-white bg-opacity-10 rounded-pill px-3 py-1">
                    <i class="bi bi-search text-white-50 small"></i>
                    <input type="text" name="buscar" value="<?php echo e(request('buscar')); ?>" placeholder="Buscar..." class="border-0 bg-transparent text-white small" style="outline:none;width:200px;">
                </div>
                <select name="fase" class="bg-white bg-opacity-10 border-0 text-white small rounded-pill px-3 py-1" style="outline:none;cursor:pointer;">
                    <option value="">Todas las fases</option>
                    <option value="propuesta" <?php echo e(request('fase')=='propuesta' ? 'selected' : ''); ?>>Propuesta</option>
                    <option value="evaluando" <?php echo e(request('fase')=='evaluando' ? 'selected' : ''); ?>>Evaluando</option>
                    <option value="aprobada" <?php echo e(request('fase')=='aprobada' ? 'selected' : ''); ?>>Aprobada</option>
                    <option value="en_curso" <?php echo e(request('fase')=='en_curso' ? 'selected' : ''); ?>>En Curso</option>
                    <option value="completada" <?php echo e(request('fase')=='completada' ? 'selected' : ''); ?>>Completada</option>
                    <option value="verificada" <?php echo e(request('fase')=='verificada' ? 'selected' : ''); ?>>Verificada</option>
                    <option value="cerrada" <?php echo e(request('fase')=='cerrada' ? 'selected' : ''); ?>>Cerrada</option>
                </select>
                <select name="prioridad" class="bg-white bg-opacity-10 border-0 text-white small rounded-pill px-3 py-1" style="outline:none;cursor:pointer;">
                    <option value="">Todas las prioridades</option>
                    <option value="baja" <?php echo e(request('prioridad')=='baja' ? 'selected' : ''); ?>>Baja</option>
                    <option value="media" <?php echo e(request('prioridad')=='media' ? 'selected' : ''); ?>>Media</option>
                    <option value="alta" <?php echo e(request('prioridad')=='alta' ? 'selected' : ''); ?>>Alta</option>
                    <option value="urgente" <?php echo e(request('prioridad')=='urgente' ? 'selected' : ''); ?>>Urgente</option>
                </select>
                <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-3">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                <?php if(request('buscar') || request('fase') || request('prioridad')): ?>
                    <a href="<?php echo e(route('sgc.mejora.index')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-3 text-white border-white border-opacity-25">
                        <i class="bi bi-x-lg"></i> Limpiar
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <table class="dt-table datatable" id="mejora-table">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Título</th>
                        <th>Origen</th>
                        <th>Prioridad</th>
                        <th>Fase</th>
                        <th>Responsable</th>
                        <th>Fecha Límite</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $mejoras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mejora): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><code><?php echo e($mejora->numero_label); ?></code></td>
                        <td><?php echo e($mejora->titulo_truncado); ?></td>
                        <td><?php echo e(ucfirst($mejora->origen ?? '-')); ?></td>
                        <td><span class="badge-status badge-<?php echo e($mejora->prioridad); ?>"><?php echo e($mejora->prioridad_label); ?></span></td>
                        <td><span class="badge-status badge-<?php echo e($mejora->fase); ?>"><?php echo e($mejora->fase_label); ?></span></td>
                        <td><?php echo e($mejora->responsable_label); ?></td>
                        <td><?php echo e($mejora->fecha_limite ? $mejora->fecha_limite->format('d/m/Y') : '-'); ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?php echo e(route('sgc.mejora.show', $mejora)); ?>" class="btn btn-sm btn-outline-primary rounded-pill" title="Ver">
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
    const table = document.getElementById('mejora-table');
    if (table && typeof $.fn.DataTable === 'function') {
        $(table).DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                zeroRecords: 'No se encontraron mejoras',
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/sgc/mejora_continua/index.blade.php ENDPATH**/ ?>