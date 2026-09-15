RESERVACIÓN CONFIRMADA
==============================================

Estimado/a {{ $reservacion->cliente_nombre }},

Nos complace informarte que tu reservación ha sido confirmada exitosamente. Te esperamos con mucho gusto.

ESTADO
----------------------------------------------
✅ Confirmada

DETALLES DE LA RESERVACIÓN
----------------------------------------------
Mesa:           {{ $reservacion->mesa->nombre ?? 'Mesa ' . $reservacion->mesa->numero }}
Fecha y hora:   {{ $reservacion->fecha_hora->format('d/m/Y H:i') }}
Personas:       {{ $reservacion->personas }}
@if($reservacion->notas)
Notas:          {{ $reservacion->notas }}
@endif

Recuerda llegar puntualmente. Si necesitas cancelar o modificar tu reservación, por favor avísanos con anticipación.

¡Te esperamos!

Atentamente,
{{ config('app.name') }}

---
Este correo fue enviado automáticamente por el sistema de facturación.
© {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
