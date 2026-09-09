<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listado de Compras</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h2, h3 { margin: 0; }
        .header { text-align: center; margin-bottom: 20px; }
        .header small { color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; }
        th { background: #eee; }
        .text-end { text-align: right; }
    </style>
</head>
<body>
    <?php $empresa = \App\Models\SystemSetting::allCached(); ?>
    <div class="header">
        <?php if($pdfLogoUrl): ?>
        <img src="<?php echo e($pdfLogoUrl); ?>" style="max-width: 80px; max-height: 60px; object-fit: contain; margin-bottom: 5px;" alt="Logo">
        <?php endif; ?>
        <h2><?php echo e(\App\Models\SystemSetting::nombreEmpresaActual()); ?></h2>
        <small>RNC: <?php echo e($empresa['empresa_rnc'] ?? 'N/A'); ?></small>
    </div>
    <h3>Listado de Compras</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Proveedor</th>
                <th>RNC/Cédula</th>
                <th>Usuario</th>
                <th>Tipo</th>
                <th>Fecha</th>
                <th class="text-end">Subtotal</th>
                <th class="text-end">ITBIS</th>
                <th class="text-end">Retenciones</th>
                <th class="text-end">Total</th>
                <th class="text-end">Total a pagar</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $compras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compra): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($compra->id); ?></td>
                    <td><?php echo e($compra->proveedor->nombre ?? 'N/A'); ?></td>
                    <td><?php echo e($compra->proveedor->rnc_cedula ?: ($compra->proveedor->rnc ?? 'N/A')); ?></td>
                    <td><?php echo e($compra->user->name ?? 'N/A'); ?></td>
                    <td><?php echo e($compra->tipoCompra->nombre ?? 'N/A'); ?></td>
                    <td><?php echo e($compra->fecha ? $compra->fecha->format('d/m/Y') : $compra->created_at->format('d/m/Y')); ?></td>
                    <td class="text-end">$<?php echo e(number_format($compra->subtotal, 2)); ?></td>
                    <td class="text-end">$<?php echo e(number_format($compra->itbis_total, 2)); ?></td>
                    <td class="text-end">$<?php echo e(number_format($compra->total_retenciones, 2)); ?></td>
                    <td class="text-end">$<?php echo e(number_format($compra->total, 2)); ?></td>
                    <td class="text-end">$<?php echo e(number_format($compra->total_pagar, 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html><?php /**PATH /var/www/html/sistema-facturacion/resources/views/compras/all-pdf.blade.php ENDPATH**/ ?>