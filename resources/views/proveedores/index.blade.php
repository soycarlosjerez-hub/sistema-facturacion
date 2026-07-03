@extends('layouts.app')

@section('title', 'Gestión de Proveedores')

@push('styles')
@include('partials.premium-ui')
<style>
.proveedores-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(59,130,246,.04);
    margin: 0;
}
.proveedores-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.proveedores-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
.proveedores-table tbody tr:last-child td { border-bottom: none; }
.proveedores-table tbody tr { transition: background .15s; }
.proveedores-table tbody tr:hover { background: rgba(59,130,246,.03); }
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
body.dark-mode .proveedores-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .proveedores-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4" style="background:linear-gradient(135deg,#3b82f6,#6366f1,#8b5cf6,#3b82f6);box-shadow:0 8px 32px rgba(59,130,246,.25);">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle" style="background:rgba(255,255,255,.2);border-color:rgba(255,255,255,.35);">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Catálogo de Proveedores</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-building me-1"></i>
                        Gestión de proveedores y contactos de negocio
                        <span class="mx-2">·</span>
                        <i class="bi bi-list-ul me-1"></i>
                        {{ $proveedores->total() }} registro(s)
                    </small>
                </div>
            </div>
            <div>
                @can('proveedores.create')
                <a href="{{ route('proveedores.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Proveedor
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="premium-card mb-4" style="animation-delay:.1s;">
        <div class="card-accent blue"></div>
        <div class="card-body p-3">
            <form method="GET" id="search-form" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" id="busqueda-proveedor" class="form-control" placeholder="Nombre, RNC, teléfono o email..." value="{{ request('buscar') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2 d-flex align-items-center">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="incluir_inactivos" value="1" id="incluir_inactivos" {{ request('incluir_inactivos') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label small fw-bold text-muted" for="incluir_inactivos">Incluir inactivos</label>
                    </div>
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('proveedores.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
                <div class="col-lg-4 text-end">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary rounded-pill dropdown-toggle" data-bs-toggle="dropdown">
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
    </div>

    <div class="premium-card" style="animation-delay:.15s;">
        <div class="card-accent blue"></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table proveedores-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Proveedor</th>
                            <th>Contacto</th>
                            <th class="text-center">Identificación (RNC)</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="proveedores-tbody">
                        @forelse($proveedores as $p)
                            <tr>
                                <td class="ps-4">
                                    @php
                                        $nombreProv = $p->nombre ?? 'P';
                                        $firstLetter = strtoupper(substr($nombreProv, 0, 1));
                                        $colors = ['#f87171', '#60a5fa', '#34d399', '#fbbf24', '#a78bfa', '#f472b6'];
                                        $color = $colors[crc32($nombreProv) % count($colors)];
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle text-white me-3 shadow-sm" style="background-color: {{ $color }};">
                                            <i class="bi bi-building fs-5"></i>
                                        </div>
                                        <div class="text-truncate">
                                            <div class="fw-bold text-dark fs-6 text-truncate" title="{{ $p->nombre }}">{{ $p->nombre }}</div>
                                            <div class="text-muted small text-truncate"><i class="bi bi-geo-alt me-1"></i>{{ $p->direccion ?? 'Sin dirección' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark"><i class="bi bi-telephone text-muted me-2"></i>{{ $p->telefono ?? '—' }}</div>
                                    <div class="text-muted small mt-1"><i class="bi bi-envelope text-muted me-2"></i>{{ $p->email ?? '—' }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border rounded-pill">
                                        <i class="bi bi-card-text me-1"></i> {{ $p->rnc_cedula ?? 'Sin RNC' }}
                                    </span>
                                </td>
                                <td class="text-center">
                    @if($p->activo)
                    <span class="status-badge bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle-fill me-1"></i> Activo
                    </span>
                    @else
                    <span class="status-badge bg-secondary bg-opacity-10 text-secondary">
                        <i class="bi bi-x-circle-fill me-1"></i> Inactivo
                    </span>
                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('proveedores.show', $p) }}" class="premium-btn-edit" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('proveedores.edit', $p) }}" class="premium-btn-edit" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('proveedores.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este proveedor?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="premium-btn-delete" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-truck fs-1" style="color:#cbd5e1;"></i>
                                    <p class="mt-2 mb-0 fw-semibold">No hay proveedores registrados</p>
                                    @can('proveedores.create')
                                    <a href="{{ route('proveedores.create') }}" class="btn btn-primary rounded-pill mt-2">Registrar primer proveedor</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(method_exists($proveedores, 'links') && $proveedores->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $proveedores->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('busqueda-proveedor');
    const tableBody = document.getElementById('proveedores-tbody');
    let timeout = null;

    if (!searchInput) return;

    const incluirInactivos = document.getElementById('incluir_inactivos');
    function actualizarBusqueda() {
        const query = searchInput.value;
        const url = new URL(window.location.href);
        url.searchParams.set('buscar', query);
        url.searchParams.set('incluir_inactivos', incluirInactivos && incluirInactivos.checked ? '1' : '');

        if (tableBody) tableBody.style.opacity = '0.5';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTbody = doc.getElementById('proveedores-tbody');
            const newPaginator = doc.querySelector('.pagination')?.outerHTML;
            if (newPaginator) {
                const oldPaginator = document.querySelector('.pagination')?.closest('.d-flex');
                if (oldPaginator) oldPaginator.outerHTML = doc.querySelector('.pagination')?.closest('.d-flex')?.outerHTML || '';
            }
            if (newTbody && tableBody) {
                tableBody.innerHTML = newTbody.innerHTML;
                tableBody.style.opacity = '1';
            }
        })
        .catch(() => {
            if (tableBody) tableBody.style.opacity = '1';
        });
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(actualizarBusqueda, 400);
    });
    if (incluirInactivos) {
        incluirInactivos.addEventListener('change', actualizarBusqueda);
    }
});
</script>
@endpush
