@extends('layouts.app')
@section('title', 'Pagos de Compra')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#059669;--accent-rgb:5,150,105;--accent-hover:#047857;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-receipt-cutoff"></i></div>
                <div>
                    <h4 class="ui-header-title">Pagos de Compra</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i>
                        <span>{{ $pagos->total() }} registro(s)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #10b981 !important;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form method="GET" action="{{ route('pagos-compras.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-lg-6">
                        <div class="ui-input-group">
                            <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="ui-input" name="search" value="{{ request('search') }}" placeholder="Buscar por observaciones...">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <select name="metodo_pago" class="ui-select form-select">
                            <option value="">Todos los métodos</option>
                            <option value="efectivo" {{ request('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                            <option value="tarjeta" {{ request('metodo_pago') == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                            <option value="transferencia" {{ request('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <button type="submit" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill w-100">
                            <i class="bi bi-funnel me-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="ui-card overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0" id="pagosTable">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">#</th>
                        <th>Compra</th>
                        <th>Proveedor</th>
                        <th class="text-end">Monto</th>
                        <th>Método</th>
                        <th>Fecha Pago</th>
                        <th class="text-center pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pagos as $p)
                    <tr>
                        <td class="ps-4 font-monospace">{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <div class="fw-semibold small">Compra #{{ str_pad($p->compra_id, 4, '0', STR_PAD_LEFT) }}</div>
                            @if($p->compra)
                            <small class="text-muted">{{ $p->compra->created_at->format('d/m/Y') }}</small>
                            @endif
                        </td>
                        <td><small>{{ $p->compra?->proveedor?->nombre ?? 'N/A' }}</small></td>
                        <td class="text-end fw-bold">RD$ {{ number_format($p->monto, 2) }}</td>
                        <td>
                            <span class="badge {{ $p->metodo_pago == 'efectivo' ? 'bg-success' : ($p->metodo_pago == 'tarjeta' ? 'bg-primary' : 'bg-info') }}">
                                {{ ucfirst($p->metodo_pago) }}
                            </span>
                        </td>
                        <td><small>{{ $p->fecha_pago?->format('d/m/Y H:i') ?? '-' }}</small></td>
                        <td class="text-center pe-4">
                            <a href="{{ route('pagos-compras.show', $p) }}" class="ui-action ui-action-view" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            <form action="{{ route('pagos-compras.cancelar', $p) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="ui-action ui-action-delete" onclick="return confirm('¿Cancelar este pago?')" title="Cancelar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="ui-empty-state">
                                <i class="bi bi-receipt"></i>
                                <p>No hay pagos de compra registrados.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3">
            {{ $pagos->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#pagosTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json', emptyTable: 'No hay pagos en este período' },
        columnDefs: [{ orderable: false, targets: [6] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
@endpush
