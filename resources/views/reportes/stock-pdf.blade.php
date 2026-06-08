<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Inventario al {{ now()->format('d/m/Y') }}</title>
<style>body{font-family:DejaVu Sans,sans-serif;font-size:8px;}table{width:100%;border-collapse:collapse;margin-top:10px;}th,td{border:1px solid #ccc;padding:3px 5px;text-align:left;}th{background:#f0f0f0;font-weight:700;text-transform:uppercase;font-size:7px;}td.text-end{text-align:right;}.totals{background:#f8f8f8;font-weight:700;}h2{margin:0;color:#333;}.meta{color:#666;margin:5px 0;}</style>
</head><body>
<h2>Reporte de Inventario</h2>
<p class="meta">Generado: {{ now()->format('d/m/Y H:i') }} &middot; {{ $totalProductos }} producto(s)</p>
<table><thead><tr>
<th>Código</th><th>Producto</th><th class="text-end">Stock</th><th class="text-end">Mínimo</th><th class="text-end">Costo</th><th class="text-end">Precio</th><th class="text-end">Valor Inv.</th>
</tr></thead><tbody>
@foreach($productos as $p)
<tr>
<td>{{ $p->codigo_barras ?? $p->referencia ?? '-' }}</td>
<td>{{ $p->nombre }}</td>
<td class="text-end">{{ $p->stock }}</td>
<td class="text-end">{{ $p->stock_minimo ?? 0 }}</td>
<td class="text-end">RD$ {{ number_format($p->precio_compra ?? 0, 2) }}</td>
<td class="text-end">RD$ {{ number_format($p->precio ?? 0, 2) }}</td>
<td class="text-end">RD$ {{ number_format($p->stock * ($p->precio_compra ?? 0), 2) }}</td>
</tr>
@endforeach
</tbody>
<tfoot>
<tr class="totals">
<td colspan="6" class="text-end">VALOR TOTAL INVENTARIO</td>
<td class="text-end">RD$ {{ number_format($totalValorInventario, 2) }}</td>
</tr>
</tfoot></table>
</body></html>