@extends('layouts.app')
@section('title', 'Historial de Pagos - ' . $instance->nombre)

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="premium-page">
<div class="container-fluid px-4">
    <div class="premium-header" style="margin-bottom: 2rem; background: linear-gradient(135deg, #059669, #10b981, #06b6d4, #059669);">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Historial de Pagos</h2>
                    <p class="mb-0 opacity-75">{{ $instance->nombre }}</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('owner.instances.pagos.create', $instance) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold text-dark">
                    <i class="bi bi-plus-lg me-2"></i>Registrar Pago
                </a>
                <a href="{{ route('owner.instances.show', $instance) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold text-dark">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-accent green"></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Mes Pagado</th>
                        <th>Monto</th>
                        <th>M&eacute;todo</th>
                        <th>Fecha de Pago</th>
                        <th>Registrado por</th>
                        <th class="pe-4">Notas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pagos as $pago)
                    <tr>
                        <td class="ps-4">{{ $pago->id }}</td>
                        <td class="fw-bold">{{ $pago->mes_pagado->isoFormat('MMMM YYYY') }}</td>
                        <td>{{ $systemMoneda ?? 'RD$' }} {{ number_format($pago->monto, 2) }}</td>
                        <td>{{ $pago->metodo_pago ?? '—' }}</td>
                        <td>{{ $pago->fecha_pago->format('d/m/Y h:i A') }}</td>
                        <td>{{ $pago->registradoPor?->name ?? '—' }}</td>
                        <td class="pe-4"><small class="text-muted">{{ $pago->notas ?? '—' }}</small></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No hay pagos registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pagos->hasPages())
        <div class="card-footer bg-transparent border-0 py-3 px-4">
            {{ $pagos->links() }}
        </div>
        @endif
    </div>
</div>
</div>
@endsection
