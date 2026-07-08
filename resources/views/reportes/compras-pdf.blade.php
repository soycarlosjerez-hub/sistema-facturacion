<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Compras {{ $desde }} al {{ $hasta }}</title>
<style>body{font-family:DejaVu Sans,sans-serif;font-size:7px;}table{width:100%;border-collapse:collapse;margin-top:10px;}th,td{border:1px solid #ccc;padding:3px 5px;text-align:left;}th{background:#f0f0f0;font-weight:700;text-transform:uppercase;font-size:6px;}td.text-end{text-align:right;}.totals{background:#f8f8f8;font-weight:700;}h2{margin:0;color:#333;}.meta{color:#666;margin:5px 0;}</style>
</head><body>
@php $empresa = \App\Models\SystemSetting::allCached(); @endphp
<div style="text-align:center;margin-bottom:8px;">
    <strong style="font-size:11px;">{{ $empresa['empresa_nombre'] ?? 'Mi Negocio' }}</strong><br>
    <span style="font-size:7px;color:#666;">RNC: {{ $empresa['empresa_rnc'] ?? 'N/A' }}</span>
</div>
<h2>Resumen de Compras</h2>
<p class="meta">Período: {{ $desde }} al {{ $hasta }} &middot; {{ $cantidad }} compra(s)</p>
<table><thead><tr>
<th>#</th><th>Proveedor</th><th>Folio</th><th>Fecha</th><th class="text-end">Subtotal</th><th class="text-end">ITBIS</th><th class="text-end">Ret ISR</th><th class="text-end">Ret ITBIS</th><th class="text-end">Total</th>
</tr></thead><tbody>
@foreach($compras as $c)
<tr>
<td>{{ str_pad($c->id,5,'0',STR_PAD_LEFT) }}</td>
<td>{{ $c->proveedor?->nombre ?? 'N/A' }}</td>
<td>{{ $c->folio ?? 'S/F' }}</td>
<td>{{ $c->fecha?->format('d/m/Y') ?? '' }}</td>
<td class="text-end">RD$ {{ number_format($c->subtotal ?? 0, 2) }}</td>
<td class="text-end">RD$ {{ number_format($c->itbis_total ?? 0, 2) }}</td>
<td class="text-end">RD$ {{ number_format($c->retencion_isr ?? 0, 2) }}</td>
<td class="text-end">RD$ {{ number_format($c->retencion_itbis ?? 0, 2) }}</td>
<td class="text-end">RD$ {{ number_format($c->total, 2) }}</td>
</tr>
@endforeach
</tbody>
<tfoot>
<tr class="totals">
<td colspan="4" class="text-end">TOTALES</td>
<td class="text-end">RD$ {{ number_format($compras->sum('subtotal'), 2) }}</td>
<td class="text-end">RD$ {{ number_format($compras->sum('itbis_total'), 2) }}</td>
<td class="text-end">RD$ {{ number_format($compras->sum('retencion_isr'), 2) }}</td>
<td class="text-end">RD$ {{ number_format($compras->sum('retencion_itbis'), 2) }}</td>
<td class="text-end">RD$ {{ number_format($compras->sum('total'), 2) }}</td>
</tr>
</tfoot></table>
<p style="color:#999;font-size:7px;margin-top:20px;">Generado: {{ now()->format('d/m/Y H:i') }}</p>
</body></html>