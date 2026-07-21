<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Formulario 14-14 - {{ $mesNombre }} {{ $anio }}</title>
    <style>
        @page {
            margin: 2cm 1.5cm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #1e293b;
            line-height: 1.4;
        }
        /* ── Header Empresa ── */
        .header-empresa {
            text-align: center;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1e293b;
        }
        .header-empresa .nombre {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-empresa .datos {
            font-size: 8px;
            color: #64748b;
            margin-top: 2px;
        }
        /* ── Título Formulario ── */
        .titulo-form {
            text-align: center;
            margin: 14px 0 4px;
        }
        .titulo-form h1 {
            font-size: 16px;
            font-weight: 800;
            color: #b91c1c;
            margin: 0;
            letter-spacing: 2px;
        }
        .titulo-form h2 {
            font-size: 10px;
            font-weight: 600;
            color: #475569;
            margin: 4px 0 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .titulo-form .periodo {
            font-size: 9px;
            color: #64748b;
            margin-top: 4px;
        }
        /* ── Datos Contribuyente ── */
        .contribuyente-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            margin: 12px 0;
        }
        .contribuyente-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .contribuyente-box td {
            padding: 3px 6px;
            font-size: 8.5px;
            vertical-align: top;
        }
        .contribuyente-box .label {
            font-weight: 700;
            color: #475569;
            white-space: nowrap;
            width: 130px;
        }
        .contribuyente-box .separator {
            border-right: 1px solid #e2e8f0;
        }
        /* ── Resúmenes ── */
        .resumen-section {
            margin: 14px 0;
        }
        .resumen-section h3 {
            font-size: 10px;
            font-weight: 700;
            color: #ffffff;
            background: #1e293b;
            padding: 5px 10px;
            margin: 0 0 6px;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .resumen-section h3.itbis {
            background: #1e40af;
        }
        .resumen-section h3.isr {
            background: #b91c1c;
        }
        .resumen-totales {
            width: 100%;
            border-collapse: collapse;
        }
        .resumen-totales td {
            padding: 5px 8px;
            font-size: 8.5px;
            border-bottom: 1px solid #e2e8f0;
        }
        .resumen-totales .lbl {
            font-weight: 600;
            color: #475569;
        }
        .resumen-totales .val {
            text-align: right;
            font-weight: 700;
            font-family: 'DejaVu Sans Mono', monospace;
        }
        .resumen-totales .total-final {
            background: #f1f5f9;
            font-weight: 800;
            font-size: 9.5px;
            border-top: 2px solid #1e293b;
        }
        /* ── Tabla Detalle ── */
        table.detalle {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 7.5px;
        }
        table.detalle thead th {
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 6px 4px;
            border-bottom: 2px solid #cbd5e1;
            text-align: left;
            white-space: nowrap;
        }
        table.detalle thead th.text-right {
            text-align: right;
        }
        table.detalle tbody td {
            padding: 4px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        table.detalle tbody td.text-right {
            text-align: right;
            font-family: 'DejaVu Sans Mono', monospace;
        }
        table.detalle tbody tr:nth-child(even) {
            background: #fafbfc;
        }
        table.detalle tfoot td {
            padding: 6px 4px;
            font-weight: 800;
            background: #f1f5f9;
            border-top: 2px solid #1e293b;
            font-size: 8px;
        }
        table.detalle tfoot td.text-right {
            text-align: right;
            font-family: 'DejaVu Sans Mono', monospace;
        }
        /* ── Declaración Jurada ── */
        .declaracion-jurada {
            margin: 20px 0 10px;
            padding: 12px;
            border: 1px dashed #94a3b8;
            border-radius: 6px;
            background: #fefce8;
        }
        .declaracion-jurada h4 {
            font-size: 10px;
            font-weight: 700;
            color: #854d0e;
            margin: 0 0 6px;
            text-transform: uppercase;
        }
        .declaracion-jurada p {
            font-size: 8px;
            color: #475569;
            margin: 0;
            line-height: 1.5;
            text-align: justify;
        }
        /* ── Firmas ── */
        .firmas {
            margin: 30px 0 10px;
            page-break-inside: avoid;
        }
        .firmas .fila-firmas {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        .firmas .columna-firma {
            display: table-cell;
            text-align: center;
            vertical-align: bottom;
            padding: 0 20px;
        }
        .firmas .linea-firma {
            border-top: 1px solid #1e293b;
            margin: 0 0 4px;
            padding-top: 4px;
        }
        .firmas .nombre-firma {
            font-size: 8px;
            font-weight: 700;
            color: #1e293b;
        }
        .firmas .cargo-firma {
            font-size: 7px;
            color: #64748b;
        }
        /* ── Footer ── */
        .footer {
            position: fixed;
            bottom: 12px;
            width: 100%;
            text-align: center;
            color: #94a3b8;
            font-size: 6.5px;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
        /* ── Utilidades ── */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .mono { font-family: 'DejaVu Sans Mono', monospace; }
        .mt-2 { margin-top: 8px; }
    </style>
</head>
<body>

    {{-- Header Empresa --}}
    <div class="header-empresa">
        <div class="nombre">{{ $empresa['empresa_nombre'] ?? 'Mi Negocio S.R.L.' }}</div>
        <div class="datos">
            RNC: {{ $empresa['empresa_rnc'] ?? 'N/A' }}
            @if(!empty($empresa['empresa_telefono']))
                &nbsp;&bull;&nbsp; Tel: {{ $empresa['empresa_telefono'] }}
            @endif
            @if(!empty($empresa['empresa_direccion']))
                &nbsp;&bull;&nbsp; {{ $empresa['empresa_direccion'] }}
            @endif
        </div>
    </div>

    {{-- Título --}}
    <div class="titulo-form">
        <h1>FORMULARIO 14-14</h1>
        <h2>Declaración Mensual de Retenciones del Impuesto Sobre la Renta y del Impuesto al Valor Agregado</h2>
        <div class="periodo">Período: {{ ucfirst($mesNombre) }} {{ $anio }} &nbsp;|&nbsp; Código de Período: {{ $resumen['periodo'] }}</div>
    </div>

    {{-- Datos del Contribuyente --}}
    <div class="contribuyente-box">
        <table>
            <tr>
                <td class="label">Contribuyente:</td>
                <td class="separator">{{ $empresa['empresa_nombre'] ?? 'N/A' }}</td>
                <td class="label">RNC:</td>
                <td>{{ $empresa['empresa_rnc'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Dirección Fiscal:</td>
                <td colspan="3">{{ $empresa['empresa_direccion'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Teléfono:</td>
                <td>{{ $empresa['empresa_telefono'] ?? 'N/A' }}</td>
                <td class="label">Correo Electrónico:</td>
                <td>{{ $empresa['empresa_email'] ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    {{-- ════════════════════════════════════════════ --}}
    {{-- SECCIÓN 1: ITBIS COBRADO EN VENTAS          --}}
    {{-- ════════════════════════════════════════════ --}}
    <div class="resumen-section">
        <h3 class="itbis">Sección I — ITBIS Cobrado en Ventas</h3>
        <table class="resumen-totales">
            <tr>
                <td class="lbl">Total ITBIS cobrado en ventas del período:</td>
                <td class="val mono">{{ number_format($resumen['itbis_ventas']['total_retenido'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="lbl">Cantidad de ventas con retención:</td>
                <td class="val">{{ $resumen['itbis_ventas']['cantidad_ventas'] ?? 0 }}</td>
            </tr>
        </table>
    </div>

    {{-- ════════════════════════════════════════════ --}}
    {{-- SECCIÓN 2: ITBIS RETENIDO EN COMPRAS        --}}
    {{-- ════════════════════════════════════════════ --}}
    <div class="resumen-section">
        <h3 class="itbis">Sección II — ITBIS Retenido en Compras</h3>
        <table class="resumen-totales">
            <tr>
                <td class="lbl">Total ITBIS retenido en compras del período:</td>
                <td class="val mono">{{ number_format($resumen['itbis_compras']['total_retenido'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="lbl">Cantidad de compras con retención:</td>
                <td class="val">{{ $resumen['itbis_compras']['cantidad_compras'] ?? 0 }}</td>
            </tr>
        </table>

        @if(!empty($resumen['itbis_compras']['detalles']))
        <table class="detalle mt-2">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>RNC Proveedor</th>
                    <th>Proveedor</th>
                    <th class="text-right">Nº Compra</th>
                    <th class="text-right">ITBIS Retenido</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resumen['itbis_compras']['detalles'] as $idx => $d)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $d['fecha'] }}</td>
                    <td>{{ $d['rnc'] ?? 'N/A' }}</td>
                    <td>{{ $d['proveedor'] ?? 'N/A' }}</td>
                    <td class="text-right mono">{{ str_pad($d['compra_id'], 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="text-right mono">{{ number_format($d['itbis_retenido'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right">TOTAL SECCIÓN II:</td>
                    <td class="text-right mono">{{ number_format($resumen['itbis_compras']['total_retenido'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
        @else
        <p style="text-align:center;color:#94a3b8;font-style:italic;margin:8px 0;">Sin compras con retención de ITBIS en este período.</p>
        @endif
    </div>

    {{-- ════════════════════════════════════════════ --}}
    {{-- SECCIÓN 3: ISR RETENIDO EN COMPRAS          --}}
    {{-- ════════════════════════════════════════════ --}}
    <div class="resumen-section">
        <h3 class="isr">Sección III — ISR Retenido en Compras</h3>
        <table class="resumen-totales">
            <tr>
                <td class="lbl">Total ISR retenido en compras del período:</td>
                <td class="val mono">{{ number_format($resumen['isr_compras']['total_retenido'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="lbl">Cantidad de compras con retención ISR:</td>
                <td class="val">{{ $resumen['isr_compras']['cantidad_compras'] ?? 0 }}</td>
            </tr>
        </table>

        @if(!empty($resumen['isr_compras']['detalles']))
        <table class="detalle mt-2">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>RNC Proveedor</th>
                    <th>Proveedor</th>
                    <th class="text-right">Nº Compra</th>
                    <th class="text-right">ISR Retenido</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resumen['isr_compras']['detalles'] as $idx => $d)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $d['fecha'] }}</td>
                    <td>{{ $d['rnc'] ?? 'N/A' }}</td>
                    <td>{{ $d['proveedor'] ?? 'N/A' }}</td>
                    <td class="text-right mono">{{ str_pad($d['compra_id'], 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="text-right mono">{{ number_format($d['isr_retenido'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right">TOTAL SECCIÓN III:</td>
                    <td class="text-right mono">{{ number_format($resumen['isr_compras']['total_retenido'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
        @else
        <p style="text-align:center;color:#94a3b8;font-style:italic;margin:8px 0;">Sin compras con retención de ISR en este período.</p>
        @endif
    </div>

    {{-- ════════════════════════════════════════════ --}}
    {{-- TOTALES GENERALES                            --}}
    {{-- ════════════════════════════════════════════ --}}
    <div class="resumen-section" style="margin-top:16px;">
        <h3 style="background:#1e293b;">Totales Generales del Período</h3>
        <table class="resumen-totales">
            <tr>
                <td class="lbl">A — Total ITBIS cobrado en ventas:</td>
                <td class="val mono">{{ number_format($resumen['itbis_ventas']['total_retenido'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="lbl">B — Total ITBIS retenido en compras:</td>
                <td class="val mono">{{ number_format($resumen['itbis_compras']['total_retenido'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="lbl">C — Total ISR retenido en compras:</td>
                <td class="val mono">{{ number_format($resumen['isr_compras']['total_retenido'] ?? 0, 2) }}</td>
            </tr>
            <tr class="total-final">
                <td class="lbl">D — TOTAL GENERAL RETENCIONES (B + C):</td>
                <td class="val mono" style="color:#b91c1c;">{{ number_format(($resumen['itbis_compras']['total_retenido'] ?? 0) + ($resumen['isr_compras']['total_retenido'] ?? 0), 2) }}</td>
            </tr>
        </table>
    </div>

    {{-- Declaración Jurada --}}
    <div class="declaracion-jurada">
        <h4><i class="bi bi-patch-check-fill"></i> Declaración Jurada</h4>
        <p>
            Bajo juramento, declaramos que los datos consignados en el presente Formulario 14-14 corresponden
            fielmente a las retenciones del Impuesto sobre la Renta y del Impuesto al Valor Agregado practicadas
            durante el período {{ ucfirst($mesNombre) }} de {{ $anio }}, conforme a lo establecido en el
            Artículo 260 del Código Tributario, sus reglamentos y resoluciones de la Dirección General de Impuestos
            Internos (DGII).
        </p>
        <p style="margin-top:4px;">
            Nos comprometemos a conservar la documentación soporte relacionada con estas retenciones durante el
            plazo establecido por la legislación tributaria vigente y a suministrarla cuando sea requerida por
            la Administración Tributaria.
        </p>
    </div>

    {{-- Firmas --}}
    <div class="firmas">
        <div class="fila-firmas">
            <div class="columna-firma">
                <div class="linea-firma" style="width:80%;margin:0 auto;"></div>
                <div class="nombre-firma">_______________________________</div>
                <div class="cargo-firma">Representante Legal</div>
            </div>
            <div class="columna-firma">
                <div class="linea-firma" style="width:80%;margin:0 auto;"></div>
                <div class="nombre-firma">_______________________________</div>
                <div class="cargo-firma">Contador / Director Financiero</div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        Generado el {{ now()->format('d/m/Y h:i A') }} &nbsp;|&nbsp; Sistema de Facturación Electrónica RD &nbsp;|&nbsp; Documento generado automáticamente
    </div>

</body>
</html>
