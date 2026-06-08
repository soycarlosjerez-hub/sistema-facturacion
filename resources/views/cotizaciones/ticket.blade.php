<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket {{ $cotizacion->numero }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: {{ $paperWidth == 58 ? '11px' : '13px' }};
            line-height: 1.3;
            color: #000;
            background: #f0f0f0;
        }
        .ticket-container {
            width: {{ $paperWidth }}mm;
            margin: 10px auto;
            padding: 5mm;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .ticket-header {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #000;
        }
        .ticket-header h1 {
            font-size: 1.3em;
            margin-bottom: 2px;
        }
        .ticket-header p {
            font-size: 0.9em;
            margin: 1px 0;
        }
        .ticket-type {
            text-align: center;
            font-weight: bold;
            font-size: 1.2em;
            margin: 8px 0;
            padding: 4px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
        }
        .ticket-info {
            margin-bottom: 8px;
            font-size: 0.95em;
        }
        .ticket-info .row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        .ticket-info .label {
            font-weight: bold;
        }
        .separator {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .separator-double {
            border-top: 3px double #000;
            margin: 6px 0;
        }
        .items {
            margin: 8px 0;
        }
        .item {
            margin-bottom: 6px;
            page-break-inside: avoid;
        }
        .item-name {
            font-weight: bold;
            word-wrap: break-word;
        }
        .item-detail {
            display: flex;
            justify-content: space-between;
            font-size: 0.95em;
        }
        .item-subtotal {
            display: flex;
            justify-content: space-between;
            font-size: 0.9em;
            color: #444;
        }
        .totals {
            margin: 8px 0;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        .total-row.grand {
            font-weight: bold;
            font-size: 1.3em;
            margin-top: 4px;
            padding-top: 4px;
            border-top: 1px dashed #000;
        }
        .footer {
            text-align: center;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed #000;
            font-size: 0.9em;
        }
        .actions {
            text-align: center;
            margin: 20px auto;
            max-width: 300px;
        }
        .actions button, .actions a {
            margin: 5px;
            padding: 10px 20px;
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }
        .actions button:hover, .actions a:hover {
            background: #0a58ca;
        }
        .actions .btn-secondary {
            background: #6c757d;
        }
        .text-muted {
            color: #666;
            font-size: 0.85em;
        }
        @media print {
            body { background: #fff; }
            .ticket-container {
                box-shadow: none;
                margin: 0;
                padding: 2mm;
            }
            .actions { display: none !important; }
            @page {
                margin: 0;
                size: {{ $paperWidth }}mm auto;
            }
        }
        @media (max-width: 600px) {
            body { font-size: 10px; }
        }
    </style>
</head>
<body>
    <article class="ticket-container" role="document" aria-label="Cotización {{ $cotizacion->numero }}">
        <header class="ticket-header">
            <h1>{{ config('app.name', 'Sistema') }}</h1>
            @if(isset($cotizacion->user) && $cotizacion->user->empresa)
                <p>RNC: {{ $cotizacion->user->empresa->rnc ?? 'N/A' }}</p>
                <p>{{ $cotizacion->user->empresa->direccion ?? '' }}</p>
                <p>Tel: {{ $cotizacion->user->empresa->telefono ?? '' }}</p>
            @endif
        </header>

        <div class="ticket-type">
            COTIZACIÓN
        </div>

        <section class="ticket-info" aria-label="Información de la cotización">
            <div class="row">
                <span class="label">Nro:</span>
                <span>{{ $cotizacion->numero }}</span>
            </div>
            <div class="row">
                <span class="label">Fecha:</span>
                <span>{{ $cotizacion->fecha->format('d/m/Y H:i') }}</span>
            </div>
            <div class="row">
                <span class="label">Válida:</span>
                <span>{{ $cotizacion->fecha_validez->format('d/m/Y') }}</span>
            </div>
            <div class="row">
                <span class="label">Estado:</span>
                <span>{{ strtoupper($cotizacion->estado_label) }}</span>
            </div>
        </section>

        <div class="separator"></div>

        <section class="ticket-info" aria-label="Información del cliente">
            <div><strong>Cliente:</strong> {{ $cotizacion->cliente?->nombre ?? 'N/A' }}</div>
            @if($cotizacion->cliente?->rnc_cedula)
                <div><strong>RNC:</strong> {{ $cotizacion->cliente->rnc_cedula }}</div>
            @endif
            @if($cotizacion->cliente?->telefono)
                <div><strong>Tel:</strong> {{ $cotizacion->cliente->telefono }}</div>
            @endif
            @if($cotizacion->cliente?->email)
                <div class="text-muted">{{ $cotizacion->cliente->email }}</div>
            @endif
        </section>

        <div class="separator"></div>

        <section class="items" aria-label="Productos">
            @foreach($cotizacion->items as $item)
                <div class="item">
                    <div class="item-name">{{ $item->nombre }}</div>
                    @if($item->codigo)
                        <div class="text-muted" style="font-size: 0.85em;">{{ $item->codigo }}</div>
                    @endif
                    <div class="item-detail">
                        <span>{{ $item->cantidad }} x RD${{ number_format($item->precio_unitario, 2) }}</span>
                        <span>RD${{ number_format($item->subtotal, 2) }}</span>
                    </div>
                    @if($item->descuento > 0)
                        <div class="item-subtotal">
                            <span>Desc:</span>
                            <span>-RD${{ number_format($item->descuento, 2) }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </section>

        <div class="separator-double"></div>

        <section class="totals" aria-label="Totales">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>RD${{ number_format($cotizacion->subtotal, 2) }}</span>
            </div>
            @if($cotizacion->descuento > 0)
                <div class="total-row">
                    <span>Descuento:</span>
                    <span>-RD${{ number_format($cotizacion->descuento, 2) }}</span>
                </div>
            @endif
            <div class="total-row">
                <span>ITBIS (18%):</span>
                <span>RD${{ number_format($cotizacion->itbis, 2) }}</span>
            </div>
            <div class="total-row grand">
                <span>TOTAL:</span>
                <span>RD${{ number_format($cotizacion->total, 2) }}</span>
            </div>
        </section>

        @if($cotizacion->notas)
            <div class="separator"></div>
            <section class="ticket-info" aria-label="Notas">
                <strong>Notas:</strong>
                <p style="margin-top: 3px;">{{ $cotizacion->notas }}</p>
            </section>
        @endif

        @if($cotizacion->condiciones)
            <div class="separator"></div>
            <section class="ticket-info" aria-label="Términos y condiciones">
                <strong>Términos:</strong>
                <p style="margin-top: 3px; font-size: 0.9em;">{{ $cotizacion->condiciones }}</p>
            </section>
        @endif

        <footer class="footer">
            <p><strong>Esta cotización es válida hasta</strong></p>
            <p style="font-size: 1.1em; margin: 3px 0;">{{ $cotizacion->fecha_validez->format('d/m/Y') }}</p>
            <p style="margin-top: 5px;">Gracias por su preferencia</p>
            <p class="text-muted" style="margin-top: 5px;">Impreso: {{ now()->format('d/m/Y H:i') }}</p>
        </footer>
    </article>

    @if($autoPrint)
        <div class="actions" role="toolbar" aria-label="Acciones de impresión">
            <button onclick="window.print()" aria-label="Imprimir ticket">
                🖨️ Imprimir
            </button>
            <a href="javascript:window.close()" class="btn-secondary" role="button" aria-label="Cerrar ventana">
                Cerrar
            </a>
        </div>

        <script>
            // Auto-imprimir al cargar
            window.addEventListener('load', function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            });
        </script>
    @endif
</body>
</html>
