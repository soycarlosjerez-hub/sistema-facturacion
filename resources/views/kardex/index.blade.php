@extends('layouts.app')

@section('title', 'Kardex de Inventario')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-journal-text text-primary me-2"></i>
                Historial de Inventario (Kardex)
            </h2>
            <p class="text-muted mb-0">Rastrea cada movimiento de tus productos</p>
        </div>
        <div>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-3 bg-light bg-opacity-50">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-lg-3">
                    <div class="input-group input-group-merge border-0 shadow-none">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="buscar" class="form-control border-0 bg-white" 
                               placeholder="Buscar por nota o concepto..." value="{{ request('buscar') }}">
                    </div>
                </div>
                <div class="col-lg-3">
                    <select name="producto_id" class="form-select border-0 shadow-none bg-white">
                        <option value="">Todos los productos</option>
                        @foreach($productos as $p)
                            <option value="{{ $p->id }}" {{ request('producto_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <select name="almacen_id" class="form-select border-0 shadow-none bg-white">
                        <option value="">Todos los almacenes</option>
                        @foreach($almacenes as $a)
                            <option value="{{ $a->id }}" {{ request('almacen_id') == $a->id ? 'selected' : '' }}>
                                {{ $a->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill w-100">Filtrar</button>
                    <a href="{{ route('kardex.index') }}" class="btn btn-light rounded-pill px-3">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Movimientos -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Fecha y Hora</th>
                        <th>Producto</th>
                        <th>Almacén</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Concepto / Nota</th>
                        <th class="text-end pe-4">Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientos as $m)
                    <tr>
                        <td class="ps-4">
                            <div class="small fw-bold text-dark">{{ $m->created_at->format('d/m/Y') }}</div>
                            <div class="text-muted small" style="font-size: 0.7rem;">{{ $m->created_at->format('h:i A') }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark small">{{ $m->producto->nombre }}</div>
                            <small class="text-muted" style="font-size: 0.7rem;">ID: {{ $m->producto->id }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border-0 p-0 fw-bold">{{ $m->almacen->nombre }}</span>
                        </td>
                        <td>
                            @if(strtolower($m->tipo) == 'entrada')
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                                    <i class="bi bi-arrow-down-left me-1"></i> Entrada
                                </span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">
                                    <i class="bi bi-arrow-up-right me-1"></i> Salida
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold {{ strtolower($m->tipo) == 'entrada' ? 'text-success' : 'text-danger' }}">
                                {{ strtolower($m->tipo) == 'entrada' ? '+' : '-' }} {{ $m->cantidad }}
                            </div>
                        </td>
                        <td>
                            <small class="text-muted">{{ $m->nota ?? $m->motivo ?? 'Movimiento de inventario' }}</small>
                        </td>
                        <td class="text-end pe-4">
                            <div class="small fw-bold text-dark">{{ $m->user->name ?? 'Sistema' }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-boxes display-1 text-muted opacity-25"></i>
                            <p class="text-muted mt-3">No hay movimientos registrados.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $movimientos->withQueryString()->links() }}
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filters = document.querySelectorAll('select, input[name="buscar"]');
        const tableBody = document.querySelector('tbody');
        const pagination = document.querySelector('.mt-4');
        let timeout = null;

        filters.forEach(filter => {
            const eventType = filter.tagName === 'SELECT' ? 'change' : 'input';
            
            filter.addEventListener(eventType, function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    const formData = new FormData(document.querySelector('form'));
                    const params = new URLSearchParams(formData).toString();
                    const url = `{{ route('kardex.index') }}?${params}`;

                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        tableBody.innerHTML = doc.querySelector('tbody').innerHTML;
                        if (pagination && doc.querySelector('.mt-4')) {
                            pagination.innerHTML = doc.querySelector('.mt-4').innerHTML;
                        }
                    });
                }, 300);
            });
        });
    });
</script>
@endsection
