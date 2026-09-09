<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Libro de Compras - <?php echo e($mesNombre); ?> <?php echo e($anio); ?></title>
<style>
body{font-family:'DejaVu Sans',sans-serif;font-size:7px;margin:0;padding:10px;color:#222;}
.header{text-align:center;margin-bottom:12px;border-bottom:2px solid #333;padding-bottom:8px;}
.header h1{font-size:10px;margin:0;color:#333;}
.header h2{font-size:9px;margin:4px 0 0;color:#555;font-weight:600;}
.header p{margin:2px 0;font-size:7px;color:#666;}
.section-title{font-size:8px;font-weight:700;background:#2c3e50;color:#fff;padding:5px 8px;margin:12px 0 6px;border-radius:2px;}
.summary-table{width:100%;border-collapse:collapse;margin-bottom:10px;font-size:7px;}
.summary-table th,.summary-table td{border:1px solid #bbb;padding:4px 6px;text-align:left;}
.summary-table th{background:#ecf0f1;font-weight:700;text-transform:uppercase;width:40%;}
.summary-table td{text-align:right;}
.summary-table tr:last-child td{background:#d5dbdb;font-weight:700;}
.main-table{width:100%;border-collapse:collapse;margin-bottom:10px;font-size:6px;}
.main-table th,.main-table td{border:1px solid #bbb;padding:3px 4px;text-align:left;}
.main-table th{background:#2c3e50;color:#fff;font-weight:700;text-transform:uppercase;font-size:6px;white-space:nowrap;}
.main-table td.num{text-align:right;}
.main-table td.center{text-align:center;}
.main-table tbody tr:nth-child(even){background:#fafafa;}
.main-table tfoot td{background:#ecf0f1;font-weight:700;border-top:2px solid #2c3e50;}
.footer{margin-top:15px;text-align:center;font-size:6px;color:#999;border-top:1px solid #ddd;padding-top:6px;}
.prov-name{max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
</style>
</head><body>

<?php $empresa = \App\Models\SystemSetting::allCached(); ?>

<div class="header">
    <?php if($pdfLogoUrl): ?>
    <img src="<?php echo e($pdfLogoUrl); ?>" style="max-width: 70px; max-height: 50px; object-fit: contain; margin-bottom: 6px;" alt="Logo">
    <?php endif; ?>
    <h1><?php echo e(strtoupper(\App\Models\SystemSetting::nombreEmpresaActual())); ?></h1>
    <p>RNC: <?php echo e($empresa['empresa_rnc'] ?? 'N/A'); ?> &nbsp;&nbsp;|&nbsp;&nbsp; RFC: <?php echo e($empresa['empresa_rfc'] ?? 'N/A'); ?></p>
    <p><?php echo e($empresa['empresa_direccion'] ?? 'Sin direccion registrada'); ?></p>
    <h2 style="margin-top:6px;">LIBRO DE COMPRAS</h2>
    <p>Periodo: <?php echo e($mesNombre); ?> <?php echo e($anio); ?></p>
</div>

<!-- Resumen General -->
<div class="section-title">RESUMEN GENERAL DEL PERIODO</div>
<table class="summary-table">
<tr><th>Total Compras (sin retenciones)</th><td>RD$ <?php echo e(number_format($resumenGeneral->total ?? 0, 2)); ?></td></tr>
<tr><th>Gran Subtotal (Base Imponible)</th><td>RD$ <?php echo e(number_format($resumenGeneral->gran_subtotal ?? 0, 2)); ?></td></tr>
<tr><th>ITBIS Creditable (Total)</th><td>RD$ <?php echo e(number_format($resumenGeneral->gran_itbis ?? 0, 2)); ?></td></tr>
<tr><th>ITBIS Retenido</th><td>RD$ <?php echo e(number_format($resumenGeneral->gran_itbis_retenido ?? 0, 2)); ?></td></tr>
<tr><th>ISR Retenido</th><td>RD$ <?php echo e(number_format($resumenGeneral->gran_isr_retenido ?? 0, 2)); ?></td></tr>
<tr><th>Gran Total (Con ITBIS)</th><td>RD$ <?php echo e(number_format($resumenGeneral->gran_total ?? 0, 2)); ?></td></tr>
</table>

<!-- Detalle de Compras -->
<div class="section-title">DETALLE DE OPERACIONES</div>
<table class="main-table">
<thead><tr>
<th class="center">#</th>
<th>Fecha</th>
<th>NCF</th>
<th>Prov. / RNC</th>
<th class="num">Subtotal</th>
<th class="num">ITBIS</th>
<th class="num">Ret. ITBIS</th>
<th class="num">Ret. ISR</th>
<th class="num">Total Bruto</th>
<th class="num">Total Neto</th>
</tr></thead>
<tbody>
<?php $__currentLoopData = $compras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr>
<td class="center"><?php echo e($idx + 1); ?></td>
<td><?php echo e($c->fecha?->format('d/m/Y') ?? ''); ?></td>
<td><?php echo e($c->ncf ?? $c->numero_ncf ?? 'N/A'); ?></td>
<td>
    <span class="prov-name" title="<?php echo e($c->proveedor?->nombre ?? 'N/A'); ?>"><?php echo e($c->proveedor?->nombre ?? 'N/A'); ?></span><br>
    <small style="color:#888;">RNC: <?php echo e($c->proveedor?->rnc ?? 'N/A'); ?></small>
</td>
<td class="num">RD$ <?php echo e(number_format($c->subtotal ?? $c->gran_subtotal ?? 0, 2)); ?></td>
<td class="num">RD$ <?php echo e(number_format($c->itbis_total ?? $c->gran_itbis ?? 0, 2)); ?></td>
<td class="num">RD$ <?php echo e(number_format($c->retencion_itbis ?? $c->gran_itbis_retenido ?? 0, 2)); ?></td>
<td class="num">RD$ <?php echo e(number_format($c->retencion_isr ?? $c->gran_isr_retenido ?? 0, 2)); ?></td>
<td class="num">RD$ <?php echo e(number_format($c->total ?? 0, 2)); ?></td>
<td class="num">RD$ <?php echo e(number_format(($c->total ?? 0) - ($c->retencion_isr ?? 0) - ($c->retencion_itbis ?? 0), 2)); ?></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody>
<tfoot>
<tr>
<td colspan="4" style="text-align:right;">TOTALES:</td>
<td class="num">RD$ <?php echo e(number_format($resumenGeneral->gran_subtotal ?? 0, 2)); ?></td>
<td class="num">RD$ <?php echo e(number_format($resumenGeneral->gran_itbis ?? 0, 2)); ?></td>
<td class="num">RD$ <?php echo e(number_format($resumenGeneral->gran_itbis_retenido ?? 0, 2)); ?></td>
<td class="num">RD$ <?php echo e(number_format($resumenGeneral->gran_isr_retenido ?? 0, 2)); ?></td>
<td class="num">RD$ <?php echo e(number_format($resumenGeneral->total ?? 0, 2)); ?></td>
<td class="num">RD$ <?php echo e(number_format(($resumenGeneral->total ?? 0) - ($resumenGeneral->gran_isr_retenido ?? 0) - ($resumenGeneral->gran_itbis_retenido ?? 0), 2)); ?></td>
</tr>
</tfoot>
</table>

<div class="footer">
    Generado el <?php echo e(now()->format('d/m/Y H:i:s')); ?> &middot; Sistema de Facturas v1.0 &middot; Republica Dominicana
</div>

</body></html>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/libros/compras/pdf.blade.php ENDPATH**/ ?>