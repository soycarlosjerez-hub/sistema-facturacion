<h1>Tu Pedido está en Camino #{{ $orden->id }}</h1>
<p>Hola <strong>{{ $orden->cliente?->nombre }}</strong>, tu pedido ya está en camino.</p>
<p>Dirección de entrega: {{ $orden->direccion_entrega }}</p>
<p><strong>Total pagado: RD$ {{ number_format($orden->subtotal + $orden->impuestos, 2) }}</strong></p>
