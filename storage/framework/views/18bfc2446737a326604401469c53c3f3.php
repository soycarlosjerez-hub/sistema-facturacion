<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orden #<?php echo e($orden->id); ?></title>
    <style>
        body { font-family: monospace; font-size: 12px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 4px 2px; }
        hr { border-top: 1px dashed #000; }
    </style>
</head>
<body>
    <div class="text-center">
        <h2>ORDEN #<?php echo e($orden->id); ?></h2>
        <p><?php echo e(now()->format('d/m/Y h:i A')); ?></p>
        <p>Tipo: <?php echo e(ucfirst($orden->tipo_orden)); ?></p>
        <?php if($orden->cliente): ?><p>Cliente: <?php echo e($orden->cliente->nombre); ?></p><?php endif; ?>
        <hr>
    </div>

    <table>
        <thead>
            <tr>
                <th>Cant</th>
                <th>Producto</th>
                <th class="text-right">Precio</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $orden->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($d->cantidad); ?></td>
                <td><?php echo e($d->producto?->nombre ?? '—'); ?></td>
                <td class="text-right">RD$ <?php echo e(number_format($d->subtotal, 2)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <hr>
    <table>
        <tr><td>Subtotal</td><td class="text-right">RD$ <?php echo e(number_format($orden->subtotal, 2)); ?></td></tr>
        <tr><td>Impuestos</td><td class="text-right">RD$ <?php echo e(number_format($orden->impuestos, 2)); ?></td></tr>
        <?php if($orden->descuento > 0): ?>
        <tr><td>Descuento</td><td class="text-right">-RD$ <?php echo e(number_format($orden->descuento, 2)); ?></td></tr>
        <?php endif; ?>
        <tr style="font-weight:bold; font-size:14px;">
            <td>TOTAL</td>
            <td class="text-right">RD$ <?php echo e(number_format($orden->subtotal + $orden->impuestos - $orden->descuento, 2)); ?></td>
        </tr>
    </table>

    <?php if($orden->pagos && $orden->pagos->count() > 0): ?>
    <hr>
    <table>
        <?php $__currentLoopData = $orden->pagos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr><td><?php echo e(ucfirst($p->metodo_pago)); ?></td><td class="text-right">RD$ <?php echo e(number_format($p->monto, 2)); ?></td></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
    <?php endif; ?>

    <div class="text-center" style="margin-top:10px;">
        <p>¡Gracias por su preferencia!</p>
    </div>
</body>
</html>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/ordenes/ticket.blade.php ENDPATH**/ ?>