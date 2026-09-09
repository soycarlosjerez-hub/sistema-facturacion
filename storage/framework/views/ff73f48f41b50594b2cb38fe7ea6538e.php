RESERVACIÓN CONFIRMADA
==============================================

Estimado/a <?php echo e($reservacion->cliente_nombre); ?>,

Nos complace informarte que tu reservación ha sido confirmada exitosamente. Te esperamos con mucho gusto.

ESTADO
----------------------------------------------
✅ Confirmada

DETALLES DE LA RESERVACIÓN
----------------------------------------------
Mesa:           <?php echo e($reservacion->mesa->nombre ?? 'Mesa ' . $reservacion->mesa->numero); ?>

Fecha y hora:   <?php echo e($reservacion->fecha_hora->format('d/m/Y H:i')); ?>

Personas:       <?php echo e($reservacion->personas); ?>

<?php if($reservacion->notas): ?>
Notas:          <?php echo e($reservacion->notas); ?>

<?php endif; ?>

Recuerda llegar puntualmente. Si necesitas cancelar o modificar tu reservación, por favor avísanos con anticipación.

¡Te esperamos!

Atentamente,
<?php echo e(config('app.name')); ?>


---
Este correo fue enviado automáticamente por el sistema de facturación.
© <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. Todos los derechos reservados.
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/emails/reservacion-confirmada-text.blade.php ENDPATH**/ ?>