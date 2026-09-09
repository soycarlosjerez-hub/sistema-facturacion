<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Caja <?php echo e($desde); ?> al <?php echo e($hasta); ?></title>
<style>body{font-family:DejaVu Sans,sans-serif;font-size:7px;}table{width:100%;border-collapse:collapse;margin-top:10px;}th,td{border:1px solid #ccc;padding:3px 5px;text-align:left;}th{background:#f0f0f0;font-weight:700;text-transform:uppercase;font-size:6px;}td.text-end{text-align:right;}.totals{background:#f8f8f8;font-weight:700;}h2{margin:0;color:#333;}.meta{color:#666;margin:5px 0;}
td.neg{color:#dc3545;}td.pos{color:#198754;}</style>
</head><body>
<?php $empresa = \App\Models\SystemSetting::allCached(); ?>
<div style="text-align:center;margin-bottom:8px;">
    <?php if($pdfLogoUrl): ?>
    <img src="<?php echo e($pdfLogoUrl); ?>" style="max-width: 80px; max-height: 60px; object-fit: contain; margin-bottom: 5px;" alt="Logo">
    <?php endif; ?>
    <strong style="font-size:11px;"><?php echo e(\App\Models\SystemSetting::nombreEmpresaActual()); ?></strong><br>
    <span style="font-size:7px;color:#666;">RNC: <?php echo e($empresa['empresa_rnc'] ?? 'N/A'); ?></span>
</div>
<h2>Reporte de Caja / Turnos</h2>
<p class="meta">Período: <?php echo e($desde); ?> al <?php echo e($hasta); ?> &middot; <?php echo e($cantidad); ?> sesión(es)</p>
<table><thead><tr>
<th>Caja</th><th>Cajero</th><th>Apertura</th><th>Cierre</th><th>Estado</th>
<th class="text-end">Inicial</th><th class="text-end">Efectivo</th><th class="text-end">Tarjeta</th><th class="text-end">Transf.</th><th class="text-end">Declarado</th><th class="text-end">Descuadre</th>
</tr></thead><tbody>
<?php $__currentLoopData = $sesiones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr>
<td><?php echo e($s->caja?->nombre ?? ''); ?></td>
<td><?php echo e($s->user?->name ?? ''); ?></td>
<td><?php echo e($s->fecha_apertura?->format('d/m/Y H:i') ?? '-'); ?></td>
<td><?php echo e($s->fecha_cierre?->format('d/m/Y H:i') ?? '-'); ?></td>
<td><?php echo e($s->estado ?? ''); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($s->monto_inicial ?? 0, 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($s->ventas_efectivo ?? 0, 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($s->ventas_tarjeta ?? 0, 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($s->ventas_transferencia ?? 0, 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($s->monto_declarado ?? 0, 2)); ?></td>
<td class="text-end <?php echo e(($s->descuadre ?? 0) >= 0 ? 'pos' : 'neg'); ?>">RD$ <?php echo e(number_format($s->descuadre ?? 0, 2)); ?></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody>
<tfoot>
<tr class="totals">
<td colspan="5" class="text-end">TOTALES</td>
<td class="text-end">RD$ <?php echo e(number_format($sesiones->sum('monto_inicial'), 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($sesiones->sum('ventas_efectivo'), 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($sesiones->sum('ventas_tarjeta'), 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($sesiones->sum('ventas_transferencia'), 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($sesiones->sum('monto_declarado'), 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($sesiones->sum('descuadre'), 2)); ?></td>
</tr>
</tfoot></table>
<p style="color:#999;font-size:7px;margin-top:20px;">Generado: <?php echo e(now()->format('d/m/Y H:i')); ?></p>
</body></html>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/reportes/caja-pdf.blade.php ENDPATH**/ ?>