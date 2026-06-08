<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Ventas {{ $desde }} al {{ $hasta }}</title>
<style>body{font-family:DejaVu Sans,sans-serif;font-size:8px;}table{width:100%;border-collapse:collapse;margin-top:10px;}th,td{border:1px solid #ccc;padding:4px 6px;text-align:left;}th{background:#f0f0f0;font-weight:700;text-transform:uppercase;font-size:7px;}td.text-end{text-align:right;}.totals{background:#f8f8f8;font-weight:700;}h2{margin:0;color:#333;}.meta{color:#666;margin:5px 0;}</style>
</head><body>
<h2>Resumen de Ventas</h2>
<p class="meta">Período: {{ $desde }} al {{ $hasta }} &middot; {{ $cantidad }} venta(s)</p>
<table><thead><tr>
<th>#</th><th>Cliente</th><th>Vendedor</th><th>NCF</th><th>Fecha</th><th class="text-end">Subtotal</th><th class="text-end">ITBIS</th><th class="text-end">Total</th>
</tr></thead><tbody>
@foreach($ventas as $v)
<tr>
<td>{{ str_pad($v->id,5,'0',STR_PAD_LEFT) }}</td>
<td>{{ $v->cliente?->nombre ?? 'Consumidor Final' }}</td>
<td>{{ $v->usuario?->name ?? '' }}</td>
<td>{{ $v->ncf ?? $v->encf ?? 'S/N' }}</td>
<td>{{ $v->created_at->format('d/m/Y') }}</td>
<td class="text-end">RD$ {{ number_format($v->subtotal ?? 0, 2) }}</td>
<td class="text-end">RD$ {{ number_format($v->impuestos ?? 0, 2) }}</td>
<td class="text-end">RD$ {{ number_format($v->total, 2) }}</td>
</tr>
@endforeach
</tbody>
<tfoot>
<tr class="totals">
<td colspan="5" class="text-end">TOTALES</td>
<td class="text-end">RD$ {{ number_format($ventas->sum('subtotal'), 2) }}</td>
<td class="text-end">RD$ {{ number_format($ventas->sum('impuestos'), 2) }}</td>
<td class="text-end">RD$ {{ number_format($ventas->sum('total'), 2) }}</td>
</tr>
</tfoot></table>
<p style="color:#999;font-size:7px;margin-top:20px;">Generado: {{ now()->format('d/m/Y H:i') }}</p>
</body></html>