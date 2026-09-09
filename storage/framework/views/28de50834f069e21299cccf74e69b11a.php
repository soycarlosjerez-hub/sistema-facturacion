<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listado de Ventas</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <?php $empresa = \App\Models\SystemSetting::allCached(); ?>
    <div style="text-align:center;margin-bottom:20px;">
        <?php if($pdfLogoUrl): ?>
        <img src="<?php echo e($pdfLogoUrl); ?>" style="max-width: 80px; max-height: 60px; object-fit: contain; margin-bottom: 5px;" alt="Logo">
        <?php endif; ?>
        <h2 style="margin:0;"><?php echo e(\App\Models\SystemSetting::nombreEmpresaActual()); ?></h2>
        <?php if(!empty($empresa['empresa_rnc'])): ?>
        <small style="color:#666;">RNC: <?php echo e($empresa['empresa_rnc']); ?></small>
        <?php endif; ?>
    </div>
    <h3>Listado de Ventas</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Usuario</th>
                <th>Tipo</th>
                <th>Fecha</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $ventas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($venta->id); ?></td>
                    <td><?php echo e($venta->cliente->nombre ?? 'N/A'); ?></td>
                    <td><?php echo e($venta->usuario->name ?? 'N/A'); ?></td>
                    <td><?php echo e($venta->tipoVenta->nombre ?? 'N/A'); ?></td>
                    <td><?php echo e($venta->created_at->format('d/m/Y')); ?></td>
                    <td>$<?php echo e(number_format($venta->total, 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html><?php /**PATH /var/www/html/sistema-facturacion/resources/views/ventas/all-pdf.blade.php ENDPATH**/ ?>