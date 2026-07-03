@extends('layouts.app')

@section('title', 'Gestión de Clientes')

@push('styles')
@include('partials.premium-ui')
<style>
.clientes-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(16,185,129,.04);
    margin: 0;
}
.clientes-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.clientes-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
.clientes-table tbody tr:last-child td { border-bottom: none; }
.clientes-table tbody tr { transition: background .15s; }
.clientes-table tbody tr:hover { background: rgba(16,185,129,.03); }
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
body.dark-mode .clientes-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .clientes-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Directorio de Clientes</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-person me-1"></i>
                        Administra tus clientes, contactos y estado de cuentas
                        <span class="mx-2">·</span>
                        <i class="bi bi-list-ul me-1"></i>
                        {{ $clientes->total() }} registro(s)
                    </small>
                </div>
            </div>
            <div>
                @can('clientes.create')
                <a href="{{ route('clientes.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Cliente
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
                        <input type="text" name="nombre" id="busqueda-cliente" class="form-control" placeholder="Nombre, RNC/Cédula, teléfono o email..." value="{{ request('nombre') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <select name="activo" class="form-select" id="filtro-activo">
                        <option value="">Todos</option>
                        <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <select name="segmento" class="form-select">
                        <option value="">Todos los segmentos</option>
                        <option value="micro" {{ request('segmento')=='micro' ? 'selected' : '' }}>Micro</option>
                        <option value="pequeno" {{ request('segmento')=='pequeno' ? 'selected' : '' }}>Pequeño</option>
                        <option value="mediano" {{ request('segmento')=='mediano' ? 'selected' : '' }}>Mediano</option>
                        <option value="grande" {{ request('segmento')=='grande' ? 'selected' : '' }}>Grande</option>
                        <option value="gobierno" {{ request('segmento')=='gobierno' ? 'selected' : '' }}>Gobierno</option>
                    </select>
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
                <div class="col-lg-3 text-end">
                    <a href="{{ route('clientes.creditos.resumen') }}" class="btn btn-outline-light rounded-pill me-2" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.25);">
                        <i class="bi bi-credit-card me-1"></i> Créditos
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary rounded-pill dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-download me-1"></i> Exportar
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                            <li><a class="dropdown-item py-2" href="{{ route('clientes.pdf') }}"><i class="bi bi-file-pdf text-danger me-2"></i> Descargar PDF</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('clientes.exportar') }}"><i class="bi bi-file-excel text-success me-2"></i> Exportar a Excel</a></li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card" style="animation-delay:.15s;">
        <div class="card-accent green"></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table clientes-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Cliente</th>
                            <th>Contacto</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Segmento</th>
                            <th class="text-end">Límite Crédito</th>
                            <th class="text-end">Balance Pte.</th>
                            <th class="text-center">Crédito</th>
                            <th class="text-center">Activo</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="clientes-tbody">
                        @forelse($clientes as $c)
                            <tr>
                                <td class="ps-4">
                                    @php
                                        $nombreCliente = $c->nombre ?? 'D';
                                        $firstLetter = strtoupper(substr($nombreCliente, 0, 1));
                                        $colors = ['#f87171', '#60a5fa', '#34d399', '#fbbf24', '#a78bfa', '#f472b6'];
                                        $color = $colors[crc32($nombreCliente) % count($colors)];
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle text-white me-3 shadow-sm" style="background-color: {{ $color }};">
                                            {{ $firstLetter }}
                                        </div>
                                        <div class="text-truncate">
                                            <div class="fw-bold text-dark fs-6 text-truncate" title="{{ $c->nombre }}">{{ $c->nombre }}</div>
                                            <div class="text-muted small text-truncate"><i class="bi bi-geo-alt me-1"></i>{{ $c->ciudad ?? $c->direccion ?? 'Sin dirección' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark"><i class="bi bi-telephone text-muted me-2"></i>{{ $c->telefono ?? '—' }}</div>
                                    <div class="text-muted small mt-1"><i class="bi bi-envelope text-muted me-2"></i>{{ $c->email ?? '—' }}</div>
                                    <div class="text-muted small mt-1"><i class="bi bi-person-lines-fill text-muted me-2"></i>{{ $c->persona_contacto ?? '—' }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="status-badge bg-{{ $c->color_badge }} bg-opacity-10 text-{{ $c->color_badge }} d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-tag"></i>
                                        {{ $c->tipo_cliente_label }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="status-badge bg-secondary bg-opacity-10 text-secondary d-inline-flex align-items-center gap-1">
                                        {{ $c->segmento_label }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="fw-semibold fs-6">RD$ {{ number_format($c->limite_credito, 2) }}</div>
                                </td>
                                <td class="text-end">
                                    @if($c->balance_pendiente > 0)
                                        <div class="fw-bold text-danger fs-6">RD$ {{ number_format($c->balance_pendiente, 2) }}</div>
                                    @else
                                        <div class="fw-bold text-success fs-6">RD$ 0.00</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($c->limite_credito > 0)
                                        <div class="d-flex align-items-center gap-1 justify-content-center">
                                            <div class="progress" style="width:50px;height:6px;">
                                                <div class="progress-bar bg-{{ $c->color_badge_estado_credito }}"
                                                    style="width:{{ min($c->utilizacion_credito, 100) }}%">
                                                </div>
                                            </div>
                                            <span class="small {{ $c->utilizacion_credito >= 80 ? 'text-danger' : 'text-muted' }}">
                                                {{ $c->utilizacion_credito }}%
                                            </span>
                                        </div>
                                        <div class="text-muted small mt-1">{{ $c->estado_credito_label }}</div>
                                    @else
                                        <span class="text-muted small">Sin límite</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="status-badge bg-{{ $c->color_badge_activo }} bg-opacity-10 text-{{ $c->color_badge_activo }} d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-{{ $c->activo ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
                                        {{ $c->activo_label }}
                                    </span>
                                    @can('clientes.edit')
                                    <button type="button" class="btn btn-sm ms-1 p-0 border-0 bg-transparent toggle-activo"
                                        data-id="{{ $c->id }}"
                                        data-activo="{{ $c->activo }}"
                                        title="Cambiar estado">
                                        <i class="bi bi-arrow-repeat text-muted" style="font-size:.8rem;"></i>
                                    </button>
                                    @endcan
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('clientes.show', $c) }}" class="premium-btn-edit" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('clientes.edit', $c) }}" class="premium-btn-edit" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('clientes.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este cliente?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="premium-btn-delete" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-1" style="color:#cbd5e1;"></i>
                                    <p class="mt-2 mb-0 fw-semibold">No hay clientes registrados</p>
                                    @can('clientes.create')
                                    <a href="{{ route('clientes.create') }}" class="btn btn-primary rounded-pill mt-2">Registrar primer cliente</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($clientes->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $clientes->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('busqueda-cliente');
    const tableBody = document.getElementById('clientes-tbody');
    let timeout = null;

    if (!searchInput) return;

    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            const query = this.value;
            const url = new URL(window.location.href);
            url.searchParams.set('nombre', query);

            if (tableBody) tableBody.style.opacity = '0.5';

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTbody = doc.getElementById('clientes-tbody');

                if (newTbody && tableBody) {
                    tableBody.innerHTML = newTbody.innerHTML;
                    tableBody.style.opacity = '1';
                }
            })
            .catch(() => {
                if (tableBody) tableBody.style.opacity = '1';
            });
        }, 400);
    });

    // Toggle activo inline
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.toggle-activo');
        if (!btn) return;

        const id = btn.dataset.id;
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        fetch('/clientes/' + id + '/toggle', {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const row = btn.closest('tr');
                const badge = row.querySelector('.status-badge');
                const icon = badge.querySelector('i');
                if (data.activo) {
                    badge.className = 'status-badge bg-success bg-opacity-10 text-success d-inline-flex align-items-center gap-1';
                    icon.className = 'bi bi-check-circle-fill';
                    badge.childNodes[1].textContent = ' Activo';
                } else {
                    badge.className = 'status-badge bg-secondary bg-opacity-10 text-secondary d-inline-flex align-items-center gap-1';
                    icon.className = 'bi bi-x-circle-fill';
                    badge.childNodes[1].textContent = ' Inactivo';
                }
                btn.dataset.activo = data.activo ? '1' : '0';
            }
        })
        .catch(() => {});
    });
});
</script>
@endpush
