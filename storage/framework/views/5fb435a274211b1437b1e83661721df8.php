<h1>Tu Pedido está en Camino #<?php echo e($orden->id); ?></h1>
<p>Hola <strong><?php echo e($orden->cliente?->nombre); ?></strong>, tu pedido ya está en camino.</p>
<p>Dirección de entrega: <?php echo e($orden->direccion_entrega); ?></p>
<p><strong>Total pagado: RD$ <?php echo e(number_format($orden->subtotal + $orden->impuestos, 2)); ?></strong></p>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/emails/orden-en-camino.blade.php ENDPATH**/ ?>