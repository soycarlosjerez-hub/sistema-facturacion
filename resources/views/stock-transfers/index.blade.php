@extends('layouts.app')
@section('title', 'Transferencias de Stock')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-arrow-left-right"></i></div>
                <div>
                    <h4 class="ui-header-title">Transferencias de Stock</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i>
                        <span>{{ $transfers->total() }} registro(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('stock-transfers.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Transferencia
                </a>
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
            <form method="GET" action="{{ route('stock-transfers.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-lg-4 col-md-6">
                        <div class="ui-input-group">
                            <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="ui-input" name="search" value="{{ request('search') }}" placeholder="Buscar por código...">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <select name="estado" class="ui-select form-select">
                            <option value="">Todos los estados</option>
                            <option value="borrador" {{ request('estado') == 'borrador' ? 'selected' : '' }}>Borrador</option>
                            <option value="aprobada" {{ request('estado') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                            <option value="enviada" {{ request('estado') == 'enviada' ? 'selected' : '' }}>Enviada</option>
                            <option value="recibida" {{ request('estado') == 'recibida' ? 'selected' : '' }}>Recibida</option>
                            <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-4">
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
            <table class="table align-middle mb-0" id="transfersTable">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">Código</th>
                        <th>Sucursal Origen</th>
                        <th>Sucursal Destino</th>
                        <th>Productos</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th class="text-center pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $t)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-semibold font-monospace small">{{ $t->codigo }}</span>
                            @if($t->notas)
                            <br><small class="text-muted" style="max-width:200px">{{ Str::limit($t->notas, 40) }}</small>
                            @endif
                        </td>
                        <td><small>{{ $t->sucursal_origen?->nombre ?? '-' }}</small></td>
                        <td><small>{{ $t->sucursal_destino?->nombre ?? '-' }}</small></td>
                        <td>
                            <span class="badge bg-secondary rounded-pill">{{ $t->detalle->count() }} ítem(s)</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $t->estado_badge_color }}">
                                {{ $t->estado_label }}
                            </span>
                        </td>
                        <td><small>{{ $t->created_at->format('d/m/Y H:i') }}</small></td>
                        <td class="text-center pe-4">
                            <a href="{{ route('stock-transfers.show', $t) }}" class="ui-action ui-action-view" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="ui-empty-state">
                                <i class="bi bi-arrow-left-right"></i>
                                <p>No hay transferencias de stock registradas.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3">
            {{ $transfers->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#transfersTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json', emptyTable: 'No hay transferencias en este período' },
        columnDefs: [{ orderable: false, targets: [6] }],
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>t<"d-flex flex-wrap justify-content-between align-items-center"ip>',
    });
});
</script>
@endpush
