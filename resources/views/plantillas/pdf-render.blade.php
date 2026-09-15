<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>
        @if($template->modulo === 'historial_ventas')
            {{ $template->nombre }} - Historial de Ventas
        @else
            {{ $template->nombre }} - Factura #{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}
        @endif
    </title>
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
            border-bottom: 2px solid {{ $templateConfig['color_primario'] ?? '#8b5cf6' }};
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .plantilla-header .empresa-info {
            font-size: 14px;
            font-weight: bold;
            color: {{ $templateConfig['color_primario'] ?? '#8b5cf6' }};
        }
        .plantilla-header .empresa-datos {
            font-size: 9px;
            color: #666;
        }
        .plantilla-header .empresa-custom {
            font-size: 9px;
            color: {{ $templateConfig['color_primario'] ?? '#8b5cf6' }};
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
            color: {{ $templateConfig['color_primario'] ?? '#8b5cf6' }};
        }
        .plantilla-doc-info .num-doc {
            font-size: 11px;
            font-weight: bold;
        }
        .plantilla-doc-info .dato-doc {
            font-size: 9px;
            color: #666;
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
            background: {{ $templateConfig['color_primario'] ?? '#8b5cf6' }};
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
        .plantilla-tabla tbody tr:nth-child(even) { background: rgba(248,250,252,0.5); }
        .plantilla-tabla tbody tr:last-child td { border-bottom: none; }
        .plantilla-totales {
            text-align: right;
            font-size: 10px;
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
            border-top: 2px solid {{ $templateConfig['color_primario'] ?? '#8b5cf6' }};
            margin-top: 3px;
            padding-top: 3px;
        }
        .plantilla-totales .total-final .label-total {
            color: {{ $templateConfig['color_primario'] ?? '#8b5cf6' }};
        }
        .plantilla-footer {
            margin-top: 8px;
            padding-top: 6px;
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
        .plantilla-filtros {
            margin-bottom: 10px;
            padding: 6px 10px;
            background: rgba(248,250,252,0.8);
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            font-size: 9px;
            color: #555;
        }
        .plantilla-filtros strong { color: #333; }
        .plantilla-resumen-historial {
            width: 50%;
            float: right;
            margin-top: 8px;
        }
        .plantilla-resumen-historial .fila-resumen {
            display: flex;
            justify-content: flex-end;
            padding: 1px 0;
            font-size: 9px;
        }
        .plantilla-resumen-historial .label-resumen {
            color: #666;
            margin-right: 15px;
        }
        .plantilla-resumen-historial .total-final {
            font-weight: bold;
            font-size: 12px;
            border-top: 2px solid {{ $templateConfig['color_primario'] ?? '#8b5cf6' }};
            margin-top: 3px;
            padding-top: 3px;
        }
        .plantilla-resumen-historial .total-final .label-resumen {
            color: {{ $templateConfig['color_primario'] ?? '#8b5cf6' }};
        }
        @media print {
            @page { margin: 10mm; }
        }
    </style>
</head>
<body>
    @php
        $primario = $templateConfig['color_primario'] ?? '#8b5cf6';
        $esAnulada = $venta->trashed() || $venta->estado === 'anulada';
        $moduloTarget = $template->modulo ?? 'ventas';
        $isHistorial = $moduloTarget === 'historial_ventas';
        $tituloDoc = $isHistorial ? 'HISTORIAL DE VENTAS' : match($moduloTarget) {
            'compras' => 'COMPROBANTE DE COMPRA',
            'cotizaciones' => 'COTIZACION',
            'devoluciones' => 'DEVOLUCION',
            'ordenes' => 'ORDEN',
            'conduces' => 'CONDUCE',
            'presupuestos' => 'PRESUPUESTO',
            default => 'FACTURA',
        };
        $variables = $variables ?? [];
        $historialVentas = $isHistorial ? ($variables['ventas'] ?? collect()) : collect();
        $historialResumen = $isHistorial ? ($variables['resumen'] ?? []) : [];
        $historialFiltros = $isHistorial ? ($variables['filtros'] ?? []) : [];
        $esEcf = isset($ecf);
        $esCompra = isset($compra);
    @endphp

    <div class="plantilla-wrapper">
        @if($esAnulada && ! $isHistorial)
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
                        @php $empresaSetting = \App\Models\SystemSetting::allCached(); @endphp
                        @if(!empty($empresaSetting['empresa_rnc']))RNC/Cedula: {{ $empresaSetting['empresa_rnc'] }} @endif
                        @if(!empty($empresaSetting['empresa_direccion']))| {{ $empresaSetting['empresa_direccion'] }} @endif
                        @if(!empty($empresaSetting['empresa_telefono']))| Tel: {{ $empresaSetting['empresa_telefono'] }} @endif
                    </div>
                    @if(($templateConfig['encabezado_texto'] ?? '') && ($templateConfig['mostrar_encabezado'] ?? true))
                    <div class="empresa-custom">{{ $templateConfig['encabezado_texto'] }}</div>
                    @endif
                </div>
                <div class="plantilla-doc-info">
                    @if($isHistorial)
                        <div class="titulo-doc">{{ $tituloDoc }}</div>
                        <div class="dato-doc">Generado: {{ now()->format('d/m/Y h:i A') }}</div>
                        <div class="dato-doc">Total registros: {{ $historialVentas->count() }}</div>
                    @elseif($esEcf)
                        <div class="titulo-doc">{{ $tituloDoc }}</div>
                        <div class="num-doc">No. {{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}</div>
                        <div class="dato-doc">Fecha emision: {{ ($ecf->venta ?? $venta)->created_at->format('d/m/Y H:i') }}</div>
                        @if(($templateConfig['mostrar_datos_fiscales'] ?? true) && ($ecf->venta ?? $venta)->ncf)
                        <div class="dato-doc">NCF: {{ ($ecf->venta ?? $venta)->ncf }}</div>
                        @endif
                        @if(($templateConfig['mostrar_datos_fiscales'] ?? true) && ($ecf->venta ?? $venta)->ncf_tipo)
                        <div class="dato-doc">Tipo: {{ strtoupper(($ecf->venta ?? $venta)->ncf_tipo) }}</div>
                        @endif
                    @elseif($esCompra)
                        <div class="titulo-doc">{{ $tituloDoc }}</div>
                        <div class="num-doc">No. {{ str_pad($compra->id, 5, '0', STR_PAD_LEFT) }}</div>
                        <div class="dato-doc">Fecha emision: {{ $compra->fecha?->format('d/m/Y') }}</div>
                    @else
                        <div class="titulo-doc">{{ $tituloDoc }}</div>
                        <div class="num-doc">No. {{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}</div>
                        <div class="dato-doc">Fecha emision: {{ $venta->created_at->format('d/m/Y H:i') }}</div>
                        @if(($templateConfig['mostrar_datos_fiscales'] ?? true) && $venta->ncf)
                        <div class="dato-doc">NCF: {{ $venta->ncf }}</div>
                        @endif
                        @if(($templateConfig['mostrar_datos_fiscales'] ?? true) && $venta->ncf_tipo)
                        <div class="dato-doc">Tipo: {{ strtoupper($venta->ncf_tipo) }}</div>
                        @endif
                        @if(($templateConfig['mostrar_datos_fiscales'] ?? true) && $venta->encf)
                        <div class="dato-doc">ENCF: {{ $venta->encf }}</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if($isHistorial)
            <!-- Historial de Ventas Mode -->

            <!-- Filtros aplicados -->
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

            <!-- Tabla de ventas -->
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

            <!-- Resumen historial -->
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

            <!-- Footer -->
            @if($templateConfig['mostrar_pie'] ?? true)
            <div class="plantilla-footer">
                @if(($templateConfig['pie_pagina_texto'] ?? ''))
                    {{ $templateConfig['pie_pagina_texto'] }}
                @else
                    Reporte generado el {{ now()->format('d/m/Y h:i A') }} | {{ \App\Models\SystemSetting::nombreEmpresaActual() }}
                @endif
                @php $slogan = \App\Models\SystemSetting::get('sistema_slogan'); @endphp
                @if($slogan)
                <div style="margin-top: 3px; font-style: italic;">{{ $slogan }}</div>
                @endif
            </div>
            @endif

        @elseif($esEcf)
            <!-- ECF Mode (same as existing) -->
            @php
                $ventaObj = $ecf->venta ?? $venta;
            @endphp

            <!-- Cliente -->
            @if($templateConfig['mostrar_datos_cliente'] ?? true)
            <div class="plantilla-cliente-box">
                <div class="d-flex">
                    <div style="flex: 1;">
                        <div class="titulo">Cliente</div>
                        <div class="valor">{{ $ventaObj->cliente->nombre ?? 'Consumidor Final' }}</div>
                    </div>
                    @php $rncCliente = $ventaObj->cliente->rnc_cedula ?? $ventaObj->cliente->documento ?? ''; @endphp
                    @if(!empty($rncCliente))
                    <div style="flex: 1;">
                        <div class="titulo">RNC/Cedula</div>
                        <div class="valor">{{ $rncCliente }}</div>
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
                    @foreach($ventaObj->detalles->where('tipo_linea', '!=', 'delivery') as $d)
                    <tr>
                        <td>
                            {{ $d->producto->nombre ?? $d->obra->titulo ?? ($d->servicio->nombre ?? 'Producto') }}
                            @if($showGarantias && !empty($d->producto->garantia_meses) && $d->producto->garantia_meses > 0)
                            <div class="plantilla-garantia">Garantia: {{ $d->producto->garantia_meses }} meses</div>
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
                        @if($showSubtotal)
                        <td class="text-right">${{ number_format($d->subtotal, 2) }}</td>
                        @endif
                        @if($showItbis)
                        <td class="text-center">{{ $d->sin_itbis ? '0' : ($d->producto->itbis_porcentaje ?? $d->itbis_porcentaje ?? 18) }}%{{ $d->sin_itbis ? ' <span style="font-size:7px;color:#dc3545;">(Sin)</span>' : '' }}</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Totales -->
            <div class="plantilla-totales">
                <div class="fila-total">
                    <span class="label-total">Subtotal:</span>
                    <span>${{ number_format($ventaObj->subtotal, 2) }}</span>
                </div>
                @if($ventaObj->impuestos > 0)
                <div class="fila-total">
                    <span class="label-total">ITBIS ({{ round(($ventaObj->impuestos / max($ventaObj->subtotal, 1)) * 100) }}%):</span>
                    <span>${{ number_format($ventaObj->impuestos, 2) }}</span>
                </div>
                @endif
                @if($ventaObj->descuento > 0)
                <div class="fila-total">
                    <span class="label-total" style="color: #dc3545;">Descuento:</span>
                    <span style="color: #dc3545;">-${{ number_format($ventaObj->descuento, 2) }}</span>
                </div>
                @endif
                @if($ventaObj->propina > 0)
                <div class="fila-total">
                    <span class="label-total">Propina:</span>
                    <span>${{ number_format($ventaObj->propina, 2) }}</span>
                </div>
                @endif
                @if($ventaObj->cargo_servicio > 0)
                <div class="fila-total">
                    <span class="label-total">Cargo Servicio:</span>
                    <span>${{ number_format($ventaObj->cargo_servicio, 2) }}</span>
                </div>
                @endif
                @if($ventaObj->delivery_fee > 0)
                <div class="fila-total">
                    <span class="label-total">Delivery Fee:</span>
                    <span>${{ number_format($ventaObj->delivery_fee, 2) }}</span>
                </div>
                @endif
                <div class="fila-total total-final">
                    <span class="label-total">TOTAL:</span>
                    <span>${{ number_format($ventaObj->total, 2) }}</span>
                </div>
            </div>

            <!-- Pagos -->
            @if(($templateConfig['mostrar_pagos'] ?? true) && $ventaObj->pagos->count() > 0)
            <div class="plantilla-pagos-titulo">FORMA DE PAGO</div>
            <div class="plantilla-pagos">
                @foreach($ventaObj->pagos as $pago)
                <div class="fila-pago">
                    <span>{{ ucfirst(str_replace('_', ' ', $pago->metodo_pago)) }}</span>
                    <span>${{ number_format($pago->monto, 2) }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Notas -->
            @if(($templateConfig['mostrar_notas'] ?? true) && !empty($ventaObj->notas))
            <div style="margin-top: 6px; font-size: 9px;">
                <strong>Notas:</strong> {{ $ventaObj->notas }}
            </div>
            @endif

            <!-- Footer -->
            @if($templateConfig['mostrar_pie'] ?? true)
            <div class="plantilla-footer">
                @php $slogan = \App\Models\SystemSetting::get('sistema_slogan'); @endphp
                @if(($templateConfig['pie_pagina_texto'] ?? ''))
                    {{ $templateConfig['pie_pagina_texto'] }}
                @else
                    Este documento es una representacion impresa de un NCF electronico. | {{ \App\Models\SystemSetting::nombreEmpresaActual() }}
                @endif
                @if($slogan)
                <div style="margin-top: 3px; font-style: italic;">{{ $slogan }}</div>
                @endif
            </div>
            @endif

        @else
            <!-- Normal Invoice/Purchase Mode (existing logic) -->

            @if($esCompra)
                <!-- Compra Mode -->
                @if($templateConfig['mostrar_datos_cliente'] ?? true)
                <div class="plantilla-cliente-box">
                    <div class="d-flex">
                        <div style="flex: 1;">
                            <div class="titulo">Proveedor</div>
                            <div class="valor">{{ $compra->proveedor?->nombre ?? 'N/A' }}</div>
                        </div>
                        @php $rncProveedor = $compra->proveedor?->rnc_cedula ?? $compra->proveedor?->documento ?? ''; @endphp
                        @if(!empty($rncProveedor))
                        <div style="flex: 1;">
                            <div class="titulo">RNC/Cedula</div>
                            <div class="valor">{{ $rncProveedor }}</div>
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
                    $showGarantias = false;
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
                        @foreach($compra->detalles as $d)
                        <tr>
                            <td>
                                {{ $d->producto->nombre ?? $d->servicio->nombre ?? 'Producto' }}
                            </td>
                            @if($showCant)
                            <td class="text-center">{{ number_format($d->cantidad, 2) }}</td>
                            @endif
                            @if($showPrecio)
                            <td class="text-right">${{ number_format($d->precio_unitario, 2) }}</td>
                            @endif
                            @if($showSubtotal)
                            <td class="text-right">${{ number_format($d->subtotal, 2) }}</td>
                            @endif
                            @if($showItbis)
                            <td class="text-center">{{ $d->itbis_porcentaje ?? 18 }}%</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="plantilla-totales">
                    <div class="fila-total">
                        <span class="label-total">Subtotal:</span>
                        <span>${{ number_format($compra->subtotal, 2) }}</span>
                    </div>
                    @if($compra->itbis_total > 0)
                    <div class="fila-total">
                        <span class="label-total">ITBIS:</span>
                        <span>${{ number_format($compra->itbis_total, 2) }}</span>
                    </div>
                    @endif
                    <div class="fila-total total-final">
                        <span class="label-total">TOTAL:</span>
                        <span>${{ number_format($compra->total, 2) }}</span>
                    </div>
                </div>

                @if(($templateConfig['mostrar_pie'] ?? true))
                <div class="plantilla-footer">
                    @if(($templateConfig['pie_pagina_texto'] ?? ''))
                        {{ $templateConfig['pie_pagina_texto'] }}
                    @else
                        Comprobante de compra generado el {{ now()->format('d/m/Y h:i A') }} | {{ \App\Models\SystemSetting::nombreEmpresaActual() }}
                    @endif
                </div>
                @endif

            @else
                <!-- Venta/Invoice Mode (original) -->
                <!-- Cliente -->
                @if($templateConfig['mostrar_datos_cliente'] ?? true)
                <div class="plantilla-cliente-box">
                    <div class="d-flex">
                        <div style="flex: 1;">
                            <div class="titulo">Cliente</div>
                            <div class="valor">{{ $venta->cliente->nombre ?? 'Consumidor Final' }}</div>
                        </div>
                        @php $rncCliente = $venta->cliente->rnc_cedula ?? $venta->cliente->documento ?? ''; @endphp
                        @if(!empty($rncCliente))
                        <div style="flex: 1;">
                            <div class="titulo">RNC/Cedula</div>
                            <div class="valor">{{ $rncCliente }}</div>
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
                        @foreach($venta->detalles->where('tipo_linea', '!=', 'delivery') as $d)
                        <tr>
                            <td>
                                {{ $d->producto->nombre ?? $d->obra->titulo ?? ($d->servicio->nombre ?? 'Producto') }}
                                @if($showGarantias && !empty($d->producto->garantia_meses) && $d->producto->garantia_meses > 0)
                                <div class="plantilla-garantia">Garantia: {{ $d->producto->garantia_meses }} meses</div>
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
                            @if($showSubtotal)
                            <td class="text-right">${{ number_format($d->subtotal, 2) }}</td>
                            @endif
                            @if($showItbis)
                            <td class="text-center">{{ $d->sin_itbis ? '0' : ($d->producto->itbis_porcentaje ?? $d->itbis_porcentaje ?? 18) }}%{{ $d->sin_itbis ? ' <span style="font-size:7px;color:#dc3545;">(Sin)</span>' : '' }}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Totales -->
                <div class="plantilla-totales">
                    <div class="fila-total">
                        <span class="label-total">Subtotal:</span>
                        <span>${{ number_format($venta->subtotal, 2) }}</span>
                    </div>
                    @if($venta->impuestos > 0)
                    <div class="fila-total">
                        <span class="label-total">ITBIS ({{ round(($venta->impuestos / max($venta->subtotal, 1)) * 100) }}%):</span>
                        <span>${{ number_format($venta->impuestos, 2) }}</span>
                    </div>
                    @endif
                    @if($venta->descuento > 0)
                    <div class="fila-total">
                        <span class="label-total" style="color: #dc3545;">Descuento:</span>
                        <span style="color: #dc3545;">-${{ number_format($venta->descuento, 2) }}</span>
                    </div>
                    @endif
                    @if($venta->propina > 0)
                    <div class="fila-total">
                        <span class="label-total">Propina:</span>
                        <span>${{ number_format($venta->propina, 2) }}</span>
                    </div>
                    @endif
                    @if($venta->cargo_servicio > 0)
                    <div class="fila-total">
                        <span class="label-total">Cargo Servicio:</span>
                        <span>${{ number_format($venta->cargo_servicio, 2) }}</span>
                    </div>
                    @endif
                    @if($venta->delivery_fee > 0)
                    <div class="fila-total">
                        <span class="label-total">Delivery Fee:</span>
                        <span>${{ number_format($venta->delivery_fee, 2) }}</span>
                    </div>
                    @endif
                    <div class="fila-total total-final">
                        <span class="label-total">TOTAL:</span>
                        <span>${{ number_format($venta->total, 2) }}</span>
                    </div>
                </div>

                <!-- Pagos -->
                @if(($templateConfig['mostrar_pagos'] ?? true) && $venta->pagos->count() > 0)
                <div class="plantilla-pagos-titulo">FORMA DE PAGO</div>
                <div class="plantilla-pagos">
                    @foreach($venta->pagos as $pago)
                    <div class="fila-pago">
                        <span>{{ ucfirst(str_replace('_', ' ', $pago->metodo_pago)) }}</span>
                        <span>${{ number_format($pago->monto, 2) }}</span>
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- Notas -->
                @if(($templateConfig['mostrar_notas'] ?? true) && !empty($venta->notas))
                <div style="margin-top: 6px; font-size: 9px;">
                    <strong>Notas:</strong> {{ $venta->notas }}
                </div>
                @endif

                <!-- Footer -->
                @if($templateConfig['mostrar_pie'] ?? true)
                <div class="plantilla-footer">
                    @php $slogan = \App\Models\SystemSetting::get('sistema_slogan'); @endphp
                    @if(($templateConfig['pie_pagina_texto'] ?? ''))
                        {{ $templateConfig['pie_pagina_texto'] }}
                    @else
                        Este documento es una representacion impresa de un NCF electronico. | {{ \App\Models\SystemSetting::nombreEmpresaActual() }}
                    @endif
                    @if($slogan)
                    <div style="margin-top: 3px; font-style: italic;">{{ $slogan }}</div>
                    @endif
                </div>
                @endif
            @endif
        @endif
    </div>
</body>
</html>
