<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura #<?php echo e($venta->id); ?></title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 15px;
        }

        .header {
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .empresa-nombre {
            font-size: 16px;
            font-weight: bold;
            color: #1a1a1a;
        }

        .empresa-info {
            font-size: 10px;
            color: #555;
            line-height: 1.4;
        }

        .factura-info {
            text-align: right;
        }

        .factura-titulo {
            font-size: 18px;
            font-weight: bold;
            color: #1a1a1a;
        }

        .factura-numero {
            font-size: 12px;
            font-weight: bold;
            color: #333;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #fff;
            background: #333;
            padding: 4px 8px;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th {
            background: #f0f0f0;
            padding: 6px 8px;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }

        td {
            padding: 5px 8px;
            border: 1px solid #ddd;
            font-size: 10px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-table {
            width: 100%;
            margin-top: 10px;
        }

        .totals-table td {
            padding: 4px 8px;
            border: none;
        }

        .totals-label {
            font-weight: bold;
            text-align: right;
            padding-right: 15px;
        }

        .totals-value {
            text-align: right;
            width: 100px;
        }

        .total-final {
            font-size: 14px;
            font-weight: bold;
            background: #f5f5f5;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            color: #fff;
        }

        .badge-completada { background: #198754; }
        .badge-pendiente { background: #ffc107; color: #333; }
        .badge-anulada { background: #dc3545; }
        .badge-cuenta_abierta { background: #0dcaf0; color: #333; }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #777;
            text-align: center;
        }

        .info-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .info-box {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            margin-right: 10px;
        }

        .info-box:last-child {
            margin-right: 0;
        }

        .info-label {
            font-size: 9px;
            color: #777;
            text-transform: uppercase;
        }

        .info-value {
            font-size: 11px;
            font-weight: bold;
            color: #333;
        }

        .ncf-display {
            font-family: monospace;
            font-size: 12px;
            letter-spacing: 1px;
        }

        .anulada-overlay {
            color: #dc3545;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            margin: 10px 0;
            border: 2px solid #dc3545;
            padding: 5px;
        }
    </style>
</head>
<body>

    <?php
        $empresa = \App\Models\SystemSetting::allCached();
        $esAnulada = $venta->trashed() || $venta->estado === 'anulada';
        $garantiaTerminos = [];
        foreach($venta->detalles->where('tipo_linea', '!=', 'delivery') as $d) {
            if(!empty($d->producto->garantia_terminos)) {
                $_key = $d->producto->id . '_' . md5($d->producto->garantia_terminos);
                if(!isset($garantiaTerminos[$_key])) {
                    $garantiaTerminos[$_key] = ['producto' => $d->producto->nombre, 'terminos' => $d->producto->garantia_terminos];
                }
            }
        }
    ?>

    <!-- HEADER EMPRESA -->
    <table style="width:100%; margin-bottom:15px;">
        <tr>
            <td style="border:none; vertical-align:top; width:60%;">
                <?php if($pdfLogoUrl): ?>
                <img src="<?php echo e($pdfLogoUrl); ?>" style="max-width: 80px; max-height: 60px; object-fit: contain; margin-bottom: 5px;" alt="Logo">
                <?php endif; ?>
                <div class="empresa-nombre"><?php echo e(\App\Models\SystemSetting::nombreEmpresaActual()); ?></div>
                <div class="empresa-info">
                    <?php if(!empty($empresa['empresa_rnc'])): ?>
                    RNC/Cédula: <?php echo e($empresa['empresa_rnc']); ?><br>
                    <?php endif; ?>
                    Dirección: <?php echo e($empresa['empresa_direccion'] ?? 'N/A'); ?><br>
                    Tel: <?php echo e($empresa['empresa_telefono'] ?? 'N/A'); ?> | Email: <?php echo e($empresa['empresa_email'] ?? 'N/A'); ?>

                </div>
            </td>
            <td style="border:none; vertical-align:top; text-align:right;">
                <div class="factura-titulo">FACTURA</div>
                <div class="factura-numero">No. <?php echo e(str_pad($venta->id, 5, '0', STR_PAD_LEFT)); ?></div>
                <div style="font-size:10px; color:#555;">
                    Fecha emisión: <?php echo e($venta->created_at->format('d/m/Y H:i')); ?><br>
                    <?php if($venta->ncf_vencimiento): ?>
                    Vence: <?php echo e($venta->ncf_vencimiento->format('d/m/Y')); ?><br>
                    <?php endif; ?>
                    <?php if($venta->tipo_comprobante === 'ecf' && $venta->encf): ?>
                    ENCF: <span class="ncf-display"><?php echo e($venta->encf); ?></span><br>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    </table>

    <!-- ESTADO -->
    <?php if($esAnulada): ?>
    <div class="anulada-overlay">DOCUMENTO ANULADO</div>
    <?php endif; ?>

    <!-- DATOS FISCALES -->
    <?php if($venta->ncf || $venta->ncf_tipo): ?>
    <div class="section-title">DATOS FISCALES</div>
    <table style="margin-bottom:10px;">
        <tr>
            <td style="border:none; width:33%;" class="info-box">
                <div class="info-label">Tipo NCF</div>
                <div class="info-value ncf-display"><?php echo e(strtoupper($venta->ncf_tipo ?? 'N/A')); ?></div>
            </td>
            <td style="border:none; width:33%;" class="info-box">
                <div class="info-label">NCF</div>
                <div class="info-value ncf-display"><?php echo e($venta->ncf ?? 'N/A'); ?></div>
            </td>
            <td style="border:none; width:33%;" class="info-box">
                <div class="info-label">Tipo Comprobante</div>
                <div class="info-value"><?php echo e(ucfirst($venta->tipo_comprobante ?? 'NCF')); ?></div>
            </td>
        </tr>
    </table>
    <?php endif; ?>

    <!-- CLIENTE -->
    <div class="section-title">DATOS DEL CLIENTE</div>
    <table style="margin-bottom:10px;">
        <tr>
            <td style="border:none; width:50%;" class="info-box">
                <div class="info-label">Cliente</div>
                <div class="info-value"><?php echo e($venta->cliente->nombre ?? 'Consumidor Final'); ?></div>
            </td>
            <?php $rncCliente = $venta->cliente->rnc_cedula ?? $venta->cliente->documento ?? ''; ?>
            <?php if(!empty($rncCliente)): ?>
            <td style="border:none; width:50%;" class="info-box">
                <div class="info-label">RNC/Cédula</div>
                <div class="info-value"><?php echo e($rncCliente); ?></div>
            </td>
            <?php else: ?>
            <td style="border:none; width:50%;"></td>
            <?php endif; ?>
        </tr>
    </table>

    <!-- DETALLE PRODUCTOS -->
    <div class="section-title">DETALLE DE PRODUCTOS/SERVICIOS</div>
    <table>
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:40%">Descripción</th>
                <th style="width:10%" class="text-center">Cant.</th>
                <th style="width:15%" class="text-right">P. Unit.</th>
                <th style="width:15%" class="text-right">Subtotal</th>
                <?php if($venta->impuestos > 0): ?>
                <th style="width:5%" class="text-center">%ITBIS</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $venta->detalles->where('tipo_linea', '!=', 'delivery'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center"><?php echo e($loop->iteration); ?></td>
                <td><?php echo e($d->producto->nombre ?? $d->obra->titulo ?? 'Producto'); ?></td>
                <td class="text-center"><?php echo e(number_format($d->cantidad, 2)); ?></td>
                <td class="text-right">$<?php echo e(number_format($d->precio_unitario, 2)); ?></td>
                <td class="text-right">$<?php echo e(number_format($d->subtotal, 2)); ?></td>
                <?php if($venta->impuestos > 0): ?>
                <td class="text-center"><?php echo e($d->sin_itbis ? '0' : ($d->producto->itbis_porcentaje ?? $d->itbis_porcentaje ?? $systemItbis ?? 18)); ?>%<?php echo e($d->sin_itbis ? ' <span style="font-size:7px;color:#dc3545;">(Sin ITBIS)</span>' : ''); ?></td>
                <?php endif; ?>
            </tr>
            <?php if(!empty($d->producto->garantia_dias) && $d->producto->garantia_dias > 0): ?>
            <tr>
                <td colspan="<?php echo e($venta->impuestos > 0 ? '6' : '5'); ?>" style="font-size: 8px; color: #0d6efd; padding-left: 20px;">Garantia: <?php echo e($d->producto->garantia_meses); ?> meses</td>
            </tr>
            <?php endif; ?>
            <?php if($d->notas): ?>
            <tr>
                <td colspan="<?php echo e($venta->impuestos > 0 ? '6' : '5'); ?>" style="font-size: 8px; font-style: italic; color: #666;"><?php echo e($d->notas); ?></td>
            </tr>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <!-- TOTALES CON DESGLOSE -->
    <table class="totals-table">
        <tr>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td class="totals-label">Subtotal Gravado:</td>
            <td class="totals-value">$<?php echo e(number_format($venta->subtotal, 2)); ?></td>
        </tr>
        <?php if($venta->impuestos > 0): ?>
        <tr>
            <td class="totals-label">ITBIS (<?php echo e($venta->impuestos > 0 ? round(($venta->impuestos / max($venta->subtotal, 1)) * 100) : 0); ?>%):</td>
            <td class="totals-value">$<?php echo e(number_format($venta->impuestos, 2)); ?></td>
        </tr>
        <?php endif; ?>
        <?php if($venta->descuento > 0): ?>
        <tr>
            <td class="totals-label">Descuento:</td>
            <td class="totals-value" style="color:#dc3545;">-$<?php echo e(number_format($venta->descuento, 2)); ?></td>
        </tr>
        <?php endif; ?>
        <?php if($venta->propina > 0): ?>
        <tr>
            <td class="totals-label">Propina:</td>
            <td class="totals-value">$<?php echo e(number_format($venta->propina, 2)); ?></td>
        </tr>
        <?php endif; ?>
        <?php if($venta->cargo_servicio > 0): ?>
        <tr>
            <td class="totals-label">Cargo Servicio:</td>
            <td class="totals-value">$<?php echo e(number_format($venta->cargo_servicio, 2)); ?></td>
        </tr>
        <?php endif; ?>
        <?php if($venta->delivery_fee > 0): ?>
        <tr>
            <td class="totals-label">Delivery Fee:</td>
            <td class="totals-value">$<?php echo e(number_format($venta->delivery_fee, 2)); ?></td>
        </tr>
        <?php endif; ?>
        <tr class="total-final">
            <td class="totals-label">TOTAL:</td>
            <td class="totals-value">$<?php echo e(number_format($venta->total, 2)); ?></td>
        </tr>
    </table>

    <!-- PAGOS -->
    <div class="section-title">FORMA DE PAGO</div>
    <table>
        <thead>
            <tr>
                <th>Método</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $venta->pagos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pago): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e(ucfirst(str_replace('_', ' ', $pago->metodo_pago))); ?></td>
                <td class="text-right">$<?php echo e(number_format($pago->monto, 2)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($venta->pagos->isEmpty()): ?>
            <tr>
                <td colspan="2" style="text-align:center; color:#999;">Sin registro de pagos</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- ESTADO Y OBSERVACIONES -->
    <div class="section-title">ESTADO</div>
    <table>
        <tr>
            <td style="border:none;">
                <span class="badge badge-<?php echo e($venta->estado); ?>"><?php echo e(strtoupper(str_replace('_', ' ', $venta->estado))); ?></span>
                &nbsp;&nbsp;
                <span style="font-size:10px; color:#555;">
                    Atendido por: <?php echo e($venta->usuario->name ?? 'N/A'); ?>

                    <?php if($venta->caja): ?>
                    | Caja: <?php echo e($venta->caja->nombre); ?>

                    <?php endif; ?>
                    <?php if($venta->sucursal): ?>
                    | Sucursal: <?php echo e($venta->sucursal->nombre); ?>

                    <?php endif; ?>
                </span>
            </td>
        </tr>
        <?php if($venta->notas): ?>
        <tr>
            <td style="border:none; font-size:10px; color:#555; margin-top:5px;">
                <strong>Notas:</strong> <?php echo e($venta->notas); ?>

            </td>
        </tr>
        <?php endif; ?>
    </table>

    <?php $slogan = \App\Models\SystemSetting::get('sistema_slogan'); ?>

    <?php if($slogan): ?>
    <div style="margin-top:15px; padding-top:10px; border-top:1px dashed #ddd; text-align:center; font-style:italic; color:#555; font-size:10px;">
        <?php echo e($slogan); ?>

    </div>
    <?php endif; ?>

    <?php if(count($garantiaTerminos) > 0): ?>
    <table style="width:100%; margin-top:10px; margin-bottom:10px; border-top:1px solid #ccc; padding-top:8px;">
        <tr>
            <td style="font-size:10px; color:#333; line-height:1.5;">
                <strong style="font-size:11px; display:block; margin-bottom:6px;">Términos de Garantía:</strong>
                <?php $__currentLoopData = $garantiaTerminos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="margin-bottom:8px;">
                    <strong style="font-size:10px;"><?php echo e($t['producto']); ?>:</strong>
                    <p style="margin:2px 0 0 8px; font-size:9px;"><?php echo e($t['terminos']); ?></p>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </td>
        </tr>
    </table>
    <?php endif; ?>

    <!-- FOOTER -->
    <div class="footer">
        Este documento es una representación impresa de un NCF electrónico.<br>
        <?php echo e(\App\Models\SystemSetting::nombreEmpresaActual()); ?><?php if(!empty($empresa['empresa_rnc'])): ?> | RNC: <?php echo e($empresa['empresa_rnc']); ?><?php endif; ?><br>
        <?php if($venta->ncf): ?>
        NCF: <?php echo e($venta->ncf); ?> | Factura No. <?php echo e(str_pad($venta->id, 5, '0', STR_PAD_LEFT)); ?>

        <?php endif; ?>
    </div>

</body>
</html>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/ventas/pdf.blade.php ENDPATH**/ ?>