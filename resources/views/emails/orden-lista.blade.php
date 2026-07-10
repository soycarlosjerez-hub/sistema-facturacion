<h1>Orden Lista para Recoger #{{ $orden->id }}</h1>
<p>Hola <strong>{{ $orden->cliente?->nombre }}</strong>, tu orden ya está lista.</p>
<p>Puedes pasar a recogerla por nuestro local.</p>
<p><strong>Total pagado: RD$ {{ number_format($orden->subtotal + $orden->impuestos, 2) }}</strong></p>
