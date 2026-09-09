<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Clientes</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <?php $empresa = \App\Models\SystemSetting::allCached(); ?>
    <div style="text-align:center;margin-bottom:20px;">
        <?php if($pdfLogoUrl): ?>
        <img src="<?php echo e($pdfLogoUrl); ?>" style="max-width: 80px; max-height: 60px; object-fit: contain; margin-bottom: 5px;" alt="Logo">
        <?php endif; ?>
        <h1 style="margin:0;font-size:20px;"><?php echo e(\App\Models\SystemSetting::nombreEmpresaActual()); ?></h1>
        <small>RNC: <?php echo e($empresa['empresa_rnc'] ?? 'N/A'); ?></small>
    </div>
    <h2>Listado de Clientes</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($cliente->nombre); ?></td>
                    <td><?php echo e($cliente->email); ?></td>
                    <td><?php echo e($cliente->telefono); ?></td>
                    <td><?php echo e($cliente->direccion); ?></td>
                    <td><?php echo e($cliente->activo_label); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/clientes/pdf.blade.php ENDPATH**/ ?>