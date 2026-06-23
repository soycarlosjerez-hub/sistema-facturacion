@extends('layouts.app')

@section('title', 'Gestión de Proveedores')

@push('styles')
<style>
    /* Premium UI Styles */
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
    .filter-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }
    .table-custom {
        border-collapse: separate;
        border-spacing: 0 0.5rem;
    }
    .table-custom tbody tr {
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: all 0.2s ease-in-out;
        border-radius: 0.75rem;
    }
    .table-custom tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
    }
    .table-custom td:first-child {
        border-top-left-radius: 0.75rem;
        border-bottom-left-radius: 0.75rem;
    }
    .table-custom td:last-child {
        border-top-right-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
    }
    .table-custom td {
        border-top: 1px solid transparent;
        border-bottom: 1px solid transparent;
        padding: 1rem 1.25rem;
        vertical-align: middle;
    }
    .avatar-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1.2rem;
        transition: transform 0.2s;
    }
    tr:hover .avatar-circle {
        transform: scale(1.1);
    }
    .status-badge {
        padding: 0.4em 0.8em;
        border-radius: 2rem;
        font-weight: 500;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .btn-icon-hover {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: background-color 0.2s;
    }
    .btn-icon-hover:hover {
        background-color: rgba(0,0,0,0.05);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="premium-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1 d-flex align-items-center">
                <i class="bi bi-truck me-3 fs-1 opacity-75"></i>
                Catálogo de Proveedores
            </h2>
            <p class="mb-0 opacity-75 fs-5">Gestión de proveedores y contactos de negocio</p>
        </div>
        <div>
            <a href="{{ route('proveedores.create') }}" class="btn btn-light text-primary fw-bold rounded-pill px-4 py-2 shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Nuevo Proveedor
            </a>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="filter-card p-4 mb-4">
        <form method="GET" id="search-form" class="row g-3 align-items-end">
            <div class="col-lg-6">
                <label class="form-label text-muted small fw-bold text-uppercase tracking-wider">Buscar Proveedor</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" id="busqueda-proveedor" class="form-control border-start-0 ps-0" 
                           placeholder="Nombre, RNC, teléfono o email..." value="{{ request('buscar') }}" autocomplete="off">
                </div>
            </div>
            <div class="col-lg-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill flex-grow-1"><i class="bi bi-funnel me-2"></i>Filtrar</button>
                <a href="{{ route('proveedores.index') }}" class="btn btn-outline-secondary rounded-circle" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
            <div class="col-lg-3 text-end">
                <div class="dropdown">
                    <button class="btn btn-light rounded-pill dropdown-toggle w-100 shadow-sm text-dark fw-medium" data-bs-toggle="dropdown">
                        <i class="bi bi-download me-1"></i> Exportar
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                        <li><a class="dropdown-item py-2" href="{{ route('proveedores.pdf') }}"><i class="bi bi-file-pdf text-danger me-2"></i> Descargar PDF</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('proveedores.exportar') }}"><i class="bi bi-file-excel text-success me-2"></i> Exportar a Excel</a></li>
                    </ul>
                </div>
            </div>
        </form>
    </div>

    <!-- Lista de Proveedores -->
    <div class="table-responsive" style="min-height: 400px;" id="proveedores-container">
        <table class="table table-custom mb-0 w-100">
            <thead class="text-muted small text-uppercase tracking-wider" style="border-bottom: 2px solid #e2e8f0;">
                <tr>
                    <th class="ps-4 pb-3">Proveedor</th>
                    <th class="pb-3">Contacto</th>
                    <th class="pb-3 text-center">Identificación (RNC)</th>
                    <th class="pb-3 text-center">Estado</th>
                    <th class="text-end pe-4 pb-3">Acciones</th>
                </tr>
            </thead>
            <tbody id="proveedores-tbody">
                @forelse($proveedores as $p)
                <tr>
                    <td class="ps-4" style="max-width: 250px;">
                        <div class="d-flex align-items-center">
                            @php
                                $nombreProv = $p->nombre ?? 'P';
                                $firstLetter = strtoupper(substr($nombreProv, 0, 1));
                                $colors = ['#f87171', '#60a5fa', '#34d399', '#fbbf24', '#a78bfa', '#f472b6'];
                                $color = $colors[crc32($nombreProv) % count($colors)];
                            @endphp
                            <div class="avatar-circle text-white me-3 shadow-sm flex-shrink-0" style="background-color: {{ $color }};">
                                <i class="bi bi-building fs-5"></i>
                            </div>
                            <div class="text-truncate">
                                <div class="fw-bold text-dark fs-6 text-truncate" title="{{ $p->nombre }}">{{ $p->nombre }}</div>
                                <div class="text-muted small text-truncate"><i class="bi bi-geo-alt me-1"></i>{{ $p->direccion ?? 'Sin dirección registrada' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-medium text-dark"><i class="bi bi-telephone text-muted me-2"></i>{{ $p->telefono ?? '—' }}</div>
                        <div class="text-muted small mt-1"><i class="bi bi-envelope text-muted me-2"></i>{{ $p->email ?? '—' }}</div>
                    </td>
                    <td class="text-center">
                        <span class="status-badge bg-light text-dark border d-inline-block">
                            <i class="bi bi-card-text me-1"></i> {{ $p->rnc_cedula ?? 'Sin RNC' }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="status-badge bg-success bg-opacity-10 text-success shadow-sm d-inline-block">
                            <i class="bi bi-check-circle-fill me-1"></i> Activo
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('proveedores.show', $p) }}" class="btn btn-icon-hover text-info" title="Ver detalle completo">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('proveedores.edit', $p) }}" class="btn btn-icon-hover text-primary" title="Editar datos">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('proveedores.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este proveedor? Se perderá su historial si no tiene compras asociadas.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-icon-hover text-danger border-0 bg-transparent" title="Eliminar proveedor">
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
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                                <i class="bi bi-truck text-muted opacity-50" style="font-size: 3rem;"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">No hay proveedores registrados</h4>
                            <p class="text-muted mb-4 text-center" style="max-width: 400px;">Aún no se han registrado proveedores en tu directorio o no coinciden con tu búsqueda. ¡Agrega tu primer proveedor!</p>
                            <a href="{{ route('proveedores.create') }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                                <i class="bi bi-plus-lg me-2"></i> Nuevo Proveedor
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    @if(method_exists($proveedores, 'links') && $proveedores->hasPages())
    <div class="mt-4 d-flex justify-content-center" id="pagination-container">
        {{ $proveedores->withQueryString()->links() }}
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('busqueda-proveedor');
        const tableBody = document.getElementById('proveedores-tbody');
        const pagination = document.getElementById('pagination-container');
        let timeout = null;

        if (!searchInput) return;

        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                const query = this.value;
                const url = new URL(window.location.href);
                url.searchParams.set('buscar', query);

                // Visual loading state
                if (tableBody) tableBody.style.opacity = '0.5';

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTbody = doc.getElementById('proveedores-tbody');
                    const newPag = doc.getElementById('pagination-container');

                    if (newTbody && tableBody) {
                        tableBody.innerHTML = newTbody.innerHTML;
                        tableBody.style.opacity = '1';
                    }
                    if (newPag && pagination) {
                        pagination.innerHTML = newPag.innerHTML;
                    } else if (pagination) {
                        pagination.innerHTML = '';
                    }
                })
                .catch(() => {
                    if (tableBody) tableBody.style.opacity = '1';
                });
            }, 400);
        });
    });
</script>
@endsection