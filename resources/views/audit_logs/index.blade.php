@extends('layouts.app')

@section('title', 'Auditoría')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-journal-text text-info me-2"></i>
                Registro de Auditoría
            </h2>
            <p class="text-muted mb-0">Trazabilidad de acciones en el sistema</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-3 bg-light bg-opacity-50">
            <form method="GET" action="{{ route('audit-logs.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-3">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-0 bg-white" placeholder="Buscar acción..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <select name="action" class="form-select border-0 bg-white">
                        <option value="">Todas las acciones</option>
                        @foreach($actions as $a)
                            <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <select name="model" class="form-select border-0 bg-white">
                        <option value="">Todos los módulos</option>
                        @foreach($models->sort() as $m)
                            <option value="{{ $m }}" {{ request('model') === $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <input type="date" name="desde" class="form-control border-0 bg-white" value="{{ request('desde') }}" placeholder="Desde">
                </div>
                <div class="col-lg-2">
                    <input type="date" name="hasta" class="form-control border-0 bg-white" value="{{ request('hasta') }}" placeholder="Hasta">
                </div>
                <div class="col-lg-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary rounded-pill flex-grow-1"><i class="bi bi-funnel"></i></button>
                    <a href="{{ route('audit-logs.index') }}" class="btn btn-light rounded-pill"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;">
                        <th class="ps-4 py-3">Fecha/Hora</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Módulo</th>
                        <th>Descripción</th>
                        <th>IP</th>
                        <th class="text-end pe-4">Detalle</th>
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
                                <span class="badge bg-{{ $badge }} bg-opacity-10 text-{{ $badge }} rounded-pill px-2 py-1">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td>
                                <span class="small text-muted">{{ class_basename($log->model_type) }}</span>
                            </td>
                            <td>
                                <span class="small">{{ Str::limit($log->description, 80) }}</span>
                            </td>
                            <td><small class="text-muted font-monospace">{{ $log->ip_address }}</small></td>
                            <td class="text-end pe-4">
                                <a href="{{ route('audit-logs.show', $log) }}" class="btn btn-sm btn-outline-info rounded-pill" title="Ver detalle">
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
        <div class="card-footer bg-white border-0">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
