@extends('layouts.app')

@section('title', 'Historial de Ventas')

@push('styles')
@include('partials.premium-ui')
<style>
.ventas-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(59,130,246,.04);
    margin: 0;
}
.ventas-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.ventas-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
.ventas-table tbody tr:last-child td { border-bottom: none; }
.ventas-table tbody tr { transition: background .15s; }
.ventas-table tbody tr:hover { background: rgba(59,130,246,.03); }
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
body.dark-mode .ventas-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .ventas-table tbody td {
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
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Gestión de Ventas</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-receipt me-1"></i>
                        Administración de ventas, facturación y cuentas por cobrar
                        <span class="mx-2">·</span>
                        <i class="bi bi-list-ul me-1"></i>
                        {{ $ventas->total() }} registro(s)
                    </small>
                </div>
            </div>
            <div>
                @can('ventas.create')
                <a href="{{ route('ventas.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Venta
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="premium-card mb-4" style="animation-delay:.1s;">
        <div class="card-accent blue"></div>
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-center" id="filtros-form">
                <div class="col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="cliente" id="busqueda-cliente" class="form-control" placeholder="Buscar por cliente o NCF..." value="{{ request('cliente') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <input type="date" name="desde" class="form-control" value="{{ request('desde') }}" placeholder="Desde">
                </div>
                <div class="col-lg-2">
                    <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}" placeholder="Hasta">
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
                <div class="col-lg-2 text-end">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary rounded-pill dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-download me-1"></i> Exportar
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                            <li><a class="dropdown-item py-2" href="{{ route('ventas.pdf', request()->query()) }}"><i class="bi bi-file-pdf text-danger me-2"></i> Descargar PDF</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('ventas.exportar', request()->query()) }}"><i class="bi bi-file-excel text-success me-2"></i> Exportar a Excel</a></li>
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
                <table class="table ventas-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Comprobante</th>
                            <th>Cliente</th>
                            <th>Fecha &amp; Hora</th>
                            <th>Tipo de Venta</th>
                            <th class="text-end">Montos</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="ventas-tbody">
                        @forelse($ventas as $v)
                            @php
                                $esFiado = in_array($v->tipoVenta?->nombre, ['Fiado', 'Crédito']);
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark fs-6">#{{ str_pad($v->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        @if($v->ncf)
                                            <span class="badge bg-light text-muted mt-1 border rounded-pill text-truncate" style="max-width:130px;">
                                                <i class="bi bi-receipt-cutoff me-1"></i>{{ $v->ncf }}
                                            </span>
                                        @else
                                            <span class="text-muted small mt-1">&mdash;</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $nombreCliente = $v->cliente->nombre ?? 'Consumidor Final';
                                        $firstLetter = strtoupper(substr($nombreCliente, 0, 1));
                                        $colors = ['#f87171', '#60a5fa', '#34d399', '#fbbf24', '#a78bfa', '#f472b6'];
                                        $color = $v->cliente ? $colors[crc32($nombreCliente) % count($colors)] : '#9ca3af';
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle text-white me-3 shadow-sm" style="background-color: {{ $color }}; width:40px;height:40px;font-size:1.1rem;">
                                            {{ $firstLetter }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-6">{{ $nombreCliente }}</div>
                                            <div class="text-muted small"><i class="bi bi-person-badge me-1"></i>Cajero: {{ $v->usuario->name ?? 'Sistema' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $v->created_at->format('d/m/Y') }}</div>
                                    <div class="text-muted small"><i class="bi bi-clock me-1"></i>{{ $v->created_at->format('h:i A') }}</div>
                                </td>
                                <td>
                                    <span class="premium-badge {{ $esFiado ? 'active' : '' }}" style="{{ !$esFiado ? 'background:rgba(59,130,246,.1);color:#3b82f6;border-color:rgba(59,130,246,.2);' : '' }}">
                                        @if($esFiado)
                                            <i class="bi bi-credit-card me-1"></i>
                                        @else
                                            <i class="bi bi-cash me-1"></i>
                                        @endif
                                        {{ $v->tipoVenta->nombre ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="text-muted small mb-1">Sub: RD$ {{ number_format($v->total - $v->impuestos, 2) }}</div>
                                    <div class="fw-bold text-primary fs-5">RD$ {{ number_format($v->total, 2) }}</div>
                                </td>
                                <td class="text-center">
                                    @if ($v->estado == 'completada')
                                        <span class="status-badge bg-success bg-opacity-10 text-success">
                                            <i class="bi bi-check-circle-fill me-1"></i>Pagada
                                        </span>
                                    @elseif ($v->estado == 'cuenta_abierta')
                                        <span class="status-badge bg-primary bg-opacity-10 text-primary">
                                            <i class="bi bi-door-open-fill me-1"></i>Cta. Abierta
                                        </span>
                                    @else
                                        <span class="status-badge bg-warning bg-opacity-10 text-warning">
                                            <i class="bi bi-exclamation-circle-fill me-1"></i>Por Pagar
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('ventas.show', $v->id) }}" class="premium-btn-edit" title="Ver Detalle">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('pagos.realizar', $v->id) }}" class="premium-btn-edit" title="Cobrar / Abono" style="background:rgba(59,130,246,.1);color:#3b82f6;border-color:rgba(59,130,246,.2);">
                                            <i class="bi bi-cash-coin"></i>
                                        </a>
                                        <a href="{{ route('venta.pdf', $v->id) }}" class="premium-btn-edit" title="Reimprimir" style="background:rgba(100,116,139,.1);color:#64748b;border-color:rgba(100,116,139,.2);">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                        <form action="{{ route('ventas.destroy', $v->id) }}" method="POST" class="d-inline form-anular" data-venta-id="{{ $v->id }}" data-venta-label="#{{ str_pad($v->id, 5, '0', STR_PAD_LEFT) }}">
                                            @csrf @method('DELETE')
                                            <input type="hidden" name="motivo" class="motivo-input">
                                            <input type="hidden" name="confirmar" value="1">
                                            <button type="button" class="premium-btn-delete btn-trigger-anular" title="Anular">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-receipt fs-1" style="color:#cbd5e1;"></i>
                                    <p class="mt-2 mb-0 fw-semibold">No hay ventas registradas</p>
                                    @can('ventas.create')
                                    <a href="{{ route('ventas.create') }}" class="btn btn-primary rounded-pill mt-2">Registrar primera venta</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($ventas->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $ventas->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('busqueda-cliente');
    const tableBody = document.getElementById('ventas-tbody');
    let timeout = null;

    if (!searchInput) return;

    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            const query = this.value;
            const url = new URL(window.location.href);
            url.searchParams.set('cliente', query);

            if (tableBody) tableBody.style.opacity = '0.5';

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTbody = doc.getElementById('ventas-tbody');

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

    // Anular venta con motivo
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-trigger-anular');
        if (!btn) return;

        const form = btn.closest('.form-anular');
        if (!form) return;

        const ventaLabel = form.dataset.ventaLabel || '';
        const motivoInput = form.querySelector('.motivo-input');

        Swal.fire({
            title: 'Anular Venta',
            html: `
                <p class="text-muted mb-3">¿Anular la venta <strong>${ventaLabel}</strong>?</p>
                <textarea id="swal-motivo" class="form-control" rows="3" placeholder="Motivo de la anulación (mín. 5 caracteres)" required></textarea>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, anular',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#ef4444',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const motivo = document.getElementById('swal-motivo').value.trim();
                if (!motivo || motivo.length < 5) {
                    Swal.showValidationMessage('El motivo debe tener al menos 5 caracteres');
                    return false;
                }
                return motivo;
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then(function(result) {
            if (result.isConfirmed) {
                motivoInput.value = result.value;
                form.submit();
            }
        });
    });
});
</script>
@endpush
