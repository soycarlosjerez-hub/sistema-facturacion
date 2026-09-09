<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Ticket Mesa <?php echo e($mesa->numero); ?></title>
<style>
body { font-family: 'Courier New', monospace; font-size: 12px; width: <?php echo e($paper); ?>mm; margin: 0 auto; padding: 8px; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 2px 0; text-align: left; }
.text-right { text-align: right; }
.text-center { text-align: center; }
.sep { border-top: 1px dashed #000; }
.fw-bold { font-weight: bold; }
.total-row td { border-top: 2px solid #000; font-weight: bold; font-size: 14px; }
.mesa-info { background: #f0f0f0; padding: 4px 8px; border-radius: 4px; }
@page { margin: 0; }
@media print { body { margin: 0; padding: 4px; } }
</style>
</head>
<body>
    <?php if($pdfLogoUrl): ?>
    <div class="text-center mb-2">
        <img src="<?php echo e($pdfLogoUrl); ?>" style="max-width: 60px; max-height: 40px; object-fit: contain;" alt="Logo">
    </div>
    <?php endif; ?>
    <div class="text-center fw-bold"><?php echo e(\App\Models\SystemSetting::nombreEmpresaActual()); ?></div>
    <div class="text-center">RNC: <?php echo e($empresa->rnc ?? 'N/A'); ?></div>
    <div class="sep"></div>
    <div class="text-center fw-bold">*** TICKET MESA ***</div>
    <div class="mesa-info text-center fw-bold">MESA #<?php echo e($mesa->numero); ?> - <?php echo e($mesa->nombre ?? ''); ?></div>
    <div class="sep"></div>
    <table>
        <tr><td>Factura:</td><td class="text-right">#<?php echo e(str_pad($venta->id, 6, '0', STR_PAD_LEFT)); ?></td></tr>
        <tr><td>Fecha:</td><td class="text-right"><?php echo e(now()->format('d/m/Y H:i')); ?></td></tr>
        <tr><td>Cliente:</td><td class="text-right"><?php echo e($venta->cliente->nombre ?? 'Consumidor Final'); ?></td></tr>
    </table>
    <div class="sep"></div>
    <table>
        <tr><th>Plato</th><th class="text-right">Cant</th><th class="text-right">Precio</th><th class="text-right">Subtotal</th></tr>
        <?php $__currentLoopData = $venta->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($d->producto->nombre ?? 'N/A'); ?>

                <?php if($d->notas): ?> <br><small style="font-size:9px;">📝 <?php echo e($d->notas); ?></small> <?php endif; ?>
            </td>
            <td class="text-right"><?php echo e($d->cantidad); ?></td>
            <td class="text-right"><?php echo e(number_format($d->precio_unitario, 2)); ?></td>
            <td class="text-right"><?php echo e(number_format($d->subtotal, 2)); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
    <div class="sep"></div>
    <table>
        <tr><td>Subtotal:</td><td class="text-right"><?php echo e(number_format($venta->subtotal, 2)); ?></td></tr>
        <?php if($venta->descuento > 0): ?>
        <tr><td>Descuento:</td><td class="text-right">-<?php echo e(number_format($venta->descuento, 2)); ?></td></tr>
        <?php endif; ?>
        <tr><td>ITBIS:</td><td class="text-right"><?php echo e(number_format($venta->impuestos, 2)); ?></td></tr>
        <tr class="total-row"><td>TOTAL:</td><td class="text-right">RD$ <?php echo e(number_format($venta->total, 2)); ?></td></tr>
    </table>
    <?php $pago = $venta->pagos->first(); ?>
    <?php if($pago): ?>
    <div class="sep"></div>
    <div class="text-center" style="font-size:11px;">
        <?php echo e(ucfirst($pago->metodo_pago)); ?>

        <?php if($venta->pagos->count() > 1): ?>
            <br>Pagos combinados (<?php echo e($venta->pagos->count()); ?>)
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="sep"></div>
    <div class="text-center">Gracias por su visita</div>
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() { window.print(); }, 300);
        });
    </script>
</body>
</html>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/restaurante/ticket.blade.php ENDPATH**/ ?>