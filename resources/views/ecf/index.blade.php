@extends('layouts.app')

@section('title', 'Comprobantes Fiscales Electrónicos (e-CF)')

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .premium-header { background: linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e); }
body.dark-mode .card { background: rgba(15,23,42,.8); }
body.dark-mode .table { color: #cbd5e1; }
body.dark-mode .table-light { background: rgba(30,41,59,.6); }
body.dark-mode .table-light th { color: #94a3b8; border-color: #334155; }
body.dark-mode .table td { border-color: #1e293b; }
body.dark-mode .premium-card .form-control:focus,
body.dark-mode .premium-card .form-select:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.15); }
body.dark-mode .premium-card .btn-primary { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 14px rgba(245,158,11,.3); }
body.dark-mode .premium-card .btn-primary:hover { box-shadow: 0 6px 20px rgba(245,158,11,.45); }
body.dark-mode .premium-card .form-check-input { background-color: #334155; border-color: #475569; }
body.dark-mode .form-check-input:checked { background-color: #f59e0b; border-color: #f59e0b; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header d-flex justify-content-between align-items-center mb-4" style="background: linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e);">
        <div class="d-flex align-items-center gap-3">
            <div class="premium-avatar-circle">
                <i class="bi bi-receipt"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-1">Comprobantes Fiscales Electrónicos</h2>
                <p class="mb-0 opacity-75">Gestión de e-CF según normas DGII - República Dominicana</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('secuencias-ecf.index') }}" class="btn btn-light btn-sm rounded-pill text-dark fw-semibold">
                <i class="bi bi-hash me-1"></i> Secuencias
            </a>
            <a href="{{ route('certificados-digitales.index') }}" class="btn btn-light btn-sm rounded-pill text-dark fw-semibold">
                <i class="bi bi-key me-1"></i> Certificados
            </a>
        </div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="premium-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble bg-primary bg-opacity-10 text-primary" style="width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
                            <i class="bi bi-file-pdf"></i>
                        </div>
                        <div>
                            <div class="stat-label">Total Emitidos</div>
                            <div class="stat-value">{{ $stats['total'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble bg-success bg-opacity-10 text-success" style="width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <div class="stat-label">Aprobados</div>
                            <div class="stat-value">{{ $stats['aprobados'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble bg-warning bg-opacity-10 text-warning" style="width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <div class="stat-label">Pendientes</div>
                            <div class="stat-value">{{ $stats['pendientes'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="premium-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble bg-danger bg-opacity-10 text-danger" style="width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <div>
                            <div class="stat-label">Rechazados</div>
                            <div class="stat-value">{{ $stats['rechazados'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card mb-3">
        <div class="card-accent amber"></div>
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">eNCF</label>
                    <input type="text" name="encf" class="form-control form-control-sm rounded-3" value="{{ request('encf') }}" placeholder="E310000000001">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">Tipo</label>
                    <select name="tipo_ecf" class="form-select form-select-sm rounded-3">
                        <option value="">Todos</option>
                        @foreach($tipos as $key => $nombre)
                            <option value="{{ $key }}" {{ request('tipo_ecf') === $key ? 'selected' : '' }}>{{ $key }} - {{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">Estado</label>
                    <select name="estado" class="form-select form-select-sm rounded-3">
                        <option value="">Todos</option>
                        @foreach($estados as $key => $info)
                            <option value="{{ $key }}" {{ request('estado') === $key ? 'selected' : '' }}>{{ $info['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">Desde</label>
                    <input type="date" name="desde" class="form-control form-control-sm rounded-3" value="{{ request('desde') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">Hasta</label>
                    <input type="date" name="hasta" class="form-control form-control-sm rounded-3" value="{{ request('hasta') }}">
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button class="btn btn-primary btn-sm rounded-pill flex-grow-1">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('ecf.index') }}" class="btn btn-light btn-sm rounded-pill">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-accent amber"></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">eNCF</th>
                        <th>Tipo</th>
                        <th>Cliente</th>
                        <th class="text-end">Monto</th>
                        <th class="text-center">Estado</th>
                        <th>Emisión</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ecfs as $ecf)
                    @php $estadoInfo = $ecf->estado_info; @endphp
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-primary" style="letter-spacing:1px;">{{ $ecf->encf }}</span>
                            <br><small class="text-muted">Venta #{{ str_pad($ecf->venta_id, 5, '0', STR_PAD_LEFT) }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $ecf->tipo_ecf }}</span>
                            <br><small class="text-muted">{{ $ecf->tipo_nombre }}</small>
                        </td>
                        <td>
                            <span class="fw-bold small">{{ $ecf->venta?->cliente?->nombre ?? 'N/A' }}</span>
                            <br><small class="text-muted">{{ $ecf->venta?->cliente?->rnc_cedula ?: 'Sin RNC' }}</small>
                        </td>
                        <td class="text-end fw-bold">RD$ {{ number_format($ecf->monto_total, 2) }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $estadoInfo['color'] }} rounded-pill px-3">
                                <i class="bi {{ $estadoInfo['icon'] }} me-1"></i>{{ $estadoInfo['label'] }}
                            </span>
                        </td>
                        <td class="small">
                            {{ $ecf->fecha_emision->format('d/m/Y') }}
                            <br><small class="text-muted">{{ $ecf->fecha_emision->format('h:i A') }}</small>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('ecf.show', $ecf) }}" class="premium-btn-edit" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($ecf->xml_content)
                            <a href="{{ route('ecf.xml', $ecf) }}" target="_blank" class="premium-btn-edit" title="Ver XML" style="margin-left:4px;">
                                <i class="bi bi-filetype-xml"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-shield-check display-3 text-muted opacity-25"></i>
                            <p class="text-muted mt-3 mb-0">No hay comprobantes electrónicos emitidos.</p>
                            <small class="text-muted">Cree secuencias e-CF y emita comprobantes desde el POS.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ecfs->hasPages())
        <div class="card-footer bg-transparent border-0 py-3">
            {{ $ecfs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
