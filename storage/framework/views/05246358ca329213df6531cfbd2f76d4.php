<?php $__env->startSection('title', 'No Conformidades'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.datatable-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-abierta { background: #fee2e2; color: #dc2626; }
    .badge-en_analisis { background: #fef3c7; color: #d97706; }
    .badge-en_accion { background: #dbeafe; color: #2563eb; }
    .badge-verificando { background: #e0f2fe; color: #0284c7; }
    .badge-cerrada { background: #dcfce7; color: #16a34a; }
    .badge-grave { background: #fee2e2; color: #dc2626; }
    .badge-menor { background: #fef3c7; color: #d97706; }
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
                    <h4 class="ui-header-title">No Conformidades</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i> Control y seguimiento de no conformidades del SGC
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('sgc.no-conformidades.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva NC
                </a>
            </div>
        </div>
        <div class="mt-3">
            <form method="GET" action="<?php echo e(route('sgc.no-conformidades.index')); ?>" class="d-flex gap-2 align-items-center flex-wrap">
                <div class="d-flex align-items-center gap-1 bg-white bg-opacity-10 rounded-pill px-3 py-1">
                    <i class="bi bi-search text-white-50 small"></i>
                    <input type="text" name="buscar" value="<?php echo e(request('buscar')); ?>" placeholder="Buscar..." class="border-0 bg-transparent text-white small" style="outline:none;width:200px;">
                </div>
                <select name="estado" class="bg-white bg-opacity-10 border-0 text-white small rounded-pill px-3 py-1" style="outline:none;cursor:pointer;">
                    <option value="">Todos los estados</option>
                    <option value="abierta" <?php echo e(request('estado')=='abierta' ? 'selected' : ''); ?>>Abierta</option>
                    <option value="en_analisis" <?php echo e(request('estado')=='en_analisis' ? 'selected' : ''); ?>>En Análisis</option>
                    <option value="en_accion" <?php echo e(request('estado')=='en_accion' ? 'selected' : ''); ?>>En Acción</option>
                    <option value="verificando" <?php echo e(request('estado')=='verificando' ? 'selected' : ''); ?>>Verificando</option>
                    <option value="cerrada" <?php echo e(request('estado')=='cerrada' ? 'selected' : ''); ?>>Cerrada</option>
                </select>
                <select name="gravedad" class="bg-white bg-opacity-10 border-0 text-white small rounded-pill px-3 py-1" style="outline:none;cursor:pointer;">
                    <option value="">Todas las gravedades</option>
                    <option value="mayor" <?php echo e(request('gravedad')=='mayor' ? 'selected' : ''); ?>>Mayor</option>
                    <option value="menor" <?php echo e(request('gravedad')=='menor' ? 'selected' : ''); ?>>Menor</option>
                </select>
                <select name="origen" class="bg-white bg-opacity-10 border-0 text-white small rounded-pill px-3 py-1" style="outline:none;cursor:pointer;">
                    <option value="">Todos los orígenes</option>
                    <option value="auditoria" <?php echo e(request('origen')=='auditoria' ? 'selected' : ''); ?>>Auditoría</option>
                    <option value="proceso_interno" <?php echo e(request('origen')=='proceso_interno' ? 'selected' : ''); ?>>Proceso Interno</option>
                    <option value="reclamo_cliente" <?php echo e(request('origen')=='reclamo_cliente' ? 'selected' : ''); ?>>Reclamo Cliente</option>
                    <option value="observacion_direccion" <?php echo e(request('origen')=='observacion_direccion' ? 'selected' : ''); ?>>Observación Dirección</option>
                </select>
                <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-3">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                <?php if(request('buscar') || request('estado') || request('gravedad') || request('origen')): ?>
                    <a href="<?php echo e(route('sgc.no-conformidades.index')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-3 text-white border-white border-opacity-25">
                        <i class="bi bi-x-lg"></i> Limpiar
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <table class="dt-table datatable" id="nc-table">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Fecha</th>
                        <th>Origen</th>
                        <th>Gravedad</th>
                        <th>Estado</th>
                        <th>Asignado A</th>
                        <th>Fecha Límite</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $noConformidades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><code><?php echo e($nc->numero_label); ?></code></td>
                        <td><?php echo e($nc->fecha_identificacion ? $nc->fecha_identificacion->format('d/m/Y') : ($nc->fecha_ocurrencia ? $nc->fecha_ocurrencia->format('d/m/Y') : '-')); ?></td>
                        <td><span class="badge-status badge-<?php echo e($nc->origen); ?>"><?php echo e($nc->origen_label); ?></span></td>
                        <td><span class="badge-status badge-<?php echo e($nc->gravedad === 'mayor' ? 'grave' : 'menor'); ?>"><?php echo e($nc->gravedad_label); ?></span></td>
                        <td><span class="badge-status badge-<?php echo e($nc->estado); ?>"><?php echo e($nc->estado_label); ?></span></td>
                        <td><?php echo e($nc->asignado_a ? $nc->asignadoA->name : '-'); ?></td>
                        <td>
                            <?php if($nc->fecha_limite): ?>
                                <span class="<?php echo e($nc->es_vencida ? 'text-danger fw-bold' : ''); ?>"><?php echo e($nc->fecha_limite->format('d/m/Y')); ?></span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?php echo e(route('sgc.no-conformidades.show', $nc)); ?>" class="btn btn-sm btn-outline-primary rounded-pill" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo e(route('sgc.no-conformidades.edit', $nc)); ?>" class="btn btn-sm btn-outline-warning rounded-pill" title="Editar">
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
    const table = document.getElementById('nc-table');
    if (table && typeof $.fn.DataTable === 'function') {
        $(table).DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                zeroRecords: 'No se encontraron no conformidades',
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/sgc/no_conformidades/index.blade.php ENDPATH**/ ?>