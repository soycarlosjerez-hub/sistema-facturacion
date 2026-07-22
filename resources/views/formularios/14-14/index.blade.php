@extends('layouts.app')
@section('title', 'Formulario 14-14 — Retenciones')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
    /* ── Header Rojo ITBIS/ISR ── */
    .premium-header.form-1414 {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 30%, #991b1b 60%, #7f1d1d 100%) !important;
        background-size: 300% 300% !important;
        animation: premiumGradientShift 6s ease infinite !important;
        box-shadow: 0 8px 32px rgba(185,28,28,.3) !important;
    }
    .premium-header.form-1414 .bubble {
        background: rgba(255,255,255,.08);
    }
    .premium-header.form-1414 .btn-outline-light {
        color: #fff !important;
        border-color: rgba(255,255,255,.5) !important;
        background: rgba(255,255,255,.1) !important;
    }
    .premium-header.form-1414 .btn-outline-light:hover {
        background: rgba(255,255,255,.25) !important;
        border-color: #fff !important;
    }

    /* ── Stat Cards ── */
    .stat-card-1414 {
        background: rgba(255,255,255,.85);
        backdrop-filter: blur(20px);
        border-radius: 1.2rem;
        border: 1px solid rgba(255,255,255,.8);
        box-shadow: 0 4px 24px rgba(0,0,0,.04);
        transition: transform .2s, box-shadow .2s;
        overflow: hidden;
        animation: premiumSlideUp .5s ease both;
    }
    .stat-card-1414:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(0,0,0,.1);
    }
    .stat-card-1414 .stat-icon {
        width: 48px; height: 48px;
        border-radius: 1rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
    }
    .stat-card-1414 .stat-label {
        font-size: .65rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 4px;
    }
    .stat-card-1414 .stat-value {
        font-size: 1.45rem;
        font-weight: 800;
        line-height: 1.2;
    }
    .stat-card-1414 .stat-sub {
        font-size: .75rem;
        color: #94a3b8;
        margin-top: 2px;
    }
    body.dark-mode .stat-card-1414 {
        background: rgba(15,23,42,.75);
        border-color: rgba(255,255,255,.06);
    }
    body.dark-mode .stat-card-1414 .stat-label { color: #94a3b8; }
    body.dark-mode .stat-card-1414 .stat-value { color: #f1f5f9; }
    body.dark-mode .stat-card-1414 .stat-sub { color: #64748b; }

    /* ── Accent Colors ── */
    .accent-itbis-cobrado { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .accent-itbis-retenido { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .accent-isr-retenido   { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .accent-total-ret      { background: linear-gradient(135deg, #f59e0b, #d97706); }

    /* ── Filter Card ── */
    .filter-card-1414 .form-control:focus,
    .filter-card-1414 .form-select:focus {
        border-color: #dc2626 !important;
        box-shadow: 0 0 0 3px rgba(220,38,38,.15) !important;
    }
    .filter-card-1414 .btn-primary {
        background: linear-gradient(135deg, #dc2626, #b91c1c) !important;
        border: none !important;
    }
    .filter-card-1414 .btn-primary:hover {
        background: linear-gradient(135deg, #b91c1c, #991b1b) !important;
        box-shadow: 0 6px 20px rgba(220,38,38,.4) !important;
    }

    /* ── Table Overrides ── */
    #proveedoresTable thead th {
        border-bottom: 2px solid #e2e8f0;
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #64748b;
        padding: 14px 12px;
        background: #f8fafc;
    }
    #proveedoresTable tbody td {
        padding: 12px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: .88rem;
    }
    #proveedoresTable tbody tr { transition: background .15s; }
    #proveedoresTable tbody tr:hover { background: rgba(220,38,38,.04); }
    #proveedoresTable tfoot td {
        padding: 14px 12px;
        border-top: 2px solid #e2e8f0;
        background: #f8fafc;
        font-weight: 800;
    }
    body.dark-mode #proveedoresTable thead th {
        background: rgba(15,23,42,.6);
        border-bottom-color: #334155;
        color: #94a3b8;
    }
    body.dark-mode #proveedoresTable tbody td {
        border-bottom-color: #1e293b;
        color: #cbd5e1;
    }
    body.dark-mode #proveedoresTable tbody tr:hover {
        background: rgba(220,38,38,.08);
    }
    body.dark-mode #proveedoresTable tfoot td {
        background: rgba(15,23,42,.6);
        border-top-color: #334155;
        color: #f1f5f9;
    }

    /* ── Empty State ── */
    .empty-state-1414 {
        text-align: center;
        padding: 3rem 1rem;
        color: #94a3b8;
    }
    .empty-state-1414 i { font-size: 3rem; margin-bottom: .75rem; }

    @media (max-width: 575.98px) {
        .filter-card-1414 .form-control,
        .filter-card-1414 .form-select { min-width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 premium-page">

    {{-- ═══ HEADER PREMIUM ═══ }}
    <div class="premium-header form-1414 d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative w-100" style="z-index:2;">
            <div>
                <h2 class="fw-bold mb-1">
                    <i class="bi bi-receipt-cutoff text-white me-2"></i>
                    Formulario 14-14
                </h2>
                <p class="mb-0" style="color:rgba(255,255,255,.8);">
                    Declaración Mensual de Retenciones ITBIS e ISR &middot;
                    {{ $mesNombre }} {{ $anio }}
                </p>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <a href="{{ route('formulario.14-14.csv', ['mes' => $mes, 'anio' => $anio]) }}"
                   class="btn btn-success rounded-pill"
                   title="Exportar CSV">
                    <i class="bi bi-filetype-csv me-1"></i> CSV
                </a>
                <a href="{{ route('formulario.14-14.pdf', ['mes' => $mes, 'anio' => $anio]) }}"
                   class="btn btn-danger rounded-pill"
                   title="Exportar PDF">
                    <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                </a>
                <div class="premium-avatar-circle ms-2">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ FILTROS ═══ }}
    <div class="premium-card filter-card-1414 mb-4">
        <div class="card-accent red"></div>
        <div class="px-4 py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label small fw-semibold mb-0">Mes</label>
                    <select name="mes" class="form-select" style="min-width:120px;">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $mes == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create($anio, $i, 1)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label small fw-semibold mb-0">Año</label>
                    <select name="anio" class="form-select" style="min-width:100px;">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ $anio == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary rounded-pill">
                        <i class="bi bi-funnel me-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══ TARJETAS DE RESUMEN ═══ }}
    <div class="row g-4 mb-4">
        {{-- ITBIS Retenido en Compras --}}
        <div class="col-sm-6 col-xl-3" style="animation-delay:.05s;">
            <div class="stat-card-1414 p-3 h-100">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="stat-icon accent-itbis-retenido text-white">
                        <i class="bi bi-arrow-return-left"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="stat-label">ITBIS Retenido (Compras)</div>
                    </div>
                </div>
                <div class="stat-value text-purple">
                    RD$ {{ number_format($resumen['itbis_compras']['total_retenido'] ?? 0, 2) }}
                </div>
                <div class="stat-sub">
                    {{ $resumen['itbis_compras']['cantidad_compras'] ?? 0 }} compra(s) con retención
                </div>
            </div>
        </div>

        {{-- ISR Retenido en Compras --}}
        <div class="col-sm-6 col-xl-3" style="animation-delay:.1s;">
            <div class="stat-card-1414 p-3 h-100">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="stat-icon accent-isr-retenido text-white">
                        <i class="bi bi-percent"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="stat-label">ISR Retenido (Compras)</div>
                    </div>
                </div>
                <div class="stat-value text-danger">
                    RD$ {{ number_format($resumen['isr_compras']['total_retenido'] ?? 0, 2) }}
                </div>
                <div class="stat-sub">
                    {{ $resumen['isr_compras']['cantidad_compras'] ?? 0 }} compra(s) con retención
                </div>
            </div>
        </div>

        {{-- ITBIS Cobrado en Ventas --}}
        <div class="col-sm-6 col-xl-3" style="animation-delay:.15s;">
            <div class="stat-card-1414 p-3 h-100">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="stat-icon accent-itbis-cobrado text-white">
                        <i class="bi bi-cart-plus"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="stat-label">ITBIS Cobrado (Ventas)</div>
                    </div>
                </div>
                <div class="stat-value text-primary">
                    RD$ {{ number_format($resumen['itbis_ventas']['total_retenido'] ?? 0, 2) }}
                </div>
                <div class="stat-sub">
                    {{ $resumen['itbis_ventas']['cantidad_ventas'] ?? 0 }} venta(s) con retención
                </div>
            </div>
        </div>

        {{-- Total Retenido --}}
        <div class="col-sm-6 col-xl-3" style="animation-delay:.2s;">
            <div class="stat-card-1414 p-3 h-100">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="stat-icon accent-total-ret text-white">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="stat-label">Total Retenido</div>
                    </div>
                </div>
                <div class="stat-value text-warning">
                    RD$ {{ number_format(($resumen['itbis_compras']['total_retenido'] ?? 0) + ($resumen['isr_compras']['total_retenido'] ?? 0), 2) }}
                </div>
                <div class="stat-sub">
                    ITBIS + ISR combinados
                </div>
            </div>
        </div>
    </div>

  
    <div class="premium-card overflow-hidden" style="animation-delay:.25s;">
        <div class="premium-card-title">
            <i class="icon-red bi bi-people"></i>
            Detalle de Retenciones por Proveedor
        </div>
        <div class="premium-card-subtitle">
            Compras del período {{ $mesNombre }} {{ $anio }} con retenciones aplicadas
        </div>
        <div class="table-responsive px-3 pb-3">
            <table id="proveedoresTable" class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">#</th>
                        <th>RNC/Cédula</th>
                        <th>Proveedor</th>
                        <th class="text-end">Compras</th>
                        <th class="text-end">ITBIS Retenido</th>
                        <th class="text-end">ISR Retenido</th>
                        <th class="text-end pe-4">Total Retenido</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Agrupar datos por proveedor desde los detalles
                        $agrupado = collect();
                        
                        if (!empty($resumen['itbis_compras']['detalles'])) {
                            foreach ($resumen['itbis_compras']['detalles'] as $d) {
                                $key = $d['rnc'] ?? 'sin-rnc';
                                if (!$agrupado->has($key)) {
                                    $agrupado[$key] = [
                                        'rnc'       => $d['rnc'] ?? 'N/A',
                                        'nombre'    => $d['proveedor'] ?? 'N/A',
                                        'itbis'     => 0,
                                        'isr'       => 0,
                                        'cantidad'  => 0,
                                        '_ids'      => [],
                                    ];
                                }
                                $agrupado[$key]['itbis'] += $d['itbis_retenido'] ?? 0;
                                $cid = $d['compra_id'] ?? null;
                                if ($cid && !in_array($cid, $agrupado[$key]['_ids'])) {
                                    $agrupado[$key]['_ids'][] = $cid;
                                    $agrupado[$key]['cantidad']++;
                                } elseif (!$cid) {
                                    $agrupado[$key]['cantidad']++;
                                }
                            }
                        }
                        if (!empty($resumen['isr_compras']['detalles'])) {
                            foreach ($resumen['isr_compras']['detalles'] as $d) {
                                $key = $d['rnc'] ?? 'sin-rnc';
                                if (!$agrupado->has($key)) {
                                    $agrupado[$key] = [
                                        'rnc'       => $d['rnc'] ?? 'N/A',
                                        'nombre'    => $d['proveedor'] ?? 'N/A',
                                        'itbis'     => 0,
                                        'isr'       => 0,
                                        'cantidad'  => 0,
                                        '_ids'      => [],
                                    ];
                                }
                                $agrupado[$key]['isr'] += $d['isr_retenido'] ?? 0;
                                $cid = $d['compra_id'] ?? null;
                                if ($cid && !in_array($cid, $agrupado[$key]['_ids'])) {
                                    $agrupado[$key]['_ids'][] = $cid;
                                    $agrupado[$key]['cantidad']++;
                                } elseif (!$cid) {
                                    // Sin compra_id, contar solo si isr > 0 (para no duplicar)
                                    if (($d['isr_retenido'] ?? 0) > 0) {
                                        $agrupado[$key]['cantidad']++;
                                    }
                                }
                            }
                        }
                        // Limpiar campo interno _ids antes de renderizar
                        $agrupado = $agrupado->map(fn($p) => collect($p)->except('_ids')->all())->values();
                    @endphp

                    @if($agrupado->isNotEmpty())
                        @foreach($agrupado as $idx => $prov)
                        <tr>
                            <td class="ps-4">{{ $idx + 1 }}</td>
                            <td><span class="font-monospace small">{{ $prov['rnc'] }}</span></td>
                            <td>
                                <span class="fw-semibold small">{{ $prov['nombre'] }}</span>
                            </td>
                            <td class="text-end">
                                <span class="badge rounded-pill bg-info bg-opacity-10 text-info fw-semibold">
                                    {{ $prov['cantidad'] }}
                                </span>
                            </td>
                            <td class="text-end text-purple fw-semibold">
                                RD$ {{ number_format($prov['itbis'], 2) }}
                            </td>
                            <td class="text-end text-danger fw-semibold">
                                RD$ {{ number_format($prov['isr'], 2) }}
                            </td>
                            <td class="text-end pe-4 fw-bold">
                                RD$ {{ number_format($prov['itbis'] + $prov['isr'], 2) }}
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7">
                                <div class="empty-state-1414 py-5">
                                    <i class="bi bi-inbox d-block"></i>
                                    <p class="mt-2 mb-0">No hay retenciones registradas para el período seleccionado.</p>
                                    <small>Seleccione otro mes o año, o registre compras con retenciones.</small>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>

                @if($agrupado->isNotEmpty())
                <tr class="table-light fw-bold">
                    <th class="ps-4 py-3 text-end text-uppercase small">Totales</th>
                    <th class="py-3"></th>
                    <th class="py-3"></th>
                    <td class="text-end py-3">
                        {{ $agrupado->sum('cantidad') }}
                    </td>
                    <td class="text-end py-3 text-purple">
                        RD$ {{ number_format($agrupado->sum('itbis'), 2) }}
                    </td>
                    <td class="text-end py-3 text-danger">
                        RD$ {{ number_format($agrupado->sum('isr'), 2) }}
                    </td>
                    <td class="text-end pe-4 py-3">
                        RD$ {{ number_format($agrupado->sum(fn($p) => $p['itbis'] + $p['isr']), 2) }}
                    </td>
                </tr>
                @endif
            </table>
        </div>
    </div>

</div>
<!-- Spacing -->
<div class="mb-5"></div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#proveedoresTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        columnDefs: [
            { orderable: false, className: 'text-end', targets: [3, 4, 5, 6] }
        ],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
@endpush
