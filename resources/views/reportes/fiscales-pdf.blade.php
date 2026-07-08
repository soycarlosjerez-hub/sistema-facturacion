<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $titulo }} - {{ $periodo->format('F Y') }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 8px; }
        h2 { text-align: center; color: #1e293b; margin-bottom: 4px; font-size: 14px; }
        .subtitle { text-align: center; color: #64748b; margin-bottom: 16px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #f1f5f9; text-align: left; padding: 6px 4px; font-size: 7px; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
        td { padding: 4px; border-bottom: 1px solid #e2e8f0; font-size: 7px; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: 700; }
        .text-muted { color: #64748b; }
        .summary { margin-top: 16px; }
        .summary td { border: none; padding: 2px 4px; font-size: 8px; }
        .total-row { background: #f8fafc; font-weight: 700; }
        .footer { position: fixed; bottom: 10px; width: 100%; text-align: center; color: #94a3b8; font-size: 6px; border-top: 1px solid #e2e8f0; padding-top: 4px; }
    </style>
</head>
<body>
    @php $empresa = \App\Models\SystemSetting::allCached(); @endphp
    <div style="text-align:center;margin-bottom:6px;">
        <strong style="font-size:12px;">{{ $empresa['empresa_nombre'] ?? 'Mi Negocio' }}</strong><br>
        <span style="font-size:7px;color:#666;">RNC: {{ $empresa['empresa_rnc'] ?? 'N/A' }}</span>
    </div>
    <h2>{{ $titulo }}</h2>
    <div class="subtitle">Período: {{ ucfirst($periodo->translatedFormat('F Y')) }} | {{ $cantidad }} registro(s)</div>

    <table>
        <thead>
            <tr>
                <th>RNC/Cédula</th>
                <th>{{ $tipo === '607' ? 'Cliente' : 'Proveedor' }}</th>
                <th>NCF/Comp.</th>
                <th>Tipo</th>
                <th>Fecha</th>
                <th class="text-end">Monto Fact.</th>
                <th class="text-end">ITBIS</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registros as $r)
            <tr>
                <td>{{ $r['rnc'] }}</td>
                <td>{{ $r['cliente'] ?? $r['proveedor'] }}</td>
                <td>{{ $r['ncf'] }}</td>
                <td>{{ $r['tipo_ncf'] }}</td>
                <td>{{ $r['fecha'] }}</td>
                <td class="text-end">{{ number_format($r['monto_facturado'], 2) }}</td>
                <td class="text-end">{{ number_format($r['itbis'], 2) }}</td>
                <td class="text-end">{{ number_format($r['total'], 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center">Sin registros</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary">
        <tr><td colspan="6"></td><td class="text-end fw-bold">Totales:</td></tr>
        <tr class="total-row">
            <td colspan="5"></td>
            <td class="text-end">RD$ {{ number_format($total_monto, 2) }}</td>
            <td class="text-end">RD$ {{ number_format($total_itbis, 2) }}</td>
            <td class="text-end">RD$ {{ number_format($total_general, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        Generado el {{ now()->format('d/m/Y h:i A') }} | Sistema de Facturación RD
    </div>
</body>
</html>
