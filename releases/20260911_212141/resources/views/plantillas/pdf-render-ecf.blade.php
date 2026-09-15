<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>e-CF {{ $variables['ecf']['encf'] ?? '' }} - {{ $template->nombre }}</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }
        .plantilla-wrapper {
            padding: 10mm;
            color: #333;
        }
        .plantilla-header {
            border-bottom: 2px solid {{ $templateConfig['color_primario'] ?? '#003876' }};
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .plantilla-header .empresa-info {
            font-size: 14px;
            font-weight: bold;
            color: {{ $templateConfig['color_primario'] ?? '#003876' }};
        }
        .plantilla-header .empresa-datos {
            font-size: 9px;
            color: #666;
        }
        .plantilla-header .empresa-custom {
            font-size: 9px;
            color: {{ $templateConfig['color_primario'] ?? '#003876' }};
            font-style: italic;
            margin-top: 3px;
        }
        .plantilla-doc-info {
            text-align: right;
            margin-bottom: 8px;
        }
        .plantilla-doc-info .titulo-doc {
            font-size: 16px;
            font-weight: bold;
            color: {{ $templateConfig['color_primario'] ?? '#003876' }};
        }
        .plantilla-doc-info .encf-box {
            background: #f4f6f8;
            border: 1px solid #ddd;
            padding: 6px 10px;
            margin-top: 4px;
            display: inline-block;
        }
        .plantilla-doc-info .encf-num {
            font-size: 14px;
            font-weight: bold;
            color: {{ $templateConfig['color_primario'] ?? '#003876' }};
            letter-spacing: 1.5px;
        }
        .plantilla-doc-info .encf-tipo {
            display: inline-block;
            background: {{ $templateConfig['color_primario'] ?? '#003876' }};
            color: white;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            margin-top: 3px;
        }
        .plantilla-doc-info .dato-doc {
            font-size: 9px;
            color: #666;
        }
        .ecf-estado-badge {
            text-align: center;
            margin-bottom: 10px;
            padding: 5px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
        }
        .ecf-estado-aprobado {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .ecf-estado-pendiente {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .ecf-estado-rechazado {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .plantilla-cliente-box {
            margin-bottom: 8px;
            padding: 5px;
            background: rgba(248,250,252,0.8);
            border-radius: 3px;
            border: 1px solid #e2e8f0;
        }
        .plantilla-cliente-box .titulo {
            font-size: 9px;
            color: #666;
            font-weight: bold;
        }
        .plantilla-cliente-box .valor {
            font-size: 10px;
        }
        .plantilla-tabla {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin-bottom: 8px;
        }
        .plantilla-tabla thead tr {
            background: {{ $templateConfig['color_primario'] ?? '#003876' }};
            color: #fff;
        }
        .plantilla-tabla thead th {
            padding: 3px 5px;
            text-align: left;
            border: none;
            font-weight: 600;
            font-size: 9px;
        }
        .plantilla-tabla thead th.text-center { text-align: center; }
        .plantilla-tabla thead th.text-right { text-align: right; }
        .plantilla-tabla tbody td {
            padding: 3px 5px;
            border-bottom: 1px solid #e2e8f0;
        }
        .plantilla-tabla tbody td.text-center { text-align: center; }
        .plantilla-tabla tbody td.text-right { text-align: right; }
        .plantilla-tabla tbody tr:last-child td { border-bottom: none; }
        .plantilla-totales {
            text-align: right;
            font-size: 10px;
            width: 55%;
            float: right;
        }
        .plantilla-totales .fila-total {
            display: flex;
            justify-content: flex-end;
            padding: 1px 0;
        }
        .plantilla-totales .label-total {
            color: #666;
            margin-right: 15px;
        }
        .plantilla-totales .total-final {
            font-weight: bold;
            font-size: 13px;
            border-top: 2px solid {{ $templateConfig['color_primario'] ?? '#003876' }};
            margin-top: 3px;
            padding-top: 3px;
        }
        .plantilla-totales .total-final .label-total {
            color: {{ $templateConfig['color_primario'] ?? '#003876' }};
        }
        .plantilla-footer {
            clear: both;
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px dashed #ccc;
            text-align: center;
            font-size: 8px;
            color: #888;
        }
        .plantilla-garantia {
            font-size: 8px;
            color: #6366f1;
            padding-left: 10px;
            margin: 2px 0;
        }
        .plantilla-notas {
            font-size: 8px;
            font-style: italic;
            color: #666;
        }
        .plantilla-pagos-titulo {
            font-size: 9px;
            font-weight: bold;
            color: #fff;
            background: #333;
            padding: 2px 6px;
            margin-top: 6px;
            margin-bottom: 4px;
            clear: both;
        }
        .plantilla-pagos .fila-pago {
            display: flex;
            justify-content: space-between;
            padding: 1px 0;
            font-size: 9px;
        }
        .plantilla-anulada {
            color: #dc3545;
            font-weight: bold;
            font-size: 12px;
            text-align: center;
            margin: 6px 0;
            border: 2px solid #dc3545;
            padding: 3px;
        }
        .ecf-qr-section {
            float: left;
            width: 35%;
            text-align: center;
            padding: 8px;
            background: #fafafa;
            border: 1px solid #eee;
            border-radius: 3px;
            margin-top: 10px;
        }
        .ecf-qr-section img {
            max-width: 140px;
        }
        .ecf-info-section {
            float: right;
            width: 58%;
            font-size: 8px;
            color: #555;
            margin-top: 10px;
        }
        .ecf-info-section p {
            margin: 3px 0;
        }
        .ecf-firma {
            font-size: 7px;
            color: #888;
            word-break: break-all;
            margin-top: 3px;
        }
        .plantilla-garantias-titulo {
            font-size: 10px;
            font-weight: bold;
            color: {{ $templateConfig['color_primario'] ?? '#003876' }};
            margin-top: 10px;
            clear: both;
        }
        @media print {
            @page { margin: 10mm; }
        }
    </style>
</head>
<body>
    @php
        $primario = $templateConfig['color_primario'] ?? '#003876';
        $esAnulada = ($variables['documento']['es_anulada'] ?? false);
        $ecfData = $variables['ecf'] ?? [];
        $estadoInfo = $ecfData['estado_info'] ?? ['label' => 'Desconocido', 'color' => 'secondary'];
        $estadoClass = match($ecfData['estado'] ?? '') {
            'aprobado' => 'ecf-estado-aprobado',
            'rechazado' => 'ecf-estado-rechazado',
            default => 'ecf-estado-pendiente',
        };
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
                    <div class="empresa-info">{{ $variables['empresa']['nombre'] }}</div>
                    <div class="empresa-datos">
                        @if(!empty($variables['empresa']['rnc']))RNC/Cedula: {{ $variables['empresa']['rnc'] }} @endif
                        @if(!empty($variables['empresa']['direccion']))| {{ $variables['empresa']['direccion'] }} @endif
                        @if(!empty($variables['empresa']['telefono']))| Tel: {{ $variables['empresa']['telefono'] }} @endif
                    </div>
                    @if(($templateConfig['encabezado_texto'] ?? '') && ($templateConfig['mostrar_encabezado'] ?? true))
                    <div class="empresa-custom">{{ $templateConfig['encabezado_texto'] }}</div>
                    @endif
                </div>
                <div class="plantilla-doc-info">
                    <div class="titulo-doc">COMPROBANTE FISCAL ELECTRÓNICO</div>
                    <div class="encf-box">
                        <div class="encf-num">{{ $ecfData['encf'] ?? '' }}</div>
                        <div class="encf-tipo">{{ $ecfData['tipo_nombre'] ?? '' }}</div>
                    </div>
                    <div class="dato-doc">No. {{ $variables['documento']['numero'] }}</div>
                    <div class="dato-doc">Fecha: {{ $variables['documento']['fecha_emision'] }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Estado DGII -->
        <div class="ecf-estado-badge {{ $estadoClass }}">
            ESTADO: {{ strtoupper($estadoInfo['label']) }}
            @if(!empty($ecfData['track_id_dgii']))
                | Track ID: {{ $ecfData['track_id_dgii'] }}
            @endif
        </div>

        <!-- Datos emitido por / fecha -->
        <table style="width: 100%; margin-bottom: 10px;">
            <tr>
                <td style="width: 50%; padding: 5px; background: #fafafa; vertical-align: top; border: 1px solid #eee;">
                    <strong style="font-size: 9px;">EMITIDO POR:</strong><br>
                    <strong>{{ $variables['empresa']['nombre'] }}</strong><br>
                    @if(!empty($variables['empresa']['rnc']))
                    RNC: {{ $variables['empresa']['rnc'] }}
                    @endif
                </td>
                <td style="width: 50%; padding: 5px; background: #fafafa; vertical-align: top; border: 1px solid #eee;">
                    <strong style="font-size: 9px;">FECHA DE EMISIÓN:</strong> {{ $variables['documento']['fecha_emision'] }}<br>
                    @if(!empty($ecfData['fecha_aprobacion']))
                    <strong style="font-size: 9px;">APROBADO DGII:</strong> {{ $ecfData['fecha_aprobacion'] }}
                    @endif
                </td>
            </tr>
        </table>

        <!-- Cliente -->
        @if($templateConfig['mostrar_datos_cliente'] ?? true)
        <div class="plantilla-cliente-box">
            <div class="titulo" style="background: {{ $primario }}; color: white; padding: 3px 6px; margin: -5px -5px 5px -5px; font-size: 9px;">DATOS DEL COMPRADOR</div>
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

        <!-- Productos -->
        @php
            $showCant = $templateConfig['mostrar_columna_cantidad'] ?? true;
            $showPrecio = $templateConfig['mostrar_columna_precio'] ?? true;
            $showSubtotal = $templateConfig['mostrar_columna_subtotal'] ?? true;
            $showItbis = $templateConfig['mostrar_columna_itbis'] ?? true;
            $showGarantias = $templateConfig['mostrar_garantias'] ?? true;
            $colCount = 1 + ($showCant ? 1 : 0) + ($showPrecio ? 1 : 0) + ($showSubtotal ? 1 : 0) + ($showItbis ? 1 : 0);
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
                @php $detalles = $ecf->venta->detalles->where('tipo_linea', '!=', 'delivery'); @endphp
                @foreach($detalles as $i => $d)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        {{ $d->producto->nombre ?? $d->obra->titulo ?? ($d->servicio->nombre ?? 'Producto') }}
                        @if($showGarantias && !empty($d->producto->garantia_meses) && $d->producto->garantia_meses > 0)
                        <div class="plantilla-garantia">Garantía: {{ $d->producto->garantia_meses }} meses</div>
                        @endif
                        @if(!empty($d->notas))
                        <div class="plantilla-notas">{{ $d->notas }}</div>
                        @endif
                    </td>
                    @if($showCant)
                    <td class="text-center">{{ number_format($d->cantidad, 2) }}</td>
                    @endif
                    @if($showPrecio)
                    <td class="text-right">${{ number_format($d->precio_unitario, 2) }}</td>
                    @endif
                    @if($showItbis)
                    <td class="text-right">${{ number_format($d->sin_itbis ? 0 : $d->subtotal * (($d->producto->itbis_porcentaje ?? $d->itbis_porcentaje ?? 0) / 100), 2) }}</td>
                    @endif
                    @if($showSubtotal)
                    <td class="text-right">${{ number_format($d->subtotal, 2) }}</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totales -->
        <div class="plantilla-totales">
            <div class="fila-total">
                <span class="label-total">Monto Gravado:</span>
                <span>${{ $ecfData['monto_gravado'] ?? '0.00' }}</span>
            </div>
            <div class="fila-total">
                <span class="label-total">Monto Exento:</span>
                <span>${{ $ecfData['monto_exento'] ?? '0.00' }}</span>
            </div>
            @if(!empty($ecfData['itbis_total']) && $ecfData['itbis_total'] !== '0.00')
            <div class="fila-total">
                <span class="label-total">ITBIS (18%):</span>
                <span>${{ $ecfData['itbis_total'] }}</span>
            </div>
            @endif
            <div class="fila-total total-final">
                <span class="label-total">TOTAL:</span>
                <span>${{ $ecfData['monto_total'] ?? '0.00' }}</span>
            </div>
        </div>

        <!-- Pagos -->
        @if(($templateConfig['mostrar_pagos'] ?? true) && count($variables['pagos'] ?? []) > 0)
        <div style="clear: both;">
            <div class="plantilla-pagos-titulo">FORMA DE PAGO</div>
            <div class="plantilla-pagos">
                @foreach($variables['pagos'] as $pago)
                <div class="fila-pago">
                    <span>{{ $pago['metodo'] }}</span>
                    <span>${{ $pago['monto'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Notas -->
        @if(($templateConfig['mostrar_notas'] ?? true) && !empty($variables['notas']))
        <div style="clear: both; margin-top: 6px; font-size: 9px;">
            <strong>Notas:</strong> {{ $variables['notas'] }}
        </div>
        @endif

        <!-- QR + Info e-CF -->
        <div style="clear: both;"></div>
        <div class="ecf-qr-section">
            @if(!empty($ecfData['qr_url']))
            <img src="{{ $ecfData['qr_url'] }}" alt="QR de Consulta DGII"><br>
            @endif
            <div style="font-size: 8px; color: #777; margin-top: 4px;">Consulte este comprobante en DGII</div>
            @if(!empty($ecfData['codigo_seguridad']))
            <div style="font-size: 8px; color: #777; margin-top: 3px;">Código: <strong>{{ $ecfData['codigo_seguridad'] }}</strong></div>
            @endif
        </div>
        <div class="ecf-info-section">
            @php $slogan = \App\Models\SystemSetting::get('sistema_slogan'); @endphp
            @if($slogan)
            <p style="font-style:italic; color:#555; text-align:center; margin-bottom:5px;">
                {{ $slogan }}
            </p>
            @endif
            <p>
                <strong>Representación Impresa del Comprobante Fiscal Electrónico (e-CF)</strong><br>
                Este documento es una representación impresa de un e-CF emitido conforme a las normas
                de la Dirección General de Impuestos Internos (DGII) de la República Dominicana.<br>
                Para verificar la autenticidad de este comprobante, escanee el código QR o visite el portal
                de consulta de e-CF de la DGII con los datos: RNC Emisor, eNCF, monto y fecha.
            </p>
            @if(($ecfData['estado'] ?? '') !== 'aprobado')
            <p style="color: #856404;">
                <strong>VERSIÓN PRELIMINAR</strong> - Este e-CF aún no ha sido aprobado por la DGII.
                @if(!empty($ecfData['mensaje_dgii']))<br>Motivo: {{ $ecfData['mensaje_dgii'] }}@endif
            </p>
            @else
            <p style="color: #155724;">
                <strong>APROBADO POR DGII</strong>
                @if(!empty($ecfData['track_id_dgii'])) - Track ID: {{ $ecfData['track_id_dgii'] }}@endif
            </p>
            @if(!empty($ecfData['firma_digital']))
            <div class="ecf-firma">
                <strong>Firma Digital:</strong> {{ substr($ecfData['firma_digital'], 0, 80) }}...
            </div>
            @endif
            @endif
        </div>

        <!-- Footer -->
        @if($templateConfig['mostrar_pie'] ?? true)
        <div class="plantilla-footer">
            @if(($templateConfig['pie_pagina_texto'] ?? ''))
                {{ $templateConfig['pie_pagina_texto'] }}
            @else
                Este documento es una representación impresa de un Comprobante Fiscal Electrónico (e-CF). | {{ $variables['empresa']['nombre'] }}
            @endif
        </div>
        @endif

        <!-- Garantías -->
        @if($showGarantias)
        @php
            $garantiaTerminos = [];
            foreach($ecf->venta->detalles->where('tipo_linea', '!=', 'delivery') as $d) {
                if(!empty($d->producto->garantia_terminos)) {
                    $_key = $d->producto->id . '_' . md5($d->producto->garantia_terminos);
                    if(!isset($garantiaTerminos[$_key])) {
                        $garantiaTerminos[$_key] = ['producto' => $d->producto->nombre, 'terminos' => $d->producto->garantia_terminos];
                    }
                }
            }
        @endphp
        @if(count($garantiaTerminos) > 0)
        <div style="clear:both; margin-top:10px; padding-top:8px; border-top:1px solid #ccc;">
            <div class="plantilla-garantias-titulo">Términos de Garantía:</div>
            @foreach($garantiaTerminos as $t)
            <div style="margin-bottom:6px;">
                <strong style="font-size:9px; color:#333;">{{ $t['producto'] }}:</strong>
                <p style="margin:2px 0 0 8px; font-size:8px; color:#555; line-height:1.4;">{{ $t['terminos'] }}</p>
            </div>
            @endforeach
        </div>
        @endif
        @endif
    </div>
</body>
</html>
