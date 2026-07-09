RESERVACIÓN RECIBIDA
==============================================

Estimado/a {{ $reservacion->cliente_nombre }},

Hemos recibido tu reservación y está pendiente de confirmación. Nuestro equipo la revisará y te notificaremos pronto.

ESTADO
----------------------------------------------
Pendiente de confirmación

DETALLES DE LA RESERVACIÓN
----------------------------------------------
Mesa:           {{ $reservacion->mesa->nombre ?? 'Mesa ' . $reservacion->mesa->numero }}
Fecha y hora:   {{ $reservacion->fecha_hora->format('d/m/Y H:i') }}
Personas:       {{ $reservacion->personas }}
@if($reservacion->notas)
Notas:          {{ $reservacion->notas }}
@endif

Si necesitas realizar algún cambio, por favor contáctanos directamente.

Atentamente,
{{ config('app.name') }}

---
Este correo fue enviado automáticamente por el sistema de facturación.
© {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
