@extends('layouts.app')

@section('title', 'Gestión de Comprobantes Fiscales')

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .premium-header { background: linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e); }
body.dark-mode .prefijo-box { background: rgba(245,158,11,.15); color: #fbbf24; }
body.dark-mode .progress { background: #1e293b !important; }
body.dark-mode .bg-light { background: rgba(30,41,59,.6) !important; }
body.dark-mode .text-dark { color: #f1f5f9 !important; }
body.dark-mode .card { background: rgba(15,23,42,.8); }
body.dark-mode .card .bg-light { background: rgba(30,41,59,.6) !important; }
body.dark-mode .dropdown-menu { background: #1e293b; border-color: #334155; }
body.dark-mode .dropdown-item { color: #94a3b8; }
body.dark-mode .dropdown-item:hover { background: #334155; color: #f1f5f9; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header d-flex justify-content-between align-items-center mb-4" style="background: linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e);">
        <div class="d-flex align-items-center gap-3">
            <div class="premium-avatar-circle">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-1">Control de Comprobantes (NCF)</h2>
                <p class="mb-0 opacity-75">Configuración de secuencias fiscales para la DGII</p>
            </div>
        </div>
        <div>
            <a href="{{ route('ncf.create') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Nueva Secuencia
            </a>
        </div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>

    <div class="row g-4">
        @forelse($sequences as $ncf)
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative border-top border-4 {{ $ncf->activo ? 'border-primary' : 'border-secondary' }}">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="prefijo-box rounded-3 bg-primary bg-opacity-10 px-2 py-1 text-primary fw-bold" style="font-size: 0.85rem;">
                            {{ $ncf->prefijo }}
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                <li><a class="dropdown-item" href="{{ route('ncf.edit', $ncf) }}"><i class="bi bi-pencil me-2"></i> Editar</a></li>
                                <li>
                                    <form action="{{ route('ncf.toggle', $ncf) }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item {{ $ncf->activo ? 'text-danger' : 'text-success' }}">
                                            <i class="bi {{ $ncf->activo ? 'bi-slash-circle' : 'bi-check-circle' }} me-2"></i>
                                            {{ $ncf->activo ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('ncf.destroy', $ncf) }}" method="POST" onsubmit="return confirm('¿Eliminar permanentemente?')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i> Eliminar</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-1">{{ $ncf->nombre }}</h6>
                    <p class="text-muted small mb-4"><i class="bi bi-calendar-event me-1"></i> Vence: {{ \Carbon\Carbon::parse($ncf->fecha_vencimiento)->format('d/m/Y') }}</p>

                    <div class="mb-2 d-flex justify-content-between align-items-end">
                        <small class="text-muted fw-bold" style="font-size: 0.65rem;">CONSUMO ACTUAL</small>
                        <span class="fw-bold small">{{ number_format(($ncf->actual / $ncf->hasta) * 100, 1) }}%</span>
                    </div>

                    <div class="progress rounded-pill mb-4" style="height: 8px; background-color: #f1f5f9;">
                        @php $usage = ($ncf->actual / $ncf->hasta) * 100; @endphp
                        <div class="progress-bar {{ $usage > 85 ? 'bg-danger' : ($usage > 60 ? 'bg-warning' : 'bg-success') }}"
                             style="width: {{ $usage }}%"></div>
                    </div>

                    <div class="p-3 bg-light rounded-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Desde:</span>
                            <span class="fw-bold text-dark small">{{ str_pad($ncf->desde, 8, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Próximo:</span>
                            <span class="fw-bold text-primary small">{{ str_pad($ncf->actual + 1, 8, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                </div>

                @if(!$ncf->activo)
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="z-index: 2;">
                    <span class="badge bg-secondary rounded-pill px-4 py-2 shadow-sm fw-bold">DESACTIVADO</span>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-receipt-cutoff display-1 text-muted opacity-25"></i>
            <p class="text-muted mt-3">No hay secuencias NCF configuradas.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
