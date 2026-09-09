<h1>Orden Confirmada #<?php echo e($orden->id); ?></h1>
<p>Gracias por tu orden <strong><?php echo e($orden->cliente?->nombre); ?></strong>.</p>

<?php if($orden->tipo_orden === 'delivery'): ?>
<p>Tu pedido será enviado a: <?php echo e($orden->direccion_entrega); ?></p>
<?php elseif($orden->tipo_orden === 'pickup'): ?>
<p>Puedes recoger tu pedido a partir de las <?php echo e($orden->hora_retiro?->format('h:i A')); ?>.</p>
<?php endif; ?>

<h3>Productos:</h3>
<ul>
<?php $__currentLoopData = $orden->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<li><?php echo e($detalle->cantidad); ?>x <?php echo e($detalle->producto?->nombre); ?> - RD$ <?php echo e(number_format($detalle->subtotal, 2)); ?></li>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul>

<p><strong>Total: RD$ <?php echo e(number_format($orden->subtotal + $orden->impuestos, 2)); ?></strong></p>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/emails/orden-confirmada.blade.php ENDPATH**/ ?>