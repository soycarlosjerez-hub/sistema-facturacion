<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listado de Ventas</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    @php $empresa = \App\Models\SystemSetting::allCached(); @endphp
    <div style="text-align:center;margin-bottom:20px;">
        <h2 style="margin:0;">{{ $empresa['empresa_nombre'] ?? 'Mi Negocio' }}</h2>
        <small style="color:#666;">RNC: {{ $empresa['empresa_rnc'] ?? 'N/A' }}</small>
    </div>
    <h3>Listado de Ventas</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Usuario</th>
                <th>Tipo</th>
                <th>Fecha</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
                <tr>
                    <td>{{ $venta->id }}</td>
                    <td>{{ $venta->cliente->nombre ?? 'N/A' }}</td>
                    <td>{{ $venta->usuario->name ?? 'N/A' }}</td>
                    <td>{{ $venta->tipoVenta->nombre ?? 'N/A' }}</td>
                    <td>{{ $venta->created_at->format('d/m/Y') }}</td>
                    <td>${{ number_format($venta->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>