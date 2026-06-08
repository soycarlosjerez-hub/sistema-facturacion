<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Conduce {{ $conduce->numero }}</title>
<style>
    @page { margin: 10mm; }
    body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #222; }
    .container { max-width: 200mm; margin: 0 auto; }
    .header { display: flex; justify-content: space-between; border-bottom: 3px solid #0d6efd; padding-bottom: 10px; margin-bottom: 15px; }
    .header .left h1 { color: #0d6efd; margin: 0 0 5px; font-size: 24px; }
    .header .left .small { color: #666; }
    .header .right { text-align: right; }
    .header .right .numero { font-size: 22px; font-weight: bold; color: #0d6efd; }
    .header .right .estado { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 10px; font-weight: bold; margin-top: 4px; }
    .estado-borrador { background: #f0f0f0; color: #555; }
    .estado-en_transito { background: #cfe2ff; color: #084298; }
    .estado-entregado { background: #d1e7dd; color: #0f5132; }
    .estado-devuelto { background: #fff3cd; color: #664d03; }
    .estado-cancelado { background: #f8d7da; color: #842029; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
    .info-box { border: 1px solid #dee2e6; border-radius: 5px; padding: 10px; }
    .info-box h3 { font-size: 11px; text-transform: uppercase; color: #666; margin: 0 0 5px; border-bottom: 1px solid #eee; padding-bottom: 4px; }
    .info-box p { margin: 2px 0; font-size: 11px; }
    .info-box p strong { display: inline-block; min-width: 70px; }
    table.items { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    table.items th { background: #0d6efd; color: white; padding: 8px; text-align: left; font-size: 10px; text-transform: uppercase; }
    table.items td { padding: 6px 8px; border-bottom: 1px solid #eee; }
    table.items tr:nth-child(even) { background: #f8f9fa; }
    table.items .right { text-align: right; }
    table.items .center { text-align: center; }
    .footer { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 25px; }
    .signature { border-top: 1px solid #222; padding-top: 5px; text-align: center; font-size: 10px; }
    .notes { background: #fff8e1; border-left: 3px solid #ffc107; padding: 8px; margin-top: 15px; font-size: 10px; }
    .small { font-size: 10px; color: #666; }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="left">
            <h1><i class="bi bi-truck"></i> CONDUCE</h1>
            <div class="small">Nota de Entrega</div>
            <p class="small">
                <strong>{{ $empresa->nombre ?? config('app.name') }}</strong><br>
                RNC: {{ $empresa->rnc ?? 'N/A' }}<br>
                {{ $empresa->direccion ?? '' }}<br>
                {{ $empresa->telefono ?? '' }}
            </p>
        </div>
        <div class="right">
            <div class="numero">{{ $conduce->numero }}</div>
            <div>Fecha: {{ $conduce->fecha->format('d/m/Y') }}</div>
            @if($conduce->fecha_entrega)
            <div>Entrega: {{ $conduce->fecha_entrega->format('d/m/Y') }}</div>
            @endif
            <div class="estado estado-{{ $conduce->estado }}">{{ $conduce->estado_label }}</div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h3>Datos del Cliente</h3>
            <p><strong>Nombre:</strong> {{ $conduce->cliente?->nombre ?? 'N/A' }}</p>
            @if($conduce->cliente?->rnc_cedula)
            <p><strong>RNC/Cédula:</strong> {{ $conduce->cliente->rnc_cedula }}</p>
            @endif
            @if($conduce->cliente?->telefono)
            <p><strong>Teléfono:</strong> {{ $conduce->cliente->telefono }}</p>
            @endif
            @if($conduce->cliente?->direccion)
            <p><strong>Dirección:</strong> {{ $conduce->cliente->direccion }}</p>
            @endif
        </div>
        <div class="info-box">
            <h3>Información de Entrega</h3>
            <p><strong>Dirección:</strong> {{ $conduce->direccion_entrega }}</p>
            @if($conduce->referencia)<p><strong>Referencia:</strong> {{ $conduce->referencia }}</p>@endif
            @if($conduce->contacto_entrega)<p><strong>Contacto:</strong> {{ $conduce->contacto_entrega }}</p>@endif
            @if($conduce->telefono_entrega)<p><strong>Tel. contacto:</strong> {{ $conduce->telefono_entrega }}</p>@endif
        </div>
    </div>

    @if($conduce->transportista || $conduce->chofer)
    <div class="info-box" style="margin-bottom: 15px;">
        <h3>Información de Transporte</h3>
        <div class="info-grid" style="margin-bottom: 0;">
            <div>
                @if($conduce->transportista)<p><strong>Transportista:</strong> {{ $conduce->transportista }}</p>@endif
                @if($conduce->vehiculo)<p><strong>Vehículo:</strong> {{ $conduce->vehiculo }}</p>@endif
                @if($conduce->placa)<p><strong>Placa:</strong> {{ $conduce->placa }}</p>@endif
            </div>
            <div>
                @if($conduce->chofer)<p><strong>Chofer:</strong> {{ $conduce->chofer }}</p>@endif
                @if($conduce->chofer_cedula)<p><strong>Cédula:</strong> {{ $conduce->chofer_cedula }}</p>@endif
            </div>
        </div>
    </div>
    @endif

    <h3 style="font-size: 12px; margin-bottom: 5px;">Productos a Entregar</h3>
    <table class="items">
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>Código</th>
                <th>Descripción</th>
                <th class="center" style="width: 60px;">Cantidad</th>
                <th class="center" style="width: 60px;">Unidad</th>
                @if($conduce->estado === 'entregado')
                <th class="center" style="width: 60px;">Recibido</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($conduce->items as $idx => $item)
            <tr>
                <td>{{ str_pad($idx + 1, 3, '0', STR_PAD_LEFT) }}</td>
                <td class="small">{{ $item->codigo ?? '-' }}</td>
                <td>{{ $item->nombre }}</td>
                <td class="center"><strong>{{ number_format($item->cantidad, 2) }}</strong></td>
                <td class="center">{{ $item->unidad }}</td>
                @if($conduce->estado === 'entregado')
                <td class="center">{{ number_format($item->cantidad_recibida ?? $item->cantidad, 2) }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="{{ $conduce->estado === 'entregado' ? 4 : 3 }}" class="right"><strong>Total items:</strong></td>
                <td class="center"><strong>{{ $conduce->total_items }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    @if($conduce->observaciones)
    <div class="notes">
        <strong>Observaciones:</strong> {{ $conduce->observaciones }}
    </div>
    @endif

    @if($conduce->estado === 'entregado')
    <div class="info-box" style="margin-top: 15px; border-color: #198754; background: #f0fff4;">
        <h3 style="color: #198754;">Entrega Confirmada</h3>
        <p><strong>Recibido por:</strong> {{ $conduce->recibido_por }}</p>
        @if($conduce->recibido_cedula)<p><strong>Cédula:</strong> {{ $conduce->recibido_cedula }}</p>@endif
        <p><strong>Fecha de recepción:</strong> {{ $conduce->fecha_recibido?->format('d/m/Y H:i') }}</p>
    </div>
    @endif

    <div class="footer">
        <div class="signature">
            <br><br>
            {{ $conduce->user?->name ?? '' }}<br>
            <em>Despachado por</em>
        </div>
        <div class="signature">
            <br><br>
            {{ $conduce->recibido_por ?? '_________________________' }}<br>
            <em>Recibido por</em>
        </div>
    </div>

    <p class="small" style="text-align: center; margin-top: 15px;">
        Documento generado el {{ now()->format('d/m/Y H:i:s') }}
    </p>
</div>
</body>
</html>
