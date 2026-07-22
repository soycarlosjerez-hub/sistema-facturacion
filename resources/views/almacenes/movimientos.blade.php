@extends('layouts.app')

@section('title', 'Historial de Movimientos')

@push('styles')
@include('partials.premium-ui')
<style>
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
    body.dark-mode .btn-icon-hover:hover { background-color: rgba(255,255,255,0.1); }
</style>
@endpush

@section('content')
<div class="ui-page">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Movimientos de Inventario</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-arrow-left-right me-1"></i>
                        <span>Historial de entradas, salidas y traslados de productos</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('almacenes.movements')
                <a href="{{ route('almacenes.movimientos.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Movimiento
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="card-body">
            <form method="GET" id="filter-form" class="row g-2 align-items-end">
                <div class="col-lg-4">
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" class="ui-input" placeholder="Buscar por producto o nota..." value="{{ request('buscar') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <select name="tipo" class="ui-select">
                        <option value="">Todos</option>
                        <option value="entrada" {{ request('tipo') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                        <option value="salida" {{ request('tipo') == 'salida' ? 'selected' : '' }}>Salida</option>
                        <option value="traslado" {{ request('tipo') == 'traslado' ? 'selected' : '' }}>Traslado</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="ui-btn ui-btn-solid rounded-pill w-100"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                </div>
                <div class="col-lg-2">
                    <a href="{{ route('almacenes.movimientos') }}" class="ui-btn ui-btn-ghost rounded-pill w-100">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card overflow-hidden" style="--delay:.2s">
        <div class="ui-card-accent"></div>
        <div class="table-responsive" id="movimientos-table">
            @include('almacenes._movimientos-table')
        </div>
    </div>

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
