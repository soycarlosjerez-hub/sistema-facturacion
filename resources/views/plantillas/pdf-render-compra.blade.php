<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $template->nombre }} - Compra #{{ str_pad($compra->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page { margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; line-height: 1.3; }
        .plantilla-wrapper { padding: 10mm; color: #333; }
        .plantilla-header { border-bottom: 2px solid {{ $templateConfig['color_primario'] ?? '#059669' }}; padding-bottom: 8px; margin-bottom: 10px; }
        .plantilla-header .empresa-info { font-size: 14px; font-weight: bold; color: {{ $templateConfig['color_primario'] ?? '#059669' }}; }
        .plantilla-header .empresa-datos { font-size: 9px; color: #666; }
        .plantilla-header .empresa-custom { font-size: 9px; color: {{ $templateConfig['color_primario'] ?? '#059669' }}; font-style: italic; margin-top: 3px; }
        .plantilla-doc-info { text-align: right; margin-bottom: 8px; }
        .plantilla-doc-info .titulo-doc { font-size: 16px; font-weight: bold; color: {{ $templateConfig['color_primario'] ?? '#059669' }}; }
        .plantilla-doc-info .num-doc { font-size: 11px; font-weight: bold; }
        .plantilla-doc-info .dato-doc { font-size: 9px; color: #666; }
        .plantilla-proveedor-box { margin-bottom: 8px; padding: 5px; background: rgba(248,250,252,0.8); border-radius: 3px; border: 1px solid #e2e8f0; }
        .plantilla-proveedor-box .titulo { font-size: 9px; color: #666; font-weight: bold; }
        .plantilla-proveedor-box .valor { font-size: 10px; }
        .plantilla-tabla { width: 100%; border-collapse: collapse; font-size: 9px; margin-bottom: 8px; }
        .plantilla-tabla thead tr { background: {{ $templateConfig['color_primario'] ?? '#059669' }}; color: #fff; }
        .plantilla-tabla thead th { padding: 3px 5px; text-align: left; border: none; font-weight: 600; font-size: 9px; }
        .plantilla-tabla thead th.text-center { text-align: center; }
        .plantilla-tabla thead th.text-right { text-align: right; }
        .plantilla-tabla tbody td { padding: 3px 5px; border-bottom: 1px solid #e2e8f0; }
        .plantilla-tabla tbody td.text-center { text-align: center; }
        .plantilla-tabla tbody td.text-right { text-align: right; }
        .plantilla-totales { text-align: right; font-size: 10px; }
        .plantilla-totales .fila-total { display: flex; justify-content: flex-end; padding: 1px 0; }
        .plantilla-totales .label-total { color: #666; margin-right: 15px; }
        .plantilla-totales .total-final { font-weight: bold; font-size: 13px; border-top: 2px solid {{ $templateConfig['color_primario'] ?? '#059669' }}; margin-top: 3px; padding-top: 3px; }
        .plantilla-totales .total-final .label-total { color: {{ $templateConfig['color_primario'] ?? '#059669' }}; }
        .plantilla-footer { margin-top: 8px; padding-top: 6px; border-top: 1px dashed #ccc; text-align: center; font-size: 8px; color: #888; }
        .plantilla-notas { font-size: 8px; font-style: italic; color: #666; }
        .plantilla-anulada { color: #dc3545; font-weight: bold; font-size: 12px; text-align: center; margin: 6px 0; border: 2px solid #dc3545; padding: 3px; }
        @media print { @page { margin: 10mm; } }
    </style>
</head>
<body>
    @php
        $esAnulada = $compra->trashed() || $compra->estado === 'anulada';
    @endphp

    <div class="plantilla-wrapper">
        @if($esAnulada)
        <div class="plantilla-anulada">DOCUMENTO ANULADO</div>
        @endif

        <!-- Header -->
        @if($templateConfig['mostrar_encabezado'] ?? true)
        <div class="plantilla-header">
            <div class="d-flex justify-content-between">
                <div>
                    @if(($templateConfig['mostrar_logo'] ?? true) && $pdfLogoUrl)
                    <img src="{{ $pdfLogoUrl }}" style="max-width: 50px; max-height: 40px; object-fit: contain; margin-bottom: 3px;" alt="Logo">
                    @endif
                    <div class="empresa-info">{{ \App\Models\SystemSetting::nombreEmpresaActual() }}</div>
                    <div class="empresa-datos">
                        @php $empresa = \App\Models\SystemSetting::allCached(); @endphp
                        @if(!empty($empresa['empresa_rnc']))RNC: {{ $empresa['empresa_rnc'] }} @endif
                        @if(!empty($empresa['empresa_direccion']))| {{ $empresa['empresa_direccion'] }} @endif
                        @if(!empty($empresa['empresa_telefono']))| Tel: {{ $empresa['empresa_telefono'] }} @endif
                    </div>
                    @if(($templateConfig['encabezado_texto'] ?? ''))
                    <div class="empresa-custom">{{ $templateConfig['encabezado_texto'] }}</div>
                    @endif
                </div>
                <div class="plantilla-doc-info">
                    <div class="titulo-doc">COMPROBANTE DE COMPRA</div>
                    <div class="num-doc">No. {{ str_pad($compra->id, 5, '0', STR_PAD_LEFT) }}</div>
                    <div class="dato-doc">Fecha: {{ $compra->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Proveedor -->
        @if($templateConfig['mostrar_datos_cliente'] ?? true)
        <div class="plantilla-proveedor-box">
            <div class="d-flex">
                <div style="flex: 1;">
                    <div class="titulo">Proveedor</div>
                    <div class="valor">{{ $compra->proveedor->nombre ?? 'Proveedor' }}</div>
                </div>
                @php $rncProv = $compra->proveedor->rnc_cedula ?? $compra->proveedor->documento ?? ''; @endphp
                @if(!empty($rncProv))
                <div style="flex: 1;">
                    <div class="titulo">RNC/Cedula</div>
                    <div class="valor">{{ $rncProv }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Productos -->
        <table class="plantilla-tabla">
            <thead>
                <tr>
                    <th style="width: 40%;">Producto</th>
                    <th class="text-center" style="width: 12%;">Cant.</th>
                    <th class="text-right" style="width: 18%;">P. Unit.</th>
                    <th class="text-right" style="width: 18%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($compra->detalles as $d)
                <tr>
                    <td>
                        {{ $d->producto->nombre ?? $d->servicio->nombre ?? 'Producto' }}
                        @if(!empty($d->notas))
                        <div class="plantilla-notas">{{ $d->notas }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($d->cantidad, 2) }}</td>
                    <td class="text-right">${{ number_format($d->precio_unitario, 2) }}</td>
                    <td class="text-right">${{ number_format($d->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totales -->
        <div class="plantilla-totales">
            <div class="fila-total">
                <span class="label-total">Subtotal:</span>
                <span>${{ number_format($compra->subtotal, 2) }}</span>
            </div>
            @if($compra->impuestos > 0)
            <div class="fila-total">
                <span class="label-total">ITBIS:</span>
                <span>${{ number_format($compra->impuestos, 2) }}</span>
            </div>
            @endif
            <div class="fila-total total-final">
                <span class="label-total">TOTAL:</span>
                <span>${{ number_format($compra->total, 2) }}</span>
            </div>
        </div>

        @if(($templateConfig['mostrar_notas'] ?? true) && !empty($compra->observaciones))
        <div style="margin-top: 6px; font-size: 9px;">
            <strong>Notas:</strong> {{ $compra->observaciones }}
        </div>
        @endif

        <!-- Footer -->
        @if($templateConfig['mostrar_pie'] ?? true)
        <div class="plantilla-footer">
            @if(($templateConfig['pie_pagina_texto'] ?? ''))
                {{ $templateConfig['pie_pagina_texto'] }}
            @else
                Comprobante de compra valido. | {{ \App\Models\SystemSetting::nombreEmpresaActual() }}
            @endif
        </div>
        @endif
    </div>
</body>
</html>
