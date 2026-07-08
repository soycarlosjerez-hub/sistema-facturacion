<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos bajos en stock</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 5px; }
        th { background: #ddd; }
    </style>
</head>
<body>
    @php $empresa = \App\Models\SystemSetting::allCached(); @endphp
    <div style="text-align:center;margin-bottom:15px;">
        <strong style="font-size:14px;">{{ $empresa['empresa_nombre'] ?? 'Mi Negocio' }}</strong><br>
        <small>RNC: {{ $empresa['empresa_rnc'] ?? 'N/A' }}</small>
    </div>
    <h2>Productos con stock bajo (5 o menos)</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $producto)
            <tr>
                <td>{{ $producto->nombre }}</td>
                <td>{{ $producto->stock }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
