@extends('layouts.app')
@section('title', 'Dashboard Tecnología')

@push('styles')
@include('partials.premium-ui')
<style>
.kpi-card {
    background: var(--glass-bg);
    backdrop-filter: blur(12px);
    border-radius: 1rem;
    padding: 1.25rem;
    border: 1px solid rgba(255,255,255,0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}
.kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    border-radius: 1rem 0 0 1rem;
}
.kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(59,130,246,0.12);
}
.kpi-card-icon {
    width: 52px;
    height: 52px;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    flex-shrink: 0;
}
.kpi-value {
    font-size: 1.75rem;
    font-weight: 700;
    line-height: 1.2;
}
.kpi-label {
    font-size: 0.75rem;
    font-weight: 500;
    letter-spacing: 0.3px;
    text-transform: uppercase;
}
.stat-chip {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 0.75rem;
    padding: 1rem 0.75rem;
    text-align: center;
    transition: all 0.25s ease;
}
.stat-chip:hover {
    background: rgba(255,255,255,0.14);
}
.section-card {
    background: var(--glass-bg);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 1rem;
    overflow: hidden;
    transition: all 0.3s ease;
}
.section-card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}
.stat-table th {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 0.85rem 1rem;
}
.stat-table td {
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    vertical-align: middle;
}
.stat-table tr {
    transition: background 0.15s ease;
}
.stat-table tbody tr:hover {
    background: rgba(59,130,246,0.04);
}
.kpi-transition {
    transition: all 0.4s ease;
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
                    <div class="ui-header-meta">
                        <i class="bi bi-bar-chart-line me-1"></i>
                        Dashboard de ventas, reparaciones e infraestructura
                        <span class="divider">·</span>
                        <i class="bi bi-clock me-1"></i>
                        <span id="dashboard-clock"></span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <button type="button" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" onclick="refreshKpis(this)" style="background:rgba(255,255,255,.15);">
                    <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
                </button>
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
            <div class="kpi-card" style="border-left-color: #3b82f6;">
                <div class="d-flex align-items-center">
                    <div class="kpi-card-icon me-3" style="background:linear-gradient(135deg,#3b82f6,#2563eb);">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <div class="kpi-label text-muted mb-1">Productos</div>
                        <div class="kpi-value fw-bold" id="kpi-productos">{{ number_format($kpis['totalProductos'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="kpi-card" style="border-left-color: #22c55e;">
                <div class="d-flex align-items-center">
                    <div class="kpi-card-icon me-3" style="background:linear-gradient(135deg,#22c55e,#16a34a);">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="kpi-label text-muted mb-1">Disponibles</div>
                        <div class="kpi-value fw-bold" id="kpi-disponibles">{{ number_format($kpis['equiposDisponibles'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="kpi-card" style="border-left-color: #f59e0b;">
                <div class="d-flex align-items-center">
                    <div class="kpi-card-icon me-3" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div>
                        <div class="kpi-label text-muted mb-1">En Reparación</div>
                        <div class="kpi-value fw-bold" id="kpi-reparacion">{{ number_format($kpis['equiposEnReparacion'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="kpi-card" style="border-left-color: #ef4444;">
                <div class="d-flex align-items-center">
                    <div class="kpi-card-icon me-3" style="background:linear-gradient(135deg,#ef4444,#dc2626);">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </div>
                    <div>
                        <div class="kpi-label text-muted mb-1">Órdenes Pend.</div>
                        <div class="kpi-value fw-bold" id="kpi-ordenes-pend">{{ number_format($kpis['ordenesPendientes'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="kpi-card" style="border-left-color: #22c55e;">
                <div class="d-flex align-items-center">
                    <div class="kpi-card-icon me-3" style="background:linear-gradient(135deg,#22c55e,#16a34a);">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="kpi-label text-muted mb-1">Técnicos</div>
                        <div class="kpi-value fw-bold" id="kpi-tecnicos">{{ number_format($kpis['totalTecnicos'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="kpi-card" style="border-left-color: #06b6d4;">
                <div class="d-flex align-items-center">
                    <div class="kpi-card-icon me-3" style="background:linear-gradient(135deg,#06b6d4,#0891b2);">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div>
                        <div class="kpi-label text-muted mb-1">Presupuestos</div>
                        <div class="kpi-value fw-bold" id="kpi-presupuestos">{{ number_format($kpis['presupuestosActivos'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="kpi-card" style="border-left-color: #a855f7;">
                <div class="d-flex align-items-center">
                    <div class="kpi-card-icon me-3" style="background:linear-gradient(135deg,#a855f7,#7c3aed);">
                        <i class="bi bi-list-ol"></i>
                    </div>
                    <div>
                        <div class="kpi-label text-muted mb-1">Órdenes Listas</div>
                        <div class="kpi-value fw-bold" id="kpi-ordenes-listas">{{ number_format($kpis['ordenesListas'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="kpi-card" style="border-left-color: #3b82f6;">
                <div class="d-flex align-items-center">
                    <div class="kpi-card-icon me-3" style="background:linear-gradient(135deg,#3b82f6,#2563eb);">
                        <i class="bi bi-badge-tm"></i>
                    </div>
                    <div>
                        <div class="kpi-label text-muted mb-1">Marcas</div>
                        <div class="kpi-value fw-bold" id="kpi-marcas">{{ number_format($kpis['totalMarcas'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="kpi-card" style="border-left-color: #06b6d4;">
                <div class="d-flex align-items-center">
                    <div class="kpi-card-icon me-3" style="background:linear-gradient(135deg,#06b6d4,#0891b2);">
                        <i class="bi bi-network"></i>
                    </div>
                    <div>
                        <div class="kpi-label text-muted mb-1">Redes</div>
                        <div class="kpi-value fw-bold" id="kpi-redes">{{ number_format($kpis['totalRedes'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="kpi-card" style="border-left-color: #22c55e;">
                <div class="d-flex align-items-center">
                    <div class="kpi-card-icon me-3" style="background:linear-gradient(135deg,#22c55e,#16a34a);">
                        <i class="bi bi-key"></i>
                    </div>
                    <div>
                        <div class="kpi-label text-muted mb-1">Licencias</div>
                        <div class="kpi-value fw-bold" id="kpi-licencias">{{ number_format($kpis['licenciasActivas'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="kpi-card" style="border-left-color: #8b5cf6;">
                <div class="d-flex align-items-center">
                    <div class="kpi-card-icon me-3" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div>
                        <div class="kpi-label text-muted mb-1">Garantías</div>
                        <div class="kpi-value fw-bold" id="kpi-garantias">{{ number_format($kpis['garantiasConfig'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="section-card">
                <div class="section-card-header d-flex justify-content-between align-items-center px-3 py-3 border-bottom border-light-subtle">
                    <h6 class="fw-bold mb-0"><i class="bi bi-clipboard2-pulse me-2"></i>Órdenes Recientes</h6>
                    <a href="{{ route('tecnicas.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i>Ver todas</a>
                </div>
                <div class="card-body p-0">
                    <table class="stat-table table table-hover mb-0">
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
                                    <a href="{{ route('tecnicas.show', $orden) }}" class="text-decoration-none fw-semibold">
                                        {{ $orden->numero_orden }}
                                    </a>
                                </td>
                                <td>{{ $orden->cliente->nombre ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $orden->estado == 'terminado' ? 'bg-success-subtle text-success' : ($orden->estado == 'cancelado' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning') }} px-2 py-1 rounded-pill">
                                        {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                                    </span>
                                </td>
                                <td>{{ $orden->created_at->format('d/m/Y') }}</td>
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

        <div class="col-lg-4 col-md-6">
            <div class="section-card">
                <div class="section-card-header d-flex justify-content-between align-items-center px-3 py-3 border-bottom border-light-subtle">
                    <h6 class="fw-bold mb-0"><i class="bi bi-file-earmark-text me-2"></i>Presupuestos Pendientes</h6>
                    <a href="{{ route('presupuestos.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i>Ver todos</a>
                </div>
                <div class="card-body p-0">
                    <table class="stat-table table table-hover mb-0">
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
                                    <a href="{{ route('presupuestos.show', $presupuesto) }}" class="text-decoration-none fw-semibold">
                                        {{ $presupuesto->numero }}
                                    </a>
                                </td>
                                <td>{{ $presupuesto->cliente->nombre ?? '-' }}</td>
                                <td>RD$ {{ number_format($presupuesto->total, 2) }}</td>
                                <td>{{ $presupuesto->valido_hasta ? $presupuesto->valido_hasta->format('d/m/Y') : '-' }}</td>
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

        <div class="col-lg-4 col-md-12">
            <div class="section-card">
                <div class="section-card-header d-flex justify-content-between align-items-center px-3 py-3 border-bottom border-light-subtle">
                    <h6 class="fw-bold mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Licencias Por Vencer</h6>
                    <a href="{{ route('licencias.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i>Ver todas</a>
                </div>
                <div class="card-body p-0">
                    <table class="stat-table table table-hover mb-0">
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
                                    <span class="text-warning">{{ $licencia->fecha_vencimiento->format('d/m/Y') }}</span>
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
