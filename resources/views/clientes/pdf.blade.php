<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Clientes</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    @php $empresa = \App\Models\SystemSetting::allCached(); @endphp
    <div style="text-align:center;margin-bottom:20px;">
        <h1 style="margin:0;font-size:20px;">{{ $empresa['empresa_nombre'] ?? 'Mi Negocio' }}</h1>
        <small>RNC: {{ $empresa['empresa_rnc'] ?? 'N/A' }}</small>
    </div>
    <h2>Listado de Clientes</h2>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->nombre }}</td>
                    <td>{{ $cliente->email }}</td>
                    <td>{{ $cliente->telefono }}</td>
                    <td>{{ $cliente->direccion }}</td>
                    <td>{{ $cliente->activo_label }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
