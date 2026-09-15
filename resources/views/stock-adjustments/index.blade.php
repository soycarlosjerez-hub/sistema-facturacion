@extends('layouts.app')

@section('title', 'Ajustes de Stock')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-box-arrow-up-right"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Ajustes de Stock</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i>
                        <span>{{ $adjustments->total() }} registro(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('stock-adjustments.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Ajuste
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form method="GET" action="{{ route('stock-adjustments.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-lg-4 col-md-6">
                        <div class="ui-input-group">
                            <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="buscar" class="ui-input" name="search" value="{{ request('search') }}" placeholder="Buscar producto, motivo, notas...">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <select name="sucursal_id" class="ui-select form-select">
                            <option value="">Todas las sucursales</option>
                            @foreach($adjustments->total() > 0 ? ($adjustments->first()?->sucursal ? collect([$adjustments->first()->sucursal]) : collect()) : collect() as $_s)
                                <option value="{{ $_s->id }}" {{ request('sucursal_id') == $_s->id ? 'selected' : '' }}>{{ $_s->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <select name="tipo" class="ui-select form-select">
                            <option value="">Todos los tipos</option>
                            <option value="ajuste" {{ request('tipo') == 'ajuste' ? 'selected' : '' }}>Ajuste</option>
                            <option value="merma" {{ request('tipo') == 'merma' ? 'selected' : '' }}>Merma</option>
                            <option value="inventario" {{ request('tipo') == 'inventario' ? 'selected' : '' }}>Inventario</option>
                            <option value="dacion" {{ request('tipo') == 'dacion' ? 'selected' : '' }}>Dación</option>
                            <option value="recepcion" {{ request('tipo') == 'recepcion' ? 'selected' : '' }}>Recepción</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <button type="submit" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill w-100">
                            <i class="bi bi-funnel me-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #10b981 !important;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table -->
    <div class="ui-card overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0" id="ajustesTable">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">#</th>
                        <th>Producto</th>
                        <th>Almacén</th>
                        <th>Tipo</th>
                        <th class="text-center">Anterior</th>
                        <th class="text-center">Nuevo</th>
                        <th class="text-center">Diferencia</th>
                        <th>Fecha</th>
                        <th class="text-center pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adjustments as $a)
                    <tr>
                        <td class="ps-4">{{ str_pad($a->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <div class="fw-semibold small">{{ $a->producto?->nombre ?? 'N/A' }}</div>
                            <small class="text-muted font-monospace">{{ $a->producto?->codigo_barras ?? '' }}</small>
                        </td>
                        <td><small>{{ $a->almacen?->nombre ?? ($a->sucursal?->nombre ?? '-') }}</small></td>
                        <td>
                            <span class="badge {{ $a->tipo == 'merma' ? 'bg-danger' : ($a->tipo == 'inventario' ? 'bg-info' : 'bg-primary') }}">
                                {{ $a->tipo_label }}
                            </span>
                        </td>
                        <td class="text-center fw-semibold">{{ $a->cantidad_anterior }}</td>
                        <td class="text-center fw-semibold">{{ $a->cantidad_nueva }}</td>
                        <td class="text-center">
                            <span class="badge {{ $a->diferencia >= 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ $a->diferencia >= 0 ? '+' : '' }}{{ $a->diferencia }}
                            </span>
                        </td>
                        <td><small>{{ $a->created_at->format('d/m/Y H:i') }}</small></td>
                        <td class="text-center pe-4">
                            <a href="{{ route('stock-adjustments.show', $a) }}" class="ui-action ui-action-view" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="ui-empty-state">
                                <i class="bi bi-box-seam"></i>
                                <p>No hay ajustes de stock registrados.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3">
            {{ $adjustments->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#ajustesTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json', emptyTable: 'No hay ajustes en este período' },
        columnDefs: [{ orderable: false, targets: [8] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
@endpush
