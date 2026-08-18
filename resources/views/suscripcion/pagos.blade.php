@extends('layouts.app')

@section('title', 'Historial de Pagos')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
            <h4 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i>Historial de Pagos</h4>
            <a href="{{ route('suscripcion.index') }}" class="btn btn-outline-primary btn-sm rounded-pill">
                <i class="bi bi-arrow-left me-1"></i>Volver a Suscripción
            </a>
        </div>
        <p class="text-muted small mb-0">Todos los pagos registrados de <strong>{{ $instance->nombre }}</strong>.</p>
    </div>

    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Mes pagado</th>
                            <th>Monto</th>
                            <th>Método</th>
                            <th>Referencia</th>
                            <th>Fecha de pago</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pagos as $pago)
                        <tr>
                            <td class="px-4 py-3 fw-semibold">{{ optional($pago->mes_pagado)->format('m/Y') ?? '—' }}</td>
                            <td>RD$ {{ number_format($pago->monto, 2) }}</td>
                            <td><span class="badge bg-light text-dark border rounded-pill">{{ ucfirst($pago->metodo_pago ?? '—') }}</span></td>
                            <td><code>{{ $pago->referencia_externa ?? '—' }}</code></td>
                            <td>{{ optional($pago->fecha_pago)->format('d/m/Y h:i A') ?? '—' }}</td>
                            <td>
                                @if($pago->estado_pago === 'completado' || $pago->estado_pago === 'pagado')
                                    <span class="badge bg-success rounded-pill">Confirmado</span>
                                @elseif($pago->estado_pago === 'pendiente')
                                    <span class="badge bg-warning text-dark rounded-pill">Pendiente</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill">{{ ucfirst($pago->estado_pago) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-receipt fs-2 d-block mb-2"></i>
                                Aún no hay pagos registrados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pagos->hasPages())
                <div class="card-footer bg-white border-0">
                    {{ $pagos->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection