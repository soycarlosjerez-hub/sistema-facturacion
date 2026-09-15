<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos de Servicio - Erpipos ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #f8fafc; }
        .hero { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; padding: 60px 0; text-align: center; }
        .content { max-width: 800px; margin: 0 auto; padding: 40px 20px; }
        h2 { color: #0f172a; margin-top: 30px; }
        .back-link { color: #3b82f6; }
    </style>
</head>
<body>
    <div class="hero">
        <h1>Términos de Servicio</h1>
        <p>Última actualización: {{ date('F Y') }}</p>
    </div>
    <div class="content">
        <p>Bienvenido a <strong>Erpipos ERP</strong>. Al acceder y utilizar nuestra plataforma, usted acepta los siguientes términos y condiciones. Por favor, lea cuidadosamente.</p>

        <h2>1. Aceptación de los Términos</h2>
        <p>Al crear una cuenta y acceder a Erpipos ERP, usted acepta quedar vinculado por estos Términos de Servicio y nuestra Política de Privacidad. Si no está de acuerdo con alguna parte de los términos, no podrá acceder al servicio.</p>

        <h2>2. Descripción del Servicio</h2>
        <p>Erpipos ERP es una plataforma SaaS (Software as a Service) que proporciona herramientas de gestión empresarial, incluyendo facturación electrónica (e-CF) conforme a los requisitos de la DGII de República Dominicana, gestión de inventarios, ventas, compras y reportes fiscales.</p>

        <h2>3. Registro y Cuenta</h2>
        <ul>
            <li>Debe proporcionar información precisa y completa al registrarse.</li>
            <li>Es responsable de mantener la confidencialidad de su cuenta y contraseña.</li>
            <li>Acepta notificar inmediatamente cualquier uso no autorizado de su cuenta.</li>
        </ul>

        <h2>4. Uso Aceptable</h2>
        <p>Usted se compromete a no:</p>
        <ul>
            <li>Utilizar el servicio para actividades ilegales o no autorizadas.</li>
            <li>Intentar acceder a datos de otros usuarios o instancias.</li>
            <li>Realizar ingeniería inversa o descompilar la plataforma.</li>
            <li>Utilizar el servicio para enviar spam o malware.</li>
        </ul>

        <h2>5. Facturación y Pagos</h2>
        <ul>
            <li>Los planes y precios se publican en nuestra página de precios.</li>
            <li>Los pagos se procesan mediante proveedores de terceros.</li>
            <li>Los cargos se facturan por adelantado en intervalos mensuales o anuales.</li>
            <li>Los pagos no son reembolsables salvo lo indicado en la Política de Reembolso.</li>
        </ul>

        <h2>6. Propiedad Intelectual</h2>
        <p>El servicio y su contenido original (código, diseño, logos, etc.) están protegidos por derechos de autor y marcas comerciales. Usted retiene la propiedad de los datos que ingrese en la plataforma.</p>

        <h2>7. Limitación de Responsabilidad</h2>
        <p>Erpipos ERP no será responsable por daños indirectos, incidentales o consecuentes. La responsabilidad total no excederá el monto pagado por usted en los 12 meses anteriores al evento.</p>

        <h2>8. Terminación</h2>
        <p>Podemos suspender o terminar su cuenta si viola estos términos. También puede cerrar su cuenta en cualquier momento. Al terminar, dejará de tener acceso al servicio pero mantendremos sus datos por 30 días para recuperación.</p>

        <h2>9. Cambios en los Términos</h2>
        <p>Nos reservamos el derecho de modificar estos términos. Le notificaremos cambios significativos por email o notificación dentro de la plataforma.</p>

        <h2>10. Contacto</h2>
        <p>Para preguntas sobre estos términos: <a href="mailto:legal@erpipos.com">legal@erpipos.com</a></p>
    </div>
    <footer class="text-center py-4 text-muted border-top">
        © {{ date('Y') }} Erpipos ERP. Todos los derechos reservados.
        <a href="/privacidad" class="back-link">Privacidad</a>
    </footer>
</body>
</html>
