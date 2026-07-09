<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservación recibida</title>
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
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
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
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #6c757d;
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
        .status-badge {
            display: inline-block;
            background-color: #fff3cd;
            color: #664d03;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin: 15px 0;
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
            color: #6c757d;
            text-decoration: none;
        }
        @media only screen and (max-width: 600px) {
            .email-body { padding: 20px 15px; }
            .info-row { flex-direction: column; gap: 2px; }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🍽️ Reservación Recibida</h1>
            <p>Hemos recibido tu solicitud de reservación</p>
        </div>

        <div class="email-body">
            <p class="greeting">
                Estimado/a <strong>{{ $reservacion->cliente_nombre }}</strong>,
            </p>

            <p>
                Hemos recibido tu reservación y está pendiente de confirmación. Nuestro equipo la revisará y te notificaremos pronto.
            </p>

            <div class="status-badge">⏳ Pendiente de confirmación</div>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Mesa:</span>
                    <span class="info-value">{{ $reservacion->mesa->nombre ?? 'Mesa ' . $reservacion->mesa->numero }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha y hora:</span>
                    <span class="info-value">{{ $reservacion->fecha_hora->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Personas:</span>
                    <span class="info-value">{{ $reservacion->personas }}</span>
                </div>
                @if($reservacion->notas)
                <div class="info-row" style="flex-direction: column; gap: 4px;">
                    <span class="info-label">Notas:</span>
                    <span class="info-value" style="font-weight: normal;">{{ $reservacion->notas }}</span>
                </div>
                @endif
            </div>

            <p>
                Si necesitas realizar algún cambio, por favor contáctanos directamente.
            </p>

            <p>
                Atentamente,<br>
                <strong>{{ config('app.name') }}</strong>
            </p>
        </div>

        <div class="email-footer">
            <p>
                Este correo fue enviado automáticamente por el sistema de facturación.<br>
                © {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>
