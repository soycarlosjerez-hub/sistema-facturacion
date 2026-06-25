@extends('layouts.app')

@section('title', 'Procesadores de Pago')

@push('styles')
@include('partials.premium-ui')
<style>
.premium-header {
    background: linear-gradient(135deg, #f59e0b, #f97316, #f59e0b, #d97706);
    background-size: 300% 300%;
    animation: premiumGradientShift 6s ease infinite;
}
.premium-header::before {
    background:
        radial-gradient(circle at 30% 40%, rgba(255,255,255,.1) 0%, transparent 50%),
        radial-gradient(circle at 70% 60%, rgba(255,255,255,.07) 0%, transparent 50%);
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-credit-card"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Procesadores de Pago</h4>
                    <small class="text-white opacity-75">Gestiona los procesadores y sus credenciales API</small>
                </div>
            </div>
            <a href="{{ route('payment-processors.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Procesador
            </a>
        </div>
    </div>

    <div class="premium-card overflow-hidden" style="animation-delay:.1s;">
        <div class="card-accent amber"></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: rgba(15,23,42,0.03);">
                    <tr style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;">
                        <th class="ps-4 py-3 text-muted fw-bold">Nombre</th>
                        <th class="py-3 text-muted fw-bold">Tipo</th>
                        <th class="py-3 text-muted fw-bold">Comisión</th>
                        <th class="py-3 text-muted fw-bold">Entorno</th>
                        <th class="text-center py-3 text-muted fw-bold">Estado</th>
                        <th class="text-end pe-4 py-3 text-muted fw-bold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($procesadores as $p)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-semibold">{{ $p->nombre }}</span>
                                @if($p->api_key)
                                    <i class="bi bi-key text-muted ms-1 small" title="API configurada"></i>
                                @endif
                            </td>
                            <td><span class="premium-badge">{{ ucfirst($p->tipo) }}</span></td>
                            <td>
                                <small class="text-muted">{{ number_format($p->comision_porcentaje, 2) }}% + RD$ {{ number_format($p->comision_fija, 2) }}</small>
                            </td>
                            <td>
                                @if($p->api_environment === 'production')
                                    <span class="premium-badge active" style="background:rgba(239,68,68,.1);border-color:rgba(239,68,68,.3);color:#dc2626;">Producción</span>
                                @elseif($p->api_environment === 'sandbox')
                                    <span class="premium-badge active">Sandbox</span>
                                @else
                                    <span class="premium-badge">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($p->activo)
                                    <span class="premium-badge active">Activo</span>
                                @else
                                    <span class="premium-badge">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('payment-processors.edit', $p) }}" class="premium-btn-edit me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('payment-processors.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este procesador?')">
                                    @csrf @method('DELETE')
                                    <button class="premium-btn-delete" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-credit-card fs-1"></i>
                                <p class="mt-2 mb-0">No hay procesadores registrados</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($procesadores->hasPages())
        <div class="border-0 py-3 px-4" style="background:transparent;">
            {{ $procesadores->links() }}
        </div>
        @endif
    </div>
</div>
@endsection