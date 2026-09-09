<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Ventas <?php echo e($desde); ?> al <?php echo e($hasta); ?></title>
<style>body{font-family:DejaVu Sans,sans-serif;font-size:8px;}table{width:100%;border-collapse:collapse;margin-top:10px;}th,td{border:1px solid #ccc;padding:4px 6px;text-align:left;}th{background:#f0f0f0;font-weight:700;text-transform:uppercase;font-size:7px;}td.text-end{text-align:right;}.totals{background:#f8f8f8;font-weight:700;}h2{margin:0;color:#333;}.meta{color:#666;margin:5px 0;}.cajero-section{margin-top:20px;page-break-before:auto;}h3{margin:10px 0 5px 0;font-size:9px;color:#444;}</style>
</head><body>
<?php $empresa = \App\Models\SystemSetting::allCached(); ?>
<div style="text-align:center;margin-bottom:8px;">
    <?php if($pdfLogoUrl): ?>
    <img src="<?php echo e($pdfLogoUrl); ?>" style="max-width: 80px; max-height: 60px; object-fit: contain; margin-bottom: 5px;" alt="Logo">
    <?php endif; ?>
    <strong style="font-size:11px;"><?php echo e(\App\Models\SystemSetting::nombreEmpresaActual()); ?></strong><br>
    <span style="font-size:7px;color:#666;">RNC: <?php echo e($empresa['empresa_rnc'] ?? 'N/A'); ?></span>
</div>
<h2>Resumen de Ventas</h2>
<p class="meta">Período: <?php echo e($desde); ?> al <?php echo e($hasta); ?> &middot; <?php echo e($cantidad); ?> venta(s) &middot; <?php echo e($totalCajas); ?> caja(s)</p>

<table><thead><tr>
<th>#</th><th>Cliente</th><th>Vendedor</th><th>NCF</th><th>Fecha</th><th class="text-end">Subtotal</th><th class="text-end">ITBIS</th><th class="text-end">Total</th>
</tr></thead><tbody>
<?php $__currentLoopData = $ventas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr>
<td><?php echo e(str_pad($v->id,5,'0',STR_PAD_LEFT)); ?></td>
<td><?php echo e($v->cliente?->nombre ?? 'Consumidor Final'); ?></td>
<td><?php echo e($v->usuario?->name ?? ''); ?></td>
<td><?php echo e($v->ncf ?? $v->encf ?? 'S/N'); ?></td>
<td><?php echo e($v->created_at->format('d/m/Y')); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($v->subtotal ?? 0, 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($v->impuestos ?? 0, 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($v->total, 2)); ?></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody>
<tfoot>
<tr class="totals">
<td colspan="5" class="text-end">TOTALES</td>
<td class="text-end">RD$ <?php echo e(number_format($ventas->sum('subtotal'), 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($ventas->sum('impuestos'), 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($ventas->sum('total'), 2)); ?></td>
</tr>
</tfoot></table>

<div class="cajero-section">
<h3>Resumen por Cajero</h3>
<table>
<thead><tr>
<th>Cajero</th><th class="text-end">Ventas</th><th class="text-end">Subtotal</th><th class="text-end">ITBIS</th><th class="text-end">Total</th><th class="text-end">Cajas</th>
</tr></thead><tbody>
<?php $__currentLoopData = $ventasPorCajero; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cajero): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr>
<td><?php echo e($cajero['cajero_nombre']); ?></td>
<td class="text-end"><?php echo e($cajero['cantidad']); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($cajero['subtotal'], 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($cajero['itbis'], 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($cajero['total'], 2)); ?></td>
<td class="text-end"><?php echo e($cajero['cajas_count']); ?></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody>
<tfoot>
<tr class="totals">
<td>TOTALES</td>
<td class="text-end"><?php echo e($ventasPorCajero->sum('cantidad')); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($ventasPorCajero->sum('subtotal'), 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($ventasPorCajero->sum('itbis'), 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($ventasPorCajero->sum('total'), 2)); ?></td>
<td class="text-end"><?php echo e($totalCajas); ?></td>
</tr>
</tfoot>
</table>
</div>

<p style="color:#999;font-size:7px;margin-top:20px;">Generado: <?php echo e(now()->format('d/m/Y H:i')); ?></p>
</body></html><?php /**PATH /var/www/html/sistema-facturacion/resources/views/reportes/ventas-pdf.blade.php ENDPATH**/ ?>