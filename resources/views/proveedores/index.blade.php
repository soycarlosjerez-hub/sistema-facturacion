@extends('layouts.app')

@section('title', 'Gestión de Proveedores')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-truck text-primary me-2"></i>
                Proveedores
            </h2>
            <p class="text-muted mb-0">Gestión de proveedores</p>
        </div>
        <div>
            <a href="{{ route('proveedores.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Proveedor
            </a>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-3 bg-light bg-opacity-50">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-lg-6">
                    <div class="input-group input-group-merge border-0 shadow-none">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="buscar" class="form-control border-0 bg-white" 
                               placeholder="Buscar por nombre, RNC o contacto..." value="{{ request('buscar') }}">
                    </div>
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Filtrar</button>
                    <a href="{{ route('proveedores.index') }}" class="btn btn-light rounded-pill px-3">Limpiar</a>
                </div>
                <div class="col-lg-3 text-end">
                    <div class="dropdown">
                        <button class="btn btn-light rounded-pill dropdown-toggle w-100" data-bs-toggle="dropdown">
                            Exportar
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                            <li><a class="dropdown-item" href="{{ route('proveedores.pdf') }}"><i class="bi bi-file-pdf text-danger me-2"></i> PDF</a></li>
                            <li><a class="dropdown-item" href="{{ route('proveedores.exportar') }}"><i class="bi bi-file-excel text-success me-2"></i> Excel</a></li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Proveedores -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Proveedor</th>
                        <th>Contacto</th>
                        <th>Identificación (RNC)</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proveedores as $p)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="icon-box me-3">
                                    <div class="rounded-3 bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center fw-bold" style="width: 45px; height: 45px;">
                                        <i class="bi bi-truck fs-5"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-bold mb-0 text-dark">{{ $p->nombre }}</div>
                                    <small class="text-muted">{{ $p->direccion ?? 'Sin dirección registrada' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small fw-bold text-dark">{{ $p->telefono ?? '—' }}</div>
                            <div class="text-muted small">{{ $p->email ?? '—' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border rounded-pill px-2">{{ $p->rnc_cedula ?? 'Sin RNC' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Activo</span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('proveedores.show', $p) }}" class="btn btn-sm btn-outline-info rounded-pill me-1" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('proveedores.edit', $p) }}" class="btn btn-sm btn-outline-primary rounded-pill me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('proveedores.destroy', $p) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('¿Borrar proveedor?')" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-truck display-1 text-muted opacity-25"></i>
                            <p class="text-muted mt-3">No hay proveedores que coincidan con la búsqueda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    @if(method_exists($proveedores, 'links'))
    <div class="mt-4">
        {{ $proveedores->links() }}
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="buscar"]');
        const tableBody = document.querySelector('tbody');
        const pagination = document.querySelector('.mt-4');
        let timeout = null;

        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                const query = this.value;
                const url = `{{ route('proveedores.index') }}?buscar=${query}`;

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
    .icon-box { transition: transform 0.2s; }
    tr:hover .icon-box { transform: scale(1.1); }
    .table > :not(caption) > * > * { padding: 1rem 0.5rem; }
</style>
@endsection