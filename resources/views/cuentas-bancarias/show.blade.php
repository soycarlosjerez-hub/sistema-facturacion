@extends('layouts.app')

@section('title', $cuentasBancarium->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
.info-item {
    background: #f8fafc;
    border-radius: 0.75rem;
    padding: 1rem;
    border-left: 3px solid #10b981;
}
.info-item .label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    font-weight: 700;
    margin-bottom: 4px;
}
.info-item .value {
    font-weight: 600;
    color: #1e293b;
}
body.dark-mode .info-item {
    background: rgba(30,41,59,.8);
}
body.dark-mode .info-item .label { color: #94a3b8; }
body.dark-mode .info-item .value { color: #f1f5f9; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-bank"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ $cuentasBancarium->nombre }}</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-building me-1"></i>
                        Detalle de la cuenta bancaria
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('cuentas-bancarias.edit', $cuentasBancarium) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-pencil-square me-1"></i>Editar
                </a>
                <a href="{{ route('cuentas-bancarias.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body text-center">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-bank fs-1"></i>
                    </div>
                    <h4 class="fw-bold">{{ $cuentasBancarium->nombre }}</h4>
                    <p class="text-muted small mb-1"><i class="bi bi-building me-1"></i>{{ $cuentasBancarium->banco ?? '—' }}</p>
                    <p class="text-muted small mb-1"><i class="bi bi-hash me-1"></i>{{ $cuentasBancarium->numero_cuenta ?? '—' }}</p>
                    <p class="text-muted small mb-3"><i class="bi bi-person me-1"></i>{{ $cuentasBancarium->titular ?? '—' }}</p>
                    @if($cuentasBancarium->activo)
                    <span class="ui-badge ui-badge-primary"><i class="bi bi-check-circle-fill me-1"></i>Activa</span>
                    @else
                    <span class="ui-badge ui-badge-neutral"><i class="bi bi-x-circle-fill me-1"></i>Inactiva</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="ui-card mb-4" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-info-circle"></i>
                    Información de la Cuenta
                </div>
                <div class="ui-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="label">Tipo de Cuenta</div>
                                <div class="value">{{ ucfirst($cuentasBancarium->tipo_cuenta ?? '—') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item" style="border-left-color: #8b5cf6;">
                                <div class="label">Moneda</div>
                                <div class="value">{{ $cuentasBancarium->moneda }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item" style="border-left-color: #f59e0b;">
                                <div class="label">Cédula / RUC del Titular</div>
                                <div class="value">{{ $cuentasBancarium->cedula_ruc ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item" style="border-left-color: #3b82f6;">
                                <div class="label">Saldo Inicial</div>
                                <div class="value">{{ number_format($cuentasBancarium->saldo_inicial, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item" style="border-left-color: #059669;">
                                <div class="label">Saldo Actual</div>
                                <div class="value fs-5 fw-bold" style="color:#059669;">{{ number_format($cuentasBancarium->saldo_actual, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection