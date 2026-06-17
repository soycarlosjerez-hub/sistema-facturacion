@extends('layouts.app')
@section('title', 'Historial de Pagos - ' . $instance->nombre)
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-cash-coin text-success me-2"></i>Historial de Pagos</h2>
            <p class="text-muted mb-0">{{ $instance->nombre }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('owner.instances.pagos.create', $instance) }}" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-plus-lg me-2"></i>Registrar Pago
            </a>
            <a href="{{ route('owner.instances.show', $instance) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
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
@endsection
