<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura #{{ $venta->id }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            width: 100%;
            margin-bottom: 20px;
        }

        .empresa {
            font-size: 14px;
            font-weight: bold;
        }

        .muted {
            color: #777;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f4f6f8;
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        .text-end {
            text-align: right;
        }

        .total-box {
            margin-top: 20px;
            width: 100%;
        }

        .total-box td {
            padding: 6px;
        }

        .total {
            font-size: 14px;
            font-weight: bold;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            background: #198754;
            color: #fff;
            font-size: 10px;
        }
    </style>
</head>
<body>

    @php $empresa = \App\Models\SystemSetting::allCached(); @endphp

    <!-- HEADER -->
    <table class="header">
        <tr>
            <td>
                <div class="empresa">{{ $empresa['empresa_nombre'] ?? 'Mi Negocio' }}</div>
                <div class="muted">RNC: {{ $empresa['empresa_rnc'] ?? 'N/A' }}</div>
                <div class="muted">Tel: {{ $empresa['empresa_telefono'] ?? 'N/A' }}</div>
            </td>
            <td class="text-end">
                <div><strong>FACTURA</strong></div>
                <div class="muted">No. {{ $venta->id }}</div>
                <div class="muted">{{ $venta->created_at->format('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    <!-- CLIENTE -->
    <table style="margin-bottom:20px;">
        <tr>
            <td>
                <strong>Cliente:</strong> {{ $venta->cliente->nombre ?? 'Consumidor Final' }}<br>
                <span class="muted">
                    Documento: {{ $venta->cliente->documento ?? 'N/A' }}
                </span>
            </td>
            <td class="text-end">
                <span class="badge">{{ strtoupper($venta->tipo_pago) }}</span>
            </td>
        </tr>
    </table>

    <!-- DETALLE -->
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-end">Precio</th>
                <th class="text-end">Cantidad</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $d)
            <tr>
                <td>{{ $d->producto->nombre }}</td>
                <td class="text-end">${{ number_format($d->precio_unitario, 2) }}</td>
                <td class="text-end">{{ $d->cantidad }}</td>
                <td class="text-end">${{ number_format($d->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TOTALES -->
    <table class="total-box">
        <tr>
            <td></td>
            <td width="200">
                <table>
                    <tr>
                        <td>Subtotal</td>
                        <td class="text-end">${{ number_format($venta->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td>ITBIS</td>
                        <td class="text-end">${{ number_format($venta->itbis, 2) }}</td>
                    </tr>
                    <tr class="total">
                        <td>Total</td>
                        <td class="text-end">${{ number_format($venta->total, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p class="muted" style="margin-top:30px;">
        Atendido por: {{ $venta->usuario->name }}
    </p>

</body>
</html>
