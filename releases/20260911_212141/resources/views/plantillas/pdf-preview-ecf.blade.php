<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vista Previa e-CF - Plantilla {{ $template->nombre ?? '' }}</title>
    <style>
        @page { margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; line-height: 1.3; }
        .plantilla-wrapper { padding: 10mm; color: #333; }
        .plantilla-header { border-bottom: 2px solid {{ $templateConfig['color_primario'] ?? '#003876' }}; padding-bottom: 8px; margin-bottom: 10px; }
        .plantilla-header .empresa-info { font-size: 14px; font-weight: bold; color: {{ $templateConfig['color_primario'] ?? '#003876' }}; }
        .plantilla-header .empresa-datos { font-size: 9px; color: #666; }
        .plantilla-header .empresa-custom { font-size: 9px; color: {{ $templateConfig['color_primario'] ?? '#003876' }}; font-style: italic; margin-top: 3px; }
        .plantilla-doc-info { text-align: right; margin-bottom: 8px; }
        .plantilla-doc-info .titulo-doc { font-size: 16px; font-weight: bold; color: {{ $templateConfig['color_primario'] ?? '#003876' }}; }
        .plantilla-doc-info .encf-box { background: #f4f6f8; border: 1px solid #ddd; padding: 6px 10px; margin-top: 4px; display: inline-block; }
        .plantilla-doc-info .encf-num { font-size: 14px; font-weight: bold; color: {{ $templateConfig['color_primario'] ?? '#003876' }}; letter-spacing: 1.5px; }
        .plantilla-doc-info .encf-tipo { display: inline-block; background: {{ $templateConfig['color_primario'] ?? '#003876' }}; color: white; padding: 2px 8px; border-radius: 3px; font-size: 9px; margin-top: 3px; }
        .plantilla-doc-info .dato-doc { font-size: 9px; color: #666; }
        .ecf-estado-badge { text-align: center; margin-bottom: 10px; padding: 5px; border-radius: 3px; font-weight: bold; font-size: 10px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .plantilla-cliente-box { margin-bottom: 8px; padding: 5px; background: rgba(248,250,252,0.8); border-radius: 3px; border: 1px solid #e2e8f0; }
        .plantilla-cliente-box .titulo { font-size: 9px; color: #666; font-weight: bold; }
        .plantilla-cliente-box .valor { font-size: 10px; }
        .plantilla-tabla { width: 100%; border-collapse: collapse; font-size: 9px; margin-bottom: 8px; }
        .plantilla-tabla thead tr { background: {{ $templateConfig['color_primario'] ?? '#003876' }}; color: #fff; }
        .plantilla-tabla thead th { padding: 3px 5px; text-align: left; border: none; font-weight: 600; font-size: 9px; }
        .plantilla-tabla thead th.text-center { text-align: center; }
        .plantilla-tabla thead th.text-right { text-align: right; }
        .plantilla-tabla tbody td { padding: 3px 5px; border-bottom: 1px solid #e2e8f0; }
        .plantilla-tabla tbody td.text-center { text-align: center; }
        .plantilla-tabla tbody td.text-right { text-align: right; }
        .plantilla-totales { text-align: right; font-size: 10px; width: 55%; float: right; }
        .plantilla-totales .fila-total { display: flex; justify-content: flex-end; padding: 1px 0; }
        .plantilla-totales .label-total { color: #666; margin-right: 15px; }
        .plantilla-totales .total-final { font-weight: bold; font-size: 13px; border-top: 2px solid {{ $templateConfig['color_primario'] ?? '#003876' }}; margin-top: 3px; padding-top: 3px; }
        .plantilla-totales .total-final .label-total { color: {{ $templateConfig['color_primario'] ?? '#003876' }}; }
        .plantilla-footer { clear: both; margin-top: 15px; padding-top: 8px; border-top: 1px dashed #ccc; text-align: center; font-size: 8px; color: #888; }
        .ecf-qr-section { float: left; width: 35%; text-align: center; padding: 8px; background: #fafafa; border: 1px solid #eee; border-radius: 3px; margin-top: 10px; }
        .ecf-info-section { float: right; width: 58%; font-size: 8px; color: #555; margin-top: 10px; }
        .preview-watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 60px; color: rgba(239,68,68,0.15); font-weight: bold; pointer-events: none; z-index: 1000; white-space: nowrap; }
    </style>
</head>
<body>
    <div class="preview-watermark">VISTA PREVIA</div>
    <div class="plantilla-wrapper">
        @if($templateConfig['mostrar_encabezado'] ?? true)
        <div class="plantilla-header">
            <div class="d-flex justify-content-between">
                <div>
                    @if(($templateConfig['mostrar_logo'] ?? true))
                    <img src="{{ $pdfLogoUrl }}" style="max-width: 50px; max-height: 40px; object-fit: contain; margin-bottom: 3px;" alt="Logo">
                    @endif
                    <div class="empresa-info">{{ $variables['empresa']['nombre'] }}</div>
                    <div class="empresa-datos">{{ $variables['empresa']['rnc'] }} | {{ $variables['empresa']['direccion'] }} | {{ $variables['empresa']['telefono'] }}</div>
                    @if(($templateConfig['encabezado_texto'] ?? '') && ($templateConfig['mostrar_encabezado'] ?? true))
                    <div class="empresa-custom">{{ $templateConfig['encabezado_texto'] }}</div>
                    @endif
                </div>
                <div class="plantilla-doc-info">
                    <div class="titulo-doc">COMPROBANTE FISCAL ELECTRÓNICO</div>
                    <div class="encf-box">
                        <div class="encf-num">{{ $variables['ecf']['encf'] ?? '' }}</div>
                        <div class="encf-tipo">{{ $variables['ecf']['tipo_nombre'] ?? '' }}</div>
                    </div>
                    <div class="dato-doc">No. {{ $variables['documento']['numero'] }}</div>
                    <div class="dato-doc">Fecha: {{ $variables['documento']['fecha_emision'] }}</div>
                </div>
            </div>
        </div>
        @endif

        <div class="ecf-estado-badge">
            ESTADO: APROBADO | Track ID: {{ $variables['ecf']['track_id_dgii'] ?? '1234567890' }}
        </div>

        <table style="width: 100%; margin-bottom: 10px;">
            <tr>
                <td style="width: 50%; padding: 5px; background: #fafafa; vertical-align: top; border: 1px solid #eee;">
                    <strong style="font-size: 9px;">EMITIDO POR:</strong><br>
                    <strong>{{ $variables['empresa']['nombre'] }}</strong><br>
                    RNC: {{ $variables['empresa']['rnc'] }}
                </td>
                <td style="width: 50%; padding: 5px; background: #fafafa; vertical-align: top; border: 1px solid #eee;">
                    <strong style="font-size: 9px;">FECHA DE EMISIÓN:</strong> {{ $variables['documento']['fecha_emision'] }}<br>
                    <strong style="font-size: 9px;">APROBADO DGII:</strong> {{ $variables['ecf']['fecha_aprobacion'] ?? '' }}
                </td>
            </tr>
        </table>

        @if($templateConfig['mostrar_datos_cliente'] ?? true)
        <div class="plantilla-cliente-box">
            <div class="titulo" style="background: {{ $templateConfig['color_primario'] ?? '#003876' }}; color: white; padding: 3px 6px; margin: -5px -5px 5px -5px; font-size: 9px;">DATOS DEL COMPRADOR</div>
            <div class="d-flex">
                <div style="flex: 1;">
                    <div class="titulo">Nombre / Razón Social</div>
                    <div class="valor">{{ $variables['cliente']['nombre'] }}</div>
                </div>
                @if(!empty($variables['cliente']['rnc_cedula']))
                <div style="flex: 1;">
                    <div class="titulo">RNC / Cédula</div>
                    <div class="valor">{{ $variables['cliente']['rnc_cedula'] }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        @php
            $showCant = $templateConfig['mostrar_columna_cantidad'] ?? true;
            $showPrecio = $templateConfig['mostrar_columna_precio'] ?? true;
            $showSubtotal = $templateConfig['mostrar_columna_subtotal'] ?? true;
            $showItbis = $templateConfig['mostrar_columna_itbis'] ?? true;
            $showGarantias = $templateConfig['mostrar_garantias'] ?? true;
        @endphp

        <table class="plantilla-tabla">
            <thead>
                <tr>
                    <th style="width: 6%;">#</th>
                    <th style="width: 44%;">Descripción</th>
                    @if($showCant)
                    <th class="text-center" style="width: 10%;">Cant.</th>
                    @endif
                    @if($showPrecio)
                    <th class="text-right" style="width: 15%;">P. Unit.</th>
                    @endif
                    @if($showItbis)
                    <th class="text-right" style="width: 12%;">ITBIS</th>
                    @endif
                    @if($showSubtotal)
                    <th class="text-right" style="width: 13%;">Subtotal</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($variables['productos'] as $i => $prod)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        {{ $prod['nombre'] }}
                        @if($showGarantias && ($prod['garantia_meses'] ?? 0) > 0)
                        <div style="font-size: 8px; color: #6366f1; padding-left: 10px;">Garantía: {{ $prod['garantia_meses'] }} meses</div>
                        @endif
                        @if(!empty($prod['notas']))
                        <div style="font-size: 8px; font-style: italic; color: #666;">{{ $prod['notas'] }}</div>
                        @endif
                    </td>
                    @if($showCant)
                    <td class="text-center">{{ $prod['cantidad'] }}</td>
                    @endif
                    @if($showPrecio)
                    <td class="text-right">${{ $prod['precio_unitario'] }}</td>
                    @endif
                    @if($showItbis)
                    <td class="text-right">${{ number_format(($prod['subtotal_decimal'] ?? 0) * (($prod['itbis_porcentaje'] ?? 0) / 100), 2) }}</td>
                    @endif
                    @if($showSubtotal)
                    <td class="text-right">${{ $prod['subtotal'] }}</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="plantilla-totales">
            <div class="fila-total">
                <span class="label-total">Monto Gravado:</span>
                <span>${{ $variables['ecf']['monto_gravado'] ?? '0.00' }}</span>
            </div>
            <div class="fila-total">
                <span class="label-total">Monto Exento:</span>
                <span>${{ $variables['ecf']['monto_exento'] ?? '0.00' }}</span>
            </div>
            @if(!empty($variables['ecf']['itbis_total']) && $variables['ecf']['itbis_total'] !== '0.00')
            <div class="fila-total">
                <span class="label-total">ITBIS (18%):</span>
                <span>${{ $variables['ecf']['itbis_total'] }}</span>
            </div>
            @endif
            <div class="fila-total total-final">
                <span class="label-total">TOTAL:</span>
                <span>${{ $variables['ecf']['monto_total'] ?? '0.00' }}</span>
            </div>
        </div>

        @if(($templateConfig['mostrar_pagos'] ?? true) && count($variables['pagos'] ?? []) > 0)
        <div style="clear: both;">
            <div style="font-size: 9px; font-weight: bold; color: #fff; background: #333; padding: 2px 6px; margin-top: 6px; margin-bottom: 4px;">FORMA DE PAGO</div>
            <div>
                @foreach($variables['pagos'] as $pago)
                <div style="display: flex; justify-content: space-between; padding: 1px 0; font-size: 9px;">
                    <span>{{ $pago['metodo'] }}</span>
                    <span>${{ $pago['monto'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if(($templateConfig['mostrar_notas'] ?? true) && !empty($variables['notas']))
        <div style="clear: both; margin-top: 6px; font-size: 9px;">
            <strong>Notas:</strong> {{ $variables['notas'] }}
        </div>
        @endif

        <div style="clear: both;"></div>
        <div class="ecf-qr-section">
            <div style="width: 120px; height: 120px; background: #eee; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #999;">[QR DGII]</div>
            <div style="font-size: 8px; color: #777; margin-top: 4px;">Consulte este comprobante en DGII</div>
            <div style="font-size: 8px; color: #777; margin-top: 3px;">Código: <strong>{{ $variables['ecf']['codigo_seguridad'] ?? 'ABC123' }}</strong></div>
        </div>
        <div class="ecf-info-section">
            <p style="font-style:italic; color:#555; text-align:center; margin-bottom:5px;">Tu solución de confianza</p>
            <p>
                <strong>Representación Impresa del Comprobante Fiscal Electrónico (e-CF)</strong><br>
                Este documento es una representación impresa de un e-CF emitido conforme a las normas
                de la Dirección General de Impuestos Internos (DGII) de la República Dominicana.
            </p>
            <p style="color: #155724;">
                <strong>APROBADO POR DGII</strong> - Track ID: {{ $variables['ecf']['track_id_dgii'] ?? '' }}<br>
                <span style="font-size: 7px; color: #888;">Firma Digital: {{ substr($variables['ecf']['firma_digital'] ?? '', 0, 60) }}...</span>
            </p>
        </div>

        @if($templateConfig['mostrar_pie'] ?? true)
        <div class="plantilla-footer">
            @if(($templateConfig['pie_pagina_texto'] ?? ''))
                {{ $templateConfig['pie_pagina_texto'] }}
            @else
                Este documento es una representación impresa de un Comprobante Fiscal Electrónico (e-CF).
            @endif
        </div>
        @endif
    </div>
</body>
</html>
