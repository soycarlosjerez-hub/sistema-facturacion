<?php $__env->startSection('title', $titulo); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
body.dark-mode .card-header.bg-white { background: rgba(15,23,42,.5) !important; border-color: rgba(255,255,255,.06) !important; }
body.dark-mode .card-header.bg-white h6 { color: #f1f5f9; }
body.dark-mode .card-header.bg-white h6 i { opacity: .9; }
.filter-card > .ui-card-accent { height:5px;border-radius:1.2rem 1.2rem 0 0; }
.filter-card .ui-select:focus { border-color:#7c3aed!important;box-shadow:0 0 0 3px rgba(139,92,246,.15)!important; }
.filter-card .ui-btn-solid { background:linear-gradient(135deg,#7c3aed,#8b5cf6)!important;border:none!important; }
.filter-card .ui-btn-solid:hover { background:linear-gradient(135deg,#6d28d9,#7c3aed)!important;box-shadow:0 6px 20px rgba(139,92,246,.4)!important; }
@media(max-width:575.98px){.filter-card .ui-select{min-width:100%;}}
#fiscalesTable thead th { border-bottom:2px solid #e2e8f0;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:14px 12px;background:#f8fafc; }
#fiscalesTable tbody td { padding:12px;vertical-align:middle;border-bottom:1px solid #f1f5f9;font-size:.88rem; }
#fiscalesTable tbody tr { transition:background .15s; }
#fiscalesTable tbody tr:hover { background:rgba(139,92,246,.04); }
#fiscalesTable tfoot td { padding:14px 12px;border-top:2px solid #e2e8f0;background:#f8fafc; }
body.dark-mode #fiscalesTable thead th { background:rgba(15,23,42,.6);border-bottom-color:#334155;color:#94a3b8; }
body.dark-mode #fiscalesTable tbody td { border-bottom-color:#1e293b;color:#cbd5e1; }
body.dark-mode #fiscalesTable tbody tr:hover { background:rgba(139,92,246,.08); }
body.dark-mode #fiscalesTable tfoot td { background:rgba(15,23,42,.6);border-top-color:#334155;color:#f1f5f9; }
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
                    <h2 class="ui-header-title"><?php echo e($titulo); ?></h2>
                    <div class="ui-header-meta">Período: <?php echo e(ucfirst($periodo->translatedFormat('F Y'))); ?></div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('reportes.fiscales', ['tipo' => $tipo === '607' ? '606' : '607', 'mes' => $mes, 'anio' => $anio])); ?>" 
                   class="ui-btn ui-btn-ghost rounded-pill">
                    <i class="bi bi-arrow-left-right me-1"></i> Cambiar a <?php echo e($tipo === '607' ? '606 (Compras)' : '607 (Ventas)'); ?>

                </a>
                <a href="<?php echo e(route('reportes.fiscales.export', request()->all())); ?>" class="ui-btn ui-btn-solid rounded-pill">
                    <i class="bi bi-download me-1"></i> CSV DGII
                </a>
                <a href="<?php echo e(route('reportes.fiscales.txt', request()->all())); ?>" class="ui-btn ui-btn-solid rounded-pill">
                    <i class="bi bi-filetype-txt me-1"></i> TXT DGII
                </a>
                <a href="<?php echo e(route('reportes.fiscales.pdf', request()->all())); ?>" class="ui-btn ui-btn-solid rounded-pill">
                    <i class="bi bi-file-pdf me-1"></i> PDF
                </a>
                <a href="<?php echo e(route('reportes.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill">
                    <i class="bi bi-grid me-1"></i> Reportes
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card filter-card mb-4">
        <div class="ui-card-accent"></div>
        <div class="px-4 py-3">
        <form method="GET" action="<?php echo e(route('reportes.fiscales')); ?>" class="row g-2 align-items-end">
            <input type="hidden" name="tipo" value="<?php echo e($tipo); ?>">
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
                <select name="anio" class="ui-select">
                    <?php for($y = now()->year; $y >= now()->year - 3; $y--): ?>
                        <option value="<?php echo e($y); ?>" <?php echo e($anio == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-funnel me-1"></i>Filtrar</button>
            </div>
        </form>
    </div>
</div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="ui-stat text-center p-3">
                <div class="ui-stat-label">Registros</div>
                <div class="ui-stat-value"><?php echo e(number_format($cantidad)); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat text-center p-3">
                <div class="ui-stat-label">Monto Facturado</div>
                <div class="ui-stat-value text-primary">RD$ <?php echo e(number_format($total_monto, 2)); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat text-center p-3">
                <div class="ui-stat-label">ITBIS</div>
                <div class="ui-stat-value text-warning">RD$ <?php echo e(number_format($total_itbis, 2)); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat text-center p-3">
                <div class="ui-stat-label">Total General</div>
                <div class="ui-stat-value text-success">RD$ <?php echo e(number_format($total_general, 2)); ?></div>
            </div>
        </div>
    </div>

    <div class="ui-card overflow-hidden">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-list-table me-2"></i>Detalle de <?php echo e($tipo === '607' ? 'Ventas' : 'Compras'); ?>

            </h5>
            <small class="text-muted"><?php echo e($cantidad); ?> registro(s)</small>
        </div>
        <div class="table-responsive px-3 py-3">
            <table id="fiscalesTable" class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">RNC/Cédula</th>
                        <th><?php echo e($tipo === '607' ? 'Cliente' : 'Proveedor'); ?></th>
                        <th>NCF / Comprobante</th>
                        <th>Tipo</th>
                        <th>Fecha</th>
                        <th class="text-end">Monto Facturado</th>
                        <th class="text-end">ITBIS</th>
                        <th class="text-end pe-4">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $registros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="ps-4 font-monospace small"><?php echo e($r['rnc']); ?></td>
                            <td><span class="fw-semibold small"><?php echo e($r['cliente'] ?? $r['proveedor']); ?></span></td>
                            <td><span class="font-monospace small"><?php echo e($r['ncf']); ?></span></td>
                            <td><span class="badge bg-light text-dark rounded-pill"><?php echo e($r['tipo_ncf']); ?></span></td>
                            <td><small><?php echo e($r['fecha']); ?></small></td>
                            <td class="text-end">RD$ <?php echo e(number_format($r['monto_facturado'], 2)); ?></td>
                            <td class="text-end text-warning fw-semibold">RD$ <?php echo e(number_format($r['itbis'], 2)); ?></td>
                            <td class="text-end pe-4 fw-bold">RD$ <?php echo e(number_format($r['total'], 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot class="fw-bold">
                    <tr>
                        <td colspan="5" class="ps-4 py-3 text-end text-uppercase small">Totales</td>
                        <td class="text-end py-3">RD$ <?php echo e(number_format($total_monto, 2)); ?></td>
                        <td class="text-end py-3 text-warning">RD$ <?php echo e(number_format($total_itbis, 2)); ?></td>
                        <td class="text-end pe-4 py-3">RD$ <?php echo e(number_format($total_general, 2)); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="ui-card mt-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-bubble bg-soft-danger flex-shrink-0" style="width:52px;height:52px;font-size:1.3rem;">
                    <i class="bi bi-info-circle"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">Formato <?php echo e($tipo); ?> - DGII</h6>
                    <p class="text-muted small mb-0">
                        Este reporte corresponde al formato <?php echo e($tipo === '607' ? '607 (Ventas)' : '606 (Compras)'); ?> 
                        requerido por la DGII para la declaración mensual de ITBIS.
                        Los datos pueden exportarse en formato CSV para subir al portal de la DGII.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Spacing --><div class="mb-5"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    $('#fiscalesTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json', emptyTable: 'No hay registros para este período' },
        columnDefs: [{ orderable: false, targets: [5,6,7] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/reportes/fiscales.blade.php ENDPATH**/ ?>