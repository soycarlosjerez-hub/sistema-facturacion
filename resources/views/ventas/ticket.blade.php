<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Venta {{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</title>
    <?php
        $printConfig = [
            'paperWidth' => 80,
            'paperSize' => '80mm',
            'fontSize' => 11,
            'bodyFontSize' => 12,
            'headerFontSize' => 13,
            'lineFontSize' => 10,
            'totalsFontSize' => 10,
            'grandFontSize' => 14,
            'footerFontSize' => 9,
            'ticketPadding' => 2,
            'autoPrint' => false,
            'impresion' => 'normal',
            'densidad' => 'normal',
            'copies' => 1,
        ];

        if ($impresora && $impresora->papel_tamano) {
            switch ($impresora->papel_tamano) {
                case '58mm':
                    $printConfig['paperWidth'] = 58;
                    $printConfig['paperSize'] = '58mm';
                    $printConfig['fontSize'] = 9;
                    $printConfig['bodyFontSize'] = 10;
                    $printConfig['headerFontSize'] = 11;
                    $printConfig['lineFontSize'] = 9;
                    $printConfig['totalsFontSize'] = 9;
                    $printConfig['grandFontSize'] = 12;
                    $printConfig['footerFontSize'] = 8;
                    break;
                case '80mm':
                    $printConfig['paperWidth'] = 80;
                    $printConfig['paperSize'] = '80mm';
                    $printConfig['fontSize'] = 11;
                    $printConfig['bodyFontSize'] = 12;
                    $printConfig['headerFontSize'] = 13;
                    $printConfig['lineFontSize'] = 10;
                    $printConfig['totalsFontSize'] = 10;
                    $printConfig['grandFontSize'] = 14;
                    $printConfig['footerFontSize'] = 9;
                    break;
                case 'A4':
                    $printConfig['paperWidth'] = 210;
                    $printConfig['paperSize'] = 'A4';
                    $printConfig['fontSize'] = 12;
                    $printConfig['bodyFontSize'] = 12;
                    $printConfig['headerFontSize'] = 16;
                    $printConfig['lineFontSize'] = 11;
                    $printConfig['totalsFontSize'] = 11;
                    $printConfig['grandFontSize'] = 15;
                    $printConfig['footerFontSize'] = 9;
                    break;
            }
        }

        if ($impresora && $impresora->auto_imprimir_ventas) {
            $printConfig['autoPrint'] = true;
        }

        if ($impresora && $impresora->configuracion) {
            $_cfg = (array) $impresora->configuracion;
            if (!empty($_cfg['font_size'])) {
                $printConfig['fontSize'] = (int) $_cfg['font_size'];
                $printConfig['bodyFontSize'] = (int) $_cfg['font_size'];
                $printConfig['headerFontSize'] = (int) $_cfg['font_size'] + 2;
                $printConfig['lineFontSize'] = (int) $_cfg['font_size'] - 1;
                $printConfig['totalsFontSize'] = (int) $_cfg['font_size'];
                $printConfig['grandFontSize'] = (int) $_cfg['font_size'] + 2;
                $printConfig['footerFontSize'] = (int) $_cfg['font_size'] - 2;
            }
            if (!empty($_cfg['copias']) && $_cfg['copias'] > 1) {
                $printConfig['copies'] = (int) $_cfg['copias'];
            }
            if (!empty($_cfg['impresion'])) {
                $printConfig['impresion'] = $_cfg['impresion'];
            }
            if (!empty($_cfg['margenes'])) {
                if (isset($_cfg['margenes']['top'])) {
                    $printConfig['ticketPadding'] = (int) $_cfg['margenes']['top'];
                }
                if (isset($_cfg['margenes']['right'])) {
                    $printConfig['ticketPaddingRight'] = (int) $_cfg['margenes']['right'];
                }
                if (isset($_cfg['margenes']['bottom'])) {
                    $printConfig['ticketPaddingBottom'] = (int) $_cfg['margenes']['bottom'];
                }
                if (isset($_cfg['margenes']['left'])) {
                    $printConfig['ticketPaddingLeft'] = (int) $_cfg['margenes']['left'];
                }
            }
        }

        $autoPrintName = $impresora ? $impresora->nombre : '';
    ?>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: {{ $printConfig['bodyFontSize'] }}px;
            line-height: {{ $printConfig['impresion'] === 'compacto' ? 1.1 : ($printConfig['impresion'] === 'espaciado' ? 1.6 : 1.4) }};
            color: #000;
            background: #fff;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .ticket {
            width: {{ $printConfig['paperWidth'] }}mm;
            margin: 0 auto;
            padding-top: {{ $printConfig['ticketPadding'] ?? 3 }}mm;
            padding-right: {{ $printConfig['ticketPaddingRight'] ?? 2 }}mm;
            padding-bottom: {{ $printConfig['ticketPaddingBottom'] ?? 3 }}mm;
            padding-left: {{ $printConfig['ticketPaddingLeft'] ?? 2 }}mm;
            font-size: {{ $printConfig['fontSize'] }}px;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .separator { border-top: 1px dashed #000; margin: 4px 0; }
        .separator-double { border-top: 2px solid #000; margin: 4px 0; }
        
        .header h1 { font-size: {{ $printConfig['headerFontSize'] }}px; margin-bottom: 2px; }
        .header p { font-size: {{ $printConfig['lineFontSize'] }}px; }
        
        .section { margin: 6px 0; }
        .row { display: flex; justify-content: space-between; margin: 2px 0; font-size: {{ $printConfig['lineFontSize'] }}px; }
        .row .label { font-weight: bold; }
        
        .items-table { width: 100%; border-spacing: 0; margin: 6px 0; }
        .items-table tr { border-bottom: 1px solid #000; }
        .items-table td { padding: 3px 0; vertical-align: top; }
        .items-table .name { width: 55%; }
        .items-table .qty { width: 20%; text-align: center; }
        .items-table .total { width: 25%; text-align: right; }
        
        .totals { margin: 8px 0; }
        .totals .row { font-size: {{ $printConfig['totalsFontSize'] }}px; margin: 2px 0; }
        .totals .grand { font-size: {{ $printConfig['grandFontSize'] }}px; font-weight: bold; border-top: 2px solid #000; padding-top: 4px; margin-top: 4px; }
        
        .footer { text-align: center; margin-top: 12px; padding-top: 8px; border-top: 1px dashed #000; font-size: {{ $printConfig['footerFontSize'] }}px; }
        .footer p { margin: 2px 0; }
        
        .ncf, .encf { text-align: center; margin: 4px 0; font-family: monospace; }
        .ncf span, .encf span { display: block; font-weight: bold; letter-spacing: 1px; }
        
        @media print {
            body { margin: 0; padding: 0; background: #fff !important; color: #000 !important; }
            .ticket { padding: 0; width: 100%; }
            * { color: #000 !important; background-color: transparent !important; }
            .separator, .separator-double { border-color: #000 !important; }
            .items-table tr { border-bottom-color: #000 !important; }
            @page { margin: 0; size: {{ $printConfig['paperSize'] }} auto; }
        }
        
        @media (max-width: 600px) {
            body { font-size: {{ $printConfig['lineFontSize'] }}px; }
            .ticket { padding: 1mm; }
        }
    </style>
</head>
<body>
    <?php $garantiaTerminos = []; ?>
    <?php foreach($venta->detalles->where('tipo_linea', '!=', 'delivery') as $d): ?>
        <?php if(!empty($d->producto->garantia_terminos)): ?>
            <?php $_key = $d->producto->id . '_' . md5($d->producto->garantia_terminos); ?>
            <?php if(!isset($garantiaTerminos[$_key])): ?>
                <?php $garantiaTerminos[$_key] = ['producto' => $d->producto->nombre, 'terminos' => $d->producto->garantia_terminos]; ?>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
    <div class="ticket">
        <div class="header center">
            @if($pdfLogoUrl)
            <div style="margin-bottom: 4px;">
                <img src="{{ $pdfLogoUrl }}" style="max-width: 60px; max-height: 45px; object-fit: contain;" alt="Logo">
            </div>
            @endif
            <h1 class="bold">{{ \App\Models\SystemSetting::nombreEmpresaActual() }}</h1>
            @php $rnc = \App\Models\SystemSetting::get('empresa_rnc'); @endphp
            @if(!empty($rnc))
            <p>RNC: {{ $rnc }}</p>
            @endif
            <p>{{ \App\Models\SystemSetting::get('empresa_direccion', '') }}</p>
            @if(!empty(\App\Models\SystemSetting::get('empresa_telefono', '')))
            <p>Tel: {{ \App\Models\SystemSetting::get('empresa_telefono') }}</p>
            @endif
            <div class="separator"></div>
        </div>

        <div class="center bold section">
            <p style="font-size: 13px;">*** FACTURA ***</p>
            <p>Venta #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</p>
            <div class="separator"></div>
        </div>

        <div class="section">
            <div class="row">
                <span class="label">Fecha:</span>
                <span>{{ $venta->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="row">
                <span class="label">Atendido por:</span>
                <span>{{ $venta->usuario->name ?? 'Sistema' }}</span>
            </div>
            @if($venta->caja)
            <div class="row">
                <span class="label">Caja:</span>
                <span>{{ $venta->caja->nombre ?? 'N/A' }}</span>
            </div>
            @endif
            @if($venta->sucursal)
            <div class="row">
                <span class="label">Sucursal:</span>
                <span>{{ $venta->sucursal->nombre ?? 'N/A' }}</span>
            </div>
            @endif
            <div class="separator"></div>
        </div>

        <div class="section">
            <p class="bold" style="margin-bottom: 4px;">Cliente: {{ $venta->cliente->nombre ?? 'Consumidor Final' }}</p>
            @if($venta->cliente && $venta->cliente->rnc_cedula)
            <p>RNC/Ced: {{ $venta->cliente->rnc_cedula }}<br>
            @endif
            @if($venta->cliente && $venta->cliente->telefono)
            Tel: {{ $venta->cliente->telefono }}<br>
            @endif
            </p>
            <p>Estado: {{ strtoupper($venta->estado) }}</p>
            <div class="separator"></div>
        </div>

        <table class="items-table">
            <tbody>
            @foreach($venta->detalles->where('tipo_linea', '!=', 'delivery') as $d)
                <tr>
                    <td class="name">{{ $d->producto->nombre ?? $d->obra->titulo ?? 'Producto' }}</td>
                    <td class="qty">{{ $d->cantidad }} x {{ number_format($d->precio_unitario, 2) }}</td>
                    <td class="total">RD{{ number_format($d->subtotal, 2) }}</td>
                </tr>
                @if(!empty($d->producto->garantia_dias) && $d->producto->garantia_dias > 0)
                <tr>
                    <td colspan="3" style="font-size: 0.65rem; font-weight: bold; padding-left: 24px;">Garantia: {{ $d->producto->garantia_meses }} meses</td>
                </tr>
                @endif
                @if($d->notas)
                <tr>
                    <td colspan="3" style="font-size: 0.65rem; padding-left: 24px;">{{ $d->notas }}</td>
                </tr>
                @endif
            @endforeach
            </tbody>
        </table>

        <div class="separator-double"></div>
        <div class="totals">
            <div class="row">
                <span>Subtotal:</span>
                <span>RD{{ number_format($venta->subtotal, 2) }}</span>
            </div>
            @php $deliveryFee = (float) $venta->delivery_fee; @endphp
            @if($deliveryFee > 0)
            <div class="row">
                <span>Cargo Delivery:</span>
                <span>RD{{ number_format($deliveryFee, 2) }}</span>
            </div>
            @endif
            @if($venta->impuestos > 0)
            <div class="row">
                <span>Impuestos (ITBIS):</span>
                <span>RD{{ number_format($venta->impuestos, 2) }}</span>
            </div>
            @endif
            @if($venta->detalles->where('tipo_linea', '!=', 'delivery')->contains(fn($d) => $d->sin_itbis))
            <div class="row" style="font-weight:700;">
                <span>Incluye líneas sin ITBIS</span>
                <span></span>
            </div>
            @endif
            @if($venta->descuento > 0)
            <div class="row">
                <span>Descuento:</span>
                <span>-RD{{ number_format($venta->descuento, 2) }}</span>
            </div>
            @endif
            <div class="row grand">
                <span>TOTAL:</span>
                <span>RD{{ number_format($venta->total, 2) }}</span>
            </div>
        </div>
        <div class="separator-double"></div>

        @php
            $totalPagado = $venta->pagos->sum('monto');
            $diferencia = $totalPagado - $venta->total;
        @endphp
        @if($diferencia >= 0)
        <div class="section">
            <div class="bold" style="font-size: 13px; margin-bottom: 2px;">Pago:</div>
            <div class="bold" style="font-size: 14px; color: #000;">RD{{ number_format($totalPagado, 2) }}</div>
            @if($diferencia > 0)
            <div class="bold" style="font-size: 13px; margin-top: 4px;">Cambio:</div>
            <div class="bold" style="font-size: 14px; color: #000;">RD{{ number_format($diferencia, 2) }}</div>
            @endif
        </div>
        <div class="separator-double"></div>
        @endif

        <div class="section" style="margin: 6px 0;">
            <p class="bold" style="margin-bottom: 4px;">Métodos de Pago:</p>
            @foreach($venta->pagos as $pago)
                <div class="row">
                    <span>{{ $pago->metodo_pago ?? 'efectivo' }}: RD{{ number_format($pago->monto, 2) }}</span>
                </div>
            @endforeach
            @if($venta->pagos->isEmpty())
                <div class="row">
                    <span>Efectivo: RD{{ number_format($venta->total, 2) }}</span>
                </div>
            @endif
        </div>

        @php $slogan = \App\Models\SystemSetting::get('sistema_slogan'); @endphp
        @if($slogan)
        <div class="separator"></div>
        <p style="font-style:italic; text-align:center; font-size:9px; margin:6px 0;">
            {{ $slogan }}
        </p>
        @endif

        @if($venta->ncf)
        <div class="separator"></div>
        <div class="ncf" style="padding: 4px; margin: 6px 0; border: 1px solid #000;">
            <span style="font-size: 9px;">NCF</span>
            <span>{{ $venta->ncf }}</span>
        </div>
        @endif

        @php
            $ecfActual = $venta->ecf;
        @endphp
        @if($ecfActual)
        <div class="separator"></div>
        <div class="encf" style="padding: 4px; margin: 6px 0; border: 2px solid #000;">
            <span style="font-size: 9px;">e-CF {{ strtoupper($ecfActual->estado) }}</span>
            <span>{{ $ecfActual->encf }}</span>
        </div>
        @endif

        @if(count($garantiaTerminos) > 0)
        <div class="separator"></div>
        <div style="font-size: 0.6rem; line-height: 1.4; padding: 4px 0;">
            <strong style="font-size: 0.65rem; display: block; margin-bottom: 4px;">Términos de Garantía:</strong>
            @foreach($garantiaTerminos as $t)
                <strong style="font-size: 0.6rem; display: block; margin-top: 6px;">{{ $t['producto'] }}:</strong>
                <p style="margin-bottom: 4px; margin-left: 6px; font-size: 0.55rem; text-align: left;">{{ $t['terminos'] }}</p>
            @endforeach
        </div>
        @endif

        <div class="footer">
            <p><strong>¡Gracias por su compra!</strong></p>
            <p>Conserva este ticket como comprobante</p>
            <p style="margin-top: 8px; font-size: 9px;">{{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            var autoPrint = {{ json_encode($printConfig['autoPrint']) }};
            var impresoraNombre = '{{ $autoPrintName }}';
            var copies = {{ $printConfig['copies'] ?? 1 }};
            var densidad = '{{ $printConfig['densidad'] ?? 'normal' }}';

            if (densidad === 'alta') {
                document.querySelectorAll('.ticket').forEach(function(el) {
                    el.style.filter = 'brightness(1.15) contrast(1.1)';
                });
            } else if (densidad === 'baja') {
                document.querySelectorAll('.ticket').forEach(function(el) {
                    el.style.filter = 'brightness(0.85) contrast(0.9)';
                });
            }

            if (autoPrint && impresoraNombre) {
                function doPrint() {
                    window.print();
                }
                if (copies > 1) {
                    for (var i = 1; i < copies; i++) {
                        setTimeout(function() { window.print(); }, i * 2000);
                    }
                }
                setTimeout(doPrint, 300);
            }
        });
    </script>
</body>
</html>
