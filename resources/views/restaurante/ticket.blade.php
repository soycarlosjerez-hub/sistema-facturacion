<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Ticket Mesa {{ $mesa->numero }}</title>
<style>
body { font-family: 'Courier New', monospace; font-size: 12px; width: {{ $paper }}mm; margin: 0 auto; padding: 8px; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 2px 0; text-align: left; }
.text-right { text-align: right; }
.text-center { text-align: center; }
.sep { border-top: 1px dashed #000; }
.fw-bold { font-weight: bold; }
.total-row td { border-top: 2px solid #000; font-weight: bold; font-size: 14px; }
.mesa-info { background: #f0f0f0; padding: 4px 8px; border-radius: 4px; }
@page { margin: 0; }
@media print { body { margin: 0; padding: 4px; } }
</style>
</head>
<body>
    <div class="text-center fw-bold">{{ $empresa->nombre ?? config('app.name') }}</div>
    <div class="text-center">RNC: {{ $empresa->rnc ?? 'N/A' }}</div>
    <div class="sep"></div>
    <div class="text-center fw-bold">*** TICKET MESA ***</div>
    <div class="mesa-info text-center fw-bold">MESA #{{ $mesa->numero }} - {{ $mesa->nombre ?? '' }}</div>
    <div class="sep"></div>
    <table>
        <tr><td>Factura:</td><td class="text-right">#{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</td></tr>
        <tr><td>Fecha:</td><td class="text-right">{{ now()->format('d/m/Y H:i') }}</td></tr>
        <tr><td>Cliente:</td><td class="text-right">{{ $venta->cliente->nombre ?? 'Consumidor Final' }}</td></tr>
    </table>
    <div class="sep"></div>
    <table>
        <tr><th>Plato</th><th class="text-right">Cant</th><th class="text-right">Precio</th><th class="text-right">Subtotal</th></tr>
        @foreach($venta->detalles as $d)
        <tr>
            <td>{{ $d->producto->nombre ?? 'N/A' }}
                @if($d->notas) <br><small style="font-size:9px;">📝 {{ $d->notas }}</small> @endif
            </td>
            <td class="text-right">{{ $d->cantidad }}</td>
            <td class="text-right">{{ number_format($d->precio_unitario, 2) }}</td>
            <td class="text-right">{{ number_format($d->subtotal, 2) }}</td>
        </tr>
        @endforeach
    </table>
    <div class="sep"></div>
    <table>
        <tr><td>Subtotal:</td><td class="text-right">{{ number_format($venta->subtotal, 2) }}</td></tr>
        @if($venta->descuento > 0)
        <tr><td>Descuento:</td><td class="text-right">-{{ number_format($venta->descuento, 2) }}</td></tr>
        @endif
        <tr><td>ITBIS:</td><td class="text-right">{{ number_format($venta->impuestos, 2) }}</td></tr>
        <tr class="total-row"><td>TOTAL:</td><td class="text-right">RD$ {{ number_format($venta->total, 2) }}</td></tr>
    </table>
    @php $pago = $venta->pagos->first(); @endphp
    @if($pago)
    <div class="sep"></div>
    <div class="text-center" style="font-size:11px;">
        {{ ucfirst($pago->metodo_pago) }}
        @if($venta->pagos->count() > 1)
            <br>Pagos combinados ({{ $venta->pagos->count() }})
        @endif
    </div>
    @endif
    <div class="sep"></div>
    <div class="text-center">Gracias por su visita</div>
</body>
</html>
