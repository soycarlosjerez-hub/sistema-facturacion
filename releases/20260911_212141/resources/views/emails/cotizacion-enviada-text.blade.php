COTIZACIÓN {{ $cotizacion->numero }}
==============================================

Estimado/a {{ $cotizacion->cliente?->nombre ?? 'Cliente' }},

Adjunto encontrará la cotización {{ $cotizacion->numero }} con los detalles
de los productos y servicios solicitados.

@if($mensajeAdicional)
Mensaje adicional:
{{ $mensajeAdicional }}

@endif
INFORMACIÓN
----------------------------------------------
Número:           {{ $cotizacion->numero }}
Fecha emisión:    {{ $cotizacion->fecha->format('d/m/Y') }}
Válida hasta:     {{ $cotizacion->fecha_validez->format('d/m/Y') }} ({{ $cotizacion->dias_validez }} días)
Atendido por:     {{ $cotizacion->user?->name ?? 'N/A' }}

DETALLE DE PRODUCTOS
----------------------------------------------
@foreach($cotizacion->items as $item)
{{ $item->nombre }} ({{ $item->codigo ?? 'N/A' }})
   Cant: {{ $item->cantidad }} x RD${{ number_format($item->precio_unitario, 2) }} = RD${{ number_format($item->subtotal, 2) }}
@endforeach

TOTALES
----------------------------------------------
Subtotal:    RD${{ number_format($cotizacion->subtotal, 2) }}
@if($cotizacion->descuento > 0)
Descuento:  -RD${{ number_format($cotizacion->descuento, 2) }}
@endif
ITBIS (18%): RD${{ number_format($cotizacion->itbis, 2) }}
----------------------------------------------
TOTAL:       RD${{ number_format($cotizacion->total, 2) }}

@if($cotizacion->condiciones)
TÉRMINOS Y CONDICIONES
----------------------------------------------
{{ $cotizacion->condiciones }}

@endif
Ver cotización en línea:
{{ $urlVer }}

La cotización completa en PDF se adjunta a este correo.

Si tiene alguna pregunta o necesita aclaraciones, no dude en contactarnos.

Atentamente,
{{ $cotizacion->user?->name ?? 'Equipo de Ventas' }}

---
Este correo fue enviado automáticamente por el sistema de facturación.
© {{ date('Y') }} Sistema de Facturación. Todos los derechos reservados.
