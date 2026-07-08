<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Gastos {{ $desde }} al {{ $hasta }}</title>
<style>body{font-family:DejaVu Sans,sans-serif;font-size:7px;}table{width:100%;border-collapse:collapse;margin-top:10px;}th,td{border:1px solid #ccc;padding:3px 5px;text-align:left;}th{background:#f0f0f0;font-weight:700;text-transform:uppercase;font-size:6px;}td.text-end{text-align:right;}.totals{background:#f8f8f8;font-weight:700;}h2{margin:0;color:#333;}.meta{color:#666;margin:5px 0;}</style>
</head><body>
@php $empresa = \App\Models\SystemSetting::allCached(); @endphp
<div style="text-align:center;margin-bottom:8px;">
    <strong style="font-size:11px;">{{ $empresa['empresa_nombre'] ?? 'Mi Negocio' }}</strong><br>
    <span style="font-size:7px;color:#666;">RNC: {{ $empresa['empresa_rnc'] ?? 'N/A' }}</span>
</div>
<h2>Resumen de Gastos</h2>
<p class="meta">Período: {{ $desde }} al {{ $hasta }} &middot; {{ $cantidad }} gasto(s)</p>
<table><thead><tr>
<th>#</th><th>Descripción</th><th>Categoría</th><th>Método de Pago</th><th>Comprobante</th><th>Fecha</th><th class="text-end">Monto</th>
</tr></thead><tbody>
@foreach($gastos as $g)
<tr>
<td>{{ str_pad($g->id,4,'0',STR_PAD_LEFT) }}</td>
<td>{{ $g->descripcion }}</td>
<td>{{ $g->categoria ?? '' }}</td>
<td>{{ $g->metodo_pago ?? '' }}</td>
<td>{{ $g->comprobante ?? '' }}</td>
<td>{{ $g->fecha_gasto?->format('d/m/Y') ?? '' }}</td>
<td class="text-end">RD$ {{ number_format($g->monto, 2) }}</td>
</tr>
@endforeach
</tbody>
<tfoot>
<tr class="totals">
<td colspan="6" class="text-end">TOTALES</td>
<td class="text-end">RD$ {{ number_format($totalGeneral, 2) }}</td>
</tr>
</tfoot></table>
<p style="color:#999;font-size:7px;margin-top:20px;">Generado: {{ now()->format('d/m/Y H:i') }}</p>
</body></html>
