@extends('layouts.app')

@section('title', 'Listas de Precios')

@push('styles')
@include('partials.premium-ui')
<style>
    .premium-header {
        background: linear-gradient(135deg, #8b5cf6, #a855f7, #7c3aed, #8b5cf6);
        background-size: 300% 300%;
        box-shadow: 0 8px 32px rgba(139,92,246,.25);
    }
    .premium-header::before {
        background:
            radial-gradient(circle at 30% 44%, rgba(255,255,255,.12) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(255,255,255,.08) 0%, transparent 50%);
    }
    .price-card {
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.4);
        border-radius: 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        position: relative;
        overflow: hidden;
    }
    .price-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        border-color: rgba(139,92,246,0.3);
    }
    .price-card::before {
        content: '';
        position: absolute; top: 0; left: 0;
        width: 4px; height: 100%;
        background: linear-gradient(to bottom, #8b5cf6, #a855f7);
        border-top-left-radius: 1.25rem; border-bottom-left-radius: 1.25rem;
        opacity: 0; transition: opacity 0.3s ease;
    }
    .price-card:hover::before { opacity: 1; }
    .price-card.status-expired::before {
        background: linear-gradient(to bottom, #dc3545, #e74c5a);
    }
    .price-card.status-warning::before {
        background: linear-gradient(to bottom, #ffc107, #ffca2c);
    }
    .price-card.status-notstarted::before {
        background: linear-gradient(to bottom, #0dcaf0, #3dd9f1);
    }
    .icon-wrapper {
        width: 48px; height: 48px;
        display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, rgba(139,92,246,0.1) 0%, rgba(168,85,247,0.1) 100%);
        border-radius: 0.75rem;
        color: #8b5cf6;
        font-size: 1.5rem;
    }
    .status-badge {
        padding: 0.4em 0.8em;
        border-radius: 2rem;
        font-weight: 500;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .btn-icon-hover {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%;
        transition: background-color 0.2s;
    }
    .btn-icon-hover:hover { background-color: rgba(0,0,0,0.05); }
    .alert-banner {
        border-radius: 1rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        animation: slideDown 0.4s ease-out;
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .alert-banner-warning {
        background: linear-gradient(135deg, rgba(255,193,7,0.12), rgba(255,193,7,0.05));
        border-color: rgba(255,193,7,0.4) !important;
    }
    .alert-banner-danger {
        background: linear-gradient(135deg, rgba(220,53,69,0.12), rgba(220,53,69,0.05));
        border-color: rgba(220,53,69,0.4) !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    @php
        $porExpirar = $listas->filter(fn($l) => isset($l->status) && $l->status['class'] === 'warning');
        $expiradas = $listas->filter(fn($l) => isset($l->status) && $l->status['class'] === 'danger');
        $vigentes = $listas->count() - $porExpirar->count() - $expiradas->count();
    @endphp

    @if($porExpirar->isNotEmpty())
    <div class="alert-banner alert-banner-warning alert alert-warning d-flex align-items-center gap-3 mb-4 p-3">
        <div class="flex-shrink-0">
            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
        </div>
        <div class="flex-grow-1">
            <strong>¡Atención!</strong> Hay <strong>{{ $porExpirar->count() }}</strong> {{ Str::plural('lista de precios', $porExpirar->count()) }} por expirar en los próximos 7 días:
            <span class="fw-semibold">{{ $porExpirar->pluck('nombre')->join(', ') }}</span>.
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($expiradas->isNotEmpty())
    <div class="alert-banner alert-banner-danger alert alert-danger d-flex align-items-center gap-3 mb-4 p-3">
        <div class="flex-shrink-0">
            <i class="bi bi-x-octagon-fill fs-4"></i>
        </div>
        <div class="flex-grow-1">
            <strong>Lista(s) expirada(s):</strong> {{ $expiradas->count() }} {{ Str::plural('lista', $expiradas->count()) }} ha expirado{{ $expiradas->count() > 1 ? 'n' : '' }}:
            <span class="fw-semibold">{{ $expiradas->pluck('nombre')->join(', ') }}</span>.
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="premium-header d-flex justify-content-between align-items-center mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex align-items-center gap-3 position-relative" style="z-index: 2;">
            <div class="premium-avatar-circle">
                <i class="bi bi-tag"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1 text-white">Listas de Precios</h4>
                <small class="text-white opacity-75">
                    <i class="bi bi-tags me-1"></i>
                    Gestiona diferentes tarifas para tus canales o clientes especiales
                </small>
            </div>
        </div>
        <div>
            @can('listas-precio.create')
            <a href="{{ route('listas-precio.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                <i class="bi bi-plus-lg me-2"></i> Nueva Lista
            </a>
            @endcan
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="premium-stat-card p-3" style="animation-delay:.05s;">
                <div class="card-accent purple"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(139,92,246,0.1);color:#8b5cf6;font-size:1.4rem;">
                        <i class="bi bi-tags"></i>
                    </div>
                    <div>
                        <div class="stat-value" style="color:#8b5cf6;">{{ $listas->count() }}</div>
                        <div class="stat-label">Total Listas</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="premium-stat-card p-3" style="animation-delay:.1s;">
                <div class="card-accent purple"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(25,135,84,0.1);color:#198754;font-size:1.4rem;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="stat-value text-success">{{ $vigentes }}</div>
                        <div class="stat-label">Vigentes</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="premium-stat-card p-3" style="animation-delay:.15s;">
                <div class="card-accent purple"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(255,193,7,0.1);color:#ffc107;font-size:1.4rem;">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div>
                        <div class="stat-value text-warning">{{ $porExpirar->count() }}</div>
                        <div class="stat-label">Por Expirar</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="premium-stat-card p-3" style="animation-delay:.2s;">
                <div class="card-accent purple"></div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(220,53,69,0.1);color:#dc3545;font-size:1.4rem;">
                        <i class="bi bi-x-octagon"></i>
                    </div>
                    <div>
                        <div class="stat-value text-danger">{{ $expiradas->count() }}</div>
                        <div class="stat-label">Expiradas</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        @forelse($listas as $lista)
        @php
            $statusClass = $lista->status['class'] ?? 'success';
            $statusLabel = $lista->status['label'] ?? 'Vigente';
            $statusIcon = $lista->status['icon'] ?? 'bi-check-circle';
            $cardStatusClass = match($statusClass) {
                'danger' => 'status-expired',
                'warning' => 'status-warning',
                'info' => 'status-notstarted',
                default => '',
            };
        @endphp
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="price-card {{ $cardStatusClass }} h-100 d-flex flex-column">
                <div class="p-4 d-flex flex-column h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-wrapper">
                            <i class="bi bi-tag-fill"></i>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-icon-hover text-muted" data-bs-toggle="dropdown" title="Acciones">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                <li><a class="dropdown-item py-2" href="{{ route('listas-precio.show', $lista) }}"><i class="bi bi-eye text-info me-2"></i>Ver detalles</a></li>
                                @can('listas-precio.edit')
                                <li><a class="dropdown-item py-2" href="{{ route('listas-precio.edit', $lista) }}"><i class="bi bi-pencil text-primary me-2"></i>Editar lista</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('listas-precio.impacto', $lista) }}"><i class="bi bi-graph-up text-warning me-2"></i>Impacto de precios</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('listas-precio.logs', $lista) }}"><i class="bi bi-clock-history text-secondary me-2"></i>Historial de cambios</a></li>
                                <li>
                                    <form action="{{ route('listas-precio.duplicar', $lista) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="dropdown-item py-2"><i class="bi bi-copy text-secondary me-2"></i>Duplicar</button>
                                    </form>
                                </li>
                                @endcan
                                @can('listas-precio.delete')
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('listas-precio.destroy', $lista) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar esta lista de precios? Se mantendrá por 30 días por seguridad.')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item py-2 text-danger"><i class="bi bi-trash text-danger me-2"></i>Eliminar</button>
                                    </form>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </div>

                    <div class="mb-auto">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h4 class="fw-bold text-dark mb-0">{{ $lista->nombre }}</h4>
                            <span class="status-badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }}">
                                <i class="bi {{ $statusIcon }} me-1"></i>{{ $statusLabel }}
                            </span>
                        </div>
                        <div class="mb-3">
                            <span class="badge bg-light text-secondary border rounded-pill px-3 py-1 fw-medium tracking-wider text-uppercase small">
                                <i class="bi bi-upc-scan me-1"></i> {{ $lista->codigo }}
                            </span>
                        </div>
                        @if($lista->descripcion)
                            <p class="text-muted small lh-sm">{{ Str::limit($lista->descripcion, 80) }}</p>
                        @endif
                    </div>

                    <div class="border-top pt-3 mt-3">
                        <div class="d-flex justify-content-between align-items-center small text-muted mb-3">
                            <span class="d-flex align-items-center gap-2 bg-light px-3 py-1 rounded-pill">
                                <i class="bi bi-box-seam" style="color: #8b5cf6;"></i>
                                <span class="fw-bold text-dark">{{ $lista->items_count }}</span> prod.
                            </span>
                            @if($lista->vigencia_desde)
                                <span class="d-flex align-items-center gap-1" title="Vigencia">
                                    <i class="bi bi-calendar3"></i>
                                    {{ $lista->vigencia_desde->format('d/m/Y') }}
                                    @if($lista->vigencia_hasta)
                                        - {{ $lista->vigencia_hasta->format('d/m/Y') }}
                                    @endif
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('listas-precio.show', $lista) }}" class="btn btn-primary bg-opacity-10 text-primary border-0 w-100 rounded-pill fw-bold" style="transition: all 0.2s;color:#8b5cf6 !important;">
                            Gestionar Precios <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="premium-card" style="min-height:400px;animation-delay:.1s;">
                <div class="card-body d-flex flex-column align-items-center justify-content-center py-5">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-4" style="width:100px;height:100px;">
                        <i class="bi bi-tag text-muted opacity-50" style="font-size:3rem;"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">No hay listas de precios</h4>
                    <p class="text-muted mb-4 text-center" style="max-width:450px;">Las listas de precios te permiten tener tarifas especiales para clientes mayoristas, promociones temporales o diferentes sucursales.</p>
                    @can('listas-precio.create')
                    <a href="{{ route('listas-precio.create') }}" class="btn btn-primary rounded-pill px-5 py-2 shadow-sm fw-bold">
                        <i class="bi bi-plus-lg me-2"></i> Crear Primera Lista
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection