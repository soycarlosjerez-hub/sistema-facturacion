@extends('layouts.app')

@section('title', 'Listas de Precios')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-tags text-primary me-2"></i>Listas de Precios</h2>
            <p class="text-muted mb-0">Gestiona m&uacute;ltiples listas de precios para diferentes canales o clientes.</p>
        </div>
        @can('listas-precio.create')
        <a href="{{ route('listas-precio.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-plus-lg me-2"></i>Nueva Lista
        </a>
        @endcan
    </div>

    <div class="row g-3">
        @forelse($listas as $lista)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="fw-bold mb-1">{{ $lista->nombre }}</h5>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">{{ $lista->codigo }}</span>
                            @if($lista->activa)
                                <span class="badge bg-success rounded-pill ms-1"><i class="bi bi-check-circle me-1"></i>Activa</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill ms-1">Inactiva</span>
                            @endif
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3">
                                @can('listas-precio.edit')
                                <li><a class="dropdown-item" href="{{ route('listas-precio.edit', $lista) }}"><i class="bi bi-pencil me-2"></i>Editar precios</a></li>
                                <li>
                                    <form action="{{ route('listas-precio.duplicar', $lista) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="dropdown-item"><i class="bi bi-copy me-2"></i>Duplicar</button>
                                    </form>
                                </li>
                                @endcan
                                @can('listas-precio.delete')
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('listas-precio.destroy', $lista) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta lista de precios?')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Eliminar</button>
                                    </form>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </div>
                    @if($lista->descripcion)
                        <p class="text-muted small mb-2">{{ $lista->descripcion }}</p>
                    @endif
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between small text-muted">
                            <span><i class="bi bi-box me-1"></i>{{ $lista->items_count ?? $lista->items()->count() }} productos</span>
                            @if($lista->vigencia_desde)
                                <span><i class="bi bi-calendar me-1"></i>{{ $lista->vigencia_desde->format('d/m/Y') }}{{ $lista->vigencia_hasta ? ' - '.$lista->vigencia_hasta->format('d/m/Y') : '' }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center py-5">
                    <i class="bi bi-tags fs-1 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">No hay listas de precios registradas.</p>
                    @can('listas-precio.create')
                    <a href="{{ route('listas-precio.create') }}" class="btn btn-primary rounded-pill mt-3">Crear primera lista</a>
                    @endcan
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
