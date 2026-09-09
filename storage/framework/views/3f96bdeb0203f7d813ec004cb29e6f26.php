<h1>Orden Lista para Recoger #<?php echo e($orden->id); ?></h1>
<p>Hola <strong><?php echo e($orden->cliente?->nombre); ?></strong>, tu orden ya está lista.</p>
<p>Puedes pasar a recogerla por nuestro local.</p>
<p><strong>Total pagado: RD$ <?php echo e(number_format($orden->subtotal + $orden->impuestos, 2)); ?></strong></p>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/emails/orden-lista.blade.php ENDPATH**/ ?>