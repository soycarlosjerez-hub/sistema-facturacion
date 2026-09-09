<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Productos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; }
        .header { border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 2px 0; font-size: 10px; color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background-color: #1e293b; color: #fff; font-size: 10px; text-transform: uppercase; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge-low { color: #b45309; font-weight: bold; }
        .badge-crit { color: #b91c1c; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 9px; color: #64748b; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <?php if($pdfLogoUrl): ?>
        <img src="<?php echo e($pdfLogoUrl); ?>" style="max-width: 60px; max-height: 45px; object-fit: contain; margin-bottom: 5px;" alt="Logo">
        <?php endif; ?>
        <h1>Reporte de Productos</h1>
        <?php
            $empresa = [];
            if (class_exists(\App\Models\SystemSetting::class)) {
                $empresa = \App\Models\SystemSetting::allCached();
            }
        ?>
        <p><?php echo e(\App\Models\SystemSetting::nombreEmpresaActual()); ?> — Generado el <?php echo e(date('d/m/Y H:i A')); ?></p>
        <p>Total de productos: <strong><?php echo e($productos->count()); ?></strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Código</th>
                <th>Descripción</th>
                <th class="text-right">Precio</th>
                <th class="text-right">Costo</th>
                <th class="text-right">ITBIS</th>
                <th class="text-center">Stock</th>
                <th class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_0 = true; $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
            <tr>
                <td><strong><?php echo e($producto->nombre); ?></strong></td>
                <td><?php echo e($producto->codigo_barras ?? '—'); ?></td>
                <td><?php echo e(Str::limit($producto->descripcion ?? '—', 40)); ?></td>
                <td class="text-right">RD$ <?php echo e(number_format($producto->precio, 2)); ?></td>
                <td class="text-right">RD$ <?php echo e(number_format($producto->precio_compra ?? 0, 2)); ?></td>
                <td class="text-right"><?php echo e(number_format($producto->itbis_porcentaje ?? $systemItbis ?? 18, 2)); ?>%</td>
                <td class="text-center <?php echo e($producto->estado_stock === 'critical' ? 'badge-crit' : ($producto->estado_stock === 'low' ? 'badge-low' : '')); ?>">
                    <?php echo e($producto->stock); ?>

                </td>
                <td class="text-center"><?php echo e($producto->activo_label); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
            <tr>
                <td colspan="8" class="text-center" style="padding: 20px;">No hay productos para mostrar.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Sistema de Facturación — Reporte generado automáticamente
    </div>
</body>
</html>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/productos/pdf.blade.php ENDPATH**/ ?>