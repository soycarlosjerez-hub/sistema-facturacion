@extends('layouts.app')
@section('title', 'Cuentas Cobrar - Aging')

@push('styles')
@include('partials.premium-ui')
<style>
.aging-card { border-left: 4px solid var(--accent, #8b5cf6); }
.aging-current { --accent: #10b981; --accent-rgb: 16, 185, 129; --accent-hover: #059669; }
.aging-30 { --accent: #3b82f6; --accent-rgb: 59, 130, 246; --accent-hover: #2563eb; }
.aging-60 { --accent: #f59e0b; --accent-rgb: 245, 158, 11; --accent-hover: #d97706; }
.aging-90 { --accent: #ef4444; --accent-rgb: 239, 68, 68; --accent-hover: #dc2626; }
.aging-total { --accent: #6366f1; --accent-rgb: 99, 102, 241; --accent-hover: #4f46e5; }
.total-row td { background: rgba(99,102,241,.08) !important; font-weight: 700 !important; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#6366f1;--accent-rgb:99,102,241;--accent-hover:#4f46e5;">
    <div class="ui-header d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body w-100">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-clock-history"></i></div>
                <div>
                    <h2 class="ui-header-title">Cuentas por Cobrar - Aging</h2>
                    <div class="ui-header-meta">Período: {{ $desde }} al {{ $hasta }}</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('reportes.ar.aging.pdf', ['desde' => $desde, 'hasta' => $hasta]) }}" class="ui-btn ui-btn-solid rounded-pill">
                    <i class="bi bi-file-pdf me-1"></i> PDF
                </a>
                <a href="{{ route('reportes.index') }}" class="ui-btn ui-btn-ghost rounded-pill">
                    <i class="bi bi-grid me-1"></i> Reportes
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="ui-card mb-4"><div class="ui-card-accent"></div><div class="px-4 py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto"><label class="ui-label small fw-semibold mb-0">Desde</label></div>
            <div class="col-auto"><input type="date" name="desde" class="ui-input" value="{{ $desde }}"></div>
            <div class="col-auto"><label class="ui-label small fw-semibold mb-0">Hasta</label></div>
            <div class="col-auto"><input type="date" name="hasta" class="ui-input" value="{{ $hasta }}"></div>
            <div class="col-auto">
                <div class="form-check">
                    <input type="checkbox" name="mostrar_sin_vencido" class="form-check-input" id="sinVencido" {{ request('mostrar_sin_vencido') ? 'checked' : '' }}>
                    <label class="form-check-label small" for="sinVencido">Mostrar sin vencer</label>
                </div>
            </div>
            <div class="col-auto"><button class="ui-btn ui-btn-solid rounded-pill"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
        </form>
    </div></div>

    <!-- Summary -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="ui-card aging-card aging-current">
                <div class="ui-card-body">
                    <div class="text-muted small mb-1">Corriente (0-30d)</div>
                    <div class="fs-4 fw-bold">RD$ {{ number_format($summary['current']['amount'] ?? 0, 2) }}</div>
                    <small class="text-muted">{{ $summary['current']['count'] ?? 0 }} factura(s)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-card aging-card aging-30">
                <div class="ui-card-body">
                    <div class="text-muted small mb-1">31-60 días</div>
                    <div class="fs-4 fw-bold">RD$ {{ number_format($summary['days_31_60']['amount'] ?? 0, 2) }}</div>
                    <small class="text-muted">{{ $summary['days_31_60']['count'] ?? 0 }} factura(s)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-card aging-card aging-60">
                <div class="ui-card-body">
                    <div class="text-muted small mb-1">61-90 días</div>
                    <div class="fs-4 fw-bold">RD$ {{ number_format($summary['days_61_90']['amount'] ?? 0, 2) }}</div>
                    <small class="text-muted">{{ $summary['days_61_90']['count'] ?? 0 }} factura(s)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-card aging-card aging-total">
                <div class="ui-card-body">
                    <div class="text-muted small mb-1">Total Pendiente</div>
                    <div class="fs-4 fw-bold">RD$ {{ number_format($summary['total_amount'] ?? 0, 2) }}</div>
                    <small class="text-muted">{{ $summary['total_count'] ?? 0 }} factura(s)</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="ui-card overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0" id="agingTable">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">Cliente</th>
                        <th>RNC/Cédula</th>
                        <th class="text-end">0-30d</th>
                        <th class="text-end">31-60d</th>
                        <th class="text-end">61-90d</th>
                        <th class="text-end pe-4">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($aging as $id => $data)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-semibold small">{{ $data['nombre'] ?? 'N/A' }}</div>
                        </td>
                        <td><small class="text-muted font-monospace">{{ $data['identificacion'] ?? '-' }}</small></td>
                        <td class="text-end">{{ number_format($data['current']['amount'] ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($data['days_31_60'] ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($data['days_61_90'] ?? 0, 2) }}</td>
                        <td class="text-end pe-4 fw-bold">{{ number_format($data['total_pendiente'] ?? 0, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="ui-empty-state">
                                <i class="bi bi-check2-circle"></i>
                                <p>No hay cuentas por cobrar en este período.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="fw-bold">
                    <tr class="total-row">
                        <td class="ps-4 py-3" colspan="2">Total General</td>
                        <td class="text-end py-3">RD$ {{ number_format($summary['current']['amount'] ?? 0, 2) }}</td>
                        <td class="text-end py-3">RD$ {{ number_format($summary['days_31_60']['amount'] ?? 0, 2) }}</td>
                        <td class="text-end py-3">RD$ {{ number_format($summary['days_61_90']['amount'] ?? 0, 2) }}</td>
                        <td class="text-end pe-4 py-3">RD$ {{ number_format($summary['total_amount'] ?? 0, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#agingTable').DataTable({
        responsive: true,
        pageLength: 50,
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, 'Todos']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        columnDefs: [{ className: 'text-end', targets: [2, 3, 4, 5] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
@endpush
