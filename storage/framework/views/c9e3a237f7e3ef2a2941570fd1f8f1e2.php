COTIZACIÓN <?php echo e($cotizacion->numero); ?>

==============================================

Estimado/a <?php echo e($cotizacion->cliente?->nombre ?? 'Cliente'); ?>,

Adjunto encontrará la cotización <?php echo e($cotizacion->numero); ?> con los detalles
de los productos y servicios solicitados.

<?php if($mensajeAdicional): ?>
Mensaje adicional:
<?php echo e($mensajeAdicional); ?>


<?php endif; ?>
INFORMACIÓN
----------------------------------------------
Número:           <?php echo e($cotizacion->numero); ?>

Fecha emisión:    <?php echo e($cotizacion->fecha->format('d/m/Y')); ?>

Válida hasta:     <?php echo e($cotizacion->fecha_validez->format('d/m/Y')); ?> (<?php echo e($cotizacion->dias_validez); ?> días)
Atendido por:     <?php echo e($cotizacion->user?->name ?? 'N/A'); ?>


DETALLE DE PRODUCTOS
----------------------------------------------
<?php $__currentLoopData = $cotizacion->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php echo e($item->nombre); ?> (<?php echo e($item->codigo ?? 'N/A'); ?>)
   Cant: <?php echo e($item->cantidad); ?> x RD$<?php echo e(number_format($item->precio_unitario, 2)); ?> = RD$<?php echo e(number_format($item->subtotal, 2)); ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

TOTALES
----------------------------------------------
Subtotal:    RD$<?php echo e(number_format($cotizacion->subtotal, 2)); ?>

<?php if($cotizacion->descuento > 0): ?>
Descuento:  -RD$<?php echo e(number_format($cotizacion->descuento, 2)); ?>

<?php endif; ?>
ITBIS (18%): RD$<?php echo e(number_format($cotizacion->itbis, 2)); ?>

----------------------------------------------
TOTAL:       RD$<?php echo e(number_format($cotizacion->total, 2)); ?>


<?php if($cotizacion->condiciones): ?>
TÉRMINOS Y CONDICIONES
----------------------------------------------
<?php echo e($cotizacion->condiciones); ?>


<?php endif; ?>
Ver cotización en línea:
<?php echo e($urlVer); ?>


La cotización completa en PDF se adjunta a este correo.

Si tiene alguna pregunta o necesita aclaraciones, no dude en contactarnos.

Atentamente,
<?php echo e($cotizacion->user?->name ?? 'Equipo de Ventas'); ?>


---
Este correo fue enviado automáticamente por el sistema de facturación.
© <?php echo e(date('Y')); ?> Sistema de Facturación. Todos los derechos reservados.
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/emails/cotizacion-enviada-text.blade.php ENDPATH**/ ?>