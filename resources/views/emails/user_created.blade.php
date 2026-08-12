<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Usuario creado</title>
    <style>
        body {font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f7; color: #333; margin: 0; padding: 0;}
        .container {width: 100%; max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);}
        .header {background: linear-gradient(135deg, #4e54c8, #8f94fb); color: #fff; padding: 30px; text-align: center;}
        .header h1 {margin: 0; font-size: 24px;}
        .content {padding: 30px;}
        .content p {margin: 0 0 15px; line-height: 1.6;}
        .credentials {background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 4px; padding: 15px; font-family: monospace;}
        .footer {background: #f1f5f9; color: #555; text-align: center; padding: 20px; font-size: 12px;}
        a {color: #4e54c8; text-decoration: none;}
        .btn {display: inline-block; background: linear-gradient(135deg, #4e54c8, #8f94fb); color: #fff !important; font-weight: 700; padding: 14px 40px; border-radius: 6px; text-decoration: none; font-size: 16px; margin: 10px 0 20px 0;}
        .btn:hover {opacity: 0.9;}
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>¡Bienvenido a {{ $instanceName }}!</h1>
    </div>
    <div class="content">
        <p>Hola {{ $name }},</p>
        <p>Su cuenta ha sido creada exitosamente. A continuación encontrará los datos de acceso que podrá utilizar para iniciar sesión en la plataforma.</p>
        <div class="credentials">
            <p><strong>Correo electr&oacute;nico:</strong> {{ $email }}</p>
            <p><strong>Contrase&ntilde;a:</strong> {{ $password }}</p>
        </div>
        <p style="text-align: center; margin: 25px 0 15px 0;">
            <a href="{{ $loginUrl }}" class="btn">Iniciar Sesi&oacute;n &rarr;</a>
        </p>
        <p>Le recomendamos cambiar la contrase&ntilde;a despu&eacute;s de su primer acceso por motivos de seguridad.</p>
        <p>Si tiene alguna duda, no dude en ponerse en contacto con el equipo de soporte.</p>
        <p>Saludos cordiales,<br/>El equipo de {{ $instanceName }}</p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} {{ $instanceName }}. Todos los derechos reservados.
    </div>
</div>
</body>
</html>
