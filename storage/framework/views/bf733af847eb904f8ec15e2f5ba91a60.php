<style>
    body { font-family: 'Courier New', monospace; }
    .ticket { max-width: <?php echo e($paper === 58 ? '58mm' : '80mm'); ?>; margin: 0 auto; padding: 4mm; }
    .ticket h1, .ticket h2, .ticket h3 { margin: 0; text-align: center; }
    .ticket .center { text-align: center; }
    .ticket .right { text-align: right; }
    .ticket .hr { border-top: 1px dashed #000; margin: 4px 0; }
    .ticket table { width: 100%; border-collapse: collapse; font-size: 11px; }
    .ticket table th, .ticket table td { padding: 1px 0; }
    .ticket table .qty { width: 40px; text-align: right; }
    .ticket table .name { text-align: left; }
    @media print {
        body { margin: 0; padding: 0; }
        .no-print { display: none; }
        .ticket { padding: 2mm; }
    }
</style>

<div class="ticket">
    <h2><i class="bi bi-truck"></i> CONDUCE</h2>
    <div class="center small"><?php echo e(str_pad('', 32, '-')); ?></div>
    <?php if($pdfLogoUrl): ?>
    <div class="center" style="margin-bottom: 5px;">
        <img src="<?php echo e($pdfLogoUrl); ?>" style="max-width: 60px; max-height: 40px; object-fit: contain;" alt="Logo">
    </div>
    <?php endif; ?>
    <p class="center"><strong><?php echo e(\App\Models\SystemSetting::nombreEmpresaActual()); ?></strong><br>
    <span class="small">RNC: <?php echo e($empresa->rnc ?? '000-0000000-0'); ?></span><br>
    <span class="small"><?php echo e($empresa->direccion ?? 'Santo Domingo, RD'); ?></span><br>
    <span class="small">Tel: <?php echo e($empresa->telefono ?? '(809) 000-0000'); ?></span></p>

    <div class="hr"></div>
    <p class="small">
        <strong>No.:</strong> <?php echo e($conduce->numero); ?><br>
        <strong>Fecha:</strong> <?php echo e($conduce->fecha->format('d/m/Y')); ?><br>
        <?php if($conduce->fecha_entrega): ?>
        <strong>Entrega:</strong> <?php echo e($conduce->fecha_entrega->format('d/m/Y')); ?><br>
        <?php endif; ?>
        <strong>Estado:</strong> <?php echo e($conduce->estado_label); ?>

    </p>

    <div class="hr"></div>
    <p class="small">
        <strong>Cliente:</strong> <?php echo e($conduce->cliente?->nombre ?? 'N/A'); ?><br>
        <?php if($conduce->cliente?->rnc_cedula): ?>
        <strong>RNC:</strong> <?php echo e($conduce->cliente->rnc_cedula); ?><br>
        <?php endif; ?>
        <?php if($conduce->cliente?->telefono): ?>
        <strong>Tel:</strong> <?php echo e($conduce->cliente->telefono); ?>

        <?php endif; ?>
    </p>

    <div class="hr"></div>
    <table>
        <thead>
            <tr>
                <th class="qty">Cant</th>
                <th class="name">Producto</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $conduce->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="qty"><?php echo e(number_format($item->cantidad, 0)); ?></td>
                <td class="name">
                    <?php echo e($item->nombre); ?>

                    <?php if($conduce->estado === 'entregado' && $item->cantidad_recibida !== null): ?>
                    <br><small>(Rec: <?php echo e(number_format($item->cantidad_recibida, 0)); ?>)</small>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="hr"></div>
    <p class="small">
        <strong>Total items:</strong> <?php echo e($conduce->total_items); ?><br>
        <?php if($conduce->peso_total): ?>
        <strong>Peso:</strong> <?php echo e(number_format($conduce->peso_total, 2)); ?> kg<br>
        <?php endif; ?>
    </p>

    <?php if($conduce->transportista || $conduce->chofer): ?>
    <div class="hr"></div>
    <p class="small">
        <strong>TRANSPORTE</strong><br>
        <?php if($conduce->transportista): ?>Empresa: <?php echo e($conduce->transportista); ?><br><?php endif; ?>
        <?php if($conduce->chofer): ?>Chofer: <?php echo e($conduce->chofer); ?><br><?php endif; ?>
        <?php if($conduce->vehiculo): ?>Vehículo: <?php echo e($conduce->vehiculo); ?><br><?php endif; ?>
        <?php if($conduce->placa): ?>Placa: <?php echo e($conduce->placa); ?><?php endif; ?>
    </p>
    <?php endif; ?>

    <?php if($conduce->estado === 'entregado'): ?>
    <div class="hr"></div>
    <p class="small">
        <strong>RECIBIDO POR:</strong> <?php echo e($conduce->recibido_por); ?><br>
        <?php if($conduce->recibido_cedula): ?>Cédula: <?php echo e($conduce->recibido_cedula); ?><br><?php endif; ?>
        <?php echo e($conduce->fecha_recibido?->format('d/m/Y H:i')); ?>

    </p>
    <?php endif; ?>

    <div class="hr"></div>
    <p class="center small">
        _________________________<br>
        Recibido conforme
    </p>
    <p class="center small" style="margin-top: 8mm;"><?php echo e(now()->format('d/m/Y H:i:s')); ?></p>
</div>

<div class="no-print text-center mt-3">
    <button onclick="window.print()" class="btn btn-primary">
        <i class="bi bi-printer me-1"></i>Imprimir
    </button>
    <button onclick="window.close()" class="btn btn-secondary">Cerrar</button>
</div>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/conduces/ticket.blade.php ENDPATH**/ ?>