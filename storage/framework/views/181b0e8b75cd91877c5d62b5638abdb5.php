<?php $__env->startSection('title', 'Libro de Retenciones Consolidado'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.filter-card .ui-select:focus { border-color:#8b5cf6!important;box-shadow:0 0 0 3px rgba(139,92,246,.15)!important; }
.filter-card .ui-btn-solid { background:linear-gradient(135deg,#7c3aed,#8b5cf6)!important;border:none!important; }
.filter-card .ui-btn-solid:hover { background:linear-gradient(135deg,#6d28d9,#7c3aed)!important;box-shadow:0 6px 20px rgba(139,92,246,.4)!important; }
@media(max-width:575.98px){.filter-card .ui-select{min-width:100%;}}
.tab-pane .table thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
.tab-pane .table tbody td { padding:10px 12px;border-bottom:1px solid #f1f5f9;font-size:.85rem; }
.tab-pane .table tbody tr { transition:background .15s; }
.tab-pane .table tbody tr:hover { background:rgba(139,92,246,.04); }
body.dark-mode .tab-pane .table thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode .tab-pane .table tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode .tab-pane .table tbody tr:hover { background:rgba(139,92,246,.08); }
.nav-tabs-custom .nav-link { color:#64748b;font-weight:600;border:none;padding:12px 24px;border-bottom:3px solid transparent;transition:all .2s; }
.nav-tabs-custom .nav-link.active { color:#8b5cf6;border-bottom-color:#8b5cf6;background:transparent; }
.nav-tabs-custom .nav-link:hover:not(.active) { color:#8b5cf6;background:rgba(139,92,246,.05); }
body.dark-mode .nav-tabs-custom .nav-link { color:#94a3b8; }
body.dark-mode .nav-tabs-custom .nav-link.active { color:#a78bfa; }
body.dark-mode .nav-tabs-custom .nav-link:hover:not(.active) { color:#a78bfa;background:rgba(139,92,246,.1); }
.summary-item { transition:transform .2s; }
.summary-item:hover { transform:translateY(-2px); }
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
                    <i class="bi bi-percent"></i>
                </div>
                <div>
                    <h2 class="ui-header-title">Libro de Retenciones Consolidado</h2>
                    <div class="ui-header-meta">Período: <?php echo e(ucfirst(Carbon\Carbon::create($anio, $mes, 1)->translatedFormat('F'))); ?> <?php echo e($anio); ?></div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('libros-retenciones.excel', request()->all())); ?>" class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
                <a href="<?php echo e(route('libros-retenciones.pdf', request()->all())); ?>" class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
                <a href="<?php echo e(route('reportes.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill"><i class="bi bi-grid me-1"></i>Reportes</a>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="ui-card filter-card mb-4">
        <div class="ui-card-accent"></div>
        <div class="px-4 py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="ui-label small fw-semibold mb-0">Mes</label>
            </div>
            <div class="col-auto">
                <select name="mes" class="ui-select">
                    <?php $__currentLoopData = range(1, 12); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($m); ?>" <?php echo e($mes == $m ? 'selected' : ''); ?>>
                            <?php echo e(Carbon\Carbon::create()->month($m)->translatedFormat('F')); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-auto">
                <label class="ui-label small fw-semibold mb-0">Año</label>
            </div>
            <div class="col-auto">
                <select name="anio" class="ui-select">
                    <?php for($y = now()->year; $y >= now()->year - 5; $y--): ?>
                        <option value="<?php echo e($y); ?>" <?php echo e($anio == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-auto">
                <label class="ui-label small fw-semibold mb-0">Tipo</label>
            </div>
            <div class="col-auto">
                <select name="tipo" class="ui-select">
                    <option value="compras" <?php echo e($tipo === 'compras' ? 'selected' : ''); ?>>Solo Compras</option>
                    <option value="ventas" <?php echo e($tipo === 'ventas' ? 'selected' : ''); ?>>Solo Ventas</option>
                    <option value="ambos" <?php echo e($tipo === 'ambos' ? 'selected' : ''); ?>>Ambos</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-search me-1"></i>Filtrar</button>
            </div>
        </form>
        </div>
    </div>

    <!-- Stats generales -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="ui-stat text-center p-3 summary-item">
                <div class="text-primary mb-1"><i class="bi bi-cart-check fs-4"></i></div>
                <div class="ui-stat-label">ISR Compras</div>
                <div class="ui-stat-value">RD$ <?php echo e(number_format($resumen['total_isr_compras'], 2)); ?></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="ui-stat text-center p-3 summary-item">
                <div class="text-warning mb-1"><i class="bi bi-arrow-return-left fs-4"></i></div>
                <div class="ui-stat-label">ITBIS Compras</div>
                <div class="ui-stat-value">RD$ <?php echo e(number_format($resumen['total_itbis_compras'], 2)); ?></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="ui-stat text-center p-3 summary-item">
                <div class="text-success mb-1"><i class="bi bi-cart-plus fs-4"></i></div>
                <div class="ui-stat-label">ISR Ventas</div>
                <div class="ui-stat-value">RD$ <?php echo e(number_format($resumen['total_isr_ventas'], 2)); ?></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="ui-stat text-center p-3 summary-item">
                <div class="text-info mb-1"><i class="bi bi-receipt fs-4"></i></div>
                <div class="ui-stat-label">ITBIS Ventas</div>
                <div class="ui-stat-value">RD$ <?php echo e(number_format($resumen['total_itbis_ventas'], 2)); ?></div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="ui-stat text-center p-3 summary-item" style="border:1px solid rgba(220,53,69,.2);">
                <div class="text-danger mb-1"><i class="bi bi-wallet2 fs-4"></i></div>
                <div class="ui-stat-label">Total Retenido</div>
                <div class="ui-stat-value">RD$ <?php echo e(number_format($resumen['gran_total'], 2)); ?></div>
            </div>
        </div>
    </div>

    <!-- Tabs: Compras / Ventas -->
    <?php if($tipo === 'compras' || $tipo === 'ambos'): ?>
    <div class="ui-card overflow-hidden mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-cart-check text-success me-2"></i>Retenciones en Compras</h5>
            <small class="text-muted"><?php echo e($compras->total()); ?> registro(s)</small>
        </div>
        
        <!-- Resumen por proveedor -->
        <?php if($porProveedor->isNotEmpty()): ?>
        <div class="px-4 py-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-building me-2"></i>Resumen por Proveedor</h6>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3 py-2" style="font-size:.68rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;">Proveedor</th>
                            <th class="py-2 text-end" style="font-size:.68rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;"># Doc</th>
                            <th class="py-2 text-end" style="font-size:.68rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;">ISR</th>
                            <th class="py-2 text-end" style="font-size:.68rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;">ITBIS</th>
                            <th class="py-2 text-end" style="font-size:.68rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $porProveedor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="ps-3 fw-semibold small"><?php echo e($pp->nombre ?? 'N/A'); ?></td>
                            <td class="text-end small"><?php echo e($pp->cantidad_compras); ?></td>
                            <td class="text-end small text-primary">RD$ <?php echo e(number_format($pp->total_isr, 2)); ?></td>
                            <td class="text-end small text-warning">RD$ <?php echo e(number_format($pp->total_itbis, 2)); ?></td>
                            <td class="text-end small fw-bold">RD$ <?php echo e(number_format($pp->total_retenido, 2)); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td class="ps-3 text-end">TOTALES:</td>
                            <td class="text-end"><?php echo e($porProveedor->sum('cantidad_compras')); ?></td>
                            <td class="text-end text-primary">RD$ <?php echo e(number_format($porProveedor->sum('total_isr'), 2)); ?></td>
                            <td class="text-end text-warning">RD$ <?php echo e(number_format($porProveedor->sum('total_itbis'), 2)); ?></td>
                            <td class="text-end">RD$ <?php echo e(number_format($porProveedor->sum('total_retenido'), 2)); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Detalle compras -->
        <div class="table-responsive px-3 pb-3">
            <table id="retencionesComprasTable" class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">#</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>RNC</th>
                        <th class="text-end">Base Imponible</th>
                        <th class="text-end">ITBIS</th>
                        <th class="text-end">Ret ISR</th>
                        <th class="text-end">Ret ITBIS</th>
                        <th class="text-end pe-4">Total Retenido</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $compras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="ps-4 font-monospace small"><?php echo e($compras->firstItem() + $i); ?></td>
                        <td><small><?php echo e($c->fecha?->format('d/m/Y') ?? ''); ?></small></td>
                        <td><span class="fw-semibold small"><?php echo e($c->proveedor?->nombre ?? 'N/A'); ?></span></td>
                        <td><span class="font-monospace small"><?php echo e($c->proveedor?->rnc ?? ''); ?></span></td>
                        <td class="text-end">RD$ <?php echo e(number_format($c->subtotal, 2)); ?></td>
                        <td class="text-end">RD$ <?php echo e(number_format($c->itbis_total, 2)); ?></td>
                        <td class="text-end text-primary">RD$ <?php echo e(number_format($c->retencion_isr ?? 0, 2)); ?></td>
                        <td class="text-end text-warning">RD$ <?php echo e(number_format($c->retencion_itbis ?? 0, 2)); ?></td>
                        <td class="text-end pe-4 fw-bold">RD$ <?php echo e(number_format(($c->retencion_isr ?? 0) + ($c->retencion_itbis ?? 0), 2)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-transparent border-0 pt-2 px-3">
            <?php echo e($compras->links()); ?>

        </div>
    </div>
    <?php endif; ?>

    <?php if($tipo === 'ventas' || $tipo === 'ambos'): ?>
    <div class="ui-card overflow-hidden mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-receipt text-primary me-2"></i>Retenciones en Ventas</h5>
            <small class="text-muted"><?php echo e($ventas->total()); ?> registro(s)</small>
        </div>
        
        <!-- Resumen por cliente -->
        <?php if($porCliente->isNotEmpty()): ?>
        <div class="px-4 py-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-people me-2"></i>Resumen por Cliente</h6>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3 py-2" style="font-size:.68rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;">Cliente</th>
                            <th class="py-2 text-end" style="font-size:.68rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;"># Doc</th>
                            <th class="py-2 text-end" style="font-size:.68rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;">ISR</th>
                            <th class="py-2 text-end" style="font-size:.68rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;">ITBIS</th>
                            <th class="py-2 text-end" style="font-size:.68rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $porCliente; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="ps-3 fw-semibold small"><?php echo e($pc->nombre ?? 'N/A'); ?></td>
                            <td class="text-end small"><?php echo e($pc->cantidad_ventas); ?></td>
                            <td class="text-end small text-primary">RD$ <?php echo e(number_format($pc->total_isr, 2)); ?></td>
                            <td class="text-end small text-warning">RD$ <?php echo e(number_format($pc->total_itbis, 2)); ?></td>
                            <td class="text-end small fw-bold">RD$ <?php echo e(number_format($pc->total_retenido, 2)); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td class="ps-3 text-end">TOTALES:</td>
                            <td class="text-end"><?php echo e($porCliente->sum('cantidad_ventas')); ?></td>
                            <td class="text-end text-primary">RD$ <?php echo e(number_format($porCliente->sum('total_isr'), 2)); ?></td>
                            <td class="text-end text-warning">RD$ <?php echo e(number_format($porCliente->sum('total_itbis'), 2)); ?></td>
                            <td class="text-end">RD$ <?php echo e(number_format($porCliente->sum('total_retenido'), 2)); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Detalle ventas -->
        <div class="table-responsive px-3 pb-3">
            <table id="retencionesVentasTable" class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">#</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>RNC/Cédula</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Ret ISR</th>
                        <th class="text-end">Ret ITBIS</th>
                        <th class="text-end pe-4">Total Retenido</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $ventas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="ps-4 font-monospace small"><?php echo e($ventas->firstItem() + $i); ?></td>
                        <td><small><?php echo e($v->created_at?->format('d/m/Y') ?? ''); ?></small></td>
                        <td><span class="fw-semibold small"><?php echo e($v->cliente?->nombre ?? 'Consumidor Final'); ?></span></td>
                        <td><span class="font-monospace small"><?php echo e($v->cliente?->rnc_cedula ?? ''); ?></span></td>
                        <td class="text-end">RD$ <?php echo e(number_format($v->total, 2)); ?></td>
                        <td class="text-end text-primary">RD$ <?php echo e(number_format($v->retencion_isr ?? 0, 2)); ?></td>
                        <td class="text-end text-warning">RD$ <?php echo e(number_format($v->retencion_itbis ?? 0, 2)); ?></td>
                        <td class="text-end pe-4 fw-bold">RD$ <?php echo e(number_format(($v->retencion_isr ?? 0) + ($v->retencion_itbis ?? 0), 2)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-transparent border-0 pt-2 px-3">
            <?php echo e($ventas->links()); ?>

        </div>
    </div>
    <?php endif; ?>
</div>
<!-- Spacing --><div class="mb-5"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    $('#retencionesComprasTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
            emptyTable: 'Sin retenciones en compras para este período'
        },
        columnDefs: [{ orderable: false, targets: [4,5,6,7,8] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
    $('#retencionesVentasTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
            emptyTable: 'Sin retenciones en ventas para este período'
        },
        columnDefs: [{ orderable: false, targets: [4,5,6,7] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/libros-retenciones/index.blade.php ENDPATH**/ ?>