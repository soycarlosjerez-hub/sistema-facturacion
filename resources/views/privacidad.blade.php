<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidad — Erpipos ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body{background:#f8fafc;padding-top:80px;padding-bottom:80px}</style>
</head>
<body>
    <div class="hero">
        <h1>Política de Privacidad</h1>
        <p>Última actualización: {{ date('F Y') }}</p>
    </div>
    <div class="content">
        <p>En <strong>Erpipos ERP</strong> nos comprometemos a proteger la privacidad de nuestros usuarios. Esta Política de Privacidad describe cómo recopilamos, usamos y protegemos su información personal.</p>

        <h2>1. Información que Recopilamos</h2>
        <h3>1.1 Información que usted proporciona:</h3>
        <ul>
            <li><strong>Datos de cuenta:</strong> nombre, email, teléfono, nombre de empresa</li>
            <li><strong>Datos fiscales:</strong> RNC, dirección fiscal, datos de facturación electrónica</li>
            <li><strong>Datos empresariales:</strong> productos, clientes, ventas, compras, inventario, proveedores</li>
            <li><strong>Datos de comunicación:</strong> preferencias de notificación, mensajes de soporte</li>
        </ul>

        <h3>1.2 Información recopilada automáticamente:</h3>
        <ul>
            <li><strong>Datos de uso:</strong> páginas visitadas, funcionalidades utilizadas, tiempos de sesión</li>
            <li><strong>Datos técnicos:</strong> tipo de navegador, sistema operativo, dirección IP</li>
            <li><strong>Logs del sistema:</strong> eventos de acceso, errores, actividad de usuarios</li>
        </ul>

        <h2>2. Finalidad del Tratamiento</h2>
        <p>Utilizamos su información para:</p>
        <ul>
            <li>Proveer y mejorar el servicio de facturación electrónica e-CF</li>
            <li>Gestionar su suscripción y facturación</li>
            <li>Comunicar notificaciones importantes (cambios de sistema, pagos)</li>
            <li>Brindar soporte técnico y atención al cliente</li>
            <li>Cumplir obligaciones legales (normativa DGII, Ley 172-13)</li>
            <li>Prevenir fraude y garantizar la seguridad del sistema</li>
        </ul>

        <h2>3. Base Legal del Tratamiento</h2>
        <ul>
            <li><strong>Ejecución contractual:</strong> datos necesarios para proveer el servicio</li>
            <li><strong>Consentimiento:</strong> datos con su autorización expresa</li>
            <li><strong>Obligación legal:</strong> datos requeridos por la DGII y regulaciones dominicanas</li>
            <li><strong>Interés legítimo:</strong> seguridad y mejora del servicio</li>
        </ul>

        <h2>4. Almacenamiento y Seguridad</h2>
        <p>Los datos se almacenan en servidores seguros con:</p>
        <ul>
            <li>Encriptación TLS/SSL en tránsito y AES-256 en reposo</li>
            <li>Control de acceso basado en roles (RBAC)</li>
            <li>Backups automáticos con retención de 90 días</li>
            <li>Monitoreo de seguridad 24/7 y auditorías periódicas</li>
            <li>Plan de recuperación ante desastres (RTO: 4 horas, RPO: 1 hora)</li>
        </ul>

        <h2>5. Compartir Información</h2>
        <p>No vendemos ni compartimos datos personales con terceros salvo:</p>
        <ul>
            <li><strong>Proveedores de servicio:</strong> hosting, pasarelas de pago, servicios de email</li>
            <li><strong>Requisitos legales:</strong> orden judicial o requerimiento de la DGII</li>
            <li><strong>Protección:</strong> para prevenir fraude o proteger derechos legales</li>
        </ul>

        <h2>6. Transferencias Internacionales</h2>
        <p>Los datos pueden ser procesados fuera de la República Dominicana por proveedores de servicios. Garantizamos que dichos proveedores cumplen con estándares adecuados de protección de datos.</p>

        <h2>7. Derechos del Usuario (Ley 172-13)</h2>
        <p>Conforme a la Ley 172-13 sobre protección de datos personales, usted tiene derecho a:</p>
        <ul>
            <li><strong>Acceso:</strong> solicitar copia de sus datos personales</li>
            <li><strong>Rectificación:</strong> corregir datos inexactos o incompletos</li>
            <li><strong>Eliminación:</strong> solicitar la eliminación de sus datos</li>
            <li><strong>Portabilidad:</strong> obtener sus datos en formato estructurado</li>
            <li><strong>Oposición:</strong> oponerse al tratamiento de ciertos datos</li>
            <li><strong>Retiro del consentimiento:</strong> retirar autorización otorgada</li>
        </ul>

        <h2>8. Cookies</h2>
        <p>Utilizamos:</p>
        <ul>
            <li><strong>Cookies esenciales:</strong> necesarias para el funcionamiento (autenticación, sesión)</li>
            <li><strong>Cookies de analítica:</strong> para entender el uso del servicio (anónimas)</li>
        </ul>

        <h2>9. Retención de Datos</h2>
        <p>Los datos se mantienen mientras usted tenga una cuenta activa o según lo exija la ley (mínimo 7 años para documentos tributarios). Al eliminar su cuenta, los datos se eliminan en 30 días, salvo obligación legal de retención.</p>

        <h2>10. Menores de Edad</h2>
        <p>El servicio no está dirigido a menores de 18 años. No recopilamos intencionalmente datos de menores.</p>

        <h2>11. Contacto</h2>
        <p>Para ejercer sus derechos o solicitar información: <a href="mailto:privacidad@erpipos.com">privacidad@erpipos.com</a></p>
    </div>
    <footer class="text-center py-4 text-muted border-top">
        © {{ date('Y') }} Erpipos ERP. Todos los derechos reservados.
        <a href="/terminos" class="back-link">Términos</a>
    </footer>
</body>
</html>
