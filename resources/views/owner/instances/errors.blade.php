@extends('layouts.app')

@section('title', 'Errores - ' . $instance->nombre)

@push('styles')
<style>
    .premium-header {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border-radius: 1rem; padding: 2rem; color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.4);
        position: relative; overflow: hidden;
    }
    .premium-header::after {
        content: ''; position: absolute; top: -50%; right: -20%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .stat-card {
        background: rgba(255,255,255,0.7);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    .error-row { cursor: pointer; transition: background 0.15s; }
    .error-row:hover { background: #fef2f2 !important; }
    .trace-block {
        background: #1e293b;
        color: #e2e8f0;
        border-radius: 0.75rem;
        padding: 1rem;
        font-family: 'SF Mono', 'Fira Code', monospace;
        font-size: 0.75rem;
        max-height: 300px;
        overflow-y: auto;
        white-space: pre-wrap;
        word-break: break-all;
    }
    .context-key { color: #60a5fa; }
    .context-value { color: #fbbf24; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- Header -->
    <div class="premium-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white bg-opacity-20 rounded-2 p-2 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bi bi-exclamation-triangle-fill fs-2 text-white"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Errores de la Instancia</h2>
                    <p class="text-white text-opacity-75 mb-0">{{ $instance->nombre }} &middot; {{ $instance->businessType?->nombre ?? 'Sin tipo' }}</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('owner.instances.show', $instance) }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bi bi-list-check fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase d-block" style="font-size:.6rem;letter-spacing:.5px;">Total</small>
                        <h3 class="fw-bold mb-0">{{ number_format($stats['total']) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bi bi-bug-fill fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase d-block" style="font-size:.6rem;letter-spacing:.5px;">Errores</small>
                        <h3 class="fw-bold mb-0">{{ number_format($stats['errors']) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase d-block" style="font-size:.6rem;letter-spacing:.5px;">Warnings</small>
                        <h3 class="fw-bold mb-0">{{ number_format($stats['warnings']) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 stat-card">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-dark bg-opacity-10 text-dark rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bi bi-x-octagon-fill fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase d-block" style="font-size:.6rem;letter-spacing:.5px;">Cr&iacute;ticos</small>
                        <h3 class="fw-bold mb-0">{{ number_format($stats['criticals']) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted">Nivel</label>
                    <select name="level" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="error" {{ request('level') == 'error' ? 'selected' : '' }}>Error</option>
                        <option value="warning" {{ request('level') == 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="critical" {{ request('level') == 'critical' ? 'selected' : '' }}>Critical</option>
                        <option value="info" {{ request('level') == 'info' ? 'selected' : '' }}>Info</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted">Fuente</label>
                    <select name="source" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        <option value="exception" {{ request('source') == 'exception' ? 'selected' : '' }}>Excepci&oacute;n</option>
                        <option value="log" {{ request('source') == 'log' ? 'selected' : '' }}>Log</option>
                        <option value="ecf" {{ request('source') == 'ecf' ? 'selected' : '' }}>e-CF</option>
                        <option value="dgii" {{ request('source') == 'dgii' ? 'selected' : '' }}>DGII</option>
                        <option value="print" {{ request('source') == 'print' ? 'selected' : '' }}>Impresi&oacute;n</option>
                        <option value="email" {{ request('source') == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="validation" {{ request('source') == 'validation' ? 'selected' : '' }}>Validaci&oacute;n</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted">Desde</label>
                    <input type="date" name="desde" class="form-control form-control-sm" value="{{ request('desde') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted">Hasta</label>
                    <input type="date" name="hasta" class="form-control form-control-sm" value="{{ request('hasta') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">Buscar</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Mensaje del error..." value="{{ request('search') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill w-100 fw-bold">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Error List -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-bug text-danger me-2"></i>Registro de Errores</h5>
            <div class="d-flex gap-2">
                <small class="text-muted">{{ $errorLogs->total() }} resultado(s)</small>
                @if($stats['total'] > 0)
                <form method="POST" action="{{ route('owner.instances.errors.clear', $instance) }}" onsubmit="return confirm('Eliminar errores con m&aacute;s de 30 d&iacute;as de antig&uuml;edad?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">
                        <i class="bi bi-trash me-1"></i>Limpiar antiguos
                    </button>
                </form>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            @if($errorLogs->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:140px;">Fecha</th>
                            <th style="width:90px;">Nivel</th>
                            <th style="width:110px;">Fuente</th>
                            <th>Mensaje</th>
                            <th style="width:120px;">Usuario</th>
                            <th style="width:100px;">IP</th>
                            <th style="width:50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($errorLogs as $log)
                        <tr class="error-row" data-bs-toggle="modal" data-bs-target="#errorModal{{ $log->id }}">
                            <td><small class="text-muted">{{ $log->created_at->format('d/m/Y H:i:s') }}</small></td>
                            <td>
                                <span class="badge bg-{{ $log->level_color }} bg-opacity-10 text-{{ $log->level_color }} rounded-pill text-uppercase" style="font-size:.65rem;">
                                    {{ $log->level }}
                                </span>
                            </td>
                            <td>
                                <i class="bi {{ $log->source_icon }} me-1 text-muted"></i>
                                <small>{{ ucfirst($log->source) }}</small>
                            </td>
                            <td class="text-truncate" style="max-width:400px;">{{ $log->title }}</td>
                            <td><small>{{ $log->user?->name ?? '—' }}</small></td>
                            <td><small class="text-muted">{{ $log->ip_address ?? '—' }}</small></td>
                            <td><i class="bi bi-chevron-right text-muted"></i></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($errorLogs->hasPages())
            <div class="p-3">
                {{ $errorLogs->links() }}
            </div>
            @endif
            @else
            <div class="text-center py-5">
                <div class="bg-success bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:72px;height:72px;">
                    <i class="bi bi-check-circle-fill text-success fs-1"></i>
                </div>
                <h5 class="fw-bold text-muted">Sin errores</h5>
                <p class="text-muted mb-0">No se encontraron errores con los filtros seleccionados.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Detail Modals -->
@foreach($errorLogs as $log)
<div class="modal fade" id="errorModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <div>
                    <span class="badge bg-{{ $log->level_color }} bg-opacity-10 text-{{ $log->level_color }} rounded-pill text-uppercase mb-2" style="font-size:.7rem;">
                        <i class="bi {{ $log->source_icon }} me-1"></i>{{ $log->level }} &middot; {{ ucfirst($log->source) }}
                    </span>
                    <h5 class="fw-bold mb-0">{{ $log->title }}</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <small class="text-muted fw-bold text-uppercase d-block" style="font-size:.6rem;">Fecha</small>
                        <span>{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted fw-bold text-uppercase d-block" style="font-size:.6rem;">Usuario</small>
                        <span>{{ $log->user?->name ?? '—' }} {{ $log->user?->email ? '(' . $log->user->email . ')' : '' }}</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted fw-bold text-uppercase d-block" style="font-size:.6rem;">IP</small>
                        <span>{{ $log->ip_address ?? '—' }}</span>
                    </div>
                </div>

                @if($log->file)
                <div class="mb-3">
                    <small class="text-muted fw-bold text-uppercase d-block" style="font-size:.6rem;">Archivo</small>
                    <code class="small">{{ $log->file }}@if($log->line):{{ $log->line }}@endif</code>
                </div>
                @endif

                <div class="mb-3">
                    <small class="text-muted fw-bold text-uppercase d-block mb-2" style="font-size:.6rem;">Mensaje Completo</small>
                    <div class="p-3 bg-light rounded-3">
                        <pre class="mb-0 small" style="white-space:pre-wrap;word-break:break-word;">{{ $log->message }}</pre>
                    </div>
                </div>

                @if($log->context)
                <div class="mb-3">
                    <small class="text-muted fw-bold text-uppercase d-block mb-2" style="font-size:.6rem;">Contexto</small>
                    <div class="trace-block">
@foreach($log->context as $key => $value)
<span class="context-key">{{ $key }}</span>: <span class="context-value">{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $value }}</span>
@endforeach
                    </div>
                </div>
                @endif

                @if($log->user_agent)
                <div>
                    <small class="text-muted fw-bold text-uppercase d-block mb-2" style="font-size:.6rem;">User Agent</small>
                    <small class="text-muted">{{ $log->user_agent }}</small>
                </div>
                @endif
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
