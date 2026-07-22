@extends('layouts.app')

@section('title', 'Secuencias de e-CF')

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .ui-card { background: rgba(15,23,42,.8); }
body.dark-mode .dropdown-menu { background: #1e293b; border-color: #334155; }
body.dark-mode .dropdown-item { color: #94a3b8; }
body.dark-mode .dropdown-item:hover { background: #334155; color: #f1f5f9; }
body.dark-mode .bg-light { background: rgba(30,41,59,.6) !important; }
body.dark-mode .progress { background: #1e293b !important; }
body.dark-mode .alert-success { background: rgba(16,185,129,.15); color: #6ee7b7; }
body.dark-mode .alert-warning { background: rgba(245,158,11,.15); color: #fcd34d; }
body.dark-mode .alert-danger { background: rgba(239,68,68,.15); color: #fca5a5; }
body.dark-mode .ui-card .ui-input:focus,
body.dark-mode .ui-card .ui-select:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.15); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>    <div class="bubble"></div>    <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-list-ol"></i></div>
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="{{ route('ecf.index') }}" class="text-decoration-none text-white-50">e-CF</a></li>
                            <li class="breadcrumb-item active text-white">Secuencias</li>
                        </ol>
                    </nav>
                    <h4 class="ui-header-title">Secuencias de Comprobantes Electrónicos</h4>
                    <div class="ui-header-meta"><i class="bi bi-info-circle me-1"></i> <span>Numeración autorizada por la DGII para emisión de e-CF</span></div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('ecf.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill"><i class="bi bi-receipt me-1"></i>Ver Documentos</a>
                <a href="{{ route('secuencias-ecf.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill"><i class="bi bi-plus-lg me-1"></i>Nueva Secuencia</a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.1s">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble bg-primary bg-opacity-10 text-primary" style="width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <div class="ui-stat-label">Total Secuencias</div>
                            <div class="ui-stat-value">{{ $stats['total'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.15s">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble bg-success bg-opacity-10 text-success" style="width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <div class="ui-stat-label">Activas</div>
                            <div class="ui-stat-value">{{ $stats['activas'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.2s">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble bg-warning bg-opacity-10 text-warning" style="width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <div class="ui-stat-label">Vencidas</div>
                            <div class="ui-stat-value">{{ $stats['vencidas'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.25s">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble bg-danger bg-opacity-10 text-danger" style="width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div>
                            <div class="ui-stat-label">Agotadas</div>
                            <div class="ui-stat-value">{{ $stats['agotadas'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @forelse($secuencias as $sec)
        <div class="col-xl-4 col-md-6">
            <div class="ui-card h-100 overflow-hidden position-relative
                {{ $sec->vencida() ? 'border-top border-4 border-warning' : ($sec->agotada() ? 'border-top border-4 border-danger' : 'border-top border-4 border-primary') }}" style="--delay:.1s">
                <div class="ui-card-accent"></div>
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
                                    <form action="{{ route('secuencias-ecf.destroy', $sec) }}" method="POST" onsubmit="return UI.confirm.delete('¿Eliminar esta secuencia?')">
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
            <a href="{{ route('secuencias-ecf.create') }}" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-plus-lg me-1"></i>Crear Primera Secuencia
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection
