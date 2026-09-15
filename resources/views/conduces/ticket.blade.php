<?php
    $_printConfig = [
        'fontSize' => 11,
        'lineHeight' => 1.4,
        'ticketPadding' => 4,
        'copies' => 1,
        'impresion' => 'normal',
        'densidad' => 'normal',
    ];

    if ($paper === 58) {
        $_printConfig['fontSize'] = 9;
    }

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
<style>
    body { font-family: 'Courier New', monospace; font-size: {{ $_printConfig['fontSize'] }}px; color: #000; -webkit-print-color-adjust: exact; print-color-adjust: exact; line-height: {{ $_lineHeight }}; }
    .ticket { max-width: {{ $paper === 58 ? '58mm' : '80mm' }}; margin: 0 auto; padding: {{ $_printConfig['ticketPadding'] }}mm; }
    .ticket h1, .ticket h2, .ticket h3 { margin: 0; text-align: center; }
    .ticket .center { text-align: center; }
    .ticket .right { text-align: right; }
    .ticket .hr { border-top: 1px dashed #000; margin: 4px 0; }
    .ticket table { width: 100%; border-collapse: collapse; font-size: {{ $_printConfig['fontSize'] }}px; }
    .ticket table th, .ticket table td { padding: 1px 0; }
    .ticket table .qty { width: 40px; text-align: right; }
    .ticket table .name { text-align: left; }
    @media print {
        body { margin: 0; padding: 0; color: #000 !important; background: #fff !important; }
        .no-print { display: none !important; }
        .ticket { padding: 2mm; }
        * { color: #000 !important; background-color: transparent !important; }
        @page { margin: 0; size: {{ $paper === 58 ? '58mm' : '80mm' }} auto; }
    }
</style>

<div class="ticket">
    <h2><i class="bi bi-truck"></i> CONDUCE</h2>
    <div class="center small">{{ str_pad('', 32, '-') }}</div>
    @if($pdfLogoUrl)
    <div class="center" style="margin-bottom: 5px;">
        <img src="{{ $pdfLogoUrl }}" style="max-width: 60px; max-height: 40px; object-fit: contain;" alt="Logo">
    </div>
    @endif
    <p class="center"><strong>{{ \App\Models\SystemSetting::nombreEmpresaActual() }}</strong><br>
    <span class="small">RNC: {{ $empresa->rnc ?? '000-0000000-0' }}</span><br>
    <span class="small">{{ $empresa->direccion ?? 'Santo Domingo, RD' }}</span><br>
    <span class="small">Tel: {{ $empresa->telefono ?? '(809) 000-0000' }}</span></p>

    <div class="hr"></div>
    <p class="small">
        <strong>No.:</strong> {{ $conduce->numero }}<br>
        <strong>Fecha:</strong> {{ $conduce->fecha->format('d/m/Y') }}<br>
        @if($conduce->fecha_entrega)
        <strong>Entrega:</strong> {{ $conduce->fecha_entrega->format('d/m/Y') }}<br>
        @endif
        <strong>Estado:</strong> {{ $conduce->estado_label }}
    </p>

    <div class="hr"></div>
    <p class="small">
        <strong>Cliente:</strong> {{ $conduce->cliente?->nombre ?? 'N/A' }}<br>
        @if($conduce->cliente?->rnc_cedula)
        <strong>RNC:</strong> {{ $conduce->cliente->rnc_cedula }}<br>
        @endif
        @if($conduce->cliente?->telefono)
        <strong>Tel:</strong> {{ $conduce->cliente->telefono }}
        @endif
    </p>

    <div class="hr"></div>
    <table>
        <thead>
            <tr>
                <th class="qty">Cant</th>
                <th class="name">Producto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($conduce->items as $item)
            <tr>
                <td class="qty">{{ number_format($item->cantidad, 0) }}</td>
                <td class="name">
                    {{ $item->nombre }}
                    @if($conduce->estado === 'entregado' && $item->cantidad_recibida !== null)
                    <br><small>(Rec: {{ number_format($item->cantidad_recibida, 0) }})</small>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="hr"></div>
    <p class="small">
        <strong>Total items:</strong> {{ $conduce->total_items }}<br>
        @if($conduce->peso_total)
        <strong>Peso:</strong> {{ number_format($conduce->peso_total, 2) }} kg<br>
        @endif
    </p>

    @if($conduce->transportista || $conduce->chofer)
    <div class="hr"></div>
    <p class="small">
        <strong>TRANSPORTE</strong><br>
        @if($conduce->transportista)Empresa: {{ $conduce->transportista }}<br>@endif
        @if($conduce->chofer)Chofer: {{ $conduce->chofer }}<br>@endif
        @if($conduce->vehiculo)Vehículo: {{ $conduce->vehiculo }}<br>@endif
        @if($conduce->placa)Placa: {{ $conduce->placa }}@endif
    </p>
    @endif

    @if($conduce->estado === 'entregado')
    <div class="hr"></div>
    <p class="small">
        <strong>RECIBIDO POR:</strong> {{ $conduce->recibido_por }}<br>
        @if($conduce->recibido_cedula)Cédula: {{ $conduce->recibido_cedula }}<br>@endif
        {{ $conduce->fecha_recibido?->format('d/m/Y H:i') }}
    </p>
    @endif

    <div class="hr"></div>
    <p class="center small">
        _________________________<br>
        Recibido conforme
    </p>
    <p class="center small" style="margin-top: 8mm;">{{ now()->format('d/m/Y H:i:s') }}</p>
</div>

<div class="no-print text-center mt-3">
    <button onclick="window.print()" class="btn btn-primary">
        <i class="bi bi-printer me-1"></i>Imprimir
    </button>
    <button onclick="window.close()" class="btn btn-secondary">Cerrar</button>
</div>
