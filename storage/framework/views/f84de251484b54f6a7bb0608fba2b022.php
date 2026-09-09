<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Libro de Ventas - <?php echo e($mesNombre); ?> <?php echo e($anio); ?></title>
<style>
body{font-family:'DejaVu Sans',sans-serif;font-size:7px;}
table{width:100%;border-collapse:collapse;margin-top:8px;}
th,td{border:1px solid #ccc;padding:3px 5px;text-align:left;vertical-align:middle;}
th{background:#e8e8e8;font-weight:700;text-transform:uppercase;font-size:6.5px;}
td.text-end{text-align:right;}
td.text-center{text-align:center;}
.totals-row{background:#f5f5f5;font-weight:700;}
.header-company{text-align:center;margin-bottom:10px;border-bottom:2px solid #333;padding-bottom:8px;}
.company-name{font-size:12px;font-weight:700;color:#333;}
.company-info{font-size:7px;color:#555;margin-top:2px;}
.report-title{font-size:11px;font-weight:700;text-align:center;margin:12px 0 4px 0;color:#333;}
.report-period{text-align:center;font-size:7px;color:#666;margin-bottom:10px;}
.section-title{font-size:8px;font-weight:700;color:#444;margin:14px 0 6px 0;background:#f0f0f0;padding:4px 6px;border-left:3px solid #333;}
.footer{color:#999;font-size:6.5px;margin-top:16px;text-align:center;border-top:1px solid #ddd;padding-top:6px;}
.badge{display:inline-block;padding:1px 4px;border-radius:2px;font-size:6px;font-weight:600;color:#fff;}
.badge-consumer{background:#95a5a6;}
.badge-rnc{background:#3498db;}
.badge-cedula{background:#2ecc71;}
.col-num{width:30px;text-align:center;}
.col-fecha{width:55px;}
.col-ncf{width:90px;}
.col-cliente{min-width:120px;}
.col-rnc{width:80px;}
.col-tipo{width:55px;}
.col-monto{width:75px;text-align:right;}
.col-descuento{width:60px;text-align:right;}
.col-vendedor{width:80px;}
.col-caja{width:70px;}
.col-estado{width:50px;text-align:center;}
.col-encf{width:40px;text-align:center;}
@media print{
body{font-size:6.5px;}
th,td{padding:2px 4px;}
}
</style>
</head><body>

<?php $empresa = \App\Models\SystemSetting::allCached(); ?>

<div class="header-company">
    <?php if($pdfLogoUrl): ?>
    <img src="<?php echo e($pdfLogoUrl); ?>" style="max-width: 80px; max-height: 55px; object-fit: contain; margin-bottom: 6px;" alt="Logo">
    <?php endif; ?>
    <div class="company-name"><?php echo e(\App\Models\SystemSetting::nombreEmpresaActual()); ?></div>
    <div class="company-info">
        RNC: <?php echo e($empresa['empresa_rnc'] ?? 'N/A'); ?> &nbsp;&nbsp;|&nbsp;&nbsp;
        <?php echo e($empresa['empresa_direccion'] ?? ''); ?> &nbsp;&nbsp;|&nbsp;&nbsp;
        Tel: <?php echo e($empresa['empresa_telefono'] ?? 'N/A'); ?>

    </div>
    <div class="company-info">
        <?php echo e($empresa['empresa_email'] ?? ''); ?>

    </div>
</div>

<div class="report-title">LIBRO DE VENTAS</div>
<div class="report-period">Período: <?php echo e($mesNombre); ?> <?php echo e($anio); ?> &nbsp;&nbsp;|&nbsp;&nbsp; Generado: <?php echo e(now()->format('d/m/Y H:i')); ?></div>


<div class="section-title">RESUMEN POR TIPO DE NCF</div>
<table>
<thead><tr>
    <th class="col-tipo">Tipo NCF</th>
    <th class="col-num">Cantidad</th>
    <th class="col-monto">Subtotal</th>
    <th class="col-monto">ITBIS</th>
    <th class="col-monto">Total</th>
</tr></thead>
<tbody>
<?php $__currentLoopData = $totales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr>
    <td><?php echo e(strtoupper($t->ncf_tipo ?? 'consumidor-final')); ?></td>
    <td class="text-center"><?php echo e($t->cantidad ?? 0); ?></td>
    <td class="text-end">RD$ <?php echo e(number_format($t->subtotal ?? 0, 2)); ?></td>
    <td class="text-end">RD$ <?php echo e(number_format($t->itbis_total ?? 0, 2)); ?></td>
    <td class="text-end">RD$ <?php echo e(number_format($t->total ?? 0, 2)); ?></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody>
<tfoot>
<tr class="totals-row">
    <td>TOTAL GENERAL</td>
    <td class="text-center"><?php echo e($resumenGeneral->total ?? 0); ?></td>
    <td class="text-end">RD$ <?php echo e(number_format($resumenGeneral->gran_subtotal ?? 0, 2)); ?></td>
    <td class="text-end">RD$ <?php echo e(number_format($resumenGeneral->gran_itbis ?? 0, 2)); ?></td>
    <td class="text-end">RD$ <?php echo e(number_format($resumenGeneral->gran_total ?? 0, 2)); ?></td>
</tr>
</tfoot>
</table>


<div class="section-title">DETALLE DE VENTAS</div>
<table>
<thead><tr>
    <th class="col-num">#</th>
    <th class="col-fecha">Fecha</th>
    <th class="col-ncf">NCF</th>
    <th class="col-tipo">Tipo NCF</th>
    <th class="col-cliente">Cliente</th>
    <th class="col-rnc">RNC/Cédula</th>
    <th class="col-tipo">Tipo Cliente</th>
    <th class="col-monto">Subtotal</th>
    <th class="col-monto">ITBIS</th>
    <th class="col-descuento">Descuento</th>
    <th class="col-monto">Total</th>
    <th class="col-encf">Encf</th>
    <th class="col-estado">Estado</th>
    <th class="col-vendedor">Vendedor</th>
    <th class="col-caja">Caja</th>
</tr></thead>
<tbody>
<?php $__currentLoopData = $ventas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php
    $cliente = $v->cliente;
    $tipoCliente = '';
    $rncCedula = '';
    if ($cliente) {
        $tipoCliente = $cliente->tipo_cliente ?? 'consumer';
        $rncCedula = $tipoCliente === 'consumer' ? 'CONSUMER FINAL' : ($cliente->rnc_cedula ?? 'N/A');
    } else {
        $tipoCliente = 'consumer';
        $rncCedula = 'CONSUMER FINAL';
    }
    $badgeClass = ($tipoCliente === 'consumer') ? 'badge-consumer' : (($tipoCliente === 'rnc') ? 'badge-rnc' : 'badge-cedula');
?>
<tr>
    <td class="text-center"><?php echo e($index + 1); ?></td>
    <td><?php echo e($v->created_at->format('d/m/Y')); ?></td>
    <td><?php echo e($v->ncf ?? 'S/N'); ?></td>
    <td class="text-center"><?php echo e(strtoupper($v->ncf_tipo ?? '')); ?></td>
    <td><?php echo e($cliente?->nombre ?? 'Consumidor Final'); ?></td>
    <td><?php echo e($rncCedula); ?></td>
    <td class="text-center"><span class="badge <?php echo e($badgeClass); ?>"><?php echo e(strtoupper($tipoCliente)); ?></span></td>
    <td class="text-end">RD$ <?php echo e(number_format($v->subtotal ?? 0, 2)); ?></td>
    <td class="text-end">RD$ <?php echo e(number_format($v->itbis ?? $v->impuestos ?? 0, 2)); ?></td>
    <td class="text-end">RD$ <?php echo e(number_format($v->descuento ?? 0, 2)); ?></td>
    <td class="text-end">RD$ <?php echo e(number_format($v->total ?? 0, 2)); ?></td>
    <td class="text-center"><?php echo e($v->encf ?? '-'); ?></td>
    <td class="text-center"><?php echo e($v->estado ?? 'V'); ?></td>
    <td><?php echo e($v->usuario?->name ?? ''); ?></td>
    <td><?php echo e($v->caja?->nombre ?? ($v->caja?->descripcion ?? '')); ?></td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody>
<tfoot>
<tr class="totals-row">
    <td colspan="7" class="text-end">TOTALES:</td>
    <td class="text-end">RD$ <?php echo e(number_format($ventas->sum('subtotal') ?: 0, 2)); ?></td>
    <td class="text-end">RD$ <?php echo e(number_format($ventas->sum('itbis') ?: $ventas->sum('impuestos') ?: 0, 2)); ?></td>
    <td class="text-end">RD$ <?php echo e(number_format($ventas->sum('descuento') ?: 0, 2)); ?></td>
    <td class="text-end">RD$ <?php echo e(number_format($ventas->sum('total') ?: 0, 2)); ?></td>
    <td colspan="4"></td>
</tr>
</tfoot>
</table>

<div class="footer">
    Documento generado el <?php echo e(now()->format('d/m/Y H:i:s')); ?> &nbsp;&nbsp;|&nbsp;&nbsp; Sistema de Facturación Digital
</div>

</body></html>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/libros/ventas/pdf.blade.php ENDPATH**/ ?>