<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Conduce <?php echo e($conduce->numero); ?></title>
<style>
    @page { margin: 10mm; }
    body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #222; }
    .container { max-width: 200mm; margin: 0 auto; }
    .header { display: flex; justify-content: space-between; border-bottom: 3px solid #0d6efd; padding-bottom: 10px; margin-bottom: 15px; }
    .header .left h1 { color: #0d6efd; margin: 0 0 5px; font-size: 24px; }
    .header .left .small { color: #666; }
    .header .right { text-align: right; }
    .header .right .numero { font-size: 22px; font-weight: bold; color: #0d6efd; }
    .header .right .estado { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 10px; font-weight: bold; margin-top: 4px; }
    .estado-borrador { background: #f0f0f0; color: #555; }
    .estado-en_transito { background: #cfe2ff; color: #084298; }
    .estado-entregado { background: #d1e7dd; color: #0f5132; }
    .estado-devuelto { background: #fff3cd; color: #664d03; }
    .estado-cancelado { background: #f8d7da; color: #842029; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
    .info-box { border: 1px solid #dee2e6; border-radius: 5px; padding: 10px; }
    .info-box h3 { font-size: 11px; text-transform: uppercase; color: #666; margin: 0 0 5px; border-bottom: 1px solid #eee; padding-bottom: 4px; }
    .info-box p { margin: 2px 0; font-size: 11px; }
    .info-box p strong { display: inline-block; min-width: 70px; }
    table.items { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    table.items th { background: #0d6efd; color: white; padding: 8px; text-align: left; font-size: 10px; text-transform: uppercase; }
    table.items td { padding: 6px 8px; border-bottom: 1px solid #eee; }
    table.items tr:nth-child(even) { background: #f8f9fa; }
    table.items .right { text-align: right; }
    table.items .center { text-align: center; }
    .footer { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 25px; }
    .signature { border-top: 1px solid #222; padding-top: 5px; text-align: center; font-size: 10px; }
    .notes { background: #fff8e1; border-left: 3px solid #ffc107; padding: 8px; margin-top: 15px; font-size: 10px; }
    .small { font-size: 10px; color: #666; }
</style>
</head>
<body>
<div class="container">
    <?php $empresaData = \App\Models\SystemSetting::allCached(); ?>
    <div class="header">
        <div class="left">
            <h1><i class="bi bi-truck"></i> CONDUCE</h1>
            <div class="small">Nota de Entrega</div>
            <?php if($pdfLogoUrl): ?>
            <img src="<?php echo e($pdfLogoUrl); ?>" style="max-width: 70px; max-height: 50px; object-fit: contain; margin-bottom: 5px;" alt="Logo">
            <?php endif; ?>
            <p class="small">
                <strong><?php echo e(\App\Models\SystemSetting::nombreEmpresaActual()); ?></strong><br>
                RNC: <?php echo e($empresaData['empresa_rnc'] ?? 'N/A'); ?><br>
                <?php echo e($empresaData['empresa_direccion'] ?? ''); ?><br>
                <?php echo e($empresaData['empresa_telefono'] ?? ''); ?>

            </p>
        </div>
        <div class="right">
            <div class="numero"><?php echo e($conduce->numero); ?></div>
            <div>Fecha: <?php echo e($conduce->fecha->format('d/m/Y')); ?></div>
            <?php if($conduce->fecha_entrega): ?>
            <div>Entrega: <?php echo e($conduce->fecha_entrega->format('d/m/Y')); ?></div>
            <?php endif; ?>
            <div class="estado estado-<?php echo e($conduce->estado); ?>"><?php echo e($conduce->estado_label); ?></div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h3>Datos del Cliente</h3>
            <p><strong>Nombre:</strong> <?php echo e($conduce->cliente?->nombre ?? 'N/A'); ?></p>
            <?php if($conduce->cliente?->rnc_cedula): ?>
            <p><strong>RNC/Cédula:</strong> <?php echo e($conduce->cliente->rnc_cedula); ?></p>
            <?php endif; ?>
            <?php if($conduce->cliente?->telefono): ?>
            <p><strong>Teléfono:</strong> <?php echo e($conduce->cliente->telefono); ?></p>
            <?php endif; ?>
            <?php if($conduce->cliente?->direccion): ?>
            <p><strong>Dirección:</strong> <?php echo e($conduce->cliente->direccion); ?></p>
            <?php endif; ?>
        </div>
        <div class="info-box">
            <h3>Información de Entrega</h3>
            <p><strong>Dirección:</strong> <?php echo e($conduce->direccion_entrega); ?></p>
            <?php if($conduce->referencia): ?><p><strong>Referencia:</strong> <?php echo e($conduce->referencia); ?></p><?php endif; ?>
            <?php if($conduce->contacto_entrega): ?><p><strong>Contacto:</strong> <?php echo e($conduce->contacto_entrega); ?></p><?php endif; ?>
            <?php if($conduce->telefono_entrega): ?><p><strong>Tel. contacto:</strong> <?php echo e($conduce->telefono_entrega); ?></p><?php endif; ?>
        </div>
    </div>

    <?php if($conduce->transportista || $conduce->chofer): ?>
    <div class="info-box" style="margin-bottom: 15px;">
        <h3>Información de Transporte</h3>
        <div class="info-grid" style="margin-bottom: 0;">
            <div>
                <?php if($conduce->transportista): ?><p><strong>Transportista:</strong> <?php echo e($conduce->transportista); ?></p><?php endif; ?>
                <?php if($conduce->vehiculo): ?><p><strong>Vehículo:</strong> <?php echo e($conduce->vehiculo); ?></p><?php endif; ?>
                <?php if($conduce->placa): ?><p><strong>Placa:</strong> <?php echo e($conduce->placa); ?></p><?php endif; ?>
            </div>
            <div>
                <?php if($conduce->chofer): ?><p><strong>Chofer:</strong> <?php echo e($conduce->chofer); ?></p><?php endif; ?>
                <?php if($conduce->chofer_cedula): ?><p><strong>Cédula:</strong> <?php echo e($conduce->chofer_cedula); ?></p><?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <h3 style="font-size: 12px; margin-bottom: 5px;">Productos a Entregar</h3>
    <table class="items">
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>Código</th>
                <th>Descripción</th>
                <th class="center" style="width: 60px;">Cantidad</th>
                <th class="center" style="width: 60px;">Unidad</th>
                <?php if($conduce->estado === 'entregado'): ?>
                <th class="center" style="width: 60px;">Recibido</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $conduce->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e(str_pad($idx + 1, 3, '0', STR_PAD_LEFT)); ?></td>
                <td class="small"><?php echo e($item->codigo ?? '-'); ?></td>
                <td><?php echo e($item->nombre); ?></td>
                <td class="center"><strong><?php echo e(number_format($item->cantidad, 2)); ?></strong></td>
                <td class="center"><?php echo e($item->unidad); ?></td>
                <?php if($conduce->estado === 'entregado'): ?>
                <td class="center"><?php echo e(number_format($item->cantidad_recibida ?? $item->cantidad, 2)); ?></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="<?php echo e($conduce->estado === 'entregado' ? 4 : 3); ?>" class="right"><strong>Total items:</strong></td>
                <td class="center"><strong><?php echo e($conduce->total_items); ?></strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <?php if($conduce->observaciones): ?>
    <div class="notes">
        <strong>Observaciones:</strong> <?php echo e($conduce->observaciones); ?>

    </div>
    <?php endif; ?>

    <?php if($conduce->estado === 'entregado'): ?>
    <div class="info-box" style="margin-top: 15px; border-color: #198754; background: #f0fff4;">
        <h3 style="color: #198754;">Entrega Confirmada</h3>
        <p><strong>Recibido por:</strong> <?php echo e($conduce->recibido_por); ?></p>
        <?php if($conduce->recibido_cedula): ?><p><strong>Cédula:</strong> <?php echo e($conduce->recibido_cedula); ?></p><?php endif; ?>
        <p><strong>Fecha de recepción:</strong> <?php echo e($conduce->fecha_recibido?->format('d/m/Y H:i')); ?></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <div class="signature">
            <br><br>
            <?php echo e($conduce->user?->name ?? ''); ?><br>
            <em>Despachado por</em>
        </div>
        <div class="signature">
            <br><br>
            <?php echo e($conduce->recibido_por ?? '_________________________'); ?><br>
            <em>Recibido por</em>
        </div>
    </div>

    <p class="small" style="text-align: center; margin-top: 15px;">
        Documento generado el <?php echo e(now()->format('d/m/Y H:i:s')); ?>

    </p>
</div>
</body>
</html>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/conduces/pdf.blade.php ENDPATH**/ ?>