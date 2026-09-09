<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud Aprobada</title>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f1f5f9;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 20px;">
<tr><td align="center">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
<tr>
<td style="background:linear-gradient(135deg,#22c55e,#16a34a);padding:32px 24px;text-align:center;">
<div style="font-size:48px;margin-bottom:8px;">&#10003;</div>
<h1 style="color:#fff;margin:0 0 8px;font-size:22px;font-weight:700;">¡Solicitud Aprobada!</h1>
<p style="color:rgba(255,255,255,0.85);margin:0;font-size:14px;">Tu instancia ya está activa</p>
</td>
</tr>
<tr>
<td style="padding:32px 24px;">
<p style="margin:0 0 20px;color:#334155;font-size:15px;line-height:1.5;">Tu solicitud para <strong>{{ $instance->nombre }}</strong> ha sido aprobada. Tu empresa ya está lista para operar.</p>

<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:24px;">
<tr style="background:#f8fafc;">
<td style="padding:12px 16px;font-weight:700;color:#475569;font-size:13px;width:40%;border-right:1px solid #e2e8f0;">Negocio</td>
<td style="padding:12px 16px;color:#1e293b;font-size:14px;font-weight:600;border-top:1px solid #e2e8f0;">{{ $instance->nombre }}</td>
</tr>
<tr>
<td style="padding:12px 16px;font-weight:700;color:#475569;font-size:13px;border-right:1px solid #e2e8f0;border-top:1px solid #e2e8f0;">Tipo de Negocio</td>
<td style="padding:12px 16px;border-top:1px solid #e2e8f0;">
<span style="background:{{ $instance->businessType->color ?? '#22c55e' }};color:#fff;padding:3px 12px;border-radius:999px;font-size:13px;font-weight:600;">
{{ $instance->businessType->nombre ?? 'N/A' }}
</span>
</td>
</tr>
</table>

@if($instance->trial_ends_at)
<p style="margin:0 0 24px;color:#334155;font-size:15px;line-height:1.5;">Tu periodo de prueba de <strong>{{ $instance->trialDays() }} días</strong> está activo. Fecha de vencimiento: {{ $instance->trial_ends_at->format('d/m/Y') }}.</p>
@endif

<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:24px;">
<tr>
<td align="center" style="padding:8px 0;">
<a href="{{ route('login') }}" style="display:inline-block;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;text-decoration:none;padding:14px 32px;border-radius:10px;font-weight:700;font-size:15px;">
Acceder a mi Panel
</a>
</td>
</tr>
</table>

<p style="margin:0;color:#64748b;font-size:13px;line-height:1.5;">Si tienes dudas, no dudes en contactar a nuestro equipo de soporte.</p>
</td>
</tr>
<tr>
<td style="background:#f8fafc;padding:24px;text-align:center;border-top:1px solid #e2e8f0;">
<p style="margin:0;color:#94a3b8;font-size:12px;">&copy; {{ date('Y') }} Erpipos ERP — Republica Dominicana</p>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
