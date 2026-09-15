<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vista Previa - Plantilla {{ $template->nombre }}</title>
    <style>
        @page { margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; line-height: 1.3; }
        .plantilla-wrapper { padding: 10mm; color: #333; }
        .plantilla-header { border-bottom: 2px solid {{ $templateConfig['color_primario'] ?? '#8b5cf6' }}; padding-bottom: 8px; margin-bottom: 10px; }
        .plantilla-header .empresa-info { font-size: 14px; font-weight: bold; color: {{ $templateConfig['color_primario'] ?? '#8b5cf6' }}; }
        .plantilla-header .empresa-datos { font-size: 9px; color: #666; }
        .plantilla-header .empresa-custom { font-size: 9px; color: {{ $templateConfig['color_primario'] ?? '#8b5cf6' }}; font-style: italic; margin-top: 3px; }
        .plantilla-doc-info { text-align: right; margin-bottom: 8px; }
        .plantilla-doc-info .titulo-doc { font-size: 16px; font-weight: bold; color: {{ $templateConfig['color_primario'] ?? '#8b5cf6' }}; }
        .plantilla-doc-info .num-doc { font-size: 11px; font-weight: bold; }
        .plantilla-doc-info .dato-doc { font-size: 9px; color: #666; }
        .plantilla-cliente-box { margin-bottom: 8px; padding: 5px; background: rgba(248,250,252,0.8); border-radius: 3px; border: 1px solid #e2e8f0; }
        .plantilla-cliente-box .titulo { font-size: 9px; color: #666; font-weight: bold; }
        .plantilla-cliente-box .valor { font-size: 10px; }
        .plantilla-tabla { width: 100%; border-collapse: collapse; font-size: 9px; margin-bottom: 8px; }
        .plantilla-tabla thead tr { background: {{ $templateConfig['color_primario'] ?? '#8b5cf6' }}; color: #fff; }
        .plantilla-tabla thead th { padding: 3px 5px; text-align: left; border: none; font-weight: 600; font-size: 9px; }
        .plantilla-tabla thead th.text-center { text-align: center; }
        .plantilla-tabla thead th.text-right { text-align: right; }
        .plantilla-tabla tbody td { padding: 3px 5px; border-bottom: 1px solid #e2e8f0; }
        .plantilla-tabla tbody td.text-center { text-align: center; }
        .plantilla-tabla tbody td.text-right { text-align: right; }
        .plantilla-tabla tbody tr:nth-child(even) { background: rgba(248,250,252,0.5); }
        .plantilla-totales { text-align: right; font-size: 10px; }
        .plantilla-totales .fila-total { display: flex; justify-content: flex-end; padding: 1px 0; }
        .plantilla-totales .label-total { color: #666; margin-right: 15px; }
        .plantilla-totales .total-final { font-weight: bold; font-size: 13px; border-top: 2px solid {{ $templateConfig['color_primario'] ?? '#8b5cf6' }}; margin-top: 3px; padding-top: 3px; }
        .plantilla-totales .total-final .label-total { color: {{ $templateConfig['color_primario'] ?? '#8b5cf6' }}; }
        .plantilla-footer { margin-top: 8px; padding-top: 6px; border-top: 1px dashed #ccc; text-align: center; font-size: 8px; color: #888; }
        .plantilla-garantia { font-size: 8px; color: #6366f1; padding-left: 10px; margin: 2px 0; }
        .plantilla-notas { font-size: 8px; font-style: italic; color: #666; }
        .plantilla-pagos-titulo { font-size: 9px; font-weight: bold; color: #fff; background: #333; padding: 2px 6px; margin-top: 6px; margin-bottom: 4px; }
        .plantilla-pagos .fila-pago { display: flex; justify-content: space-between; padding: 1px 0; font-size: 9px; }
        .plantilla-filtros { margin-bottom: 10px; padding: 6px 10px; background: rgba(248,250,252,0.8); border: 1px solid #e2e8f0; border-radius: 3px; font-size: 9px; color: #555; }
        .plantilla-filtros strong { color: #333; }
        .plantilla-resumen-historial { width: 50%; float: right; margin-top: 8px; }
        .plantilla-resumen-historial .fila-resumen { display: flex; justify-content: flex-end; padding: 1px 0; font-size: 9px; }
        .plantilla-resumen-historial .label-resumen { color: #666; margin-right: 15px; }
        .plantilla-resumen-historial .total-final { font-weight: bold; font-size: 12px; border-top: 2px solid {{ $templateConfig['color_primario'] ?? '#8b5cf6' }}; margin-top: 3px; padding-top: 3px; }
        .plantilla-resumen-historial .total-final .label-resumen { color: {{ $templateConfig['color_primario'] ?? '#8b5cf6' }}; }
        .preview-watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 60px; color: rgba(239,68,68,0.15); font-weight: bold; pointer-events: none; z-index: 1000; white-space: nowrap; }
    </style>
</head>
<body>
    <div class="preview-watermark">VISTA PREVIA</div>
    <div class="plantilla-wrapper">
        @php
            $modulo = $template->modulo ?? 'ventas';
            $isHistorial = $modulo === 'historial_ventas';
            $variables = $variables ?? [];
            $historialVentas = $isHistorial ? ($variables['ventas'] ?? collect()) : collect();
            $historialResumen = $isHistorial ? ($variables['resumen'] ?? []) : [];
            $historialFiltros = $isHistorial ? ($variables['filtros'] ?? []) : [];
        @endphp

        @if($isHistorial)
            <!-- Historial de Ventas Preview Mode -->
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
                        <div class="titulo-doc">HISTORIAL DE VENTAS</div>
                        <div class="dato-doc">Generado: {{ now()->format('d/m/Y h:i A') }}</div>
                        <div class="dato-doc">Total registros: {{ $historialVentas->count() }}</div>
                    </div>
                </div>
            </div>

            @if(!empty($historialFiltros['cliente']) || !empty($historialFiltros['desde']) || !empty($historialFiltros['hasta']))
            <div class="plantilla-filtros">
                <strong>Filtros aplicados:</strong>
                @if(!empty($historialFiltros['desde'])) Desde: {{ $historialFiltros['desde'] }} @endif
                @if(!empty($historialFiltros['hasta'])) | Hasta: {{ $historialFiltros['hasta'] }} @endif
                @if(!empty($historialFiltros['cliente'])) | Cliente: {{ $historialFiltros['cliente'] }} @endif
            </div>
            @endif

            @php
                $showNcf = $templateConfig['mostrar_datos_fiscales'] ?? true;
                $showUsuario = true;
                $showSucursal = true;
                $showSubtotal = $templateConfig['mostrar_columna_subtotal'] ?? true;
                $showItbis = $templateConfig['mostrar_columna_itbis'] ?? true;
            @endphp

            <table class="plantilla-tabla">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        @if($showNcf)
                        <th style="width: 12%;">NCF</th>
                        @endif
                        <th style="width: 18%;">Cliente</th>
                        @if($showUsuario)
                        <th style="width: 12%;">Cajero</th>
                        @endif
                        @if($showSucursal)
                        <th style="width: 10%;">Sucursal</th>
                        @endif
                        <th style="width: 8%;">Tipo</th>
                        <th style="width: 10%;">Fecha</th>
                        <th style="width: 7%;">Hora</th>
                        @if($showSubtotal)
                        <th class="text-right" style="width: 10%;">Subtotal</th>
                        @endif
                        @if($showItbis)
                        <th class="text-right" style="width: 9%;">ITBIS</th>
                        @endif
                        <th class="text-right" style="width: 10%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historialVentas as $v)
                    <tr>
                        <td>{{ $v['id'] }}</td>
                        @if($showNcf)
                        <td>{{ $v['ncf'] }}</td>
                        @endif
                        <td>{{ $v['cliente'] }}</td>
                        @if($showUsuario)
                        <td>{{ $v['usuario'] }}</td>
                        @endif
                        @if($showSucursal)
                        <td>{{ $v['sucursal'] }}</td>
                        @endif
                        <td>{{ $v['tipo'] }}</td>
                        <td>{{ $v['fecha'] }}</td>
                        <td>{{ $v['hora'] }}</td>
                        @if($showSubtotal)
                        <td class="text-right">${{ $v['subtotal'] }}</td>
                        @endif
                        @if($showItbis)
                        <td class="text-right">${{ $v['impuestos'] }}</td>
                        @endif
                        <td class="text-right"><strong>${{ $v['total'] }}</strong></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 20px; color: #999;">No hay ventas para mostrar</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @if($templateConfig['mostrar_pagos'] ?? true)
            <div class="plantilla-resumen-historial">
                <div class="fila-resumen">
                    <span class="label-resumen">Cantidad de ventas:</span>
                    <span>{{ $historialResumen['cantidad'] ?? 0 }}</span>
                </div>
                @if($showSubtotal)
                <div class="fila-resumen">
                    <span class="label-resumen">Subtotal:</span>
                    <span>${{ $historialResumen['subtotal'] ?? '0.00' }}</span>
                </div>
                @endif
                @if($showItbis)
                <div class="fila-resumen">
                    <span class="label-resumen">ITBIS:</span>
                    <span>${{ $historialResumen['impuestos'] ?? '0.00' }}</span>
                </div>
                @endif
                <div class="fila-resumen total-final">
                    <span class="label-resumen">TOTAL:</span>
                    <span>${{ $historialResumen['total'] ?? '0.00' }}</span>
                </div>
            </div>
            @endif

            @if($templateConfig['mostrar_pie'] ?? true)
            <div class="plantilla-footer">
                @if(($templateConfig['pie_pagina_texto'] ?? ''))
                    {{ $templateConfig['pie_pagina_texto'] }}
                @else
                    Reporte generado el {{ now()->format('d/m/Y h:i A') }} | {{ $variables['empresa']['nombre'] }}
                @endif
            </div>
            @endif

        @else
            <!-- Normal Invoice Preview Mode -->
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
                        <div class="titulo-doc">FACTURA</div>
                        <div class="num-doc">No. {{ $variables['documento']['numero'] }}</div>
                        <div class="dato-doc">Fecha emision: {{ $variables['documento']['fecha_emision'] }}</div>
                        @if(($templateConfig['mostrar_datos_fiscales'] ?? true))
                        <div class="dato-doc">NCF: {{ $variables['documento']['ncf'] }}</div>
                        <div class="dato-doc">Tipo: {{ $variables['documento']['ncf_tipo'] }}</div>
                        @endif
                    </div>
                </div>
            </div>

            @if($templateConfig['mostrar_datos_cliente'] ?? true)
            <div class="plantilla-cliente-box">
                <div class="d-flex">
                    <div style="flex: 1;">
                        <div class="titulo">Cliente</div>
                        <div class="valor">{{ $variables['cliente']['nombre'] }}</div>
                    </div>
                    @if(!empty($variables['cliente']['rnc_cedula']))
                    <div style="flex: 1;">
                        <div class="titulo">RNC/Cedula</div>
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
                        <th style="width: 40%;">Producto</th>
                        @if($showCant)
                        <th class="text-center" style="width: 12%;">Cant.</th>
                        @endif
                        @if($showPrecio)
                        <th class="text-right" style="width: 18%;">P. Unit.</th>
                        @endif
                        @if($showSubtotal)
                        <th class="text-right" style="width: 18%;">Total</th>
                        @endif
                        @if($showItbis)
                        <th class="text-center" style="width: 12%;">ITBIS</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($variables['productos'] as $prod)
                    <tr>
                        <td>
                            {{ $prod['nombre'] }}
                            @if($showGarantias && $prod['garantia_meses'] > 0)
                            <div class="plantilla-garantia">Garantia: {{ $prod['garantia_meses'] }} meses</div>
                            @endif
                            @if(!empty($prod['notas']))
                            <div class="plantilla-notas">{{ $prod['notas'] }}</div>
                            @endif
                        </td>
                        @if($showCant)
                        <td class="text-center">{{ $prod['cantidad'] }}</td>
                        @endif
                        @if($showPrecio)
                        <td class="text-right">{{ $prod['precio_unitario'] }}</td>
                        @endif
                        @if($showSubtotal)
                        <td class="text-right">{{ $prod['subtotal'] }}</td>
                        @endif
                        @if($showItbis)
                        <td class="text-center">{{ $prod['itbis_porcentaje'] }}%</td>
                        @endif
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
                    <span>{{ $variables['totales']['impuestos'] }}</span>
                </div>
                @if($variables['totales']['descuento'] != '0.00')
                <div class="fila-total">
                    <span class="label-total" style="color: #dc3545;">Descuento:</span>
                    <span style="color: #dc3545;">-{{ $variables['totales']['descuento'] }}</span>
                </div>
                @endif
                <div class="fila-total total-final">
                    <span class="label-total">TOTAL:</span>
                    <span>{{ $variables['totales']['total'] }}</span>
                </div>
            </div>

            @if(($templateConfig['mostrar_pagos'] ?? true) && count($variables['pagos']) > 0)
            <div class="plantilla-pagos-titulo">FORMA DE PAGO</div>
            <div class="plantilla-pagos">
                @foreach($variables['pagos'] as $pago)
                <div class="fila-pago">
                    <span>{{ $pago['metodo'] }}</span>
                    <span>{{ $pago['monto'] }}</span>
                </div>
                @endforeach
            </div>
            @endif

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
                    Este documento es una representacion impresa de un NCF electronico.
                @endif
            </div>
            @endif
        @endif
    </div>
</body>
</html>
