@extends('layouts.app')

@section('title', 'Detalle de Auditoría')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Detalle de Auditoría</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-hash me-1"></i>
                        <span>#{{ $auditLog->id }}</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('audit-logs.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ui-card h-100" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="card-body">
                    <h5 class="fw-bold mb-4"><i class="bi bi-clock-history me-2" style="color:#64748b;"></i> Información</h5>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Descripción</small>
                            <span class="fw-semibold">{{ $auditLog->description }}</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Acción</small>
                            <div>
                                @php $badge = match($auditLog->action) { 'created' => 'success', 'updated' => 'warning', 'deleted' => 'danger', default => 'info' }; @endphp
                                <span class="badge bg-{{ $badge }} bg-opacity-10 text-{{ $badge }} rounded-pill px-3 py-1">{{ ucfirst($auditLog->action) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Módulo</small>
                            <span class="fw-semibold">{{ class_basename($auditLog->model_type) }} #{{ $auditLog->model_id }}</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Modelo completo</small>
                            <span class="font-monospace text-muted small">{{ $auditLog->model_type }}</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Usuario</small>
                            <span class="fw-semibold">{{ $auditLog->user?->name ?? '—' }} (#{{ $auditLog->user_id }})</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Fecha/Hora</small>
                            <span class="fw-semibold">{{ $auditLog->created_at->format('d/m/Y h:i:s A') }}</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Dirección IP</small>
                            <span class="font-monospace">{{ $auditLog->ip_address }}</span>
                        </div>
                    </div>
                    @if($auditLog->user_agent)
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">User Agent</small>
                            <span class="font-monospace text-muted small" style="word-break:break-all;">{{ $auditLog->user_agent }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            @if($auditLog->old_values && count($auditLog->old_values) > 0)
            <div class="ui-card mb-4" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-arrow-left-circle me-2" style="color:#64748b;"></i> Valores Anteriores</h6>
                    <pre class="mb-0" style="font-size:.75rem;max-height:300px;overflow-y:auto;">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
            @endif

            @if($auditLog->new_values && count($auditLog->new_values) > 0)
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-arrow-right-circle me-2" style="color:#64748b;"></i> Valores Nuevos</h6>
                    <pre class="mb-0" style="font-size:.75rem;max-height:300px;overflow-y:auto;">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection