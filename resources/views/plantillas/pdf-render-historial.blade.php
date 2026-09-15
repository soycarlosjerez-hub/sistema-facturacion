<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Ventas</title>
    <style>
        @page { margin: 10mm; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
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

        .venta-separator {
            page-break-before: always;
            margin-top: 10mm;
            padding-top: 10mm;
            border-top: 3px solid #333;
        }
    </style>
</head>
<body>
    @php
        $sistema = \App\Models\SystemSetting::allCached();
        $slogan = $sistema['sistema_slogan'] ?? '';
        $ventas = $variables['ventas'] ?? collect();
    @endphp

    @foreach($ventas as $index => $venta)
    @php
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
    @endphp

    <!-- HEADER EMPRESA -->
    <table style="width:100%; margin-bottom:15px;">
        <tr>
            <td style="border:none; vertical-align:top; width:60%;">
                @if($pdfLogoUrl)
                <img src="{{ $pdfLogoUrl }}" style="max-width: 80px; max-height: 60px; object-fit: contain; margin-bottom: 5px;" alt="Logo">
                @endif
                <div class="empresa-nombre">{{ \App\Models\SystemSetting::nombreEmpresaActual() }}</div>
                <div class="empresa-info">
                    @if(!empty($empresa['empresa_rnc']))
                    RNC/Cédula: {{ $empresa['empresa_rnc'] }}<br>
                    @endif
                    Dirección: {{ $empresa['empresa_direccion'] ?? 'N/A' }}<br>
                    Tel: {{ $empresa['empresa_telefono'] ?? 'N/A' }} | Email: {{ $empresa['empresa_email'] ?? 'N/A' }}
                </div>
            </td>
            <td style="border:none; vertical-align:top; text-align:right;">
                <div class="factura-titulo">FACTURA</div>
                <div class="factura-numero">No. {{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div style="font-size:10px; color:#555;">
                    Fecha emisión: {{ $venta->created_at->format('d/m/Y H:i') }}<br>
                    @if($venta->ncf_vencimiento)
                    Vence: {{ $venta->ncf_vencimiento->format('d/m/Y') }}<br>
                    @endif
                    @if($venta->tipo_comprobante === 'ecf' && $venta->encf)
                    ENCF: <span class="ncf-display">{{ $venta->encf }}</span><br>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- ESTADO -->
    @if($esAnulada)
    <div class="anulada-overlay">DOCUMENTO ANULADO</div>
    @endif

    <!-- DATOS FISCALES -->
    @if($venta->ncf || $venta->ncf_tipo)
    <div class="section-title">DATOS FISCALES</div>
    <table style="margin-bottom:10px;">
        <tr>
            <td style="border:none; width:33%;" class="info-box">
                <div class="info-label">Tipo NCF</div>
                <div class="info-value ncf-display">{{ strtoupper($venta->ncf_tipo ?? 'N/A') }}</div>
            </td>
            <td style="border:none; width:33%;" class="info-box">
                <div class="info-label">NCF</div>
                <div class="info-value ncf-display">{{ $venta->ncf ?? 'N/A' }}</div>
            </td>
            <td style="border:none; width:33%;" class="info-box">
                <div class="info-label">Tipo Comprobante</div>
                <div class="info-value">{{ ucfirst($venta->tipo_comprobante ?? 'NCF') }}</div>
            </td>
        </tr>
    </table>
    @endif

    <!-- CLIENTE -->
    <div class="section-title">DATOS DEL CLIENTE</div>
    <table style="margin-bottom:10px;">
        <tr>
            <td style="border:none; width:50%;" class="info-box">
                <div class="info-label">Cliente</div>
                <div class="info-value">{{ $venta->cliente->nombre ?? 'Consumidor Final' }}</div>
            </td>
            @php $rncCliente = $venta->cliente->rnc_cedula ?? $venta->cliente->documento ?? ''; @endphp
            @if(!empty($rncCliente))
            <td style="border:none; width:50%;" class="info-box">
                <div class="info-label">RNC/Cédula</div>
                <div class="info-value">{{ $rncCliente }}</div>
            </td>
            @else
            <td style="border:none; width:50%;"></td>
            @endif
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
                @if($venta->impuestos > 0)
                <th style="width:5%" class="text-center">%ITBIS</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles->where('tipo_linea', '!=', 'delivery') as $d)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $d->producto->nombre ?? $d->obra->titulo ?? 'Producto' }}</td>
                <td class="text-center">{{ number_format($d->cantidad, 2) }}</td>
                <td class="text-right">${{ number_format($d->precio_unitario, 2) }}</td>
                <td class="text-right">${{ number_format($d->subtotal, 2) }}</td>
                @if($venta->impuestos > 0)
                <td class="text-center">{{ $d->sin_itbis ? '0' : ($d->producto->itbis_porcentaje ?? $d->itbis_porcentaje ?? $sistema['impuesto_itbis'] ?? 18) }}%{{ $d->sin_itbis ? ' <span style="font-size:7px;color:#dc3545;">(Sin ITBIS)</span>' : '' }}</td>
                @endif
            </tr>
            @if(!empty($d->producto->garantia_dias) && $d->producto->garantia_dias > 0)
            <tr>
                <td colspan="{{ $venta->impuestos > 0 ? '6' : '5' }}" style="font-size: 8px; color: #0d6efd; padding-left: 20px;">Garantia: {{ $d->producto->garantia_meses }} meses</td>
            </tr>
            @endif
            @if($d->notas)
            <tr>
                <td colspan="{{ $venta->impuestos > 0 ? '6' : '5' }}" style="font-size: 8px; font-style: italic; color: #666;">{{ $d->notas }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>

    <!-- TOTALES CON DESGLOSE -->
    <table class="totals-table">
        <tr>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td class="totals-label">Subtotal Gravado:</td>
            <td class="totals-value">${{ number_format($venta->subtotal, 2) }}</td>
        </tr>
        @if($venta->impuestos > 0)
        <tr>
            <td class="totals-label">ITBIS ({{ $venta->impuestos > 0 ? round(($venta->impuestos / max($venta->subtotal, 1)) * 100) : 0 }}%):</td>
            <td class="totals-value">${{ number_format($venta->impuestos, 2) }}</td>
        </tr>
        @endif
        @if($venta->descuento > 0)
        <tr>
            <td class="totals-label">Descuento:</td>
            <td class="totals-value" style="color:#dc3545;">-${{ number_format($venta->descuento, 2) }}</td>
        </tr>
        @endif
        @if($venta->propina > 0)
        <tr>
            <td class="totals-label">Propina:</td>
            <td class="totals-value">${{ number_format($venta->propina, 2) }}</td>
        </tr>
        @endif
        @if($venta->cargo_servicio > 0)
        <tr>
            <td class="totals-label">Cargo Servicio:</td>
            <td class="totals-value">${{ number_format($venta->cargo_servicio, 2) }}</td>
        </tr>
        @endif
        @if($venta->delivery_fee > 0)
        <tr>
            <td class="totals-label">Delivery Fee:</td>
            <td class="totals-value">${{ number_format($venta->delivery_fee, 2) }}</td>
        </tr>
        @endif
        <tr class="total-final">
            <td class="totals-label">TOTAL:</td>
            <td class="totals-value">${{ number_format($venta->total, 2) }}</td>
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
            @foreach($venta->pagos as $pago)
            <tr>
                <td>{{ ucfirst(str_replace('_', ' ', $pago->metodo_pago)) }}</td>
                <td class="text-right">${{ number_format($pago->monto, 2) }}</td>
            </tr>
            @endforeach
            @if($venta->pagos->isEmpty())
            <tr>
                <td colspan="2" style="text-align:center; color:#999;">Sin registro de pagos</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- ESTADO Y OBSERVACIONES -->
    <div class="section-title">ESTADO</div>
    <table>
        <tr>
            <td style="border:none;">
                <span class="badge badge-{{ $venta->estado }}">{{ strtoupper(str_replace('_', ' ', $venta->estado)) }}</span>
                &nbsp;&nbsp;
                <span style="font-size:10px; color:#555;">
                    Atendido por: {{ $venta->usuario->name ?? 'N/A' }}
                    @if($venta->caja)
                    | Caja: {{ $venta->caja->nombre }}
                    @endif
                    @if($venta->sucursal)
                    | Sucursal: {{ $venta->sucursal->nombre }}
                    @endif
                </span>
            </td>
        </tr>
        @if($venta->notas)
        <tr>
            <td style="border:none; font-size:10px; color:#555; margin-top:5px;">
                <strong>Notas:</strong> {{ $venta->notas }}
            </td>
        </tr>
        @endif
    </table>

    @if($slogan)
    <div style="margin-top:15px; padding-top:10px; border-top:1px dashed #ddd; text-align:center; font-style:italic; color:#555; font-size:10px;">
        {{ $slogan }}
    </div>
    @endif

    @if(count($garantiaTerminos) > 0)
    <table style="width:100%; margin-top:10px; margin-bottom:10px; border-top:1px solid #ccc; padding-top:8px;">
        <tr>
            <td style="font-size:10px; color:#333; line-height:1.5;">
                <strong style="font-size:11px; display:block; margin-bottom:6px;">Términos de Garantía:</strong>
                @foreach($garantiaTerminos as $t)
                <div style="margin-bottom:8px;">
                    <strong style="font-size:10px;">{{ $t['producto'] }}:</strong>
                    <p style="margin:2px 0 0 8px; font-size:9px;">{{ $t['terminos'] }}</p>
                </div>
                @endforeach
            </td>
        </tr>
    </table>
    @endif

    <!-- FOOTER -->
    <div class="footer">
        Este documento es una representación impresa de un NCF electrónico.<br>
        {{ \App\Models\SystemSetting::nombreEmpresaActual() }}@if(!empty($empresa['empresa_rnc'])) | RNC: {{ $empresa['empresa_rnc'] }}@endif<br>
        @if($venta->ncf)
        NCF: {{ $venta->ncf }} | Factura No. {{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}
        @endif
    </div>

    @if($index < $ventas->count() - 1)
    <div class="venta-separator"></div>
    @endif

    @endforeach

</body>
</html>
