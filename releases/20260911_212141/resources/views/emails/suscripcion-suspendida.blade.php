<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Suscripción suspendida</title>
    <style>
        body {font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f7; color: #333; margin: 0; padding: 0;}
        .container {width: 100%; max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);}
        .header {background: linear-gradient(135deg, #b91c1c, #f87171); color: #fff; padding: 30px; text-align: center;}
        .header h1 {margin: 0; font-size: 24px;}
        .content {padding: 30px;}
        .content p {margin: 0 0 15px; line-height: 1.6;}
        .details {background: #fef2f2; border: 1px solid #fecaca; border-radius: 4px; padding: 15px; margin-bottom: 15px;}
        .details p {margin: 0 0 8px; font-size: 14px;}
        .badge {display: inline-block; background: #fee2e2; color: #b91c1c; border-radius: 999px; padding: 4px 12px; font-size: 12px; font-weight: 700;}
        .footer {background: #f1f5f9; color: #555; text-align: center; padding: 20px; font-size: 12px;}
        a {color: #b91c1c; text-decoration: none;}
        .btn {display: inline-block; background: linear-gradient(135deg, #b91c1c, #f87171); color: #fff !important; font-weight: 700; padding: 14px 40px; border-radius: 6px; text-decoration: none; font-size: 16px; margin: 10px 0 20px 0;}
        .btn:hover {opacity: 0.9;}
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Suscripción suspendida</h1>
    </div>
    <div class="content">
        <p>Estimado cliente,</p>
        <p>La suscripci&oacute;n de <strong>{{ $empresa }}</strong> (plan <span class="badge">{{ $planNombre }}</span>) ha sido suspendida por falta de pago.</p>
        <div class="details">
            <p><strong>Meses atrasados:</strong> {{ $mesesAtrasados }}</p>
            <p><strong>Saldo pendiente estimado:</strong> RD$ {{ $deuda }}</p>
        </div>
        <p>El acceso a la plataforma ha sido restringido. Para desbloquear el sistema, realice su pago de inmediato.</p>
        <p style="text-align: center; margin: 25px 0 15px 0;">
            <a href="{{ $suscripcionUrl }}" class="btn">Regularizar Pago</a>
        </p>
        <p>Si ya realiz&oacute; el pago o necesita ayuda, contacte a su asesor de ventas para regularizar la situaci&oacute;n lo antes posible.</p>
        <p>Saludos cordiales,<br/>El equipo de soporte.</p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} {{ $empresa }}. Todos los derechos reservados.
    </div>
</div>
</body>
</html>