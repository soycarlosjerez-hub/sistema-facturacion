@extends('layouts.app')

@section('title', 'API Request Logs')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    {{-- Header --}}
    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-server"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">API Request Logs</h2>
                    <p class="mb-0 opacity-75">Registro de todas las peticiones realizadas a la API</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="ui-stat h-100">
                <div class="card-body p-3 text-center">
                    <small class="ui-stat-label d-block">Hoy Total</small>
                    <h3 class="ui-stat-value mb-0">{{ number_format($stats['total'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat h-100">
                <div class="card-body p-3 text-center">
                    <small class="ui-stat-label d-block">Exitosas (2xx)</small>
                    <h3 class="ui-stat-value mb-0 text-success">{{ number_format($stats['success'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat h-100">
                <div class="card-body p-3 text-center">
                    <small class="ui-stat-label d-block">Errores (4xx/5xx)</small>
                    <h3 class="ui-stat-value mb-0 text-danger">{{ number_format($stats['errors'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat h-100">
                <div class="card-body p-3 text-center">
                    <small class="ui-stat-label d-block">Tiempo Promedio</small>
                    <h3 class="ui-stat-value mb-0">{{ number_format($stats['avg_response_time'] ?? 0, 0) }}ms</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="ui-card mb-4" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('owner.api-requests') }}">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="ui-label">M&eacute;todo</label>
                        <select name="method" class="ui-select">
                            <option value="">Todos</option>
                            <option value="GET" {{ request('method') == 'GET' ? 'selected' : '' }}>GET</option>
                            <option value="POST" {{ request('method') == 'POST' ? 'selected' : '' }}>POST</option>
                            <option value="PUT" {{ request('method') == 'PUT' ? 'selected' : '' }}>PUT</option>
                            <option value="PATCH" {{ request('method') == 'PATCH' ? 'selected' : '' }}>PATCH</option>
                            <option value="DELETE" {{ request('method') == 'DELETE' ? 'selected' : '' }}>DELETE</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="ui-label">URI</label>
                        <input type="text" name="uri" class="ui-input" placeholder="Buscar URI..." value="{{ request('uri') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="ui-label">Estado</label>
                        <select name="response_status" class="ui-select">
                            <option value="">Todos</option>
                            <option value="200" {{ request('response_status') == '200' ? 'selected' : '' }}>200 OK</option>
                            <option value="201" {{ request('response_status') == '201' ? 'selected' : '' }}>201 Created</option>
                            <option value="401" {{ request('response_status') == '401' ? 'selected' : '' }}>401 Unauthorized</option>
                            <option value="403" {{ request('response_status') == '403' ? 'selected' : '' }}>403 Forbidden</option>
                            <option value="404" {{ request('response_status') == '404' ? 'selected' : '' }}>404 Not Found</option>
                            <option value="422" {{ request('response_status') == '422' ? 'selected' : '' }}>422 Unprocessable</option>
                            <option value="500" {{ request('response_status') == '500' ? 'selected' : '' }}>500 Server Error</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="ui-label">Desde</label>
                        <input type="date" name="fecha_desde" class="ui-input" value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="ui-label">Hasta</label>
                        <input type="date" name="fecha_hasta" class="ui-input" value="{{ request('fecha_hasta') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="ui-btn ui-btn-solid w-100"><i class="bi bi-search me-1"></i> Filtrar</button>
                    </div>
                </div>
                @if(request()->anyFilled(['method','uri','response_status','fecha_desde','fecha_hasta','search']))
                <div class="mt-2">
                    <a href="{{ route('owner.api-requests') }}" class="ui-btn ui-btn-ghost btn-sm"><i class="bi bi-x-lg me-1"></i> Limpiar filtros</a>
                </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="ui-card" style="--delay:.2s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-list me-2"></i> Peticiones ({{ $logs->total() }})</h5>
            <span class="ui-badge ui-badge-neutral">{{ $logs->perPage() }} por p&aacute;gina</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">M&eacute;todo</th>
                        <th>URI</th>
                        <th style="width: 80px;">Estado</th>
                        <th style="width: 100px;">Duraci&oacute;n</th>
                        <th style="width: 120px;">Usuario</th>
                        <th style="width: 140px;">IP</th>
                        <th style="width: 160px;">Fecha/Hora</th>
                        <th style="width: 80px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>
                            <span class="ui-badge ui-badge-{{ $log->method === 'GET' ? 'info' : ($log->method === 'POST' ? 'primary' : ($log->method === 'PUT' || $log->method === 'PATCH' ? 'warning' : 'danger')) }}">
                                {{ $log->method }}
                            </span>
                        </td>
                        <td>
                            <code style="font-size: 0.8em;">{{ Str::limit($log->uri, 60) }}</code>
                        </td>
                        <td>
                            <span class="ui-badge ui-badge-{{ $log->response_status >= 200 && $log->response_status < 300 ? 'success' : ($log->response_status >= 400 && $log->response_status < 500 ? 'warning' : 'danger') }}">
                                {{ $log->response_status }}
                            </span>
                        </td>
                        <td>{{ $log->response_time_ms ?? '-' }}ms</td>
                        <td>{{ $log->user?->name ?? ($log->businessInstance?->owner?->name ?? '-') }}</td>
                        <td><small>{{ $log->ip_address ?? '-' }}</small></td>
                        <td><small>{{ $log->created_at?->format('Y-m-d H:i:s') }}</small></td>
                        <td>
                            <button class="ui-action ui-action-view" onclick="showDetails({{ $log->id }}, '{{ addslashes(json_encode($log)) }}')" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                            <p class="text-muted">No hay registros de peticiones</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="card-footer bg-transparent border-0 py-3 px-4">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

    {{-- Detail Modal --}}
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="fw-bold"><i class="bi bi-info-circle me-2"></i> Detalle de Petici&oacute;n</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-2">
                            <small class="text-muted fw-bold d-block">M&eacute;todo:</small>
                            <span id="modal-method" class="ui-badge ui-badge-primary"></span>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted fw-bold d-block">Estado:</small>
                            <span id="modal-status" class="ui-badge"></span>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted fw-bold d-block">Duraci&oacute;n:</small>
                            <span id="modal-duration"></span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted fw-bold d-block">IP:</small>
                            <span id="modal-ip"></span>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted fw-bold d-block">Fecha:</small>
                            <span id="modal-date"></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted fw-bold d-block">URI:</small>
                        <pre id="modal-uri" class="bg-light p-2 rounded" style="max-height: 100px; overflow-y: auto;"></pre>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted fw-bold d-block">User Agent:</small>
                        <pre id="modal-ua" class="bg-light p-2 rounded" style="max-height: 60px; overflow-y: auto;"></pre>
                    </div>
                    <hr>
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#headers-tab">Headers</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#body-tab">Request Body</button>
                        </li>
                    </ul>
                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="headers-tab">
                            <pre id="modal-headers" class="bg-light p-2 rounded" style="max-height: 300px; overflow-y: auto;"></pre>
                        </div>
                        <div class="tab-pane fade" id="body-tab">
                            <pre id="modal-body" class="bg-light p-2 rounded" style="max-height: 300px; overflow-y: auto;"></pre>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="ui-btn ui-btn-ghost" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
function showDetails(id, logJson) {
    try {
        var log = typeof logJson === 'string' ? JSON.parse(logJson) : logJson;

        document.getElementById('modal-method').textContent = log.method;
        document.getElementById('modal-method').className = 'ui-badge ui-badge-' + (log.method === 'GET' ? 'info' : (log.method === 'POST' ? 'primary' : (log.method === 'PUT' || log.method === 'PATCH' ? 'warning' : 'danger')));

        var statusClass = log.response_status >= 200 && log.response_status < 300 ? 'success' : (log.response_status >= 400 && log.response_status < 500 ? 'warning' : 'danger');
        var statusBadge = document.getElementById('modal-status');
        statusBadge.textContent = log.response_status;
        statusBadge.className = 'ui-badge ui-badge-' + statusClass;

        document.getElementById('modal-duration').textContent = (log.response_time_ms || 0) + 'ms';
        document.getElementById('modal-ip').textContent = log.ip_address || '-';
        document.getElementById('modal-date').textContent = log.created_at || '-';
        document.getElementById('modal-uri').textContent = log.uri || '-';
        document.getElementById('modal-ua').textContent = log.user_agent || '-';

        document.getElementById('modal-headers').textContent = log.request_headers ? JSON.stringify(log.request_headers, null, 2) : 'No headers disponibles';
        document.getElementById('modal-body').textContent = log.request_body ? JSON.stringify(log.request_body, null, 2) : 'No body disponible';

        new bootstrap.Modal(document.getElementById('detailModal')).show();
    } catch(e) {
        console.error('Error parsing log:', e);
    }
}
</script>
@endpush

@push('scripts')
<script>
function showDetails(id, logJson) {
    try {
        var log = typeof logJson === 'string' ? JSON.parse(logJson) : logJson;

        document.getElementById('modal-method').textContent = log.method;
        document.getElementById('modal-method').className = 'badge bg-' + (log.method === 'GET' ? 'info' : (log.method === 'POST' ? 'primary' : (log.method === 'PUT' || log.method === 'PATCH' ? 'warning' : 'danger')));

        var statusClass = log.response_status >= 200 && log.response_status < 300 ? 'success' : (log.response_status >= 400 && log.response_status < 500 ? 'warning' : 'danger');
        var statusBadge = document.getElementById('modal-status');
        statusBadge.textContent = log.response_status;
        statusBadge.className = 'badge bg-' + statusClass;

        document.getElementById('modal-duration').textContent = (log.response_time_ms || 0) + 'ms';
        document.getElementById('modal-ip').textContent = log.ip_address || '-';
        document.getElementById('modal-date').textContent = log.created_at || '-';
        document.getElementById('modal-uri').textContent = log.uri || '-';
        document.getElementById('modal-ua').textContent = log.user_agent || '-';

        document.getElementById('modal-headers').textContent = log.request_headers ? JSON.stringify(log.request_headers, null, 2) : 'No headers disponibles';
        document.getElementById('modal-body').textContent = log.request_body ? JSON.stringify(log.request_body, null, 2) : 'No body disponible';

        new bootstrap.Modal(document.getElementById('detailModal')).show();
    } catch(e) {
        console.error('Error parsing log:', e);
    }
}
</script>
@endpush
