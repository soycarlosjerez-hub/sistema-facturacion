<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización {{ $cotizacion->numero }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-header p {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .email-body {
            padding: 30px 25px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .greeting strong {
            color: #0d6efd;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 14px;
        }
        .info-label {
            color: #6c757d;
        }
        .info-value {
            font-weight: 600;
            color: #212529;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }
        .items-table th {
            background-color: #f8f9fa;
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #f0f0f0;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
        }
        .totals {
            margin: 20px 0;
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-radius: 6px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 14px;
        }
        .totals-row.total {
            border-top: 2px solid #0d6efd;
            margin-top: 8px;
            padding-top: 10px;
            font-size: 18px;
            font-weight: 700;
            color: #0d6efd;
        }
        .cta-button {
            display: inline-block;
            background-color: #0d6efd;
            color: #ffffff !important;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .cta-container {
            text-align: center;
            margin: 30px 0;
        }
        .mensaje-adicional {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #664d03;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px 25px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }
        .email-footer a {
            color: #0d6efd;
            text-decoration: none;
        }
        .attachment-notice {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 10px 15px;
            border-radius: 4px;
            font-size: 13px;
            margin: 15px 0;
            text-align: center;
        }
        @media only screen and (max-width: 600px) {
            .email-body { padding: 20px 15px; }
            .items-table { font-size: 12px; }
            .items-table th, .items-table td { padding: 6px 4px; }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>📋 Cotización {{ $cotizacion->numero }}</h1>
            <p>Fecha: {{ $cotizacion->fecha->format('d/m/Y') }} | Válida hasta: {{ $cotizacion->fecha_validez->format('d/m/Y') }}</p>
        </div>

        <div class="email-body">
            <p class="greeting">
                Estimado/a <strong>{{ $cotizacion->cliente?->nombre ?? 'Cliente' }}</strong>,
            </p>

            <p>
                Adjunto encontrará la cotización <strong>{{ $cotizacion->numero }}</strong> con los detalles de los productos y servicios solicitados.
            </p>

            @if($mensajeAdicional)
                <div class="mensaje-adicional">
                    <strong>💬 Mensaje:</strong><br>
                    {{ $mensajeAdicional }}
                </div>
            @endif

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Número:</span>
                    <span class="info-value">{{ $cotizacion->numero }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha de emisión:</span>
                    <span class="info-value">{{ $cotizacion->fecha->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Válida hasta:</span>
                    <span class="info-value">{{ $cotizacion->fecha_validez->format('d/m/Y') }} ({{ $cotizacion->dias_validez }} días)</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Atendido por:</span>
                    <span class="info-value">{{ $cotizacion->user?->name ?? 'N/A' }}</span>
                </div>
            </div>

            <h3 style="color: #495057; margin-top: 25px;">Detalle de productos</h3>
            <table class="items-table" role="table" aria-label="Detalle de productos">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-right">Precio</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cotizacion->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->nombre }}</strong>
                                @if($item->codigo)
                                    <br><small style="color: #6c757d;">{{ $item->codigo }}</small>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->cantidad }}</td>
                            <td class="text-right">RD${{ number_format($item->precio_unitario, 2) }}</td>
                            <td class="text-right">RD${{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals">
                <div class="totals-row">
                    <span>Subtotal:</span>
                    <span>RD${{ number_format($cotizacion->subtotal, 2) }}</span>
                </div>
                @if($cotizacion->descuento > 0)
                    <div class="totals-row">
                        <span>Descuento:</span>
                        <span>- RD${{ number_format($cotizacion->descuento, 2) }}</span>
                    </div>
                @endif
                <div class="totals-row">
                    <span>ITBIS (18%):</span>
                    <span>RD${{ number_format($cotizacion->itbis, 2) }}</span>
                </div>
                <div class="totals-row total">
                    <span>TOTAL:</span>
                    <span>RD${{ number_format($cotizacion->total, 2) }}</span>
                </div>
            </div>

            <div class="attachment-notice">
                📎 La cotización completa en PDF se adjunta a este correo
            </div>

            <div class="cta-container">
                <a href="{{ $urlVer }}" class="cta-button">
                    Ver cotización en línea
                </a>
            </div>

            @if($cotizacion->condiciones)
                <div class="info-box" style="border-left-color: #6c757d;">
                    <strong style="display: block; margin-bottom: 8px;">Términos y condiciones:</strong>
                    <p style="margin: 0; font-size: 13px; color: #495057;">{{ $cotizacion->condiciones }}</p>
                </div>
            @endif

            <p style="margin-top: 25px;">
                Si tiene alguna pregunta o necesita aclaraciones, no dude en contactarnos.<br>
                Quedamos a su disposición.
            </p>

            <p style="margin-top: 20px;">
                Atentamente,<br>
                <strong>{{ $cotizacion->user?->name ?? 'Equipo de Ventas' }}</strong>
            </p>
        </div>

        <div class="email-footer">
            <p>
                Este correo fue enviado automáticamente por el sistema de facturación.<br>
                <a href="{{ $urlVer }}">Ver cotización #{{ $cotizacion->numero }}</a>
            </p>
            <p style="margin-top: 10px; color: #adb5bd;">
                © {{ date('Y') }} Sistema de Facturación. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>
