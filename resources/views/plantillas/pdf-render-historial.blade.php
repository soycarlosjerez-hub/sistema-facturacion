<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $template->nombre }} - Historial de Ventas</title>
    <style>
        @page { margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; line-height: 1.3; }
        .plantilla-wrapper { padding: 10mm; color: #333; }
        .plantilla-header { border-bottom: 2px solid {{ $templateConfig['color_primario'] ?? '#2563eb' }}; padding-bottom: 8px; margin-bottom: 10px; }
        .plantilla-header .empresa-info { font-size: 14px; font-weight: bold; color: {{ $templateConfig['color_primario'] ?? '#2563eb' }}; }
        .plantilla-header .empresa-datos { font-size: 9px; color: #666; }
        .plantilla-header .empresa-custom { font-size: 9px; color: {{ $templateConfig['color_primario'] ?? '#2563eb' }}; font-style: italic; margin-top: 3px; }
        .plantilla-doc-info { text-align: right; }
        .plantilla-doc-info .titulo-doc { font-size: 16px; font-weight: bold; color: {{ $templateConfig['color_primario'] ?? '#2563eb' }}; }
        .plantilla-doc-info .dato-doc { font-size: 9px; color: #666; }
        .plantilla-filtros { margin-bottom: 10px; padding: 6px 10px; background: rgba(248,250,252,0.8); border: 1px solid #e2e8f0; border-radius: 3px; font-size: 9px; color: #555; }
        .plantilla-filtros strong { color: #333; }
        .plantilla-tabla { width: 100%; border-collapse: collapse; font-size: 8px; margin-bottom: 8px; }
        .plantilla-tabla thead tr { background: {{ $templateConfig['color_primario'] ?? '#2563eb' }}; color: #fff; }
        .plantilla-tabla thead th { padding: 4px 5px; text-align: left; border: none; font-weight: 600; font-size: 8px; white-space: nowrap; }
        .plantilla-tabla thead th.text-center { text-align: center; }
        .plantilla-tabla thead th.text-right { text-align: right; }
        .plantilla-tabla tbody td { padding: 3px 5px; border-bottom: 1px solid #e2e8f0; }
        .plantilla-tabla tbody td.text-center { text-align: center; }
        .plantilla-tabla tbody td.text-right { text-align: right; }
        .plantilla-tabla tbody tr:nth-child(even) { background: rgba(248,250,252,0.5); }
        .plantilla-tabla tbody tr:last-child td { border-bottom: none; }
        .plantilla-resumen { width: 50%; float: right; margin-top: 8px; }
        .plantilla-resumen .fila-resumen { display: flex; justify-content: flex-end; padding: 1px 0; font-size: 9px; }
        .plantilla-resumen .label-resumen { color: #666; margin-right: 15px; }
        .plantilla-resumen .total-final { font-weight: bold; font-size: 12px; border-top: 2px solid {{ $templateConfig['color_primario'] ?? '#2563eb' }}; margin-top: 3px; padding-top: 3px; }
        .plantilla-resumen .total-final .label-resumen { color: {{ $templateConfig['color_primario'] ?? '#2563eb' }}; }
        .plantilla-footer { clear: both; margin-top: 12px; padding-top: 6px; border-top: 1px dashed #ccc; text-align: center; font-size: 8px; color: #888; }
        @media print { @page { margin: 10mm; } }
    </style>
</head>
<body>
    @php
        $primario = $templateConfig['color_primario'] ?? '#2563eb';
        $variables = $variables ?? [];
        $empresa = $variables['empresa'] ?? [];
        $filtros = $variables['filtros'] ?? [];
        $ventas = $variables['ventas'] ?? collect();
        $resumen = $variables['resumen'] ?? [];
    @endphp

    <div class="plantilla-wrapper">
        <!-- Header -->
        @if($templateConfig['mostrar_encabezado'] ?? true)
        <div class="plantilla-header">
            <div style="display: flex; justify-content: space-between;">
                <div>
                    @if(($templateConfig['mostrar_logo'] ?? true) && $pdfLogoUrl)
                    <img src="{{ $pdfLogoUrl }}" style="max-width: 50px; max-height: 40px; object-fit: contain; margin-bottom: 3px;" alt="Logo">
                    @endif
                    <div class="empresa-info">{{ $empresa['nombre'] ?? '' }}</div>
                    <div class="empresa-datos">
                        @if(!empty($empresa['rnc']))RNC: {{ $empresa['rnc'] }} @endif
                        @if(!empty($empresa['direccion']))| {{ $empresa['direccion'] }} @endif
                        @if(!empty($empresa['telefono']))| Tel: {{ $empresa['telefono'] }} @endif
                    </div>
                    @if(($templateConfig['encabezado_texto'] ?? '') && ($templateConfig['mostrar_encabezado'] ?? true))
                    <div class="empresa-custom">{{ $templateConfig['encabezado_texto'] }}</div>
                    @endif
                </div>
                <div class="plantilla-doc-info">
                    <div class="titulo-doc">HISTORIAL DE VENTAS</div>
                    <div class="dato-doc">Generado: {{ now()->format('d/m/Y h:i A') }}</div>
                    <div class="dato-doc">Total registros: {{ $ventas->count() }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Filtros aplicados -->
        @if(!empty($filtros['cliente']) || !empty($filtros['desde']) || !empty($filtros['hasta']))
        <div class="plantilla-filtros">
            <strong>Filtros aplicados:</strong>
            @if(!empty($filtros['desde'])) Desde: {{ $filtros['desde'] }} @endif
            @if(!empty($filtros['hasta'])) | Hasta: {{ $filtros['hasta'] }} @endif
            @if(!empty($filtros['cliente'])) | Cliente: {{ $filtros['cliente'] }} @endif
        </div>
        @endif

        <!-- Tabla de ventas -->
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
                @forelse($ventas as $v)
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

        <!-- Resumen -->
        @if($templateConfig['mostrar_pagos'] ?? true)
        <div class="plantilla-resumen">
            <div class="fila-resumen">
                <span class="label-resumen">Cantidad de ventas:</span>
                <span>{{ $resumen['cantidad'] ?? 0 }}</span>
            </div>
            @if($showSubtotal)
            <div class="fila-resumen">
                <span class="label-resumen">Subtotal:</span>
                <span>${{ $resumen['subtotal'] ?? '0.00' }}</span>
            </div>
            @endif
            @if($showItbis)
            <div class="fila-resumen">
                <span class="label-resumen">ITBIS:</span>
                <span>${{ $resumen['impuestos'] ?? '0.00' }}</span>
            </div>
            @endif
            <div class="fila-resumen total-final">
                <span class="label-resumen">TOTAL:</span>
                <span>${{ $resumen['total'] ?? '0.00' }}</span>
            </div>
        </div>
        @endif

        <!-- Footer -->
        @if($templateConfig['mostrar_pie'] ?? true)
        <div class="plantilla-footer">
            @if(($templateConfig['pie_pagina_texto'] ?? ''))
                {{ $templateConfig['pie_pagina_texto'] }}
            @else
                Reporte de ventas generado el {{ now()->format('d/m/Y h:i A') }} | {{ $empresa['nombre'] ?? '' }}
            @endif
            @if(!empty($empresa['slogan']))
            <div style="margin-top: 3px; font-style: italic;">{{ $empresa['slogan'] }}</div>
            @endif
        </div>
        @endif
    </div>
</body>
</html>
