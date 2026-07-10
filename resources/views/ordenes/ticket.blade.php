<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orden #{{ $orden->id }}</title>
    <style>
        body { font-family: monospace; font-size: 12px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 4px 2px; }
        hr { border-top: 1px dashed #000; }
    </style>
</head>
<body>
    <div class="text-center">
        <h2>ORDEN #{{ $orden->id }}</h2>
        <p>{{ now()->format('d/m/Y h:i A') }}</p>
        <p>Tipo: {{ ucfirst($orden->tipo_orden) }}</p>
        @if($orden->cliente)<p>Cliente: {{ $orden->cliente->nombre }}</p>@endif
        <hr>
    </div>

    <table>
        <thead>
            <tr>
                <th>Cant</th>
                <th>Producto</th>
                <th class="text-right">Precio</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orden->detalles as $d)
            <tr>
                <td>{{ $d->cantidad }}</td>
                <td>{{ $d->producto?->nombre ?? '—' }}</td>
                <td class="text-right">RD$ {{ number_format($d->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <hr>
    <table>
        <tr><td>Subtotal</td><td class="text-right">RD$ {{ number_format($orden->subtotal, 2) }}</td></tr>
        <tr><td>Impuestos</td><td class="text-right">RD$ {{ number_format($orden->impuestos, 2) }}</td></tr>
        @if($orden->descuento > 0)
        <tr><td>Descuento</td><td class="text-right">-RD$ {{ number_format($orden->descuento, 2) }}</td></tr>
        @endif
        <tr style="font-weight:bold; font-size:14px;">
            <td>TOTAL</td>
            <td class="text-right">RD$ {{ number_format($orden->subtotal + $orden->impuestos - $orden->descuento, 2) }}</td>
        </tr>
    </table>

    @if($orden->pagos && $orden->pagos->count() > 0)
    <hr>
    <table>
        @foreach($orden->pagos as $p)
        <tr><td>{{ ucfirst($p->metodo_pago) }}</td><td class="text-right">RD$ {{ number_format($p->monto, 2) }}</td></tr>
        @endforeach
    </table>
    @endif

    <div class="text-center" style="margin-top:10px;">
        <p>¡Gracias por su preferencia!</p>
    </div>
</body>
</html>
