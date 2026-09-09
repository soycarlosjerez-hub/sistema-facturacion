<?php $__env->startSection('title', 'Comisiones de Vendedores'); ?>

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
    .dt-table tbody tr:hover { background: rgba(139,92,241,.03); }
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
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Comisiones de Vendedores</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-graph-up me-1"></i> Reporte de comisiones por ventas
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('reportes.index')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
        <div class="mt-3">
            <form method="GET" action="<?php echo e(route('reportes.comisiones-vendedores')); ?>" class="d-flex gap-2 align-items-center flex-wrap">
                <div class="d-flex align-items-center gap-1 bg-white bg-opacity-10 rounded-pill px-3 py-1">
                    <i class="bi bi-calendar text-white-50 small"></i>
                    <input type="date" name="fecha_desde" value="<?php echo e(request('fecha_desde')); ?>" class="border-0 bg-transparent text-white small" style="outline:none;">
                </div>
                <div class="d-flex align-items-center gap-1 bg-white bg-opacity-10 rounded-pill px-3 py-1">
                    <i class="bi bi-calendar text-white-50 small"></i>
                    <input type="date" name="fecha_hasta" value="<?php echo e(request('fecha_hasta')); ?>" class="border-0 bg-transparent text-white small" style="outline:none;">
                </div>
                <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-3">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                <?php if(request('fecha_desde') || request('fecha_hasta')): ?>
                    <a href="<?php echo e(route('reportes.comisiones-vendedores')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-3 text-white border-white border-opacity-25">
                        <i class="bi bi-x-lg"></i> Limpiar
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body text-center py-3">
                    <i class="bi bi-people" style="font-size:1.5rem;color:var(--accent);"></i>
                    <div class="detail-label mt-2">Vendedores</div>
                    <div class="detail-value fw-bold fs-4"><?php echo e($vendedores->count() ?? 0); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent" style="background:#22c55e"></div>
                <div class="ui-card-body text-center py-3">
                    <i class="bi bi-receipt" style="font-size:1.5rem;color:#22c55e;"></i>
                    <div class="detail-label mt-2">Total Ventas</div>
                    <div class="detail-value fw-bold fs-4">RD$ <?php echo e(number_format($totalVentas ?? 0, 2)); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="background:#3b82f6"></div>
                <div class="ui-card-body text-center py-3">
                    <i class="bi bi-percent" style="font-size:1.5rem;color:#3b82f6;"></i>
                    <div class="detail-label mt-2">Comisión Promedio</div>
                    <div class="detail-value fw-bold fs-4"><?php echo e($comisionPromedio ?? 0); ?>%</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-card" style="--delay:.25s">
                <div class="ui-card-accent" style="background:#f59e0b"></div>
                <div class="ui-card-body text-center py-3">
                    <i class="bi bi-cash-stack" style="font-size:1.5rem;color:#f59e0b;"></i>
                    <div class="detail-label mt-2">Total Comisiones</div>
                    <div class="detail-value fw-bold fs-4">RD$ <?php echo e(number_format($totalComisiones ?? 0, 2)); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.3s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <table class="dt-table datatable" id="comisiones-table">
                <thead>
                    <tr>
                        <th>Vendedor</th>
                        <th>Ventas Realizadas</th>
                        <th>Monto Total</th>
                        <th>% Comisión</th>
                        <th>Comisión</th>
                        <th>Período</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $vendedores ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendedor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="fw-semibold"><?php echo e($vendedor->nombre ?? $vendedor->name ?? '—'); ?></td>
                        <td><?php echo e($vendedor->ventas_count ?? 0); ?></td>
                        <td class="fw-bold" style="color:#059669;">RD$ <?php echo e(number_format($vendedor->monto_total ?? 0, 2)); ?></td>
                        <td><?php echo e($vendedor->porcentaje_comision ?? 0); ?>%</td>
                        <td class="fw-bold" style="color:#f59e0b;">RD$ <?php echo e(number_format($vendedor->comision_total ?? 0, 2)); ?></td>
                        <td><?php echo e($vendedor->periodo ?? '—'); ?></td>
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
    const table = document.getElementById('comisiones-table');
    if (table && typeof $.fn.DataTable === 'function') {
        $(table).DataTable({
            responsive: true,
            pageLength: 15,
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
                zeroRecords: 'No se encontraron comisiones',
                infoEmpty: 'Sin registros',
                infoFiltered: '(filtrado de _MAX_ total)'
            },
            order: [[3, 'desc']],
            dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip'
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/reportes/comisiones-vendedores.blade.php ENDPATH**/ ?>