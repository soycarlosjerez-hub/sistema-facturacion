RESERVACIÓN CANCELADA
==============================================

Estimado/a <?php echo e($reservacion->cliente_nombre); ?>,

Lamentamos informarte que tu reservación ha sido cancelada.

ESTADO
----------------------------------------------
❌ Cancelada

DETALLES DE LA RESERVACIÓN
----------------------------------------------
Mesa:           <?php echo e($reservacion->mesa->nombre ?? 'Mesa ' . $reservacion->mesa->numero); ?>

Fecha y hora:   <?php echo e($reservacion->fecha_hora->format('d/m/Y H:i')); ?>

Personas:       <?php echo e($reservacion->personas); ?>

<?php if($reservacion->notas): ?>
Notas:          <?php echo e($reservacion->notas); ?>

<?php endif; ?>

¿Deseas reagendar?
No te preocupes, puedes realizar una nueva reservación contactándonos directamente.

Disculpa las molestias.

Atentamente,
<?php echo e(config('app.name')); ?>


---
Este correo fue enviado automáticamente por el sistema de facturación.
© <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. Todos los derechos reservados.
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/emails/reservacion-cancelada-text.blade.php ENDPATH**/ ?>