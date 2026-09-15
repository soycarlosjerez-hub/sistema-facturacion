<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservación cancelada</title>
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
            background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);
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
            border-left: 4px solid #dc3545;
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
            background-color: #f8d7da;
            color: #842029;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin: 15px 0;
        }
        .info-note {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #664d03;
        }
        .cta-button {
            display: inline-block;
            background-color: #dc3545;
            color: #ffffff !important;
            padding: 12px 28px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            margin: 20px 0;
            text-align: center;
        }
        .cta-container {
            text-align: center;
            margin: 25px 0;
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
            color: #dc3545;
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
            <h1>❌ Reservación Cancelada</h1>
            <p>Tu reservación ha sido cancelada</p>
        </div>

        <div class="email-body">
            <p class="greeting">
                Estimado/a <strong>{{ $reservacion->cliente_nombre }}</strong>,
            </p>

            <p>
                Lamentamos informarte que tu reservación ha sido <strong>cancelada</strong>.
            </p>

            <div class="status-badge">❌ Cancelada</div>

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

            <div class="info-note">
                <strong>💡 ¿Deseas reagendar?</strong><br>
                No te preocupes, puedes realizar una nueva reservación contactándonos directamente o a través de nuestro sistema.
            </div>

            <p>
                Disculpa las molestias.<br>
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
