<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #000; padding:5px; }
        th { background:#f2f2f2; }
    </style>
</head>
<body>

<h3>Movimientos de Almacén</h3>

<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Producto</th>
            <th>Almacén</th>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Usuario</th>
        </tr>
    </thead>
    <tbody>
        @foreach($movimientos as $m)
            <tr>
                <td>{{ $m->created_at }}</td>
                <td>{{ $m->producto->nombre }}</td>
                <td>{{ $m->almacen->nombre }}</td>
                <td>{{ $m->tipo }}</td>
                <td>{{ $m->cantidad }}</td>
                <td>{{ $m->user->name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
