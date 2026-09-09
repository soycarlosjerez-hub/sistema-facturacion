<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #000; padding:5px; }
        th { background:#f2f2f2; }
    </style>
</head>
<body>

<?php $empresa = \App\Models\SystemSetting::allCached(); ?>
<div style="text-align:center;margin-bottom:15px;">
    <?php if($pdfLogoUrl): ?>
    <img src="<?php echo e($pdfLogoUrl); ?>" style="max-width: 80px; max-height: 60px; object-fit: contain; margin-bottom: 5px;" alt="Logo">
    <?php endif; ?>
    <strong style="font-size:14px;"><?php echo e(\App\Models\SystemSetting::nombreEmpresaActual()); ?></strong><br>
    <small>RNC: <?php echo e($empresa['empresa_rnc'] ?? 'N/A'); ?></small>
</div>
<h3>Movimientos de Almacén</h3>

<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Producto</th>
            <th>Almacén</th>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Usuario</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $movimientos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($m->created_at); ?></td>
                <td><?php echo e($m->producto->nombre); ?></td>
                <td><?php echo e($m->almacen->nombre); ?></td>
                <td><?php echo e($m->tipo); ?></td>
                <td><?php echo e($m->cantidad); ?></td>
                <td><?php echo e($m->user->name ?? 'Sistema'); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

</body>
</html>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/almacenes/movimientos-pdf.blade.php ENDPATH**/ ?>