<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>e-CF {{ $ecf->encf }}</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a1a; }
        .header { width: 100%; margin-bottom: 15px; border-bottom: 2px solid #003876; padding-bottom: 10px; }
        .empresa-nombre { font-size: 18px; font-weight: bold; color: #003876; }
        .rnc { color: #555; font-size: 11px; }
        .titulo { text-align: right; }
        .titulo h1 { font-size: 16px; color: #003876; margin: 0; font-weight: bold; }
        .encf-box { background: #f4f6f8; border: 1px solid #ddd; padding: 8px; margin-top: 8px; text-align: right; }
        .encf { font-size: 14px; font-weight: bold; color: #003876; letter-spacing: 1.5px; }
        .tipo { display: inline-block; background: #003876; color: white; padding: 2px 8px; border-radius: 3px; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #f4f6f8; padding: 6px; text-align: left; font-size: 9px; text-transform: uppercase; }
        td { padding: 5px; border-bottom: 1px solid #eee; font-size: 10px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .totales { margin-top: 10px; width: 50%; float: right; }
        .totales td { padding: 4px 8px; }
        .total-final { background: #003876; color: white; font-weight: bold; }
        .footer { clear: both; margin-top: 30px; padding-top: 10px; border-top: 1px dashed #999; }
        .qr-section { float: left; width: 35%; text-align: center; padding: 10px; background: #fafafa; border: 1px solid #eee; }
        .qr-section img { max-width: 150px; }
        .info-section { float: right; width: 60%; }
        .small { font-size: 8px; color: #777; }
        .estado { padding: 4px 10px; border-radius: 3px; font-size: 9px; font-weight: bold; display: inline-block; }
        .estado-aprobado { background: #d4edda; color: #155724; }
        .estado-pendiente { background: #fff3cd; color: #856404; }
        .estado-rechazado { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <table style="margin: 0;">
            <tr>
                <td style="width: 60%; vertical-align: top; padding: 0;">
                    @php $empresa = \App\Models\SystemSetting::allCached(); @endphp
                    <div class="empresa-nombre">{{ $empresa['empresa_nombre'] ?? 'EMPRESA DEMO SRL' }}</div>
                    <div class="rnc">RNC: {{ $empresa['empresa_rnc'] ?? '000000000' }}</div>
                    <div class="rnc">{{ $empresa['empresa_direccion'] ?? 'Santo Domingo, R.D.' }}</div>
                    <div class="rnc">Tel: {{ $empresa['empresa_telefono'] ?? '809-000-0000' }}</div>
                </td>
                <td style="width: 40%; vertical-align: top; padding: 0;" class="titulo">
                    <span class="tipo">COMPROBANTE FISCAL ELECTRÓNICO</span>
                    <h1>e-CF</h1>
                    <div class="encf-box">
                        <div class="encf">{{ $ecf->encf }}</div>
                        <div class="small">{{ $ecf->tipo_nombre }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @php
        $estadoInfo = $ecf->estado_info;
        $estadoClass = $ecf->estado === 'aprobado' ? 'estado-aprobado' : ($ecf->estado === 'rechazado' ? 'estado-rechazado' : 'estado-pendiente');
    @endphp

    <div class="text-center" style="margin-bottom: 10px;">
        <span class="estado {{ $estadoClass }}">
            ESTADO: {{ strtoupper($estadoInfo['label']) }}
            @if($ecf->track_id_dgii)
                | Track ID: {{ $ecf->track_id_dgii }}
            @endif
        </span>
    </div>

    <table style="margin-bottom: 15px;">
        <tr>
            <td style="width: 50%; padding: 5px; background: #fafafa; vertical-align: top;">
                <strong style="font-size: 9px;">EMITIDO POR:</strong><br>
                <strong>{{ $empresa['empresa_nombre'] ?? 'EMPRESA DEMO SRL' }}</strong><br>
                RNC: {{ $empresa['empresa_rnc'] ?? '000000000' }}
            </td>
            <td style="width: 50%; padding: 5px; background: #fafafa; vertical-align: top;">
                <strong style="font-size: 9px;">FECHA DE EMISIÓN:</strong> {{ $ecf->fecha_emision->format('d/m/Y') }}<br>
                <strong style="font-size: 9px;">HORA:</strong> {{ $ecf->fecha_emision->format('h:i:s A') }}<br>
                @if($ecf->fecha_aprobacion)
                <strong style="font-size: 9px;">APROBADO DGII:</strong> {{ $ecf->fecha_aprobacion->format('d/m/Y h:i:s A') }}
                @endif
            </td>
        </tr>
    </table>

    @if($ecf->venta && $ecf->venta->cliente)
    <table style="margin-bottom: 15px;">
        <tr>
            <td colspan="2" style="background: #003876; color: white; padding: 5px; font-weight: bold; font-size: 9px;">DATOS DEL COMPRADOR</td>
        </tr>
        <tr>
            <td style="width: 50%; padding: 5px;">
                <strong style="font-size: 8px;">NOMBRE / RAZÓN SOCIAL:</strong><br>
                {{ $ecf->venta->cliente->nombre }}
            </td>
            <td style="width: 50%; padding: 5px;">
                <strong style="font-size: 8px;">RNC / CÉDULA:</strong>
                {{ $ecf->venta->cliente->rnc_cedula ?: 'N/A' }}
            </td>
        </tr>
    </table>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 50%;">Descripción</th>
                <th style="width: 10%; text-align: right;">Cant.</th>
                <th style="width: 15%; text-align: right;">Precio</th>
                <th style="width: 10%; text-align: right;">ITBIS</th>
                <th style="width: 15%; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ecf->venta->detalles as $i => $d)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $d->producto->nombre }}</td>
                <td class="text-end">{{ $d->cantidad }}</td>
                <td class="text-end">${{ number_format($d->precio_unitario, 2) }}</td>
                <td class="text-end">${{ number_format($d->subtotal * ($d->producto->itbis_porcentaje / 100), 2) }}</td>
                <td class="text-end">${{ number_format($d->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totales">
        <tr>
            <td>Monto Gravado</td>
            <td class="text-end">${{ number_format($ecf->monto_gravado_total, 2) }}</td>
        </tr>
        <tr>
            <td>Monto Exento</td>
            <td class="text-end">${{ number_format($ecf->monto_exento_total, 2) }}</td>
        </tr>
        <tr>
            <td>ITBIS (18%)</td>
            <td class="text-end">${{ number_format($ecf->itbis_total, 2) }}</td>
        </tr>
        <tr class="total-final">
            <td>TOTAL</td>
            <td class="text-end">${{ number_format($ecf->monto_total, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        <div class="qr-section">
            <img src="{{ $qrUrl }}" alt="QR de Consulta"><br>
            <div class="small">Consulte este comprobante en DGII</div>
            <div class="small" style="margin-top: 5px;">Código: <strong>{{ $ecf->codigo_seguridad }}</strong></div>
        </div>
        <div class="info-section">
            <p class="small">
                <strong>Representación Impresa del Comprobante Fiscal Electrónico (e-CF)</strong><br>
                Este documento es una representación impresa de un e-CF emitido conforme a las normas
                de la Dirección General de Impuestos Internos (DGII) de la República Dominicana.<br>
                Para verificar la autenticidad de este comprobante, escanee el código QR o visite el portal
                de consulta de e-CF de la DGII con los datos: RNC Emisor, eNCF, monto y fecha.
            </p>
            @if($ecf->estado !== 'aprobado')
            <p class="small" style="color: #856404;">
                <strong>VERSIÓN PRELIMINAR</strong> - Este e-CF aún no ha sido aprobado por la DGII.
                @if($ecf->mensaje_dgii)<br>Motivo: {{ $ecf->mensaje_dgii }}@endif
            </p>
            @else
            <p class="small" style="color: #155724;">
                <strong>APROBADO POR DGII</strong> - Track ID: {{ $ecf->track_id_dgii }}<br>
                Firma Digital: {{ substr($ecf->firma_digital ?? 'N/A', 0, 60) }}...
            </p>
            @endif
        </div>
    </div>
</body>
</html>
