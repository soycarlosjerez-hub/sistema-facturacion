<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización {{ $cotizacione->numero }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { display: flex; justify-content: space-between; border-bottom: 3px solid #0ea5e9; padding-bottom: 15px; margin-bottom: 20px; }
        .company-info h1 { color: #0ea5e9; margin: 0; font-size: 24px; }
        .cotizacion-info { text-align: right; }
        .cotizacion-info h2 { margin: 0; color: #0ea5e9; font-size: 18px; }
        .client-info { margin-bottom: 20px; padding: 10px; background: #f8fafc; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #0ea5e9; color: white; padding: 8px; text-align: left; font-size: 11px; }
        td { padding: 8px; border-bottom: 1px solid #e2e8f0; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .totals { width: 300px; margin-left: auto; }
        .totals tr td { padding: 5px 10px; }
        .totals .total-row { background: #0ea5e9; color: white; font-weight: bold; font-size: 14px; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 10px; color: #64748b; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 12px; font-size: 10px; font-weight: bold; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-success { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            @php $empresa = \App\Models\SystemSetting::allCached(); @endphp
            <h1>{{ $empresa['empresa_nombre'] ?? 'Mi Negocio' }}</h1>
            <p>{{ $empresa['sistema_slogan'] ?? 'Sistema de Facturación' }}</p>
            <small>{{ $empresa['empresa_rnc'] ?? '' }}</small>
        </div>
    <div class="cotizacion-info">
        <h2>COTIZACIÓN</h2>
        <p><strong>{{ $cotizacione->numero }}</strong></p>
        <p>Fecha: {{ $cotizacione->fecha ? $cotizacione->fecha->format('d/m/Y') : 'No especificada' }}</p>
        <p>Válida hasta: {{ $cotizacione->fecha_validez ? $cotizacione->fecha_validez->format('d/m/Y') : 'No especificada' }}</p>
        <p class="badge badge-info">{{ strtoupper($cotizacione->estado_label) }}</p>
    </div>
    </div>

    <div class="client-info">
        <strong>Cliente:</strong> {{ $cotizacione->cliente?->nombre ?? 'Consumidor Final' }}<br>
        @if($cotizacione->cliente?->documento)
        <strong>Documento:</strong> {{ $cotizacione->cliente->documento }}<br>
        @endif
        @if($cotizacione->cliente?->telefono)
        <strong>Teléfono:</strong> {{ $cotizacione->cliente->telefono }}<br>
        @endif
        @if($cotizacione->cliente?->email)
        <strong>Email:</strong> {{ $cotizacione->cliente->email }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-center">Cantidad</th>
                <th class="text-end">Precio</th>
                <th class="text-end">Desc.</th>
                <th class="text-end">ITBIS</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cotizacione->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->nombre }}</strong><br>
                    <small style="color: #64748b;">{{ $item->codigo ?? '' }} · {{ $item->unidad }}</small>
                </td>
                <td class="text-center">{{ number_format($item->cantidad, 2) }}</td>
                <td class="text-end">RD${{ number_format($item->precio_unitario, 2) }}</td>
                <td class="text-end">RD${{ number_format($item->descuento, 2) }}</td>
                <td class="text-end">RD${{ number_format($item->itbis, 2) }}</td>
                <td class="text-end"><strong>RD${{ number_format($item->total, 2) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal:</td>
            <td class="text-end">RD${{ number_format($cotizacione->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>ITBIS (18%):</td>
            <td class="text-end">RD${{ number_format($cotizacione->itbis, 2) }}</td>
        </tr>
        @if($cotizacione->descuento > 0)
        <tr>
            <td>Descuento:</td>
            <td class="text-end">-RD${{ number_format($cotizacione->descuento, 2) }}</td>
        </tr>
        @endif
        <tr class="total-row">
            <td>TOTAL:</td>
            <td class="text-end">RD${{ number_format($cotizacione->total, 2) }}</td>
        </tr>
    </table>

    @if($cotizacione->condiciones)
    <div style="margin-top: 30px; padding: 10px; background: #f8fafc; border-radius: 5px;">
        <strong>Términos y Condiciones:</strong><br>
        {{ $cotizacione->condiciones }}
    </div>
    @endif

    @if($cotizacione->notas)
    <div style="margin-top: 15px; padding: 10px; background: #fef3c7; border-radius: 5px;">
        <strong>Notas:</strong><br>
        {{ $cotizacione->notas }}
    </div>
    @endif

    <div class="footer">
        <p><strong>Generado:</strong> {{ now()->format('d/m/Y H:i') }} | <strong>Por:</strong> {{ $cotizacione->user?->name ?? 'Sistema' }}</p>
        <p style="text-align: center; margin-top: 20px;">Esta cotización es válida hasta {{ $cotizacione->fecha_validez ? $cotizacione->fecha_validez->format('d/m/Y') : 'No especificada' }}. Para confirmar el pedido, contacte con nosotros.</p>
    </div>
</body>
</html>
