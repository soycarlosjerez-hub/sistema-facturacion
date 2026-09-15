<h1>Orden Confirmada #{{ $orden->id }}</h1>
<p>Gracias por tu orden <strong>{{ $orden->cliente?->nombre }}</strong>.</p>

@if($orden->tipo_orden === 'delivery')
<p>Tu pedido será enviado a: {{ $orden->direccion_entrega }}</p>
@elseif($orden->tipo_orden === 'pickup')
<p>Puedes recoger tu pedido a partir de las {{ $orden->hora_retiro?->format('h:i A') }}.</p>
@endif

<h3>Productos:</h3>
<ul>
@foreach($orden->detalles as $detalle)
<li>{{ $detalle->cantidad }}x {{ $detalle->producto?->nombre }} - RD$ {{ number_format($detalle->subtotal, 2) }}</li>
@endforeach
</ul>

<p><strong>Total: RD$ {{ number_format($orden->subtotal + $orden->impuestos, 2) }}</strong></p>
