@extends('layouts.app')

@section('title', 'Reporte de Equipos')

@push('styles')
@include('partials.premium-ui')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<style>
    .ui-card-subtitle { color: var(--text-muted); font-size: 0.85rem; }
    .stat-card { border-radius: 12px; padding: 16px; text-align: center; }
    .stat-card .stat-value { font-size: 1.8rem; font-weight: 800; }
    .stat-card .stat-label { font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; }
    .stat-card.primary { background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); }
    .stat-card.success { background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); }
    .stat-card.warning { background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); }
    .stat-card.info { background: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.2); }
    .stat-card.danger { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); }
    .dataTables_wrapper .dataTables_filter input { border-radius: 20px; }
    .dataTables_wrapper .dataTables_length select { border-radius: 20px; }
</style>
@endpush

@section('content')
<div class="ui-page">
    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle" style="--accent:#8b5cf6;--accent-rgb:139,92,246;">
                    <i class="bi bi-phone"></i>
                </div>
                <div>
                    <div class="ui-header-title">Reporte de Equipos</div>
                    <div class="ui-header-meta">
                        <i class="bi bi-graph-up me-1"></i>
                        Historial de ventas de equipos con trazabilidad IMEI/Serial
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('ventas.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-circle me-2"></i>Nueva Venta
                </a>
                <a href="{{ route('equipos.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-list-ul me-2"></i>Ver Equipos
                </a>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="ui-card mb-4">
        <div class="ui-card-body">
            <form method="GET" action="{{ route('reportes.equipos') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="ui-label small fw-semibold">Desde</label>
                    <input type="date" name="desde" class="ui-input" value="{{ $desde }}" required>
                </div>
                <div class="col-md-3">
                    <label class="ui-label small fw-semibold">Hasta</label>
                    <input type="date" name="hasta" class="ui-input" value="{{ $hasta }}" required>
                </div>
                <div class="col-md-2">
                    <label class="ui-label small fw-semibold">Marca</label>
                    <select name="marca" class="ui-select">
                        <option value="">Todas</option>
                        @foreach($marcas as $m)
                            <option value="{{ $m }}" {{ $marca == $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="ui-label small fw-semibold">Tipo</label>
                    <select name="tipo_dispositivo" class="ui-select">
                        <option value="">Todos</option>
                        @foreach($tiposDispositivo as $t)
                            <option value="{{ $t }}" {{ $tipoDispositivo == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="ui-btn ui-btn-primary rounded-pill flex-grow-1">
                        <i class="bi bi-funnel me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('reportes.equipos.export') . '?desde=' . $desde . '&hasta=' . $hasta . '&marca=' . $marca . '&tipo_dispositivo=' . $tipoDispositivo }}" class="ui-btn ui-btn-solid rounded-pill" style="background:#10b981;border-color:#10b981;" title="Exportar Excel">
                        <i class="bi bi-file-earmark-excel"></i>
                    </a>
                    <a href="{{ route('reportes.equipos.pdf') . '?desde=' . $desde . '&hasta=' . $hasta . '&marca=' . $marca . '&tipo_dispositivo=' . $tipoDispositivo }}" class="ui-btn ui-btn-solid rounded-pill" style="background:#ef4444;border-color:#ef4444;" title="Exportar PDF">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card primary">
                <div class="stat-value">{{ number_format($totalEquipos) }}</div>
                <div class="stat-label">Total Equipos</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card success">
                <div class="stat-value">{{ number_format($totalVendidos) }}</div>
                <div class="stat-label">Vendidos</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card warning">
                <div class="stat-value">{{ number_format($totalDisponibles) }}</div>
                <div class="stat-label">Disponibles</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card danger">
                <div class="stat-value" style="color: var(--accent);">RD$ {{ number_format($totalIngresos, 2) }}</div>
                <div class="stat-label">Ingresos</div>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="ui-card">
        <div class="ui-card-body p-0">
            <table class="table table-hover align-middle mb-0" id="equiposTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>IMEI</th>
                        <th>ESN</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Color</th>
                        <th>Tipo</th>
                        <th>Cliente</th>
                        <th>NCF</th>
                        <th>Precio</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($equipos as $equipoVenta)
                    <tr>
                        <td>{{ $loop->iteration + ($equipos->currentPage() - 1) * $equipos->perPage() }}</td>
                        <td><code>{{ $equipoVenta->equipo->serial_imei }}</code></td>
                        <td>{{ $equipoVenta->equipo->serial_esn ?? '-' }}</td>
                        <td>{{ $equipoVenta->equipo->marca }}</td>
                        <td>{{ $equipoVenta->equipo->modelo ?? '-' }}</td>
                        <td>{{ $equipoVenta->equipo->color ?? '-' }}</td>
                        <td>{{ ucfirst($equipoVenta->equipo->tipo_dispositivo ?? '') }}</td>
                        <td>{{ $equipoVenta->venta->cliente->nombre ?? '-' }}</td>
                        <td><small>{{ $equipoVenta->venta->ncf ?? '-' }}</small></td>
                        <td class="fw-bold text-success">RD$ {{ number_format($equipoVenta->precio_vendido, 2) }}</td>
                        <td>{{ $equipoVenta->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('ventas.show', $equipoVenta->venta_id) }}" class="btn btn-sm btn-outline-primary" title="Ver venta">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No hay equipos que coincidan con los filtros
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $equipos->withQueryString()->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#equiposTable').DataTable({
            responsive: true,
            pageLength: 20,
            lengthMenu: [10, 20, 50, 100],
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ equipos',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ equipos',
                infoEmpty: 'No hay equipos',
                infoFiltered: '(filtrado de _MAX_ total)',
                paginate: {
                    first: 'Primero',
                    last: 'Último',
                    next: 'Siguiente',
                    previous: 'Anterior'
                },
                zeroRecords: 'No se encontraron equipos',
            },
            order: [[10, 'desc']],
            columnDefs: [
                { orderable: false, targets: [11] },
                { className: 'dt-head-center dt-head-center', targets: '_all' }
            ]
        });
    });
</script>
@endpush
