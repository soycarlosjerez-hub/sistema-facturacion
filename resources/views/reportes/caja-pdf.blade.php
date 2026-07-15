<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Caja {{ $desde }} al {{ $hasta }}</title>
<style>body{font-family:DejaVu Sans,sans-serif;font-size:7px;}table{width:100%;border-collapse:collapse;margin-top:10px;}th,td{border:1px solid #ccc;padding:3px 5px;text-align:left;}th{background:#f0f0f0;font-weight:700;text-transform:uppercase;font-size:6px;}td.text-end{text-align:right;}.totals{background:#f8f8f8;font-weight:700;}h2{margin:0;color:#333;}.meta{color:#666;margin:5px 0;}
td.neg{color:#dc3545;}td.pos{color:#198754;}</style>
</head><body>
@php $empresa = \App\Models\SystemSetting::allCached(); @endphp
<div style="text-align:center;margin-bottom:8px;">
    <strong style="font-size:11px;">{{ $empresa['empresa_nombre'] ?? 'Mi Negocio' }}</strong><br>
    <span style="font-size:7px;color:#666;">RNC: {{ $empresa['empresa_rnc'] ?? 'N/A' }}</span>
</div>
<h2>Reporte de Caja / Turnos</h2>
<p class="meta">Período: {{ $desde }} al {{ $hasta }} &middot; {{ $cantidad }} sesión(es)</p>
<table><thead><tr>
<th>Caja</th><th>Cajero</th><th>Apertura</th><th>Cierre</th><th>Estado</th>
<th class="text-end">Inicial</th><th class="text-end">Efectivo</th><th class="text-end">Tarjeta</th><th class="text-end">Transf.</th><th class="text-end">Declarado</th><th class="text-end">Descuadre</th>
</tr></thead><tbody>
@foreach($sesiones as $s)
<tr>
<td>{{ $s->caja?->nombre ?? '' }}</td>
<td>{{ $s->user?->name ?? '' }}</td>
<td>{{ $s->fecha_apertura?->format('d/m/Y H:i') ?? '-' }}</td>
<td>{{ $s->fecha_cierre?->format('d/m/Y H:i') ?? '-' }}</td>
<td>{{ $s->estado ?? '' }}</td>
<td class="text-end">RD$ {{ number_format($s->monto_inicial ?? 0, 2) }}</td>
<td class="text-end">RD$ {{ number_format($s->ventas_efectivo ?? 0, 2) }}</td>
<td class="text-end">RD$ {{ number_format($s->ventas_tarjeta ?? 0, 2) }}</td>
<td class="text-end">RD$ {{ number_format($s->ventas_transferencia ?? 0, 2) }}</td>
<td class="text-end">RD$ {{ number_format($s->monto_declarado ?? 0, 2) }}</td>
<td class="text-end {{ ($s->descuadre ?? 0) >= 0 ? 'pos' : 'neg' }}">RD$ {{ number_format($s->descuadre ?? 0, 2) }}</td>
</tr>
@endforeach
</tbody>
<tfoot>
<tr class="totals">
<td colspan="5" class="text-end">TOTALES</td>
<td class="text-end">RD$ {{ number_format($sesiones->sum('monto_inicial'), 2) }}</td>
<td class="text-end">RD$ {{ number_format($sesiones->sum('ventas_efectivo'), 2) }}</td>
<td class="text-end">RD$ {{ number_format($sesiones->sum('ventas_tarjeta'), 2) }}</td>
<td class="text-end">RD$ {{ number_format($sesiones->sum('ventas_transferencia'), 2) }}</td>
<td class="text-end">RD$ {{ number_format($sesiones->sum('monto_declarado'), 2) }}</td>
<td class="text-end">RD$ {{ number_format($sesiones->sum('descuadre'), 2) }}</td>
</tr>
</tfoot></table>
<p style="color:#999;font-size:7px;margin-top:20px;">Generado: {{ now()->format('d/m/Y H:i') }}</p>
</body></html>
