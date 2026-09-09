<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kardex de Inventario</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1e293b; }
        .header { border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 2px 0; font-size: 10px; color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 5px 6px; text-align: left; }
        th { background-color: #1e293b; color: #fff; font-size: 9px; text-transform: uppercase; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .entrada { color: #16a34a; font-weight: bold; }
        .salida { color: #dc2626; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 9px; color: #64748b; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <?php if($pdfLogoUrl): ?>
        <img src="<?php echo e($pdfLogoUrl); ?>" style="max-width: 60px; max-height: 45px; object-fit: contain; margin-bottom: 5px;" alt="Logo">
        <?php endif; ?>
        <h1>Kardex de Inventario</h1>
        <?php $empresa = \App\Models\SystemSetting::allCached(); ?>
        <p><?php echo e(\App\Models\SystemSetting::nombreEmpresaActual()); ?> — Generado el <?php echo e(date('d/m/Y H:i A')); ?></p>
        <p>Total de movimientos: <strong><?php echo e($movimientos->count()); ?></strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Producto</th>
                <th class="text-center">Almacén</th>
                <th class="text-center">Tipo</th>
                <th class="text-center">Cantidad</th>
                <th>Concepto / Nota</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_0 = true; $__currentLoopData = $movimientos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
            <tr>
                <td><?php echo e($m->created_at->format('d/m/Y h:i A')); ?></td>
                <td><strong><?php echo e($m->producto->nombre); ?></strong></td>
                <td class="text-center"><?php echo e($m->almacen->nombre); ?></td>
                <td class="text-center <?php echo e($m->tipo); ?>"><?php echo e(ucfirst($m->tipo)); ?></td>
                <td class="text-center <?php echo e($m->tipo); ?>"><?php echo e($m->tipo === 'entrada' ? '+' : '-'); ?><?php echo e($m->cantidad); ?></td>
                <td><?php echo e($m->nota ?? $m->motivo ?? 'Movimiento de inventario'); ?></td>
                <td><?php echo e($m->user->name ?? 'Sistema'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px;">No hay movimientos para mostrar.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Sistema de Facturación — Reporte generado automáticamente
    </div>
</body>
</html>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/kardex/pdf.blade.php ENDPATH**/ ?>