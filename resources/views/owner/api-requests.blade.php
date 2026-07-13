@extends('layouts.app')

@section('title', 'API Request Logs')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-server"></i> API Request Logs</h2>
            <p class="text-muted">Registro de todas las peticiones realizadas a la API</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Hoy Total</h6>
                            <h3 class="mb-0">{{ number_format($stats['total'] ?? 0) }}</h3>
                        </div>
                        <i class="fas fa-calendar-day fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Exitosas (2xx)</h6>
                            <h3 class="mb-0">{{ number_format($stats['success'] ?? 0) }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Errores (4xx/5xx)</h6>
                            <h3 class="mb-0">{{ number_format($stats['errors'] ?? 0) }}</h3>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Tiempo Promedio</h6>
                            <h3 class="mb-0">{{ number_format($stats['avg_response_time'] ?? 0, 0) }}ms</h3>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('owner.api-requests') }}">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Método</label>
                        <select name="method" class="form-select">
                            <option value="">Todos</option>
                            <option value="GET" {{ request('method') == 'GET' ? 'selected' : '' }}>GET</option>
                            <option value="POST" {{ request('method') == 'POST' ? 'selected' : '' }}>POST</option>
                            <option value="PUT" {{ request('method') == 'PUT' ? 'selected' : '' }}>PUT</option>
                            <option value="PATCH" {{ request('method') == 'PATCH' ? 'selected' : '' }}>PATCH</option>
                            <option value="DELETE" {{ request('method') == 'DELETE' ? 'selected' : '' }}>DELETE</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">URI</label>
                        <input type="text" name="uri" class="form-control" placeholder="Buscar URI..." value="{{ request('uri') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="response_status" class="form-select">
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
                        <label class="form-label">Desde</label>
                        <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Filtrar</button>
                    </div>
                </div>
                @if(request()->anyFilled(['method','uri','response_status','fecha_desde','fecha_hasta','search']))
                <div class="mt-2">
                    <a href="{{ route('owner.api-requests') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i> Limpiar filtros</a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list"></i> Peticiones ({{ $logs->total() }})</h5>
            <span class="badge bg-secondary">{{ $logs->perPage() }} por página</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">Método</th>
                            <th>URI</th>
                            <th style="width: 80px;">Estado</th>
                            <th style="width: 100px;">Duración</th>
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
                                <span class="badge bg-{{ $log->method === 'GET' ? 'info' : ($log->method === 'POST' ? 'primary' : ($log->method === 'PUT' || $log->method === 'PATCH' ? 'warning' : 'danger')) }}">
                                    {{ $log->method }}
                                </span>
                            </td>
                            <td>
                                <code style="font-size: 0.8em;">{{ Str::limit($log->uri, 60) }}</code>
                            </td>
                            <td>
                                <span class="badge bg-{{ $log->response_status >= 200 && $log->response_status < 300 ? 'success' : ($log->response_status >= 400 && $log->response_status < 500 ? 'warning' : 'danger') }}">
                                    {{ $log->response_status }}
                                </span>
                            </td>
                            <td>{{ $log->response_time_ms ?? '-' }}ms</td>
                            <td>{{ $log->user?->name ?? ($log->businessInstance?->owner?->name ?? '-') }}</td>
                            <td><small>{{ $log->ip_address ?? '-' }}</small></td>
                            <td><small>{{ $log->created_at?->format('Y-m-d H:i:s') }}</small></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="showDetails({{ $log->id }}, '{{ addslashes(json_encode($log)) }}')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay registros de peticiones</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $logs->links() }}
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle"></i> Detalle de Petición</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-2">
                        <strong>Método:</strong>
                        <span id="modal-method" class="badge bg-primary"></span>
                    </div>
                    <div class="col-md-2">
                        <strong>Estado:</strong>
                        <span id="modal-status" class="badge"></span>
                    </div>
                    <div class="col-md-2">
                        <strong>Duración:</strong>
                        <span id="modal-duration"></span>
                    </div>
                    <div class="col-md-4">
                        <strong>IP:</strong>
                        <span id="modal-ip"></span>
                    </div>
                    <div class="col-md-2">
                        <strong>Fecha:</strong>
                        <span id="modal-date"></span>
                    </div>
                </div>
                <div class="mb-3">
                    <strong>URI:</strong>
                    <pre id="modal-uri" class="bg-light p-2 rounded" style="max-height: 100px; overflow-y: auto;"></pre>
                </div>
                <div class="mb-3">
                    <strong>User Agent:</strong>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.badge.bg-info { background-color: #0dcaf0 !important; }
.badge.bg-primary { background-color: #0d6efd !important; }
.badge.bg-warning { background-color: #ffc107 !important; color: #000 !important; }
.badge.bg-danger { background-color: #dc3545 !important; }
.badge.bg-success { background-color: #198754 !important; }
</style>
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
