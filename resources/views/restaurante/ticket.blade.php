<?php
    $_printConfig = [
        'fontSize' => 12,
        'lineHeight' => 1.4,
        'ticketPadding' => 8,
        'copies' => 1,
        'impresion' => 'normal',
        'densidad' => 'normal',
    ];

    if (isset($impresora) && $impresora && $impresora->configuracion) {
        $_cfg = (array) $impresora->configuracion;
        if (!empty($_cfg['font_size'])) {
            $_printConfig['fontSize'] = (int) $_cfg['font_size'];
        }
        if (!empty($_cfg['copias']) && $_cfg['copias'] > 1) {
            $_printConfig['copies'] = (int) $_cfg['copias'];
        }
        if (!empty($_cfg['impresion'])) {
            $_printConfig['impresion'] = $_cfg['impresion'];
        }
        if (!empty($_cfg['densidad'])) {
            $_printConfig['densidad'] = $_cfg['densidad'];
        }
        if (!empty($_cfg['margenes']) && isset($_cfg['margenes']['top'])) {
            $_printConfig['ticketPadding'] = (int) $_cfg['margenes']['top'];
        }
    }

    $_lineHeight = $_printConfig['impresion'] === 'compacto' ? 1.1 : ($_printConfig['impresion'] === 'espaciado' ? 1.6 : 1.4);
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Ticket Mesa {{ $mesa->numero }}</title>
<style>
body { font-family: 'Courier New', monospace; font-size: {{ $_printConfig['fontSize'] }}px; width: {{ $paper }}mm; margin: 0 auto; padding: {{ $_printConfig['ticketPadding'] }}px; line-height: {{ $_lineHeight }}; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 2px 0; text-align: left; }
.text-right { text-align: right; }
.text-center { text-align: center; }
.sep { border-top: 1px dashed #000; }
.fw-bold { font-weight: bold; }
.total-row td { border-top: 2px solid #000; font-weight: bold; font-size: {{ $_printConfig['fontSize'] + 2 }}px; }
.mesa-info { background: transparent; padding: 4px 8px; border-radius: 4px; }
@media print {
    body { margin: 0; padding: 4px; color: #000 !important; background: #fff !important; }
    * { color: #000 !important; background-color: transparent !important; }
    @page { margin: 0; size: {{ $paper }}mm auto; }
}
</style>
</head>
<body style="font-family: 'Courier New', monospace; font-size: {{ $_printConfig['fontSize'] }}px; width: {{ $paper }}mm; margin: 0 auto; padding: {{ $_printConfig['ticketPadding'] }}px; color: #000; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
    @if($pdfLogoUrl)
    <div class="text-center mb-2">
        <img src="{{ $pdfLogoUrl }}" style="max-width: 60px; max-height: 40px; object-fit: contain;" alt="Logo">
    </div>
    @endif
    <div class="text-center fw-bold">{{ \App\Models\SystemSetting::nombreEmpresaActual() }}</div>
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
    <script>
        window.addEventListener('load', function() {
            var copies = {{ $_printConfig['copies'] ?? 1 }};
            var densidad = '{{ $_printConfig['densidad'] ?? 'normal' }}';

            if (densidad === 'alta') {
                document.querySelectorAll('body').forEach(function(el) {
                    el.style.filter = 'brightness(1.15) contrast(1.1)';
                });
            } else if (densidad === 'baja') {
                document.querySelectorAll('body').forEach(function(el) {
                    el.style.filter = 'brightness(0.85) contrast(0.9)';
                });
            }

            if (copies > 1) {
                for (var i = 1; i < copies; i++) {
                    setTimeout(function() { window.print(); }, i * 2000);
                }
            }
            setTimeout(function() { window.print(); }, 300);
        });
    </script>
</body>
</html>
