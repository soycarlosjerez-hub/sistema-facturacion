@extends('layouts.app')
@section('title', 'Dashboard Tecnología')

@push('styles')
@include('partials.premium-ui')
<style>
:root {
    --accent: #3b82f6;
    --accent-rgb: 59,130,246;
    --accent-hover: #2563eb;
}
.kpi-card {
    background: rgba(255,255,255,0.9);
    border-radius: 1rem;
    padding: 1.25rem;
    border: 1px solid rgba(59,130,246,0.1);
    transition: all 0.3s;
}
.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59,130,246,0.15);
}
.kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 2rem;
    font-size: 0.75rem;
    font-weight: 600;
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-cpu"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Tienda de Tecnología</h4>
                    <div class="ui-header-meta">Dashboard de ventas, reparaciones e infraestructura</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('tecnologia.dashboard.kpis') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" style="background:rgba(255,255,255,.15);" onclick="refreshKpis(this)">
                    <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="kpi-card">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon me-3" style="background:linear-gradient(135deg,#3b82f6,#2563eb);">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Productos</div>
                        <div class="fw-bold fs-5">{{ number_format($kpis['totalProductos'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="kpi-card">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon me-3" style="background:linear-gradient(135deg,#22c55e,#16a34a);">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Disponibles</div>
                        <div class="fw-bold fs-5">{{ number_format($kpis['equiposDisponibles'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="kpi-card">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon me-3" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div>
                        <div class="text-muted small">En Reparación</div>
                        <div class="fw-bold fs-5">{{ number_format($kpis['equiposEnReparacion'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="kpi-card">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon me-3" style="background:linear-gradient(135deg,#ef4444,#dc2626);">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Órdenes Pend.</div>
                        <div class="fw-bold fs-5">{{ number_format($kpis['ordenesPendientes'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="kpi-card">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon me-3" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Técnicos</div>
                        <div class="fw-bold fs-5">{{ number_format($kpis['totalTecnicos'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="kpi-card">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon me-3" style="background:linear-gradient(135deg,#06b6d4,#0891b2);">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Presupuestos</div>
                        <div class="fw-bold fs-5">{{ number_format($kpis['presupuestosActivos'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="kpi-card text-center">
                <div class="text-muted small mb-1">Marcas Tecnológicas</div>
                <div class="fw-bold fs-4" style="color:#3b82f6;">{{ number_format($kpis['totalMarcas'] ?? 0) }}</div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="kpi-card text-center">
                <div class="text-muted small mb-1">Redes Config</div>
                <div class="fw-bold fs-4" style="color:#06b6d4;">{{ number_format($kpis['totalRedes'] ?? 0) }}</div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="kpi-card text-center">
                <div class="text-muted small mb-1">Licencias Activas</div>
                <div class="fw-bold fs-4" style="color:#22c55e;">{{ number_format($kpis['licenciasActivas'] ?? 0) }}</div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="kpi-card text-center">
                <div class="text-muted small mb-1">Garantías Config</div>
                <div class="fw-bold fs-4" style="color:#8b5cf6;">{{ number_format($kpis['garantiasConfig'] ?? 0) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-clipboard2-pulse me-2"></i>Órdenes Recientes</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº Orden</th>
                                    <th>Cliente</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrdenes ?? [] as $orden)
                                <tr>
                                    <td>
                                        <a href="{{ route('tecnicas.show', $orden) }}" class="text-decoration-none">
                                            {{ $orden->numero_orden }}
                                        </a>
                                    </td>
                                    <td>{{ $orden->cliente->nombre ?? '-' }}</td>
                                    <td>
                                        <span class="status-badge {{ $orden->estado == 'terminado' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                                        </span>
                                    </td>
                                    <td>{{ $orden->created_at->format('Y-m-d') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No hay órdenes recientes</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-file-earmark-text me-2"></i>Presupuestos Pendientes</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº Presupuesto</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Válida</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($presupuestosPendientes ?? [] as $presupuesto)
                                <tr>
                                    <td>
                                        <a href="{{ route('presupuestos.show', $presupuesto) }}" class="text-decoration-none">
                                            {{ $presupuesto->numero }}
                                        </a>
                                    </td>
                                    <td>{{ $presupuesto->cliente->nombre ?? '-' }}</td>
                                    <td>RD$ {{ number_format($presupuesto->total, 2) }}</td>
                                    <td>{{ $presupuesto->valido_hasta->format('Y-m-d') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No hay presupuestos pendientes</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Licencias Por Vencer</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Clave</th>
                                    <th>Producto</th>
                                    <th>Vence</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($licenciasPorVencer ?? [] as $licencia)
                                <tr>
                                    <td><small>{{ $licencia->clave_licencia }}</small></td>
                                    <td>{{ $licencia->producto->nombre ?? '-' }}</td>
                                    <td>
                                        <span class="text-warning">{{ $licencia->fecha_vencimiento->format('Y-m-d') }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">No hay licencias por vencer</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-pie-chart me-2"></i>Órdenes por Estado</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($estadosOrdenes ?? [] as $estado => $count)
                        <div class="col-md-4 col-sm-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="fw-bold fs-4">{{ $count }}</div>
                                <div class="text-muted small">{{ ucfirst(str_replace('_', ' ', $estado)) }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h6 class="fw-bold mb-0"><i class="bi bi-graph-up me-2"></i>Resumen Financiero</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded text-center">
                                <div class="text-muted small mb-1">Ventas del Mes</div>
                                <div class="fw-bold fs-4" style="color:#22c55e;">RD$ {{ number_format($kpis['ventasMes'] ?? 0, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded text-center">
                                <div class="text-muted small mb-1">Ingresos Reparaciones</div>
                                <div class="fw-bold fs-4" style="color:#3b82f6;">RD$ {{ number_format($kpis['ingresosMes'] ?? 0, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function refreshKpis(btn) {
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Actualizando...';
    btn.disabled = true;
    
    fetch('{{ route('tecnologia.dashboard.kpis') }}')
        .then(response => response.json())
        .then(data => {
            // Update KPI values
            document.querySelectorAll('.kpi-card .fw-bold').forEach((el, index) => {
                // Logic to update KPIs would go here
            });
            
            btn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Actualizar';
            btn.disabled = false;
        })
        .catch(error => {
            console.error('Error:', error);
            btn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Actualizar';
            btn.disabled = false;
        });
}
</script>
@endpush
