@extends('layouts.app')

@section('title', 'Listas de Precios')

@push('styles')
<style>
    .premium-header {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        border-radius: 1rem;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
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
        border-color: rgba(99,102,241,0.3);
    }
    .price-card::before {
        content: '';
        position: absolute; top: 0; left: 0;
        width: 4px; height: 100%;
        background: linear-gradient(to bottom, #4f46e5, #818cf8);
        border-top-left-radius: 1.25rem; border-bottom-left-radius: 1.25rem;
        opacity: 0; transition: opacity 0.3s ease;
    }
    .price-card:hover::before { opacity: 1; }
    .icon-wrapper {
        width: 48px; height: 48px;
        display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg, rgba(79,70,229,0.1) 0%, rgba(99,102,241,0.1) 100%);
        border-radius: 0.75rem;
        color: #4f46e5;
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
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="premium-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1 d-flex align-items-center">
                <i class="bi bi-tags me-3 fs-1 opacity-75"></i> Listas de Precios
            </h2>
            <p class="mb-0 opacity-75 fs-5">Gestiona diferentes tarifas para tus canales o clientes especiales</p>
        </div>
        <div>
            @can('listas-precio.create')
            <a href="{{ route('listas-precio.create') }}" class="btn btn-light text-primary fw-bold rounded-pill px-4 py-2 shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Nueva Lista
            </a>
            @endcan
        </div>
    </div>

    <div class="row g-4">
        @forelse($listas as $lista)
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="price-card h-100 d-flex flex-column">
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
                                    <form action="{{ route('listas-precio.destroy', $lista) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar esta lista de precios? Los productos asociados volverán a su precio base.')">
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
                            @if($lista->activa)
                                <span class="status-badge bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill me-1"></i>Activa</span>
                            @else
                                <span class="status-badge bg-danger bg-opacity-10 text-danger"><i class="bi bi-x-circle-fill me-1"></i>Inactiva</span>
                            @endif
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
                                <i class="bi bi-box-seam text-primary"></i>
                                <span class="fw-bold text-dark">{{ $lista->items_count ?? $lista->items()->count() }}</span> prod.
                            </span>
                            @if($lista->vigencia_desde)
                                <span class="d-flex align-items-center gap-1" title="Vigencia">
                                    <i class="bi bi-calendar3"></i>
                                    {{ $lista->vigencia_desde->format('d/m/y') }}
                                    @if($lista->vigencia_hasta)
                                        - {{ $lista->vigencia_hasta->format('d/m/y') }}
                                    @endif
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('listas-precio.show', $lista) }}" class="btn btn-primary bg-opacity-10 text-primary border-0 w-100 rounded-pill fw-bold hover-bg-primary" style="transition: all 0.2s;">
                            Gestionar Precios <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-white" style="min-height:400px;">
                <div class="card-body d-flex flex-column align-items-center justify-content-center py-5">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-4" style="width:100px;height:100px;">
                        <i class="bi bi-tags text-muted opacity-50" style="font-size:3rem;"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-2">No hay listas de precios</h3>
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