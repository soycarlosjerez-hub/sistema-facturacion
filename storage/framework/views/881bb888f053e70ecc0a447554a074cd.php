<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Compras <?php echo e($desde); ?> al <?php echo e($hasta); ?></title>
<style>body{font-family:DejaVu Sans,sans-serif;font-size:7px;}table{width:100%;border-collapse:collapse;margin-top:10px;}th,td{border:1px solid #ccc;padding:3px 5px;text-align:left;}th{background:#f0f0f0;font-weight:700;text-transform:uppercase;font-size:6px;}td.text-end{text-align:right;}.totals{background:#f8f8f8;font-weight:700;}h2{margin:0;color:#333;}.meta{color:#666;margin:5px 0;}</style>
</head><body>
<?php $empresa = \App\Models\SystemSetting::allCached(); ?>
<div style="text-align:center;margin-bottom:8px;">
    <?php if($pdfLogoUrl): ?>
    <img src="<?php echo e($pdfLogoUrl); ?>" style="max-width: 80px; max-height: 60px; object-fit: contain; margin-bottom: 5px;" alt="Logo">
    <?php endif; ?>
    <strong style="font-size:11px;"><?php echo e(\App\Models\SystemSetting::nombreEmpresaActual()); ?></strong><br>
    <span style="font-size:7px;color:#666;">RNC: <?php echo e($empresa['empresa_rnc'] ?? 'N/A'); ?></span>
</div>
<h2>Resumen de Compras</h2>
<p class="meta">Período: <?php echo e($desde); ?> al <?php echo e($hasta); ?> &middot; <?php echo e($cantidad); ?> compra(s)</p>
<table><thead><tr>
<th>#</th><th>Proveedor</th><th>Folio</th><th>Fecha</th><th class="text-end">Subtotal</th><th class="text-end">ITBIS</th><th class="text-end">Ret ISR</th><th class="text-end">Ret ITBIS</th><th class="text-end">Total</th>
</tr></thead><tbody>
<?php $__currentLoopData = $compras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr>
<td><?php echo e(str_pad($c->id,5,'0',STR_PAD_LEFT)); ?></td>
<td><?php echo e($c->proveedor?->nombre ?? 'N/A'); ?></td>
<td><?php echo e($c->folio ?? 'S/F'); ?></td>
<td><?php echo e($c->fecha?->format('d/m/Y') ?? ''); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($c->subtotal ?? 0, 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($c->itbis_total ?? 0, 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($c->retencion_isr ?? 0, 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($c->retencion_itbis ?? 0, 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($c->total, 2)); ?></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody>
<tfoot>
<tr class="totals">
<td colspan="4" class="text-end">TOTALES</td>
<td class="text-end">RD$ <?php echo e(number_format($compras->sum('subtotal'), 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($compras->sum('itbis_total'), 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($compras->sum('retencion_isr'), 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($compras->sum('retencion_itbis'), 2)); ?></td>
<td class="text-end">RD$ <?php echo e(number_format($compras->sum('total'), 2)); ?></td>
</tr>
</tfoot></table>
<p style="color:#999;font-size:7px;margin-top:20px;">Generado: <?php echo e(now()->format('d/m/Y H:i')); ?></p>
</body></html><?php /**PATH /var/www/html/sistema-facturacion/resources/views/reportes/compras-pdf.blade.php ENDPATH**/ ?>