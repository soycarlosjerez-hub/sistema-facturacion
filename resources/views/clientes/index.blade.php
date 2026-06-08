@extends('layouts.app')

@section('title', 'Gestión de Clientes')

@section('content')
<div class="container-fluid px-4">
    <!-- Header Moderno -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-people text-primary me-2"></i>
                Directorio de Clientes
            </h2>
            <p class="text-muted mb-0">Administra tus clientes y el control de deudas</p>
        </div>
        <div>
            <a href="{{ route('clientes.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-person-plus me-1"></i> Nuevo Cliente
            </a>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-3 bg-light bg-opacity-50">
            <form method="GET" id="search-form" class="row g-2 align-items-center">
                <div class="col-lg-6">
                    <div class="input-group input-group-merge border-0 shadow-none">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="nombre" class="form-control border-0 bg-white" 
                               placeholder="Buscar por nombre, RNC o teléfono..." value="{{ request('nombre') }}">
                    </div>
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Filtrar</button>
                    <a href="{{ route('clientes.index') }}" class="btn btn-light rounded-pill px-3">Limpiar</a>
                </div>
                <div class="col-lg-3 text-end">
                    <div class="dropdown">
                        <button class="btn btn-light rounded-pill dropdown-toggle w-100" data-bs-toggle="dropdown">
                            Exportar
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                            <li><a class="dropdown-item" href="{{ route('clientes.pdf') }}"><i class="bi bi-file-pdf text-danger me-2"></i> PDF</a></li>
                            <li><a class="dropdown-item" href="{{ route('clientes.exportar') }}"><i class="bi bi-file-excel text-success me-2"></i> Excel</a></li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Clientes -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Cliente</th>
                        <th>Contacto</th>
                        <th>Identificación</th>
                        <th>Balance Pendiente</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $c)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-wrapper me-3">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 0.9rem;">
                                        {{ strtoupper(substr($c->nombre, 0, 1)) }}
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-bold mb-0 text-dark">{{ $c->nombre }}</div>
                                    <small class="text-muted text-truncate d-block" style="max-width: 200px;">{{ $c->direccion ?? 'Sin dirección' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small fw-bold text-dark">{{ $c->telefono ?? '—' }}</div>
                            <div class="text-muted small">{{ $c->email ?? '—' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border rounded-pill px-2">{{ $c->rnc_cedula ?? 'Sin RNC' }}</span>
                        </td>
                        <td>
                            @if($c->balance_pendiente > 0)
                                <div class="fw-bold text-danger">RD$ {{ number_format($c->balance_pendiente, 2) }}</div>
                                <small class="text-muted" style="font-size: 0.7rem;">Deuda pendiente</small>
                            @else
                                <div class="fw-bold text-success">RD$ 0.00</div>
                                <small class="text-muted" style="font-size: 0.7rem;">Al día</small>
                            @endif
                        </td>
                        <td>
                            @if($c->balance_pendiente > 5000)
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Riesgo Alto</span>
                            @elseif($c->balance_pendiente > 0)
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Deudor</span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Solvente</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('clientes.show', $c) }}" class="btn btn-sm btn-outline-primary rounded-pill me-1" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('clientes.edit', $c) }}" class="btn btn-sm btn-outline-warning rounded-pill me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('clientes.destroy', $c) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('¿Borrar cliente?')" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-people display-1 text-muted opacity-25"></i>
                            <p class="text-muted mt-3">No hay clientes que coincidan con la búsqueda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $clientes->links() }}
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="nombre"]');
        const tableBody = document.querySelector('tbody');
        const pagination = document.querySelector('.mt-4');
        let timeout = null;

        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                const query = this.value;
                const url = `{{ route('clientes.index') }}?nombre=${query}`;

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
</script>

<style>
    .avatar-wrapper { transition: transform 0.2s; }
    tr:hover .avatar-wrapper { transform: scale(1.1); }
    .table > :not(caption) > * > * { padding: 1rem 0.5rem; }
</style>
@endsection