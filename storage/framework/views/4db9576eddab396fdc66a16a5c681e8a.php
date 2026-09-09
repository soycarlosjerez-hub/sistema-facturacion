<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Instancia</title>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f1f5f9;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 20px;">
<tr><td align="center">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
<tr>
<td style="background:linear-gradient(135deg,#3b82f6,#8b5cf6);padding:32px 24px;text-align:center;">
<h1 style="color:#fff;margin:0 0 8px;font-size:22px;font-weight:700;">Nueva Solicitud de Instancia</h1>
<p style="color:rgba(255,255,255,0.85);margin:0;font-size:14px;">Se ha registrado un nuevo negocio que espera tu validación</p>
</td>
</tr>
<tr>
<td style="padding:32px 24px;">
<p style="margin:0 0 20px;color:#334155;font-size:15px;line-height:1.5;">Un nuevo usuario ha registrado su negocio en la plataforma. A continuación los detalles:</p>

<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:24px;">
<tr style="background:#f8fafc;">
<td style="padding:12px 16px;font-weight:700;color:#475569;font-size:13px;width:40%;border-right:1px solid #e2e8f0;">Solicitante</td>
<td style="padding:12px 16px;color:#1e293b;font-size:14px;"><?php echo e($user->name); ?></td>
</tr>
<tr>
<td style="padding:12px 16px;font-weight:700;color:#475569;font-size:13px;border-right:1px solid #e2e8f0;border-top:1px solid #e2e8f0;">Email</td>
<td style="padding:12px 16px;color:#1e293b;font-size:14px;border-top:1px solid #e2e8f0;"><?php echo e($user->email); ?></td>
</tr>
<tr style="background:#f8fafc;">
<td style="padding:12px 16px;font-weight:700;color:#475569;font-size:13px;border-right:1px solid #e2e8f0;border-top:1px solid #e2e8f0;">Negocio</td>
<td style="padding:12px 16px;color:#1e293b;font-size:14px;font-weight:600;border-top:1px solid #e2e8f0;"><?php echo e($instance->nombre); ?></td>
</tr>
<tr>
<td style="padding:12px 16px;font-weight:700;color:#475569;font-size:13px;border-right:1px solid #e2e8f0;border-top:1px solid #e2e8f0;">Tipo de Negocio</td>
<td style="padding:12px 16px;border-top:1px solid #e2e8f0;">
<span style="background:<?php echo e($instance->businessType->color ?? '#3b82f6'); ?>;color:#fff;padding:3px 12px;border-radius:999px;font-size:13px;font-weight:600;">
<?php echo e($instance->businessType->nombre ?? 'N/A'); ?>

</span>
</td>
</tr>
<tr style="background:#f8fafc;">
<td style="padding:12px 16px;font-weight:700;color:#475569;font-size:13px;border-right:1px solid #e2e8f0;border-top:1px solid #e2e8f0;">RNC</td>
<td style="padding:12px 16px;color:#1e293b;font-size:14px;border-top:1px solid #e2e8f0;"><?php echo e($instance->rnc); ?></td>
</tr>
<tr>
<td style="padding:12px 16px;font-weight:700;color:#475569;font-size:13px;border-right:1px solid #e2e8f0;border-top:1px solid #e2e8f0;">Teléfono</td>
<td style="padding:12px 16px;color:#1e293b;font-size:14px;border-top:1px solid #e2e8f0;"><?php echo e($instance->telefono); ?></td>
</tr>
<tr style="background:#f8fafc;">
<td style="padding:12px 16px;font-weight:700;color:#475569;font-size:13px;border-right:1px solid #e2e8f0;border-top:1px solid #e2e8f0;">Dirección</td>
<td style="padding:12px 16px;color:#1e293b;font-size:14px;border-top:1px solid #e2e8f0;"><?php echo e($instance->direccion); ?></td>
</tr>
<tr>
<td style="padding:12px 16px;font-weight:700;color:#475569;font-size:13px;border-right:1px solid #e2e8f0;border-top:1px solid #e2e8f0;">Registrado</td>
<td style="padding:12px 16px;color:#1e293b;font-size:14px;border-top:1px solid #e2e8f0;"><?php echo e($instance->created_at->format('d/m/Y H:i')); ?></td>
</tr>
</table>

<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:24px;">
<tr>
<td align="center" style="padding:8px 0;">
<a href="<?php echo e(route('owner.instances.index')); ?>" style="display:inline-block;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;text-decoration:none;padding:14px 32px;border-radius:10px;font-weight:700;font-size:15px;">
Ver Todas las Solicitudes
</a>
</td>
</tr>
</table>

<p style="margin:0;color:#64748b;font-size:13px;line-height:1.5;">Esta solicitud está esperando tu validación para activar la instancia. Una vez aprobada, el usuario podrá acceder a su panel.</p>
</td>
</tr>
<tr>
<td style="background:#f8fafc;padding:24px;text-align:center;border-top:1px solid #e2e8f0;">
<p style="margin:0;color:#94a3b8;font-size:12px;">&copy; <?php echo e(date('Y')); ?> Erpipos ERP — Republica Dominicana</p>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/emails/nueva-solicitud-instancia.blade.php ENDPATH**/ ?>