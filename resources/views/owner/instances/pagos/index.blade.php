@extends('layouts.app')
@section('title', 'Historial de Pagos - ' . $instance->nombre)

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Historial de Pagos</h2>
                    <p class="mb-0 opacity-75">{{ $instance->nombre }}</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.instances.pagos.create', $instance) }}" class="ui-btn ui-btn-solid" style="background:#10b981;border-color:#10b981">
                    <i class="bi bi-plus-lg me-2"></i>Registrar Pago
                </a>
                <a href="{{ route('owner.instances.show', $instance) }}" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#10b981"></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Mes Pagado</th>
                        <th>Monto</th>
                        <th>M&eacute;todo</th>
                        <th>Fecha de Pago</th>
                        <th>Estado</th>
                        <th>Registrado por</th>
                        <th class="pe-4">Acciones</th>
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
                        <td>
                            @if(in_array($pago->estado_pago, ['completado', 'pagado']))
                                <span class="badge bg-success rounded-pill">Confirmado</span>
                            @elseif($pago->estado_pago === 'pendiente')
                                <span class="badge bg-warning text-dark rounded-pill">Pendiente</span>
                            @else
                                <span class="badge bg-secondary rounded-pill">{{ ucfirst($pago->estado_pago) }}</span>
                            @endif
                        </td>
                        <td>{{ $pago->registradoPor?->name ?? '—' }}</td>
                        <td class="pe-4">
                            @if($pago->estado_pago === 'pendiente')
                                <form method="POST" action="{{ route('owner.instances.pagos.confirmar', [$instance, $pago]) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm rounded-pill" onclick="return confirm('¿Confirmar este pago? La instancia quedará al día.')">
                                        <i class="bi bi-check2-circle me-1"></i>Confirmar
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No hay pagos registrados.</td>
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
