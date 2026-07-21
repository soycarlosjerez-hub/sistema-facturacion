<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura #{{ $venta->id }}</title>
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

    @php
        $empresa = \App\Models\SystemSetting::allCached();
        $esAnulada = $venta->trashed() || $venta->estado === 'anulada';
    @endphp

    <!-- HEADER EMPRESA -->
    <table style="width:100%; margin-bottom:15px;">
        <tr>
            <td style="border:none; vertical-align:top; width:60%;">
                <div class="empresa-nombre">{{ $empresa['empresa_nombre'] ?? 'Mi Negocio' }}</div>
                <div class="empresa-info">
                    RNC/Cédula: {{ $empresa['empresa_rnc'] ?? 'N/A' }}<br>
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
            <td style="border:none; width:50%;" class="info-box">
                <div class="info-label">RNC/Cédula</div>
                <div class="info-value">{{ $venta->cliente->rnc_cedula ?? $venta->cliente->documento ?? '00000000000' }}</div>
            </td>
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
                <th style="width:5%" class="text-center">%ITBIS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $index => $d)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $d->producto->nombre }}</td>
                <td class="text-center">{{ number_format($d->cantidad, 2) }}</td>
                <td class="text-right">${{ number_format($d->precio_unitario, 2) }}</td>
                <td class="text-right">${{ number_format($d->subtotal, 2) }}</td>
                <td class="text-center">{{ $d->producto->itbis_porcentaje ?? 18 }}%</td>
            </tr>
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
        <tr>
            <td class="totals-label">ITBIS ({{ $venta->impuestos > 0 ? round(($venta->impuestos / max($venta->subtotal, 1)) * 100) : 18 }}%):</td>
            <td class="totals-value">${{ number_format($venta->impuestos, 2) }}</td>
        </tr>
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

    <!-- FOOTER -->
    <div class="footer">
        Este documento es una representación impresa de un NCF electrónico.<br>
        {{ $empresa['empresa_nombre'] ?? 'Mi Negocio' }} | RNC: {{ $empresa['empresa_rnc'] ?? 'N/A' }}<br>
        @if($venta->ncf)
        NCF: {{ $venta->ncf }} | Factura No. {{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}
        @endif
    </div>

</body>
</html>
