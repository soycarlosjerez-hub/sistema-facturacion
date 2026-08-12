@extends('layouts.app')

@section('title', 'Detalle de Ganancias')

@push('styles')
@include('partials.premium-ui')
<style>
/* Earnings Show Styles */
.period-badge {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .5rem 1rem;
    border-radius: var(--radius-lg);
    font-size: .85rem;
    font-weight: 600;
    background: rgba(14,165,233,.08);
    color: #0ea5e9;
    border: 1px solid rgba(14,165,233,.15);
}
.grand-total-card {
    background: linear-gradient(135deg, rgba(14,165,233,.1), rgba(14,165,233,.03));
    border: 2px solid rgba(14,165,233,.2);
    border-radius: var(--radius-2xl);
    padding: 1.5rem 2rem;
    text-align: center;
}
.grand-total-amount {
    font-size: 2.5rem;
    font-weight: 800;
    color: #0ea5e9;
}
.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .75rem 0;
    border-bottom: 1px solid #f1f5f9;
}
.detail-row:last-child { border-bottom: none; }
.detail-label {
    font-size: .85rem;
    color: #64748b;
    font-weight: 500;
}
.detail-value {
    font-size: .95rem;
    font-weight: 600;
    color: #1e293b;
}
.amount-positive { color: #16a34a; font-weight: 700; }
.amount-base { color: #0ea5e9; }
.amount-tip { color: #d97706; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#0ea5e9;--accent-rgb:14,165,233;--accent-hover:#0284c7;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Detalle de Ganancias</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-calendar-range me-1"></i>
                        <span>{{ $earning->periodo_inicio->format('d/m/Y') }} — {{ $earning->periodo_fin->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('driver-earnings.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Driver & Period Info --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="ui-card h-100" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3" style="color:#0ea5e9;">
                        <i class="bi bi-person-badge me-2"></i>Repartidor
                    </h6>
                    <div class="text-center mb-3">
                        <div class="driver-avatar mx-auto mb-2" style="width:64px;height:64px;font-size:1.3rem;">
                            {{ strtoupper(substr($earning->driver->nombre, 0, 1) . substr($earning->driver->apellido, 0, 1)) }}
                        </div>
                        <h5 class="fw-bold mb-1">{{ $earning->driver->nombre }} {{ $earning->driver->apellido }}</h5>
                        <span class="ui-badge ui-badge-neutral">{{ $earning->driver->cedula }}</span>
                    </div>
                    <hr>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Teléfono</span>
                        <span class="ui-detail-value">{{ $earning->driver->telefono }}</span>
                    </div>
                    <div class="ui-detail-row">
                        <span class="ui-detail-label">Licencia</span>
                        <span class="ui-detail-value">{{ $earning->driver->licencia_conducir ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="ui-card h-100" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3" style="color:#0ea5e9;">
                        <i class="bi bi-calendar-range me-2"></i>Período
                    </h6>
                    <div class="period-badge mb-3">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ $earning->periodo_inicio->format('d/m/Y') }} al {{ $earning->periodo_fin->format('d/m/Y') }}
                    </div>

                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-box-seam me-2"></i>Total Entregas</span>
                        <span class="detail-value">{{ $earning->total_entregas }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-baseball me-2"></i>Monto Base</span>
                        <span class="detail-value amount-base">${{ number_format($earning->monto_base, 2) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-heart me-2"></i>Propinas</span>
                        <span class="detail-value amount-tip">${{ number_format($earning->propinas, 2) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-percent me-2"></i>Comisión Plataforma</span>
                        <span class="detail-value" style="color:#dc2626;">-${{ number_format($earning->comision_plataforma ?? 0, 2) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="bi bi-journal-text me-2"></i>Nota</span>
                        <span class="detail-value">{{ $earning->nota ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grand Total --}}
    <div class="grand-total-card mb-4" style="--delay:.2s">
        <small class="text-muted d-block mb-1" style="font-size:.75rem;letter-spacing:.5px;text-transform:uppercase;">Total Neto a Pagar</small>
        <div class="grand-total-amount">${{ number_format($earning->total_ganancias, 2) }}</div>
        <small class="text-muted">
            {{ $earning->total_entregas }} entregas × base + propinas − comisión
        </small>
    </div>

    {{-- Detail Table --}}
    <div class="ui-card" style="--delay:.25s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <div class="p-3 border-bottom">
                <h6 class="fw-bold mb-0" style="color:#0ea5e9;">
                    <i class="bi bi-clipboard-data me-2"></i>Detalle de Entregas
                </h6>
            </div>
            <div class="table-responsive">
                <table class="ui-table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Fecha</th>
                            <th>Orden #</th>
                            <th>Cliente</th>
                            <th class="text-end">Base</th>
                            <th class="text-end">Propina</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($details as $detail)
                        <tr>
                            <td class="ps-4">
                                <div class="small">
                                    <div class="fw-medium">{{ $detail->fecha->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $detail->fecha->format('h:i A') }}</small>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('delivery-tracking.show', $detail->tracking) }}" class="text-decoration-none fw-semibold" style="color:#0ea5e9;">
                                    #{{ $detail->tracking->orden_id }}
                                </a>
                            </td>
                            <td>{{ $detail->tracking->orden?->cliente?->nombre ?? 'N/A' }}</td>
                            <td class="text-end amount-base">${{ number_format($detail->monto_base, 2) }}</td>
                            <td class="text-end amount-tip">${{ number_format($detail->propina, 2) }}</td>
                            <td class="text-end fw-bold">${{ number_format($detail->total, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                Sin entregas en este período
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        @if($details->isNotEmpty())
                        <tr style="background: rgba(14,165,233,.04);">
                            <td colspan="3" class="ps-4 fw-bold text-uppercase" style="font-size:.75rem;letter-spacing:.5px;color:#64748b;">Totales</td>
                            <td class="text-end fw-bold amount-base">${{ number_format($details->sum('monto_base'), 2) }}</td>
                            <td class="text-end fw-bold amount-tip">${{ number_format($details->sum('propina'), 2) }}</td>
                            <td class="text-end fw-bold" style="color:#16a34a;">${{ number_format($details->sum('total'), 2) }}</td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
