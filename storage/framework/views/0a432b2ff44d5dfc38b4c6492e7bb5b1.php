<?php $__env->startSection('title', 'Resumen de Ventas'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.filter-card > .ui-card-accent { height:5px;border-radius:1.2rem 1.2rem 0 0; }
.filter-card .ui-input:focus { border-color:#7c3aed!important;box-shadow:0 0 0 3px rgba(139,92,246,.15)!important; }
.filter-card .ui-btn-solid { background:linear-gradient(135deg,#7c3aed,#8b5cf6)!important;border:none!important; }
.filter-card .ui-btn-solid:hover { background:linear-gradient(135deg,#6d28d9,#7c3aed)!important;box-shadow:0 6px 20px rgba(139,92,246,.4)!important; }
@media(max-width:575.98px){.filter-card .ui-input{min-width:100%;}}
#ventasTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#ventasTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#ventasTable tbody tr { transition:background .15s; }
#ventasTable tbody tr:hover { background:rgba(139,92,246,.04); }
#ventasTable tfoot td { padding:14px 12px;border-top:2px solid #e2e8f0;background:#f8fafc; }
body.dark-mode #ventasTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #ventasTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #ventasTable tbody tr:hover { background:rgba(139,92,246,.08); }
body.dark-mode #ventasTable tfoot td { background:rgba(15,23,42,.6);border-top-color:#334155;color:#f1f5f9; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">
    <div class="ui-header d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body w-100">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-bar-chart-line"></i>
                </div>
                <div>
                    <h2 class="ui-header-title">Resumen de Ventas</h2>
                    <div class="ui-header-meta">Período: <?php echo e($desde); ?> al <?php echo e($hasta); ?> &middot; <?php echo e($cantidad); ?> venta(s) &middot; <?php echo e($totalCajas); ?> caja(s)</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('reportes.ventas.csv', ['desde' => $desde, 'hasta' => $hasta])); ?>" class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-download me-1"></i> CSV</a>
                <a href="<?php echo e(route('reportes.ventas.pdf', ['desde' => $desde, 'hasta' => $hasta])); ?>" class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-file-pdf me-1"></i> PDF</a>
                <a href="<?php echo e(route('reportes.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill"><i class="bi bi-grid me-1"></i> Reportes</a>
            </div>
        </div>
    </div>

    <div class="ui-card filter-card mb-4"><div class="ui-card-accent"></div><div class="px-4 py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><label class="ui-label small fw-semibold mb-0">Desde</label></div>
            <div class="col-auto"><input type="date" name="desde" class="ui-input" value="<?php echo e($desde); ?>"></div>
            <div class="col-auto"><label class="ui-label small fw-semibold mb-0">Hasta</label></div>
            <div class="col-auto"><input type="date" name="hasta" class="ui-input" value="<?php echo e($hasta); ?>"></div>
            <div class="col-auto"><button class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div></div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="ui-stat text-center p-3">
                <div class="ui-stat-label">Cantidad</div>
                <div class="ui-stat-value"><?php echo e($cantidad); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat text-center p-3">
                <div class="ui-stat-label">Total General</div>
                <div class="ui-stat-value text-primary">RD$ <?php echo e(number_format($totalGeneral, 2)); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat text-center p-3">
                <div class="ui-stat-label">ITBIS</div>
                <div class="ui-stat-value text-warning">RD$ <?php echo e(number_format($totalItbis, 2)); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat text-center p-3">
                <div class="ui-stat-label">Efectivo Recibido</div>
                <div class="ui-stat-value text-success">RD$ <?php echo e(number_format($totalEfectivo, 2)); ?></div>
            </div>
        </div>
    </div>

    
    <div class="ui-card mb-4">
        <div class="ui-card-accent"></div>
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-person-badge me-2"></i>Resumen por Cajero</h5>
            <div class="row g-3">
                <?php $__currentLoopData = $ventasPorCajero; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cajero): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-lg-6 col-xl-4">
                    <div class="ui-stat p-3 h-100">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="ui-stat-label">Cajero</div>
                                <div class="fw-bold fs-6"><?php echo e($cajero['cajero_nombre']); ?></div>
                            </div>
                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary fw-semibold">
                                <?php echo e($cajero['cantidad']); ?> venta(s)
                            </span>
                        </div>
                        <hr class="my-2 opacity-25">
                        <div class="row g-2 small">
                            <div class="col-6">
                                <div class="text-muted small">Total Vendido</div>
                                <div class="fw-bold text-primary">RD$ <?php echo e(number_format($cajero['total'], 2)); ?></div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small">Cajas Usadas</div>
                                <div class="fw-bold"><?php echo e($cajero['cajas_count']); ?></div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small">Subtotal</div>
                                <div class="fw-semibold">RD$ <?php echo e(number_format($cajero['subtotal'], 2)); ?></div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small">ITBIS</div>
                                <div class="fw-semibold text-warning">RD$ <?php echo e(number_format($cajero['itbis'], 2)); ?></div>
                            </div>
                        </div>
                        <?php $cajasUsadas = collect($cajero['cajas_usadas']); ?>
                        <?php if($cajasUsadas->isNotEmpty()): ?>
                        <div class="mt-2">
                            <small class="text-muted">Cajas: <?php echo e($cajasUsadas->join(', ')); ?></small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <div class="ui-card overflow-hidden">
        <div class="table-responsive px-3 py-3">
            <table id="ventasTable" class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">#</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>NCF/e-CF</th>
                        <th>Fecha</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">ITBIS</th>
                        <th class="text-end pe-4">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $ventas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="ps-4"><?php echo e(str_pad($v->id, 5, '0', STR_PAD_LEFT)); ?></td>
                            <td><span class="fw-semibold small"><?php echo e($v->cliente?->nombre ?? 'Consumidor Final'); ?></span><br><small class="text-muted font-monospace"><?php echo e($v->cliente?->rnc_cedula ?? ''); ?></small></td>
                            <td><small><?php echo e($v->usuario?->name ?? ''); ?></small></td>
                            <td><span class="font-monospace small"><?php echo e($v->ncf ?? $v->encf ?? 'S/N'); ?></span></td>
                            <td><small><?php echo e($v->created_at->format('d/m/Y h:i A')); ?></small></td>
                            <td class="text-end">RD$ <?php echo e(number_format($v->subtotal ?? 0, 2)); ?></td>
                            <td class="text-end text-warning fw-semibold">RD$ <?php echo e(number_format($v->impuestos ?? 0, 2)); ?></td>
                            <td class="text-end pe-4 fw-bold">RD$ <?php echo e(number_format($v->total, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot class="fw-bold">
                    <tr>
                        <td colspan="5" class="ps-4 py-3 text-end text-uppercase small">Totales</td>
                        <td class="text-end py-3">RD$ <?php echo e(number_format($ventas->sum('subtotal'), 2)); ?></td>
                        <td class="text-end py-3 text-warning">RD$ <?php echo e(number_format($ventas->sum('impuestos'), 2)); ?></td>
                        <td class="text-end pe-4 py-3">RD$ <?php echo e(number_format($ventas->sum('total'), 2)); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<!-- Spacing --><div class="mb-5"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    $('#ventasTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json', emptyTable: 'No hay ventas en este período' },
        columnDefs: [{ orderable: false, targets: [5, 6, 7] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/reportes/ventas.blade.php ENDPATH**/ ?>