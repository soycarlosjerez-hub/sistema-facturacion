<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listado de Compras</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h2, h3 { margin: 0; }
        .header { text-align: center; margin-bottom: 20px; }
        .header small { color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; }
        th { background: #eee; }
        .text-end { text-align: right; }
    </style>
</head>
<body>
    @php $empresa = \App\Models\SystemSetting::allCached(); @endphp
    <div class="header">
        @if($pdfLogoUrl)
        <img src="{{ $pdfLogoUrl }}" style="max-width: 80px; max-height: 60px; object-fit: contain; margin-bottom: 5px;" alt="Logo">
        @endif
        <h2>{{ \App\Models\SystemSetting::nombreEmpresaActual() }}</h2>
        <small>RNC: {{ $empresa['empresa_rnc'] ?? 'N/A' }}</small>
    </div>
    <h3>Listado de Compras</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Proveedor</th>
                <th>RNC/Cédula</th>
                <th>Usuario</th>
                <th>Tipo</th>
                <th>Fecha</th>
                <th class="text-end">Subtotal</th>
                <th class="text-end">ITBIS</th>
                <th class="text-end">Retenciones</th>
                <th class="text-end">Total</th>
                <th class="text-end">Total a pagar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($compras as $compra)
                <tr>
                    <td>{{ $compra->id }}</td>
                    <td>{{ $compra->proveedor->nombre ?? 'N/A' }}</td>
                    <td>{{ $compra->proveedor->rnc_cedula ?: ($compra->proveedor->rnc ?? 'N/A') }}</td>
                    <td>{{ $compra->user->name ?? 'N/A' }}</td>
                    <td>{{ $compra->tipoCompra->nombre ?? 'N/A' }}</td>
                    <td>{{ $compra->fecha ? $compra->fecha->format('d/m/Y') : $compra->created_at->format('d/m/Y') }}</td>
                    <td class="text-end">${{ number_format($compra->subtotal, 2) }}</td>
                    <td class="text-end">${{ number_format($compra->itbis_total, 2) }}</td>
                    <td class="text-end">${{ number_format($compra->total_retenciones, 2) }}</td>
                    <td class="text-end">${{ number_format($compra->total, 2) }}</td>
                    <td class="text-end">${{ number_format($compra->total_pagar, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>