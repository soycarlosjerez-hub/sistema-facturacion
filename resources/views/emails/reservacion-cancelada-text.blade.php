RESERVACIÓN CANCELADA
==============================================

Estimado/a {{ $reservacion->cliente_nombre }},

Lamentamos informarte que tu reservación ha sido cancelada.

ESTADO
----------------------------------------------
❌ Cancelada

DETALLES DE LA RESERVACIÓN
----------------------------------------------
Mesa:           {{ $reservacion->mesa->nombre ?? 'Mesa ' . $reservacion->mesa->numero }}
Fecha y hora:   {{ $reservacion->fecha_hora->format('d/m/Y H:i') }}
Personas:       {{ $reservacion->personas }}
@if($reservacion->notas)
Notas:          {{ $reservacion->notas }}
@endif

¿Deseas reagendar?
No te preocupes, puedes realizar una nueva reservación contactándonos directamente.

Disculpa las molestias.

Atentamente,
{{ config('app.name') }}

---
Este correo fue enviado automáticamente por el sistema de facturación.
© {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
