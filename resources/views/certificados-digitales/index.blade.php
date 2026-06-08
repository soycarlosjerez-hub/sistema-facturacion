@extends('layouts.app')

@section('title', 'Certificados Digitales')

@section('content')
<div class="container-fluid px-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('ecf.index') }}" class="text-decoration-none">e-CF</a></li>
                    <li class="breadcrumb-item active">Certificados Digitales</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0"><i class="bi bi-key me-2"></i>Certificados Digitales</h3>
            <p class="text-muted mb-0">Certificados para firma de e-CF (.p12 / .pfx) emitidos por entidad autorizada</p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <a href="{{ route('ecf.index') }}" class="btn btn-light rounded-pill px-3 me-2">
                <i class="bi bi-receipt me-1"></i>Ver Documentos
            </a>
            <a href="{{ route('certificados-digitales.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i>Nuevo Certificado
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
                            <i class="bi bi-key"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size:0.7rem;">Total</small>
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
                            <small class="text-muted text-uppercase fw-bold" style="font-size:0.7rem;">Vigentes</small>
                            <h3 class="fw-bold mb-0 mt-1">{{ $stats['vigentes'] }}</h3>
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
                            <small class="text-muted text-uppercase fw-bold" style="font-size:0.7rem;">Por Vencer (≤30d)</small>
                            <h3 class="fw-bold mb-0 mt-1">{{ $stats['por_vencer'] }}</h3>
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
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size:0.7rem;">Vencidos</small>
                            <h3 class="fw-bold mb-0 mt-1">{{ $stats['vencidos'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @forelse($certificados as $cert)
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden
                {{ !$cert->vigente() ? 'border-top border-4 border-danger' : ($cert->diasParaVencer() <= 30 ? 'border-top border-4 border-warning' : 'border-top border-4 border-success') }}">

                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-key-fill text-primary fs-4"></i>
                                <h5 class="fw-bold mb-0">{{ $cert->nombre }}</h5>
                            </div>
                            <small class="text-muted">{{ $cert->emisor_cert ?? 'Emisor no especificado' }}</small>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-light rounded-circle btn-sm" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                <li><a class="dropdown-item" href="{{ route('certificados-digitales.edit', $cert) }}"><i class="bi bi-pencil me-2"></i>Editar</a></li>
                                <li>
                                    <form action="{{ route('certificados-digitales.toggle', $cert) }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item {{ $cert->activo ? 'text-danger' : 'text-success' }}">
                                            <i class="bi {{ $cert->activo ? 'bi-slash-circle' : 'bi-check-circle' }} me-2"></i>
                                            {{ $cert->activo ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('certificados-digitales.destroy', $cert) }}" method="POST" onsubmit="return confirm('¿Eliminar certificado?')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Eliminar</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    @if(!$cert->vigente())
                        <div class="alert alert-danger border-0 rounded-3 py-2 small mb-3">
                            <i class="bi bi-x-octagon me-1"></i>Vencido el {{ $cert->fecha_vencimiento->format('d/m/Y') }}
                        </div>
                    @elseif($cert->diasParaVencer() <= 30)
                        <div class="alert alert-warning border-0 rounded-3 py-2 small mb-3">
                            <i class="bi bi-clock-history me-1"></i>Vence en {{ $cert->diasParaVencer() }} días
                        </div>
                    @else
                        <div class="alert alert-success border-0 rounded-3 py-2 small mb-3">
                            <i class="bi bi-check-circle me-1"></i>Vigente hasta {{ $cert->fecha_vencimiento->format('d/m/Y') }}
                        </div>
                    @endif

                    <div class="row g-2 small mb-2">
                        <div class="col-6">
                            <span class="text-muted d-block">RNC Emisor</span>
                            <span class="fw-bold">{{ $cert->rnc_emisor }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">RNC Titular</span>
                            <span class="fw-bold">{{ $cert->rnc_titular }}</span>
                        </div>
                    </div>

                    <div class="bg-light rounded-3 p-2 small">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Documentos firmados:</span>
                            <span class="fw-bold">{{ $cert->documentos()->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Serial:</span>
                            <span class="fw-bold text-truncate ms-2" style="max-width: 60%;">{{ $cert->serial_number ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-key display-1 text-muted opacity-25"></i>
            <p class="text-muted mt-3">No hay certificados digitales registrados.</p>
            <a href="{{ route('certificados-digitales.create') }}" class="btn btn-primary rounded-pill">
                <i class="bi bi-plus-lg me-1"></i>Registrar Primer Certificado
            </a>
            <p class="small text-muted mt-2">
                <i class="bi bi-info-circle me-1"></i>Si no tiene un certificado, el sistema usará firma simulada (modo sandbox).
            </p>
        </div>
        @endforelse
    </div>
</div>
@endsection
