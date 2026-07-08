@extends('layouts.app')

@section('title', 'Cuentas Bancarias')

@push('styles')
@include('partials.premium-ui')
<style>
.cuentas-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(5,150,105,.04);
    margin: 0;
}
.cuentas-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.cuentas-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
.cuentas-table tbody tr:last-child td { border-bottom: none; }
.cuentas-table tbody tr { transition: background .15s; }
.cuentas-table tbody tr:hover { background: rgba(5,150,105,.03); }
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
body.dark-mode .cuentas-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .cuentas-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4" style="background:linear-gradient(135deg,#059669,#10b981,#34d399,#059669);box-shadow:0 8px 32px rgba(5,150,105,.25);">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle" style="background:rgba(255,255,255,.2);border-color:rgba(255,255,255,.35);">
                    <i class="bi bi-bank"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Cuentas Bancarias</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-building me-1"></i>
                        Gestión de cuentas bancarias para pagos por transferencia
                        <span class="mx-2">·</span>
                        <i class="bi bi-list-ul me-1"></i>
                        {{ $cuentas->total() }} registro(s)
                    </small>
                </div>
            </div>
            <div>
                @can('cuentas-bancarias.create')
                <a href="{{ route('cuentas-bancarias.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Cuenta
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="premium-card mb-4" style="animation-delay:.1s;">
        <div class="card-accent green"></div>
        <div class="card-body p-3">
            <form method="GET" id="search-form" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" id="busqueda-cuenta" class="form-control" placeholder="Nombre, banco, número de cuenta..." value="{{ request('buscar') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2 d-flex align-items-center">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="incluir_inactivos" value="1" id="incluir_inactivos" {{ request('incluir_inactivos') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label small fw-bold text-muted" for="incluir_inactivos">Incluir inactivas</label>
                    </div>
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('cuentas-bancarias.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card" style="animation-delay:.15s;">
        <div class="card-accent green"></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table cuentas-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Cuenta</th>
                            <th>Banco / Número</th>
                            <th class="text-center">Moneda</th>
                            <th class="text-end">Saldo Actual</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="cuentas-tbody">
                        @forelse($cuentas as $c)
                            <tr>
                                <td class="ps-4">
                                    @php
                                        $colors = ['#059669', '#10b981', '#34d399', '#6ee7b7', '#047857', '#065f46'];
                                        $color = $colors[crc32($c->nombre) % count($colors)];
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle text-white me-3 shadow-sm" style="background-color: {{ $color }};">
                                            <i class="bi bi-bank fs-5"></i>
                                        </div>
                                        <div class="text-truncate">
                                            <div class="fw-bold text-dark fs-6 text-truncate" title="{{ $c->nombre }}">{{ $c->nombre }}</div>
                                            <div class="text-muted small text-truncate"><i class="bi bi-person me-1"></i>{{ $c->titular ?? 'Sin titular' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark"><i class="bi bi-building text-muted me-2"></i>{{ $c->banco ?? '—' }}</div>
                                    <div class="text-muted small mt-1"><i class="bi bi-hash text-muted me-2"></i>{{ $c->numero_cuenta ?? '—' }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border rounded-pill">{{ $c->moneda }}</span>
                                </td>
                                <td class="text-end fw-bold">{{ number_format($c->saldo_actual, 2) }}</td>
                                <td class="text-center">
                                    @if($c->activo)
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
                                    <a href="{{ route('cuentas-bancarias.show', $c) }}" class="premium-btn-edit" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('cuentas-bancarias.edit', $c) }}" class="premium-btn-edit" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('cuentas-bancarias.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar esta cuenta bancaria?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="premium-btn-delete" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-bank fs-1" style="color:#cbd5e1;"></i>
                                    <p class="mt-2 mb-0 fw-semibold">No hay cuentas bancarias registradas</p>
                                    @can('cuentas-bancarias.create')
                                    <a href="{{ route('cuentas-bancarias.create') }}" class="btn btn-primary rounded-pill mt-2">Registrar primera cuenta</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(method_exists($cuentas, 'links') && $cuentas->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $cuentas->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('busqueda-cuenta');
    const tableBody = document.getElementById('cuentas-tbody');
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
            const newTbody = doc.getElementById('cuentas-tbody');
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
