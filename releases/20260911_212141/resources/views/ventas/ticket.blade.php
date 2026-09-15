<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Venta {{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: #fff;
        }
        .ticket {
            width: 80mm;
            margin: 0 auto;
            padding: 2mm;
            font-size: 11px;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .separator { border-top: 1px dashed #000; margin: 4px 0; }
        .separator-double { border-top: 2px solid #000; margin: 4px 0; }
        
        .header h1 { font-size: 16px; margin-bottom: 2px; }
        .header p { font-size: 10px; }
        
        .section { margin: 6px 0; }
        .row { display: flex; justify-content: space-between; margin: 2px 0; font-size: 11px; }
        .row .label { font-weight: bold; }
        
        .items-table { width: 100%; border-spacing: 0; margin: 6px 0; }
        .items-table tr { border-bottom: 1px dotted #ccc; }
        .items-table td { padding: 3px 0; vertical-align: top; }
        .items-table .name { width: 55%; }
        .items-table .qty { width: 20%; text-align: center; }
        .items-table .total { width: 25%; text-align: right; }
        
        .totals { margin: 8px 0; }
        .totals .row { font-size: 11px; margin: 2px 0; }
        .totals .grand { font-size: 15px; font-weight: bold; border-top: 2px solid #000; padding-top: 4px; margin-top: 4px; }
        
        .footer { text-align: center; margin-top: 12px; padding-top: 8px; border-top: 1px dashed #000; font-size: 10px; }
        .footer p { margin: 2px 0; }
        
        .ncf, .encf { text-align: center; margin: 4px 0; font-family: monospace; }
        .ncf span, .encf span { display: block; font-weight: bold; letter-spacing: 1px; }
        
        @media print {
            body { margin: 0; padding: 0; background: #fff; }
            .ticket { padding: 0; width: 100%; }
            @page { margin: 0; size: 80mm auto; }
        }
        
        @media (max-width: 600px) {
            body { font-size: 10px; }
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
                    <td colspan="3" style="font-size: 0.65rem; color: #0d6efd; padding-left: 24px;">Garantia: {{ $d->producto->garantia_meses }} meses</td>
                </tr>
                @endif
                @if($d->notas)
                <tr>
                    <td colspan="3" style="font-size: 0.65rem; font-style: italic; color: #666; padding-left: 24px;">{{ $d->notas }}</td>
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
            <div class="row" style="color:#dc3545;font-weight:700;">
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
        <p style="font-style:italic; color:#666; text-align:center; font-size:9px; margin:6px 0;">
            {{ $slogan }}
        </p>
        @endif

        @if($venta->ncf)
        <div class="separator"></div>
        <div class="ncf" style="background: #f0f0f0; padding: 4px; margin: 6px 0;">
            <span style="font-size: 9px; color: #666;">NCF</span>
            <span>{{ $venta->ncf }}</span>
        </div>
        @endif

        @php
            $ecfActual = $venta->ecf;
        @endphp
        @if($ecfActual)
        <div class="separator"></div>
        <div class="encf" style="background: {{ $ecfActual->estado == 'aprobado' ? '#e8f5e9' : '#fff3e0' }}; padding: 4px; margin: 6px 0; border: 1px solid {{ $ecfActual->estado == 'aprobado' ? '#4caf50' : '#ff9800' }}">
            <span style="font-size: 9px; color: #666;">e-CF {{ strtoupper($ecfActual->estado) }}</span>
            <span>{{ $ecfActual->encf }}</span>
        </div>
        @endif

        @if(count($garantiaTerminos) > 0)
        <div class="separator"></div>
        <div style="font-size: 0.6rem; color: #333; line-height: 1.4; padding: 4px 0;">
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
            <p style="margin-top: 8px; font-size: 9px; color: #666;">{{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            // Si hay impresora configurada para auto-imprimir, se dispara window.print()
            // y se pasa el nombre de la impresora como parametro para que el usuario la reconozca
            const impresoraNombre = '{{ $impresora->nombre ?? "" }}';
            if (impresoraNombre) {
                setTimeout(function() { window.print(); }, 300);
            }
        });
    </script>
</body>
</html>
