@extends('layouts.app')

@section('title', 'Historial de Movimientos')

@push('styles')
<style>
    .premium-header {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 1rem; padding: 2rem; color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
        position: relative; overflow: hidden;
    }
    .premium-header::after {
        content: ''; position: absolute; top: -50%; right: -20%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .filter-card {
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    .btn-icon-hover {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%; transition: background-color 0.2s;
    }
    .btn-icon-hover:hover { background-color: rgba(0,0,0,0.05); }
    .status-badge {
        padding: 0.4em 0.8em; border-radius: 2rem;
        font-weight: 500; font-size: 0.75rem; letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Header Moderno -->
    <div class="premium-header d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1 d-flex align-items-center">
                <i class="bi bi-arrow-down-up me-3 fs-1 opacity-75"></i>Movimientos de Inventario
            </h2>
            <p class="mb-0 opacity-75 fs-5">Historial de entradas, salidas y traslados de productos</p>
        </div>
        <div>
            @can('almacenes.movements')
            <a href="{{ route('almacenes.movimientos.create') }}" class="btn btn-light text-primary fw-bold rounded-pill px-4 py-2 shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Nuevo Movimiento
            </a>
            @endcan
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="filter-card p-3 mb-4">
        <form method="GET" id="filter-form" class="row g-2 align-items-end">
            <div class="col-lg-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" class="form-control border-start-0 ps-0" placeholder="Buscar por producto o nota..." value="{{ request('buscar') }}" autocomplete="off">
                </div>
            </div>
            <div class="col-lg-2">
                <select name="tipo" class="form-select bg-white">
                    <option value="">Todos</option>
                    <option value="entrada" {{ request('tipo') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                    <option value="salida" {{ request('tipo') == 'salida' ? 'selected' : '' }}>Salida</option>
                    <option value="traslado" {{ request('tipo') == 'traslado' ? 'selected' : '' }}>Traslado</option>
                </select>
            </div>
            <div class="col-lg-2">
                <button type="submit" class="btn btn-primary rounded-pill w-100"><i class="bi bi-funnel me-1"></i>Filtrar</button>
            </div>
            <div class="col-lg-2">
                <a href="{{ route('almacenes.movimientos') }}" class="btn btn-outline-secondary rounded-pill w-100">Limpiar</a>
            </div>
        </form>
    </div>

    <!-- Lista de Movimientos -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive" id="movimientos-table">
            @include('almacenes._movimientos-table')
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-4" id="movimientos-pagination">
        @if($movimientos->hasPages())
            {{ $movimientos->withQueryString()->links() }}
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filters = document.querySelectorAll('#filter-form input, #filter-form select');
        const tableWrap = document.getElementById('movimientos-table');
        const paginationWrap = document.getElementById('movimientos-pagination');
        let timeout = null;

        filters.forEach(filter => {
            const eventType = filter.tagName === 'SELECT' || filter.type === 'date' ? 'change' : 'input';
            filter.addEventListener(eventType, function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    const params = new URLSearchParams(new FormData(document.getElementById('filter-form'))).toString();
                    const url = `{{ route('almacenes.movimientos') }}?${params}`;

                    tableWrap.style.opacity = '0.4';

                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => {
                        if (!r.ok) throw new Error('Error');
                        return r.json();
                    })
                    .then(data => {
                        tableWrap.innerHTML = data.html;
                        paginationWrap.innerHTML = data.pagination;
                        tableWrap.style.opacity = '1';
                    })
                    .catch(() => {
                        window.location.href = url;
                    });
                }, 300);
            });
        });
    });
</script>
@endsection
