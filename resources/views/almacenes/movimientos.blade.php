@extends('layouts.app')

@section('title', 'Historial de Movimientos')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Moderno -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold mb-0">Movimientos de Almacén</h3>
            <p class="text-muted mb-0">Control histórico de entradas y salidas de inventario</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('almacenes.movimientos.create') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Nuevo Movimiento
            </a>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-3 bg-light bg-opacity-50">
            <form method="GET" id="filter-form" class="row g-2 align-items-end">
                <div class="col-lg-3">
                    <div class="input-group input-group-merge border-0 shadow-none">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="producto" class="form-control border-0 bg-white" 
                               placeholder="Buscar por producto..." value="{{ request('producto') }}">
                    </div>
                </div>
                <div class="col-lg-3">
                    <select name="almacen" class="form-select border-0 shadow-none bg-white">
                        <option value="">Todos los almacenes</option>
                        @foreach($almacenes as $a)
                            <option value="{{ $a->id }}" @selected(request('almacen') == $a->id)>
                                {{ $a->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <input type="date" name="desde" class="form-control border-0 bg-white" value="{{ request('desde') }}">
                </div>
                <div class="col-lg-2">
                    <input type="date" name="hasta" class="form-control border-0 bg-white" value="{{ request('hasta') }}">
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill w-100">Filtrar</button>
                    <div class="dropdown">
                        <button class="btn btn-light rounded-pill dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-download"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                            <li><a class="dropdown-item" href="{{ route('almacenes.movimientos.pdf', request()->query()) }}"><i class="bi bi-file-pdf text-danger me-2"></i> PDF</a></li>
                            <li><a class="dropdown-item" href="{{ route('almacenes.movimientos.excel', request()->query()) }}"><i class="bi bi-file-excel text-success me-2"></i> Excel</a></li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
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
