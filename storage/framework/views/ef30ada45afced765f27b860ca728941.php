RESERVACIÓN RECIBIDA
==============================================

Estimado/a <?php echo e($reservacion->cliente_nombre); ?>,

Hemos recibido tu reservación y está pendiente de confirmación. Nuestro equipo la revisará y te notificaremos pronto.

ESTADO
----------------------------------------------
Pendiente de confirmación

DETALLES DE LA RESERVACIÓN
----------------------------------------------
Mesa:           <?php echo e($reservacion->mesa->nombre ?? 'Mesa ' . $reservacion->mesa->numero); ?>

Fecha y hora:   <?php echo e($reservacion->fecha_hora->format('d/m/Y H:i')); ?>

Personas:       <?php echo e($reservacion->personas); ?>

<?php if($reservacion->notas): ?>
Notas:          <?php echo e($reservacion->notas); ?>

<?php endif; ?>

Si necesitas realizar algún cambio, por favor contáctanos directamente.

Atentamente,
<?php echo e(config('app.name')); ?>


---
Este correo fue enviado automáticamente por el sistema de facturación.
© <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. Todos los derechos reservados.
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/emails/reservacion-recibida-text.blade.php ENDPATH**/ ?>