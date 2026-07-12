<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Categorías</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; }
        .header { border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 2px 0; font-size: 10px; color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background-color: #1e293b; color: #fff; font-size: 10px; text-transform: uppercase; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .text-center { text-align: center; }
        .footer { margin-top: 20px; font-size: 9px; color: #64748b; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Categorías</h1>
        @php $empresa = \App\Models\SystemSetting::allCached(); @endphp
        <p>{{ $empresa['empresa_nombre'] ?? 'Mi Negocio' }} — Generado el {{ date('d/m/Y H:i A') }}</p>
        <p>Total de categorías: <strong>{{ $categorias->count() }}</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th class="text-center">Productos</th>
                <th class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categorias as $categoria)
            <tr>
                <td><strong>{{ $categoria->nombre }}</strong></td>
                <td>{{ $categoria->descripcion ?? '—' }}</td>
                <td class="text-center">{{ $categoria->productos_count ?? $categoria->productos->count() }}</td>
                <td class="text-center">{{ $categoria->activa ? 'Activa' : 'Inactiva' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center" style="padding: 20px;">No hay categorías para mostrar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Sistema de Facturación — Reporte generado automáticamente
    </div>
</body>
</html>
