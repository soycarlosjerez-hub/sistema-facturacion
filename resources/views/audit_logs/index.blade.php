@extends('layouts.app')

@section('title', 'Auditoría')

@push('styles')
@include('partials.premium-ui')
<style>
.btn-icon-hover {
    width: 32px; height: 32px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 50% !important;
    padding: 0;
    transition: all 0.2s;
}
.btn-icon-hover:hover { transform: scale(1.15); }
.status-badge {
    padding: 0.4em 0.8em;
    border-radius: 2rem;
    font-weight: 500;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Registro de Auditoría</h4>
                    <small class="text-white opacity-75">Trazabilidad de acciones en el sistema</small>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-card mb-4" style="animation-delay:.1s;">
        <div class="card-accent red"></div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('audit-logs.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-3">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Buscar acción..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <select name="action" class="form-select">
                        <option value="">Todas las acciones</option>
                        @foreach($actions as $a)
                            <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <select name="model" class="form-select">
                        <option value="">Todos los módulos</option>
                        @foreach($models->sort() as $m)
                            <option value="{{ $m }}" {{ request('model') === $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <input type="date" name="desde" class="form-control" value="{{ request('desde') }}" placeholder="Desde">
                </div>
                <div class="col-lg-2">
                    <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}" placeholder="Hasta">
                </div>
                <div class="col-lg-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary rounded-pill flex-grow-1"><i class="bi bi-funnel"></i></button>
                    <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card overflow-hidden" style="animation-delay:.2s;">
        <div class="card-accent red"></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: rgba(15,23,42,0.03);">
                    <tr style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;">
                        <th class="ps-4 py-3 text-muted fw-bold">Fecha/Hora</th>
                        <th class="py-3 text-muted fw-bold">Usuario</th>
                        <th class="py-3 text-muted fw-bold">Acción</th>
                        <th class="py-3 text-muted fw-bold">Módulo</th>
                        <th class="py-3 text-muted fw-bold">Descripción</th>
                        <th class="py-3 text-muted fw-bold">IP</th>
                        <th class="text-end pe-4 py-3 text-muted fw-bold">Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="ps-4">
                                <span class="small fw-semibold">{{ $log->created_at->format('d/m/Y') }}</span>
                                <br><small class="text-muted">{{ $log->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <span class="fw-semibold small">{{ $log->user?->name ?? '—' }}</span>
                            </td>
                            <td>
                                @php
                                    $badge = match($log->action) {
                                        'created' => 'success',
                                        'updated' => 'warning',
                                        'deleted' => 'danger',
                                        default => 'info'
                                    };
                                @endphp
                                <span class="status-badge bg-{{ $badge }} bg-opacity-10 text-{{ $badge }}">{{ ucfirst($log->action) }}</span>
                            </td>
                            <td>
                                <span class="small text-muted">{{ class_basename($log->model_type) }}</span>
                            </td>
                            <td>
                                <span class="small">{{ Str::limit($log->description, 80) }}</span>
                            </td>
                            <td><small class="text-muted font-monospace">{{ $log->ip_address }}</small></td>
                            <td class="text-end pe-4">
                                <a href="{{ route('audit-logs.show', $log) }}" class="btn btn-sm btn-outline-info rounded-pill btn-icon-hover" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mt-2 mb-0">Sin registros de auditoría</p>
                                <small>Las acciones comenzarán a registrarse automáticamente</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-0 py-3 px-4" style="background:transparent;">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection