@extends('layouts.app')

@section('title', 'Gestión de Cajas')

@push('styles')
<style>
    .premium-header {
        background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
        border-radius: 1rem;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(8, 145, 178, 0.4);
        position: relative;
        overflow: hidden;
    }
    .premium-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .avatar-circle {
        width: 44px; height: 44px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 1.2rem;
        transition: transform 0.2s;
    }
    .status-badge {
        padding: 0.4em 0.8em;
        border-radius: 2rem;
        font-weight: 500;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- Premium Header -->
    <div class="premium-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white bg-opacity-20 rounded-2 p-2 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bi bi-cash-register fs-2 text-white"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Cajas y Turnos</h2>
                    <p class="text-white text-opacity-75 mb-0">Administra múltiples cajas registradoras. Cada cajero abre su propia caja al iniciar el turno.</p>
                </div>
            </div>
            <a href="{{ route('cajas.create') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold shadow-sm text-cyan-800">
                <i class="bi bi-plus-circle me-2"></i>Nueva Caja
            </a>
        </div>
    </div>

    @if(session('success'))
        @if(session('deactivated'))
            <div class="alert alert-warning rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #f59e0b !important;">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('success') }}
            </div>
        @else
            <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #198754 !important;">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        </div>
    @endif

    @if($sesionActivaUsuario)
        <div class="alert rounded-4 shadow-sm border-0 mb-4 d-flex align-items-center justify-content-between" style="background: linear-gradient(90deg, rgba(34,197,94,0.1), rgba(56,189,248,0.1)); border-left: 4px solid #22c55e !important;">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-success bg-opacity-25 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-cash-stack fs-5"></i>
                </div>
                <div>
                    <div class="small text-muted">Sesión activa</div>
                    <strong>{{ $sesionActivaUsuario->caja->nombre }}</strong>
                    @if($sesionActivaUsuario->caja->codigo)
                        <span class="badge bg-dark ms-1">{{ $sesionActivaUsuario->caja->codigo }}</span>
                    @endif
                    <span class="text-muted small ms-2">desde {{ $sesionActivaUsuario->fecha_apertura->format('h:i A') }}</span>
                </div>
            </div>
            <a href="{{ route('ventas.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
                <i class="bi bi-cart-plus me-1"></i>Ir al POS
            </a>
        </div>
    @endif

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-cash-register fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total</div>
                        <div class="fs-3 fw-bold">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-play-circle-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Abiertas</div>
                        <div class="fs-3 fw-bold text-success">{{ $stats['abiertas'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-stop-circle-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Cerradas</div>
                        <div class="fs-3 fw-bold">{{ $stats['cerradas'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-pause-circle-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Inactivas</div>
                        <div class="fs-3 fw-bold text-warning">{{ $stats['inactivas'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="d-flex justify-content-end mb-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="toggleInactivas" {{ session('hide_inactive') ? 'checked' : '' }}>
            <label class="form-check-label fw-bold text-muted small" for="toggleInactivas">
                <i class="bi bi-eye-slash me-1"></i>Ocultar inactivas
            </label>
        </div>
    </div>

    <!-- Caja Cards -->
    <div class="row g-4">
        @foreach($cajasConStats as $caja)
            @php
                $sesionActiva = $caja->sesionActiva();
                $isMySession = $sesionActiva && $sesionActiva->user_id == auth()->id();
                $esAdmin = auth()->user()->role === 'admin';
                $estadoClass = !$caja->activo ? 'inactiva' : $caja->estado;
                $headerGradient = match($estadoClass) {
                    'abierta' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                    'inactiva' => 'linear-gradient(135deg, #94a3b8 0%, #64748b 100%)',
                    default => 'linear-gradient(135deg, #64748b 0%, #475569 100%)',
                };
                $opacityStyle = $estadoClass === 'inactiva' ? 'opacity: 0.7;' : '';
            @endphp
            <div class="col-lg-4 col-md-6" data-card-id="{{ $caja->id }}">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100" style="{{ $opacityStyle }}">
                    <!-- Header with gradient -->
                    <div class="card-header border-0 text-white py-3" style="background: {{ $headerGradient }}; position: relative; overflow: hidden;">
                        <div style="position: relative; z-index: 2;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="small opacity-75 fw-bold text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 1px;">
                                        @if($caja->codigo){{ $caja->codigo }}@else C{{ str_pad($caja->id, 2, '0', STR_PAD_LEFT) }}@endif
                                    </div>
                                    <h5 class="fw-bold mb-0 text-white">{{ $caja->nombre }}</h5>
                                </div>
                                <i class="bi bi-cash-stack opacity-25" style="font-size: 2.5rem;"></i>
                            </div>
                            <div class="mt-2">
                                @if(! $caja->activo)
                                    <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 py-1"><i class="bi bi-pause-fill me-1"></i>INACTIVA</span>
                                @elseif($caja->estado == 'abierta')
                                    <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 py-1"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>ABIERTA</span>
                                @else
                                    <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 py-1"><i class="bi bi-circle me-1"></i>CERRADA</span>
                                @endif
                            </div>
                        </div>
                        <div style="position: absolute; top: -50%; right: -20%; width: 200px; height: 200px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                    </div>

                    <!-- Body -->
                    <div class="card-body p-4">
                        @if($caja->ubicacion)
                            <div class="d-flex align-items-center gap-2 mb-2 text-muted small">
                                <i class="bi bi-geo-alt"></i>
                                <span>{{ $caja->ubicacion }}</span>
                            </div>
                        @endif
                        @if($caja->sucursal)
                            <div class="d-flex align-items-center gap-2 mb-2 text-muted small">
                                <i class="bi bi-building"></i>
                                <span>{{ $caja->sucursal->nombre }}</span>
                            </div>
                        @endif

                        @if($sesionActiva)
                            <div class="p-2 rounded-3 mb-3" style="background: rgba(34,197,94,0.08); border-left: 3px solid #22c55e;">
                                <div class="small fw-bold text-success mb-1">
                                    <i class="bi bi-person-circle me-1"></i>{{ $sesionActiva->user->name }}
                                </div>
                                <div class="small text-muted">
                                    <i class="bi bi-clock me-1"></i>{{ $sesionActiva->fecha_apertura->format('h:i A') }}
                                    · Fondo: <strong>RD$ {{ number_format($sesionActiva->monto_inicial, 0) }}</strong>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex align-items-center gap-2 mb-1 text-muted small">
                            <i class="bi bi-graph-up text-primary"></i>
                            <span>Ventas históricas: <strong>RD$ {{ number_format($caja->ventas_historico, 0) }}</strong></span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-1 text-muted small">
                            <i class="bi bi-clock-history text-info"></i>
                            <span>Total de turnos: <strong>{{ $caja->total_sesiones }}</strong></span>
                        </div>
                        @if($caja->ultima_sesion)
                            <div class="d-flex align-items-center gap-2 mb-1 text-muted small">
                                <i class="bi bi-calendar"></i>
                                <span>Última: <strong>{{ $caja->ultima_sesion->created_at->diffForHumans() }}</strong></span>
                            </div>
                        @endif

                        <!-- Actions according to status -->
                        <div class="mt-3">
                            @if($caja->estado == 'abierta' && $isMySession)
                                <div class="d-grid gap-2">
                                    <a href="{{ route('ventas.create') }}" class="btn btn-primary rounded-pill fw-bold">
                                        <i class="bi bi-cart-plus me-1"></i>IR AL POS
                                    </a>
                                    <a href="{{ route('cajas.cierre', $caja->id) }}" class="btn btn-warning rounded-pill fw-bold">
                                        <i class="bi bi-lock me-1"></i>CERRAR TURNO
                                    </a>
                                </div>
                            @elseif($caja->estado == 'abierta')
                                <button class="btn btn-secondary w-100 rounded-pill" disabled>
                                    <i class="bi bi-lock-fill me-1"></i>EN USO POR OTRO CAJERO
                                </button>
                            @elseif($caja->activo)
                                <button type="button" class="btn btn-primary w-100 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#modalAbrir{{ $caja->id }}">
                                    <i class="bi bi-play-circle me-1"></i>ABRIR CAJA
                                </button>
                            @else
                                <button class="btn btn-secondary w-100 rounded-pill" disabled>
                                    <i class="bi bi-pause-circle me-1"></i>CAJA INACTIVA
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Admin Actions -->
                    @if($esAdmin)
                        <div class="card-footer bg-light bg-opacity-50 border-top p-3 d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-warning rounded-pill flex-fill"
                                    data-bs-toggle="modal" data-bs-target="#modalQuickEdit"
                                    data-id="{{ $caja->id }}"
                                    data-nombre="{{ $caja->nombre }}"
                                    data-codigo="{{ $caja->codigo }}"
                                    data-ubicacion="{{ $caja->ubicacion }}"
                                    data-activo="{{ $caja->activo ? '1' : '0' }}">
                                <i class="bi bi-lightning-charge-fill me-1"></i> Rápida
                            </button>
                            <a href="{{ route('cajas.edit', $caja->id) }}" class="btn btn-sm btn-outline-primary rounded-pill flex-fill">
                                <i class="bi bi-pencil-square me-1"></i> Completa
                            </a>
                            <form action="{{ route('cajas.destroy', $caja->id) }}" method="POST" class="flex-fill" onsubmit="return confirm('¿Eliminar la caja {{ $caja->nombre }}? Esta acción no se puede deshacer.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill w-100" {{ $caja->estado == 'abierta' ? 'disabled' : '' }}>
                                    <i class="bi bi-trash me-1"></i>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Modal Abrir Caja -->
            @if($caja->activo && $caja->estado == 'cerrada')
            <div class="modal fade" id="modalAbrir{{ $caja->id }}" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content border-0 shadow-xl rounded-4 overflow-hidden">
                        <form action="{{ route('cajas.abrir', $caja->id) }}" method="POST">
                            @csrf
                            <!-- Premium Header -->
                            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                <div class="d-flex align-items-center gap-3 text-white">
                                    <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        <i class="bi bi-play-circle-fill fs-3"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0 text-white">Abrir Caja</h5>
                                        <small class="text-white text-opacity-75">Iniciar nuevo turno</small>
                                    </div>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            
                            <div class="modal-body p-4">
                                <!-- Caja Info Card -->
                                <div class="bg-light rounded-4 p-3 mb-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                            <i class="bi bi-cash-register fs-4"></i>
                                        </div>
                                        <div class="flex-fill">
                                            <h6 class="fw-bold mb-1">{{ $caja->nombre }}</h6>
                                            <div class="d-flex gap-2 flex-wrap">
                                                @if($caja->codigo)
                                                    <span class="badge bg-dark rounded-pill px-3 py-1">{{ $caja->codigo }}</span>
                                                @endif
                                                @if($caja->ubicacion)
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1">
                                                        <i class="bi bi-geo-alt me-1"></i>{{ $caja->ubicacion }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <p class="text-muted small text-center mb-4">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Indica el fondo inicial (efectivo en la gaveta al iniciar el turno)
                                </p>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted small text-uppercase mb-2">
                                        <i class="bi bi-cash me-1"></i>Fondo Inicial
                                    </label>
                                    <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden">
                                        <span class="input-group-text bg-white border-end-0 fw-bold text-primary" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                                            RD$
                                        </span>
                                        <input type="number" name="monto_inicial" id="montoInicial{{ $caja->id }}" class="form-control border-start-0 fw-bold fs-5 text-center" value="0" min="0" step="0.01" required autofocus
                                            style="background: #f8fafc;"
                                            placeholder="0.00">
                                    </div>
                                    <div class="form-text text-center text-muted small mt-2">
                                        Presiona <kbd class="bg-dark text-white px-2 py-1 rounded">Enter</kbd> para confirmar
                                    </div>
                                </div>
                                
                                <!-- Quick amount buttons -->
                                <div class="d-flex gap-2 justify-content-center mb-4">
                                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-2 small" data-monto="0">
                                        <i class="bi bi-dash-circle me-1"></i>Sin fondo
                                    </button>
                                    <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-2 small" data-monto="100">
                                        RD$ 100
                                    </button>
                                    <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-2 small" data-monto="500">
                                        RD$ 500
                                    </button>
                                    <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-2 small" data-monto="1000">
                                        RD$ 1,000
                                    </button>
                                </div>
                            </div>
                            
                            <div class="modal-footer border-0 p-4 pt-0 bg-light rounded-bottom-4">
                                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">
                                    <i class="bi bi-x me-1"></i>Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
                                    <i class="bi bi-play-fill me-1"></i>Abrir Caja
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modalEl = document.getElementById('modalAbrir{{ $caja->id }}');
                const input = document.getElementById('montoInicial{{ $caja->id }}');
                
                // Quick amount buttons
                modalEl.querySelectorAll('[data-monto]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        input.value = btn.dataset.monto;
                        input.focus();
                    });
                });
                
                // Auto-select on focus
                input.addEventListener('focus', () => {
                    input.select();
                });
                
                // Format on blur
                input.addEventListener('blur', () => {
                    const val = parseFloat(input.value) || 0;
                    input.value = val.toFixed(2);
                });
            });
            </script>
            @endif
        @endforeach
        
        @if($cajasConStats->isEmpty())
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-5">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-cash-register fs-1"></i>
                        </div>
                        <h4 class="fw-bold mb-2">No hay cajas registradas</h4>
                        <p class="text-muted mb-4">Crea tu primera caja para empezar a vender.</p>
                        <a href="{{ route('cajas.create') }}" class="btn btn-primary rounded-pill px-5 py-3 fw-bold fs-6">
                            <i class="bi bi-plus-circle me-2"></i>Crear Primera Caja
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Edición Rápida -->
<div class="modal fade" id="modalQuickEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form id="quickEditForm">
                <div class="modal-header border-0 pb-0 text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <div>
                        <h5 class="fw-bold mb-0"><i class="bi bi-lightning-charge-fill me-2"></i>Edición Rápida</h5>
                        <small class="opacity-75">Modifica los datos básicos sin recargar la página</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="qe-id" name="id">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Nombre <span class="text-danger">*</span></label>
                        <div class="input-group shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-tag-fill text-warning"></i></span>
                            <input type="text" id="qe-nombre" name="nombre" class="form-control border-start-0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Código</label>
                        <div class="input-group shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-upc text-warning"></i></span>
                            <input type="text" id="qe-codigo" name="codigo" class="form-control border-start-0" placeholder="C01, C02...">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Ubicación</label>
                        <div class="input-group shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-geo-alt-fill text-warning"></i></span>
                            <input type="text" id="qe-ubicacion" name="ubicacion" class="form-control border-start-0" placeholder="Opcional">
                        </div>
                    </div>
                    <div class="p-2 rounded-3 d-flex align-items-center gap-2 mb-2" style="background: rgba(34,197,94,0.08);">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" id="qe-activo" name="activo" value="1">
                        </div>
                        <label class="form-check-label fw-bold mb-0" for="qe-activo">Caja activa</label>
                    </div>
                    <div id="qe-error" class="alert alert-danger rounded-3 d-none small mb-0"></div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark" id="qe-submit">
                        <i class="bi bi-check-lg me-1"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(auth()->user()->role === 'admin')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalQuickEdit');
    const form = document.getElementById('quickEditForm');
    const errorBox = document.getElementById('qe-error');
    const submitBtn = document.getElementById('qe-submit');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    modal.addEventListener('show.bs.modal', (event) => {
        const btn = event.relatedTarget;
        const id = btn.dataset.id;
        const nombre = btn.dataset.nombre;
        const codigo = btn.dataset.codigo || '';
        const ubicacion = btn.dataset.ubicacion || '';
        const activo = btn.dataset.activo === '1';

        document.getElementById('qe-id').value = id;
        document.getElementById('qe-nombre').value = nombre;
        document.getElementById('qe-codigo').value = codigo;
        document.getElementById('qe-ubicacion').value = ubicacion;
        document.getElementById('qe-activo').checked = activo;
        errorBox.classList.add('d-none');
        errorBox.textContent = '';

        const header = modal.querySelector('.modal-header small');
        if (header) header.textContent = 'Editando: ' + nombre;

        setTimeout(() => document.getElementById('qe-nombre').focus(), 300);
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('qe-id').value;
        const data = new FormData();
        data.append('_token', csrfToken);
        data.append('_method', 'PUT');
        data.append('nombre', document.getElementById('qe-nombre').value.trim());
        data.append('codigo', document.getElementById('qe-codigo').value.trim());
        data.append('ubicacion', document.getElementById('qe-ubicacion').value.trim());
        if (document.getElementById('qe-activo').checked) {
            data.append('activo', '1');
        }

        const nombre = document.getElementById('qe-nombre').value.trim();
        if (!nombre) {
            errorBox.textContent = 'El nombre es obligatorio.';
            errorBox.classList.remove('d-none');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...';
        errorBox.classList.add('d-none');

        try {
            const resp = await fetch(`/cajas/${id}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: data,
            });

            const result = await resp.json();

            if (!resp.ok) {
                let msg = 'Error al guardar.';
                if (result.errors) {
                    msg = Object.values(result.errors).flat().join(' ');
                } else if (result.message) {
                    msg = result.message;
                }
                errorBox.textContent = msg;
                errorBox.classList.remove('d-none');
                return;
            }

            // Éxito: actualizar la card en el DOM sin recargar
            const card = document.querySelector(`[data-card-id="${id}"]`);
            if (card && result.caja) {
                const c = result.caja;
                const h5 = card.querySelector('.card-header h5');
                if (h5) h5.textContent = c.nombre;
                const codeEl = card.querySelector('.card-header .small');
                if (codeEl) codeEl.textContent = c.codigo || ('C' + String(c.id).padStart(2, '0'));
                const locLine = card.querySelector('.bi-geo-alt')?.parentElement;
                if (locLine) {
                    if (c.ubicacion) {
                        locLine.querySelector('span').textContent = c.ubicacion;
                        locLine.style.display = '';
                    } else {
                        locLine.style.display = 'none';
                    }
                }
                const qeBtn = card.querySelector('[data-bs-target="#modalQuickEdit"]');
                if (qeBtn) {
                    qeBtn.dataset.nombre = c.nombre;
                    qeBtn.dataset.codigo = c.codigo || '';
                    qeBtn.dataset.ubicacion = c.ubicacion || '';
                    qeBtn.dataset.activo = c.activo ? '1' : '0';
                }
                card.style.transition = 'box-shadow 0.4s';
                card.style.boxShadow = '0 0 0 4px rgba(245,158,11,0.4)';
                setTimeout(() => card.style.boxShadow = '', 800);
            }

            bootstrap.Modal.getInstance(modal).hide();
            showToast('Caja actualizada correctamente', 'success');
        } catch (err) {
            errorBox.textContent = 'Error de red: ' + err.message;
            errorBox.classList.remove('d-none');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Guardar Cambios';
        }
    });

    // Toggle inactivas
    const toggleInactivas = document.getElementById('toggleInactivas');
    if (toggleInactivas) {
        toggleInactivas.addEventListener('change', () => {
            const url = new URL(window.location.href);
            url.searchParams.set('hide_inactive', toggleInactivas.checked ? '1' : '0');
            window.location.href = url.toString();
        });
    }

    function showToast(msg, type) {
        const id = 'toast-' + Date.now();
        const html = `
            <div id="${id}" class="toast align-items-center text-white border-0 bg-${type}" role="alert">
                <div class="d-flex">
                    <div class="toast-body fw-bold">${msg}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`;
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1200';
            document.body.appendChild(container);
        }
        container.insertAdjacentHTML('beforeend', html);
        const toastEl = document.getElementById(id);
        new bootstrap.Toast(toastEl, { delay: 3000 }).show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    }
});
</script>
@endif
@endsection