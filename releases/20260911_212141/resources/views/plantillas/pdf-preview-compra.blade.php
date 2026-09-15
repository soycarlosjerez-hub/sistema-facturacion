<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vista Previa - Plantilla {{ $template->nombre }}</title>
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
        .preview-watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 60px; color: rgba(239,68,68,0.15); font-weight: bold; pointer-events: none; z-index: 1000; white-space: nowrap; }
        @media print { @page { margin: 10mm; } }
    </style>
</head>
<body>
    <div class="preview-watermark">VISTA PREVIA</div>
    <div class="plantilla-wrapper">
        <div class="plantilla-header">
            <div class="d-flex justify-content-between">
                <div>
                    @if(($templateConfig['mostrar_logo'] ?? true))
                    <img src="{{ $pdfLogoUrl }}" style="max-width: 50px; max-height: 40px; object-fit: contain; margin-bottom: 3px;" alt="Logo">
                    @endif
                    <div class="empresa-info">{{ $variables['empresa']['nombre'] }}</div>
                    <div class="empresa-datos">{{ $variables['empresa']['rnc'] }} | {{ $variables['empresa']['direccion'] }} | {{ $variables['empresa']['telefono'] }}</div>
                    @if(($templateConfig['encabezado_texto'] ?? ''))
                    <div class="empresa-custom">{{ $templateConfig['encabezado_texto'] }}</div>
                    @endif
                </div>
                <div class="plantilla-doc-info">
                    <div class="titulo-doc">COMPROBANTE DE COMPRA</div>
                    <div class="num-doc">No. {{ $variables['documento']['numero'] }}</div>
                    <div class="dato-doc">Fecha: {{ $variables['documento']['fecha_emision'] }}</div>
                </div>
            </div>
        </div>

        @if($templateConfig['mostrar_datos_cliente'] ?? true)
        <div class="plantilla-proveedor-box">
            <div class="d-flex">
                <div style="flex: 1;">
                    <div class="titulo">Proveedor</div>
                    <div class="valor">{{ $variables['proveedor']['nombre'] }}</div>
                </div>
                @if(!empty($variables['proveedor']['rnc_cedula']))
                <div style="flex: 1;">
                    <div class="titulo">RNC/Cedula</div>
                    <div class="valor">{{ $variables['proveedor']['rnc_cedula'] }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

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
                @foreach($variables['productos'] as $prod)
                <tr>
                    <td>
                        {{ $prod['nombre'] }}
                        @if(!empty($prod['notas']))
                        <div class="plantilla-notas">{{ $prod['notas'] }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $prod['cantidad'] }}</td>
                    <td class="text-right">{{ $prod['precio_unitario'] }}</td>
                    <td class="text-right">{{ $prod['subtotal'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="plantilla-totales">
            <div class="fila-total">
                <span class="label-total">Subtotal:</span>
                <span>{{ $variables['totales']['subtotal'] }}</span>
            </div>
            <div class="fila-total">
                <span class="label-total">ITBIS (18%):</span>
                <span>{{ $variables['totales']['itbis'] }}</span>
            </div>
            <div class="fila-total total-final">
                <span class="label-total">TOTAL:</span>
                <span>{{ $variables['totales']['total'] }}</span>
            </div>
        </div>

        @if(($templateConfig['mostrar_notas'] ?? true) && !empty($variables['notas']))
        <div style="margin-top: 6px; font-size: 9px;">
            <strong>Notas:</strong> {{ $variables['notas'] }}
        </div>
        @endif

        @if($templateConfig['mostrar_pie'] ?? true)
        <div class="plantilla-footer">
            @if(($templateConfig['pie_pagina_texto'] ?? ''))
                {{ $templateConfig['pie_pagina_texto'] }}
            @else
                Comprobante de compra valido.
            @endif
        </div>
        @endif
    </div>
</body>
</html>
