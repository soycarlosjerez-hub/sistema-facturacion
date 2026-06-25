@extends('layouts.app')

@section('title', 'Detalle de Auditoría')

@push('styles')
@include('partials.premium-ui')
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
                    <h4 class="fw-bold mb-1 text-white">Detalle de Auditoría</h4>
                    <small class="text-white opacity-75">#{{ $auditLog->id }}</small>
                </div>
            </div>
            <a href="{{ route('audit-logs.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="premium-card h-100" style="animation-delay:.1s;">
                <div class="card-accent red"></div>
                <div class="premium-card-title">
                    <i class="bi bi-clock-history icon-red"></i>
                    Información
                </div>
                <div class="card-body pt-0">
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Descripción</div>
                        <div class="premium-detail-value">{{ $auditLog->description }}</div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Acción</div>
                        <div class="premium-detail-value">
                            @php $badge = match($auditLog->action) { 'created' => 'success', 'updated' => 'warning', 'deleted' => 'danger', default => 'info' }; @endphp
                            <span class="badge bg-{{ $badge }} bg-opacity-10 text-{{ $badge }} rounded-pill px-3 py-1">{{ ucfirst($auditLog->action) }}</span>
                        </div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Módulo</div>
                        <div class="premium-detail-value">{{ class_basename($auditLog->model_type) }} #{{ $auditLog->model_id }}</div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Modelo completo</div>
                        <div class="premium-detail-value"><small class="font-monospace text-muted">{{ $auditLog->model_type }}</small></div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Usuario</div>
                        <div class="premium-detail-value fw-semibold">{{ $auditLog->user?->name ?? '—' }} (#{{ $auditLog->user_id }})</div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Fecha/Hora</div>
                        <div class="premium-detail-value">{{ $auditLog->created_at->format('d/m/Y h:i:s A') }}</div>
                    </div>
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">Dirección IP</div>
                        <div class="premium-detail-value"><span class="font-monospace">{{ $auditLog->ip_address }}</span></div>
                    </div>
                    @if($auditLog->user_agent)
                    <div class="premium-detail-row">
                        <div class="premium-detail-label">User Agent</div>
                        <div class="premium-detail-value"><small class="text-muted font-monospace" style="word-break:break-all;">{{ $auditLog->user_agent }}</small></div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            @if($auditLog->old_values && count($auditLog->old_values) > 0)
            <div class="premium-card mb-4" style="animation-delay:.15s;">
                <div class="card-accent red"></div>
                <div class="premium-card-title">
                    <i class="bi bi-arrow-left-circle icon-red"></i>
                    Valores Anteriores
                </div>
                <div class="card-body pt-0">
                    <pre class="mb-0" style="font-size:.75rem;max-height:300px;overflow-y:auto;">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
            @endif

            @if($auditLog->new_values && count($auditLog->new_values) > 0)
            <div class="premium-card" style="animation-delay:.2s;">
                <div class="card-accent red"></div>
                <div class="premium-card-title">
                    <i class="bi bi-arrow-right-circle icon-red"></i>
                    Valores Nuevos
                </div>
                <div class="card-body pt-0">
                    <pre class="mb-0" style="font-size:.75rem;max-height:300px;overflow-y:auto;">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection