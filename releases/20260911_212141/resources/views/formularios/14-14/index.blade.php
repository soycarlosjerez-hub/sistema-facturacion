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
        padding: 2rem 2.5rem !important;
        border-radius: 1.5rem !important;
        overflow: hidden !important;
        position: relative !important;
    }
    .premium-header.form-1414 .bubble {
        background: radial-gradient(circle, rgba(255,255,255,.1) 0%, transparent 70%);
        width: 200px; height: 200px;
        border-radius: 50%;
        position: absolute;
        animation: bubbleFloat 8s ease-in-out infinite;
    }
    .premium-header.form-1414 .bubble:nth-child(1) { top: -50px; left: 10%; animation-delay: 0s; }
    .premium-header.form-1414 .bubble:nth-child(2) { bottom: -80px; right: 15%; animation-delay: 2s; width: 150px; height: 150px; }
    .premium-header.form-1414 .bubble:nth-child(3) { top: 50%; left: 50%; animation-delay: 4s; width: 100px; height: 100px; }
    
    @keyframes bubbleFloat {
        0%, 100% { transform: translateY(0) scale(1); opacity: 0.6; }
        50% { transform: translateY(-20px) scale(1.1); opacity: 0.9; }
    }
    
    .premium-header.form-1414 .btn-outline-light {
        color: #fff !important;
        border-color: rgba(255,255,255,.5) !important;
        background: rgba(255,255,255,.1) !important;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }
    .premium-header.form-1414 .btn-outline-light:hover {
        background: rgba(255,255,255,.25) !important;
        border-color: #fff !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,.2);
    }
    
    .header-badge {
        background: rgba(255,255,255,.2);
        backdrop-filter: blur(10px);
        border-radius: 2rem;
        padding: 0.4rem 1rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: #fff;
        border: 1px solid rgba(255,255,255,.3);
        display: inline-block;
        margin-bottom: 0.5rem;
    }

    /* ── Stat Cards ── */
    .stat-card-1414 {
        background: rgba(255,255,255,.9);
        backdrop-filter: blur(20px);
        border-radius: 1.5rem;
        border: 1px solid rgba(255,255,255,.8);
        box-shadow: 0 8px 32px rgba(0,0,0,.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        animation: premiumSlideUp .6s ease both;
        position: relative;
    }
    .stat-card-1414::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--card-accent);
    }
    .stat-card-1414:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 60px rgba(0,0,0,.12);
    }
    .stat-card-1414 .stat-icon {
        width: 56px; height: 56px;
        border-radius: 1.2rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,.15);
        flex-shrink: 0;
    }
    .stat-card-1414 .stat-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 6px;
    }
    .stat-card-1414 .stat-value {
        font-size: 1.6rem;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -0.5px;
    }
    .stat-card-1414 .stat-sub {
        font-size: 0.78rem;
        color: #94a3b8;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .stat-card-1414 .stat-sub i {
        font-size: 0.7rem;
    }
    body.dark-mode .stat-card-1414 {
        background: rgba(15,23,42,.8);
        border-color: rgba(255,255,255,.08);
        box-shadow: 0 8px 32px rgba(0,0,0,.3);
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
    .filter-card-1414 {
        background: rgba(255,255,255,.9);
        backdrop-filter: blur(20px);
        border-radius: 1.5rem;
        border: 1px solid rgba(255,255,255,.8);
        box-shadow: 0 4px 24px rgba(0,0,0,.06);
        overflow: hidden;
    }
    .filter-card-1414 .form-control:focus,
    .filter-card-1414 .form-select:focus {
        border-color: #dc2626 !important;
        box-shadow: 0 0 0 3px rgba(220,38,38,.15) !important;
    }
    .filter-card-1414 .btn-primary {
        background: linear-gradient(135deg, #dc2626, #b91c1c) !important;
        border: none !important;
        box-shadow: 0 4px 12px rgba(220,38,38,.3);
        transition: all 0.3s ease;
    }
    .filter-card-1414 .btn-primary:hover {
        background: linear-gradient(135deg, #b91c1c, #991b1b) !important;
        box-shadow: 0 6px 20px rgba(220,38,38,.5) !important;
        transform: translateY(-2px);
    }

    /* ── Table Styling ── */
    .table-container-1414 {
        background: rgba(255,255,255,.9);
        backdrop-filter: blur(20px);
        border-radius: 1.5rem;
        border: 1px solid rgba(255,255,255,.8);
        box-shadow: 0 4px 24px rgba(0,0,0,.06);
        overflow: hidden;
    }
    body.dark-mode .table-container-1414 {
        background: rgba(15,23,42,.8);
        border-color: rgba(255,255,255,.08);
    }
    #proveedoresTable thead th {
        border-bottom: 2px solid #e2e8f0;
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #64748b;
        padding: 16px 14px;
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        font-weight: 700;
        white-space: nowrap;
    }
    #proveedoresTable tbody td {
        padding: 14px 14px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }
    #proveedoresTable tbody tr { transition: all 0.2s ease; }
    #proveedoresTable tbody tr:hover { 
        background: rgba(220,38,38,.06);
        transform: scale(1.002);
    }
    #proveedoresTable .totales-row {
        background: linear-gradient(135deg, #fef2f2, #fee2e2) !important;
        font-weight: 800;
    }
    #proveedoresTable .totales-row td {
        padding: 16px 14px;
        border-top: 2px solid #fecaca;
        font-size: 0.95rem;
        color: #991b1b;
    }
    body.dark-mode #proveedoresTable thead th {
        background: linear-gradient(135deg, rgba(15,23,42,.8), rgba(30,41,59,.8));
        border-bottom-color: #334155;
        color: #94a3b8;
    }
    body.dark-mode #proveedoresTable tbody td {
        border-bottom-color: #1e293b;
        color: #cbd5e1;
    }
    body.dark-mode #proveedoresTable tbody tr:hover {
        background: rgba(220,38,38,.1);
    }
    body.dark-mode #proveedoresTable .totales-row {
        background: linear-gradient(135deg, rgba(127,29,29,.2), rgba(153,27,27,.15)) !important;
    }
    body.dark-mode #proveedoresTable .totales-row td {
        border-top-color: #7f1d1d;
        color: #fecaca;
    }

    /* ── Provider Info ── */
    .provider-name {
        font-weight: 600;
        color: #1e293b;
        max-width: 250px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    body.dark-mode .provider-name { color: #e2e8f0; }
    
    .provider-rnc {
        font-family: 'Courier New', monospace;
        font-size: 0.82rem;
        background: #f1f5f9;
        padding: 4px 10px;
        border-radius: 0.5rem;
        color: #475569;
        font-weight: 600;
    }
    body.dark-mode .provider-rnc {
        background: rgba(30,41,59,.8);
        color: #94a3b8;
    }

    /* ── Amount Badges ── */
    .amount-cell {
        font-weight: 700;
        font-size: 0.92rem;
    }
    .amount-itbis { color: #7c3aed; }
    .amount-isr { color: #dc2626; }
    .amount-total { color: #d97706; font-size: 1rem; }

    /* ── Purchase Count Badge ── */
    .purchase-count {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
        padding: 6px 14px;
        border-radius: 2rem;
        font-weight: 700;
        font-size: 0.85rem;
        display: inline-block;
        min-width: 40px;
        text-align: center;
    }
    body.dark-mode .purchase-count {
        background: rgba(59,130,246,.2);
        color: #93c5fd;
    }

    /* ── Empty State ── */
    .empty-state-1414 {
        text-align: center;
        padding: 4rem 2rem;
        color: #94a3b8;
    }
    .empty-state-1414 i { 
        font-size: 4rem; 
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    .empty-state-1414 h5 {
        color: #64748b;
        font-weight: 600;
    }

    /* ── Export Buttons ── */
    .export-btn {
        padding: 0.6rem 1.2rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        border: none;
    }
    .export-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,.2);
    }

    @media (max-width: 575.98px) {
        .premium-header.form-1414 { padding: 1.5rem !important; }
        .filter-card-1414 .form-control,
        .filter-card-1414 .form-select { min-width: 100%; }
        .stat-card-1414 .stat-value { font-size: 1.3rem; }
        #proveedoresTable { font-size: 0.85rem; }
    }
</style>
@endpush

@section('content')
@php
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
            if ($cid) {
                if (!in_array($cid, $agrupado[$key]['_ids'])) {
                    $agrupado[$key]['_ids'][] = $cid;
                    $agrupado[$key]['cantidad']++;
                }
            } elseif (!in_array('sin-id', $agrupado[$key]['_ids'])) {
                $agrupado[$key]['_ids'][] = 'sin-id';
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
            if ($cid) {
                if (!in_array($cid, $agrupado[$key]['_ids'])) {
                    $agrupado[$key]['_ids'][] = $cid;
                    $agrupado[$key]['cantidad']++;
                }
            } elseif (!in_array('sin-id', $agrupado[$key]['_ids'])) {
                $agrupado[$key]['_ids'][] = 'sin-id';
                $agrupado[$key]['cantidad']++;
            }
        }
    }
    $agrupado = $agrupado->map(fn($p) => collect($p)->except('_ids')->all())->values();
@endphp
<div class="container-fluid px-4 premium-page">

    {{-- ═══ HEADER PREMIUM ═══ --}}
    <div class="premium-header form-1414 d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative w-100" style="z-index:2;">
            <div>
                <span class="header-badge">
                    <i class="bi bi-shield-check me-1"></i> Documento Oficial
                </span>
                <h2 class="fw-bold mb-1 text-white">
                    <i class="bi bi-receipt-cutoff me-2"></i>
                    Formulario 14-14
                </h2>
                <p class="mb-0 text-white-50 fs-6">
                    <i class="bi bi-calendar-event me-1"></i>
                    {{ $mesNombre }} {{ $anio }} &bull; Retenciones ITBIS e ISR
                </p>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap mt-3 mt-md-0">
                <a href="{{ route('formulario.14-14.csv', ['mes' => $mes, 'anio' => $anio]) }}"
                   class="export-btn btn btn-success text-white"
                   title="Exportar CSV">
                    <i class="bi bi-filetype-csv me-1"></i> CSV
                </a>
                <a href="{{ route('formulario.14-14.pdf', ['mes' => $mes, 'anio' => $anio]) }}"
                   class="export-btn btn btn-danger text-white"
                   title="Exportar PDF">
                    <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                </a>
            </div>
        </div>
    </div>

    {{-- ═══ FILTROS ═══ --}}
    <div class="filter-card-1414 mb-4">
        <div class="card-accent red"></div>
        <div class="p-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-funnel-fill text-danger fs-5"></i>
                <h6 class="mb-0 fw-bold text-uppercase" style="font-size:0.75rem;letter-spacing:1px;color:#64748b;">
                    Filtros de Búsqueda
                </h6>
            </div>
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-auto">
                    <label class="form-label small fw-semibold mb-0 text-muted">
                        <i class="bi bi-calendar-month me-1"></i>Mes
                    </label>
                    <select name="mes" class="form-select form-select-sm" style="min-width:140px;">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $mes == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create($anio, $i, 1)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label small fw-semibold mb-0 text-muted">
                        <i class="bi bi-calendar-year me-1"></i>Año
                    </label>
                    <select name="anio" class="form-select form-select-sm" style="min-width:110px;">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ $anio == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary btn-sm rounded-pill px-4">
                        <i class="bi bi-search me-1"></i> Aplicar
                    </button>
                    <a href="{{ route('formulario.14-14.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill ms-1">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══ TARJETAS DE RESUMEN ═══ --}}
    <div class="row g-4 mb-4">
        {{-- ITBIS Retenido en Compras --}}
        <div class="col-sm-6 col-xl-3" style="animation-delay:.05s;">
            <div class="stat-card-1414 p-4 h-100" style="--card-accent: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="stat-icon accent-itbis-retenido text-white">
                        <i class="bi bi-arrow-return-left"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="stat-label">ITBIS Retenido</div>
                        <small class="text-muted" style="font-size:0.68rem;">Compras del período</small>
                    </div>
                </div>
                <div class="stat-value text-purple mb-2">
                    RD$ {{ number_format($resumen['itbis_compras']['total_retenido'] ?? 0, 2) }}
                </div>
                <div class="stat-sub text-muted">
                    <i class="bi bi-receipt"></i>
                    {{ $resumen['itbis_compras']['cantidad_compras'] ?? 0 }} compra(s) con retención
                </div>
            </div>
        </div>

        {{-- ISR Retenido en Compras --}}
        <div class="col-sm-6 col-xl-3" style="animation-delay:.1s;">
            <div class="stat-card-1414 p-4 h-100" style="--card-accent: linear-gradient(135deg, #ef4444, #dc2626);">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="stat-icon accent-isr-retenido text-white">
                        <i class="bi bi-percent"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="stat-label">ISR Retenido</div>
                        <small class="text-muted" style="font-size:0.68rem;">Compras del período</small>
                    </div>
                </div>
                <div class="stat-value text-danger mb-2">
                    RD$ {{ number_format($resumen['isr_compras']['total_retenido'] ?? 0, 2) }}
                </div>
                <div class="stat-sub text-muted">
                    <i class="bi bi-receipt"></i>
                    {{ $resumen['isr_compras']['cantidad_compras'] ?? 0 }} compra(s) con retención
                </div>
            </div>
        </div>

        {{-- ITBIS Cobrado en Ventas --}}
        <div class="col-sm-6 col-xl-3" style="animation-delay:.15s;">
            <div class="stat-card-1414 p-4 h-100" style="--card-accent: linear-gradient(135deg, #3b82f6, #2563eb);">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="stat-icon accent-itbis-cobrado text-white">
                        <i class="bi bi-cart-plus"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="stat-label">ITBIS Cobrado</div>
                        <small class="text-muted" style="font-size:0.68rem;">Ventas del período</small>
                    </div>
                </div>
                <div class="stat-value text-primary mb-2">
                    RD$ {{ number_format($resumen['itbis_ventas']['total_retenido'] ?? 0, 2) }}
                </div>
                <div class="stat-sub text-muted">
                    <i class="bi bi-basket"></i>
                    {{ $resumen['itbis_ventas']['cantidad_ventas'] ?? 0 }} venta(s) con retención
                </div>
            </div>
        </div>

        {{-- Total Retenido --}}
        <div class="col-sm-6 col-xl-3" style="animation-delay:.2s;">
            <div class="stat-card-1414 p-4 h-100" style="--card-accent: linear-gradient(135deg, #f59e0b, #d97706);">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="stat-icon accent-total-ret text-white">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="stat-label">Total Retenido</div>
                        <small class="text-muted" style="font-size:0.68rem;">ITBIS + ISR combinados</small>
                    </div>
                </div>
                <div class="stat-value text-warning mb-2">
                    RD$ {{ number_format(($resumen['itbis_compras']['total_retenido'] ?? 0) + ($resumen['isr_compras']['total_retenido'] ?? 0), 2) }}
                </div>
                <div class="stat-sub text-muted">
                    <i class="bi bi-graph-up-arrow"></i>
                    Acumulado del período
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ TABLA DE PROVEEDORES ═══ --}}
    <div class="table-container-1414 mb-5">
        <div class="p-4 pb-2">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-people-fill text-danger fs-5"></i>
                    <h6 class="mb-0 fw-bold text-uppercase" style="font-size:0.75rem;letter-spacing:1px;color:#64748b;">
                        Detalle por Proveedor
                    </h6>
                    @if($agrupado->isNotEmpty())
                        <span class="badge bg-danger rounded-pill ms-2">{{ $agrupado->count() }}</span>
                    @endif
                </div>
                @if($agrupado->isEmpty())
                    <div class="empty-state-1414">
                        <i class="bi bi-inbox d-block"></i>
                        <h5>No hay datos disponibles</h5>
                        <p class="text-muted small mb-0">No se encontraron retenciones para el período seleccionado</p>
                    </div>
                @endif
            </div>
        </div>
        
        @if($agrupado->isNotEmpty())
        <div class="table-responsive px-3 pb-3">
            <table id="proveedoresTable" class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3" style="width:60px;">#</th>
                        <th>RNC/Cédula</th>
                        <th>Proveedor</th>
                        <th class="text-end" style="width:100px;"># Compras</th>
                        <th class="text-end">ITBIS Retenido</th>
                        <th class="text-end">ISR Retenido</th>
                        <th class="text-end pe-4" style="width:140px;">Total Retenido</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agrupado as $idx => $prov)
                    <tr>
                        <td class="ps-4 text-muted fw-semibold">{{ $idx + 1 }}</td>
                        <td><span class="provider-rnc">{{ $prov['rnc'] }}</span></td>
                        <td><span class="provider-name" title="{{ $prov['nombre'] }}">{{ $prov['nombre'] }}</span></td>
                        <td class="text-end"><span class="purchase-count">{{ $prov['cantidad'] }}</span></td>
                        <td class="text-end amount-cell amount-itbis">RD$ {{ number_format($prov['itbis'], 2) }}</td>
                        <td class="text-end amount-cell amount-isr">RD$ {{ number_format($prov['isr'], 2) }}</td>
                        <td class="text-end pe-4 amount-cell amount-total">RD$ {{ number_format($prov['itbis'] + $prov['isr'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="totales-row">
                        <td class="ps-4 py-3 text-end text-uppercase small" style="letter-spacing:1px;">Totales</td>
                        <td class="py-3"></td>
                        <td class="py-3"></td>
                        <td class="text-end py-3 fw-bold">{{ $agrupado->sum('cantidad') }}</td>
                        <td class="text-end py-3 amount-itbis">RD$ {{ number_format($agrupado->sum('itbis'), 2) }}</td>
                        <td class="text-end py-3 amount-isr">RD$ {{ number_format($agrupado->sum('isr'), 2) }}</td>
                        <td class="text-end pe-4 py-3 amount-total">RD$ {{ number_format($agrupado->sum(fn($p) => $p['itbis'] + $p['isr']), 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif
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
            processing: "Procesando...",
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "Ningún dato disponible en esta tabla",
            infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            infoFiltered: "(filtrado de un total de _MAX_ registros)",
            search: "Buscar:",
            loadingRecords: "Cargando...",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            },
            aria: {
                sortAscending: ": Activar para ordenar ascendente",
                sortDescending: ": Activar para ordenar descendente"
            },
            select: {
                rows: {
                    _: "%d filas seleccionadas",
                    0: "",
                    1: "1 fila seleccionada"
                }
            },
            decimal: ",",
            thousands: "."
        },
        columns: [
            { className: 'ps-4' },
            { },
            { },
            { className: 'text-end', orderable: false },
            { className: 'text-end', orderable: false },
            { className: 'text-end', orderable: false },
            { className: 'text-end pe-4', orderable: false }
        ],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
@endpush
