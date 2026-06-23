@extends('layouts.app')

@section('title', 'Categorías')

@push('styles')
<style>
    .premium-header {
        background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
        border-radius: 1rem;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(236, 72, 153, 0.4);
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
    .filter-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }
    .avatar-circle {
        width: 44px; height: 44px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 1.2rem;
        transition: transform 0.2s;
    }
    tr:hover .avatar-circle { transform: scale(1.1); }
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
                <i class="bi bi-tags me-3 fs-1 opacity-75"></i> Gestión de Categorías
            </h2>
            <p class="mb-0 opacity-75 fs-5">Clasifica y organiza tus productos e inventario</p>
        </div>
        <div>
            <a href="{{ route('categorias.create') }}" class="btn btn-light text-primary fw-bold rounded-pill px-4 py-2 shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Nueva Categoría
            </a>
        </div>
    </div>

    <div class="table-responsive" style="min-height:400px;">
        <table class="table table-hover align-middle mb-0 w-100">
            <thead class="text-muted small text-uppercase tracking-wider" style="border-bottom: 2px solid #e2e8f0;">
                <tr>
                    <th class="ps-4 pb-3">Categoría</th>
                    <th class="pb-3">Descripción</th>
                    <th class="text-center pb-3">Productos</th>
                    <th class="text-center pb-3">Estado</th>
                    <th class="text-end pe-4 pb-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categorias as $c)
                <tr>
                    <td class="ps-4" style="max-width:250px;">
                        <div class="d-flex align-items-center">
                            @php
                                $nombreCat = $c->nombre ?? 'C';
                                $firstLetter = strtoupper(substr($nombreCat, 0, 1));
                                $colors = ['#f87171', '#60a5fa', '#34d399', '#fbbf24', '#a78bfa', '#f472b6'];
                                $color = $colors[crc32($nombreCat) % count($colors)];
                            @endphp
                            <div class="avatar-circle text-white me-3 shadow-sm" style="background-color: {{ $color }};">
                                {{ $firstLetter }}
                            </div>
                            <div class="text-truncate">
                                <div class="fw-bold text-dark fs-6 text-truncate" title="{{ $c->nombre }}">{{ $c->nombre }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="text-muted small text-truncate" style="max-width:300px;" title="{{ $c->descripcion }}">{{ $c->descripcion ?? 'Sin descripción' }}</div>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-secondary border rounded-pill">
                            <i class="bi bi-box-seam me-1"></i> {{ $c->productos_count }} prod.
                        </span>
                    </td>
                    <td class="text-center">
                        @if($c->activa)
                            <span class="status-badge bg-success bg-opacity-10 text-success">
                                <i class="bi bi-check-circle-fill me-1"></i> Activa
                            </span>
                        @else
                            <span class="status-badge bg-secondary bg-opacity-10 text-secondary">
                                <i class="bi bi-x-circle-fill me-1"></i> Inactiva
                            </span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('categorias.edit', $c) }}" class="btn btn-icon-hover text-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('categorias.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar esta categoría? Solo es posible si no tiene productos asociados.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-icon-hover text-danger border-0 bg-transparent" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center justify-content-center p-5">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-4" style="width:100px;height:100px;">
                                <i class="bi bi-tags text-muted opacity-50" style="font-size:3rem;"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">No hay categorías registradas</h4>
                            <p class="text-muted mb-4 text-center" style="max-width:400px;">Aún no se han registrado categorías. Agrupa tus productos para una mejor organización.</p>
                            <a href="{{ route('categorias.create') }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                                <i class="bi bi-plus-lg me-2"></i> Crear Categoría
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categorias->hasPages())
    <div class="mt-4 d-flex justify-content-center" id="pagination-container">
        {{ $categorias->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection