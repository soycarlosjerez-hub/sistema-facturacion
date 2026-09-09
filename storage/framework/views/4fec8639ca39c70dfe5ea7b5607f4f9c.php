<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Categorías</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; }
        .header { border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 2px 0; font-size: 10px; color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background-color: #1e293b; color: #fff; font-size: 10px; text-transform: uppercase; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .text-center { text-align: center; }
        .footer { margin-top: 20px; font-size: 9px; color: #64748b; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <?php if($pdfLogoUrl): ?>
        <img src="<?php echo e($pdfLogoUrl); ?>" style="max-width: 60px; max-height: 45px; object-fit: contain; margin-bottom: 5px;" alt="Logo">
        <?php endif; ?>
        <h1>Reporte de Categorías</h1>
        <?php $empresa = \App\Models\SystemSetting::allCached(); ?>
        <p><?php echo e(\App\Models\SystemSetting::nombreEmpresaActual()); ?> — Generado el <?php echo e(date('d/m/Y H:i A')); ?></p>
        <p>Total de categorías: <strong><?php echo e($categorias->count()); ?></strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th class="text-center">Productos</th>
                <th class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_0 = true; $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
            <tr>
                <td><strong><?php echo e($categoria->nombre); ?></strong></td>
                <td><?php echo e($categoria->descripcion ?? '—'); ?></td>
                <td class="text-center"><?php echo e($categoria->products_count ?? $categoria->products->count()); ?></td>
                <td class="text-center"><?php echo e($categoria->activa ? 'Activa' : 'Inactiva'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
            <tr>
                <td colspan="4" class="text-center" style="padding: 20px;">No hay categorías para mostrar.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        Sistema de Facturación — Reporte generado automáticamente
    </div>
</body>
</html>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/categorias/pdf.blade.php ENDPATH**/ ?>