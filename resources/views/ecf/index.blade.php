@extends('layouts.app')

@section('title', 'Comprobantes Fiscales Electrónicos (e-CF)')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-file-pdf text-primary me-2"></i>
                Comprobantes Fiscales Electrónicos
            </h2>
            <p class="text-muted mb-0">Gestión de e-CF según normas DGII - República Dominicana</p>
        </div>
        <div>
            <a href="{{ route('secuencias-ecf.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill me-2">
                <i class="bi bi-hash me-1"></i> Secuencias
            </a>
            <a href="{{ route('certificados-digitales.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill me-2">
                <i class="bi bi-key me-1"></i> Certificados
            </a>
        </div>
    </div>

    <style>
        .icon-bubble { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; }
    </style>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-file-pdf"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size:0.7rem;">Total Emitidos</small>
                            <h3 class="fw-bold mb-0 mt-1">{{ $stats['total'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size:0.7rem;">Aprobados</small>
                            <h3 class="fw-bold mb-0 mt-1">{{ $stats['aprobados'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size:0.7rem;">Pendientes</small>
                            <h3 class="fw-bold mb-0 mt-1">{{ $stats['pendientes'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-bubble bg-danger bg-opacity-10 text-danger">
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size:0.7rem;">Rechazados</small>
                            <h3 class="fw-bold mb-0 mt-1">{{ $stats['rechazados'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-3">
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

    <div class="card border-0 shadow-sm rounded-4">
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
                            <a href="{{ route('ecf.show', $ecf) }}" class="btn btn-sm btn-outline-primary rounded-pill" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($ecf->xml_content)
                            <a href="{{ route('ecf.xml', $ecf) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill" title="Ver XML">
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
