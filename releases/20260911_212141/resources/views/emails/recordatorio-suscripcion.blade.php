<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body {font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f7; color: #333; margin: 0; padding: 0;}
        .container {width: 100%; max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);}
        .header {background: linear-gradient(135deg, #d97706, #fbbf24); color: #fff; padding: 30px; text-align: center;}
        .header h1 {margin: 0; font-size: 24px;}
        .content {padding: 30px;}
        .content p {margin: 0 0 15px; line-height: 1.6;}
        .details {background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 4px; padding: 15px; margin-bottom: 15px;}
        .details p {margin: 0 0 8px; font-size: 14px;}
        .footer {background: #f1f5f9; color: #555; text-align: center; padding: 20px; font-size: 12px;}
        a {color: #d97706; text-decoration: none;}
        .btn {display: inline-block; background: linear-gradient(135deg, #d97706, #fbbf24); color: #fff !important; font-weight: 700; padding: 14px 40px; border-radius: 6px; text-decoration: none; font-size: 16px; margin: 10px 0 20px 0;}
        .btn:hover {opacity: 0.9;}
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>{{ $titulo }}</h1>
    </div>
    <div class="content">
        <p>Estimado cliente,</p>
        <p>Le recordamos que la suscripci&oacute;n de <strong>{{ $empresa }}</strong> est&aacute; por vencer.</p>
        <div class="details">
            <p><strong>D&iacute;as restantes:</strong> {{ $dias }}</p>
            <p><strong>Fecha de vencimiento:</strong> {{ $fechaVencimiento }}</p>
            <p><strong>Saldo pendiente estimado:</strong> RD$ {{ $deuda }}</p>
        </div>
        <p>Realice su pago para continuar disfrutando de todos los m&oacute;dulos del sistema sin interrupciones.</p>
        <p style="text-align: center; margin: 25px 0 15px 0;">
            <a href="{{ $suscripcionUrl }}" class="btn">Pagar Ahora</a>
        </p>
        <p>Saludos cordiales,<br/>El equipo de {{ config('app.name') }}</p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
    </div>
</div>
</body>
</html>