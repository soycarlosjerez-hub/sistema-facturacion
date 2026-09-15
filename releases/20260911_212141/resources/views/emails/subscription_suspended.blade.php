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
        .footer {background: #f1f5f9; color: #555; text-align: center; padding: 20px; font-size: 12px;}
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Suscripción suspendida</h1>
    </div>
    <div class="content">
        <p>Estimado cliente,</p>
        <p>La suscripción de <strong>{{ $instanceName }}</strong> (plan {{ $planName }}) ha sido suspendida por falta de pago.</p>
        <p>Saldo pendiente estimado: <strong>RD$ {{ $deuda }}</strong></p>
        <p>El acceso a la plataforma quedará restringido hasta que regularice el pago de su mensualidad.</p>
        <p>Si ya realizó el pago o necesita ayuda, contacte a su asesor de ventas para regularizar la situación lo antes posible.</p>
        <p>Saludos cordiales,<br/>El equipo de soporte.</p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} {{ $instanceName }}. Todos los derechos reservados.
    </div>
</div>
</body>
</html>
