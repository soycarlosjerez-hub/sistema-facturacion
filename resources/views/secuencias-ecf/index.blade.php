@extends('layouts.app')

@section('title', 'Secuencias de e-CF')

@section('content')
<div class="container-fluid px-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('ecf.index') }}" class="text-decoration-none">e-CF</a></li>
                    <li class="breadcrumb-item active">Secuencias</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0"><i class="bi bi-shield-check me-2"></i>Secuencias de Comprobantes Electrónicos</h3>
            <p class="text-muted mb-0">Numeración autorizada por la DGII para emisión de e-CF</p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <a href="{{ route('ecf.index') }}" class="btn btn-light rounded-pill px-3">
                <i class="bi bi-receipt me-1"></i>Ver Documentos
            </a>
            <a href="{{ route('secuencias-ecf.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i>Nueva Secuencia
            </a>
        </div>
    </div>

    <style>
        .icon-bubble { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; }
    </style>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size:0.7rem;">Total Secuencias</small>
                            <h3 class="fw-bold mb-0 mt-1">{{ $stats['total'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size:0.7rem;">Activas</small>
                            <h3 class="fw-bold mb-0 mt-1">{{ $stats['activas'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size:0.7rem;">Vencidas</small>
                            <h3 class="fw-bold mb-0 mt-1">{{ $stats['vencidas'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble bg-danger bg-opacity-10 text-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size:0.7rem;">Agotadas</small>
                            <h3 class="fw-bold mb-0 mt-1">{{ $stats['agotadas'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @forelse($secuencias as $sec)
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative
                {{ $sec->vencida() ? 'border-top border-4 border-warning' : ($sec->agotada() ? 'border-top border-4 border-danger' : 'border-top border-4 border-primary') }}">

                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold fs-6">
                                {{ $sec->tipo_ecf }}
                            </span>
                            <h5 class="fw-bold mt-2 mb-0">{{ $sec->nombre }}</h5>
                            <small class="text-muted">{{ \App\Models\SecuenciaEcf::TIPOS[$sec->tipo_ecf] ?? '' }}</small>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-light rounded-circle btn-sm" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                <li><a class="dropdown-item" href="{{ route('secuencias-ecf.edit', $sec) }}"><i class="bi bi-pencil me-2"></i>Editar</a></li>
                                <li>
                                    <form action="{{ route('secuencias-ecf.toggle', $sec) }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item {{ $sec->activo ? 'text-danger' : 'text-success' }}">
                                            <i class="bi {{ $sec->activo ? 'bi-slash-circle' : 'bi-check-circle' }} me-2"></i>
                                            {{ $sec->activo ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('secuencias-ecf.destroy', $sec) }}" method="POST" onsubmit="return confirm('¿Eliminar esta secuencia?')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Eliminar</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    @if($sec->vencida())
                        <div class="alert alert-warning border-0 rounded-3 py-2 small mb-3">
                            <i class="bi bi-clock-history me-1"></i>Vencida el {{ $sec->fecha_vencimiento->format('d/m/Y') }}
                        </div>
                    @elseif($sec->agotada())
                        <div class="alert alert-danger border-0 rounded-3 py-2 small mb-3">
                            <i class="bi bi-exclamation-triangle me-1"></i>Secuencia agotada
                        </div>
                    @else
                        <div class="alert alert-success border-0 rounded-3 py-2 small mb-3">
                            <i class="bi bi-check-circle me-1"></i>Vence {{ $sec->fecha_vencimiento->format('d/m/Y') }}
                        </div>
                    @endif

                    @php $uso = $sec->porcentajeUso(); @endphp
                    <div class="mb-2 d-flex justify-content-between align-items-end">
                        <small class="text-muted fw-bold" style="font-size:0.7rem;">CONSUMO</small>
                        <span class="fw-bold small">{{ number_format($uso, 1) }}%</span>
                    </div>
                    <div class="progress rounded-pill mb-3" style="height: 8px; background: #f1f5f9;">
                        <div class="progress-bar {{ $uso > 85 ? 'bg-danger' : ($uso > 60 ? 'bg-warning' : 'bg-success') }}"
                             style="width: {{ $uso }}%"></div>
                    </div>

                    <div class="row g-2 text-center small">
                        <div class="col-4">
                            <div class="bg-light rounded-3 p-2">
                                <small class="text-muted d-block">Desde</small>
                                <span class="fw-bold">{{ str_pad($sec->desde, 10, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-2">
                                <small class="text-muted d-block">Próximo</small>
                                <span class="fw-bold text-primary">{{ $sec->tipo_ecf }}{{ str_pad($sec->actual + 1, 10, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-light rounded-3 p-2">
                                <small class="text-muted d-block">Hasta</small>
                                <span class="fw-bold">{{ str_pad($sec->hasta, 10, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-shield-check display-1 text-muted opacity-25"></i>
            <p class="text-muted mt-3">No hay secuencias e-CF configuradas.</p>
            <a href="{{ route('secuencias-ecf.create') }}" class="btn btn-primary rounded-pill">
                <i class="bi bi-plus-lg me-1"></i>Crear Primera Secuencia
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection
