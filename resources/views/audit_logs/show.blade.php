@extends('layouts.app')

@section('title', 'Detalle de Auditoría')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-info-circle text-info me-2"></i>
                Detalle de Auditoría
            </h2>
            <p class="text-muted mb-0">#{{ $auditLog->id }}</p>
        </div>
        <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Información</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted small fw-semibold">Descripción</div>
                        <div class="col-md-8">{{ $auditLog->description }}</div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted small fw-semibold">Acción</div>
                        <div class="col-md-8">
                            @php $badge = match($auditLog->action) { 'created' => 'success', 'updated' => 'warning', 'deleted' => 'danger', default => 'info' }; @endphp
                            <span class="badge bg-{{ $badge }} bg-opacity-10 text-{{ $badge }} rounded-pill px-3 py-1">{{ ucfirst($auditLog->action) }}</span>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted small fw-semibold">Módulo</div>
                        <div class="col-md-8">{{ class_basename($auditLog->model_type) }} #{{ $auditLog->model_id }}</div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted small fw-semibold">Modelo completo</div>
                        <div class="col-md-8"><small class="font-monospace text-muted">{{ $auditLog->model_type }}</small></div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted small fw-semibold">Usuario</div>
                        <div class="col-md-8 fw-semibold">{{ $auditLog->user?->name ?? '—' }} (#{{ $auditLog->user_id }})</div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted small fw-semibold">Fecha/Hora</div>
                        <div class="col-md-8">{{ $auditLog->created_at->format('d/m/Y h:i:s A') }}</div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted small fw-semibold">Dirección IP</div>
                        <div class="col-md-8"><span class="font-monospace">{{ $auditLog->ip_address }}</span></div>
                    </div>
                    @if($auditLog->user_agent)
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted small fw-semibold">User Agent</div>
                        <div class="col-md-8"><small class="text-muted font-monospace" style="word-break:break-all;">{{ $auditLog->user_agent }}</small></div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            @if($auditLog->old_values && count($auditLog->old_values) > 0)
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold text-danger"><i class="bi bi-arrow-left-circle me-2"></i>Valores Anteriores</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <pre class="mb-0" style="font-size:.75rem;max-height:300px;overflow-y:auto;">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
            @endif

            @if($auditLog->new_values && count($auditLog->new_values) > 0)
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold text-success"><i class="bi bi-arrow-right-circle me-2"></i>Valores Nuevos</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <pre class="mb-0" style="font-size:.75rem;max-height:300px;overflow-y:auto;">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
