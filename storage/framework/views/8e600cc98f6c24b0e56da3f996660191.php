<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Equipos</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; margin: 0; padding: 20px; }
        h1 { font-size: 20px; margin-bottom: 5px; color: #1a1a1a; }
        .subtitle { font-size: 12px; color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 9px; }
        th { background: #f0f0f0; border: 1px solid #ddd; padding: 6px 4px; text-align: left; font-weight: bold; }
        td { border: 1px solid #ddd; padding: 5px 4px; }
        .footer { margin-top: 30px; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
        .total { font-size: 14px; font-weight: bold; color: #059669; margin-top: 15px; }
        .header-section { display: flex; justify-content: space-between; align-items: center; }
        .stat-box { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 8px 15px; text-align: center; }
        .stat-box .val { font-size: 18px; font-weight: bold; }
        .stat-box .lbl { font-size: 9px; color: #666; }
    </style>
</head>
<body>
    <div class="header-section">
        <div>
            <h1>Reporte de Equipos</h1>
            <div class="subtitle">
                Fecha de generación: <?php echo e(now()->format('d/m/Y H:i')); ?>

                &nbsp;|&nbsp;
                Periodo: <?php echo e($desde); ?> a <?php echo e($hasta); ?>

            </div>
        </div>
        <div class="stat-box">
            <div class="val" style="color: #059669;">RD$ <?php echo e(number_format($totalIngresos, 2)); ?></div>
            <div class="lbl">Total Ingresos</div>
        </div>
    </div>

    <div style="display: flex; gap: 20px; margin-top: 15px;">
        <div class="stat-box">
            <div class="val"><?php echo e(number_format($equipos->count())); ?></div>
            <div class="lbl">Equipos Vendidos</div>
        </div>
        <div class="stat-box">
            <div class="val">RD$ <?php echo e(number_format($equipos->isNotEmpty() ? $totalIngresos / $equipos->count() : 0, 2)); ?></div>
            <div class="lbl">Promedio por Equipo</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>IMEI</th>
                <th>ESN</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Color</th>
                <th>Tipo</th>
                <th>Cliente</th>
                <th>NCF</th>
                <th>Precio</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $equipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $ev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($i + 1); ?></td>
                <td><?php echo e($ev->equipo->serial_imei); ?></td>
                <td><?php echo e($ev->equipo->serial_esn ?? '-'); ?></td>
                <td><?php echo e($ev->equipo->marca); ?></td>
                <td><?php echo e($ev->equipo->modelo ?? '-'); ?></td>
                <td><?php echo e($ev->equipo->color ?? '-'); ?></td>
                <td><?php echo e(ucfirst($ev->equipo->tipo_dispositivo ?? '')); ?></td>
                <td><?php echo e($ev->venta->cliente->nombre ?? '-'); ?></td>
                <td><?php echo e($ev->venta->ncf ?? '-'); ?></td>
                <td>RD$ <?php echo e(number_format($ev->precio_vendido, 2)); ?></td>
                <td><?php echo e($ev->created_at->format('d/m/Y H:i')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot>
            <tr style="background: #f0f0f0; font-weight: bold;">
                <td colspan="9" style="text-align: right;">TOTAL:</td>
                <td>RD$ <?php echo e(number_format($totalIngresos, 2)); ?></td>
                <td><?php echo e($equipos->count()); ?> equipos</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Generado desde el sistema de facturación · <?php echo e(config('app.name')); ?> · <?php echo e(now()->format('d/m/Y H:i:s')); ?>

    </div>
</body>
</html>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/reportes/equipos-pdf.blade.php ENDPATH**/ ?>