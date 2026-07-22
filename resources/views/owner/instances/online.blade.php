@extends('layouts.app')
@section('title', 'Usuarios Online - ' . $instance->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
.online-dot {
    width: 10px; height: 10px; border-radius: 50%;
    background: #22c55e;
    box-shadow: 0 0 0 3px rgba(34,197,94,.25);
    animation: pulse-green 2s infinite;
    display: inline-block;
}
@keyframes pulse-green {
    0%, 100% { box-shadow: 0 0 0 3px rgba(34,197,94,.25); }
    50%       { box-shadow: 0 0 0 6px rgba(34,197,94,.1); }
}
.user-row {
    background: white;
    border-radius: 12px;
    padding: 12px 16px;
    border: 1px solid rgba(0,0,0,.06);
    transition: box-shadow .2s, transform .2s;
    display: flex; align-items: center; gap: 14px;
}
.user-row:hover { box-shadow: 0 4px 16px rgba(0,0,0,.08); transform: translateX(4px); }
.avatar-sm {
    width: 44px; height: 44px; border-radius: 50%;
    background: linear-gradient(135deg,#6366f1,#8b5cf6);
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; color: white; font-size: .9rem; flex-shrink: 0;
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-wifi"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Usuarios Online</h2>
                    <p class="mb-0 opacity-75">{{ $instance->nombre }} &middot; últimos 5 minutos</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.instances.show', $instance) }}" class="ui-btn ui-btn-ghost rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Instancia
                </a>
                <a href="{{ route('owner.online.index') }}" class="ui-btn ui-btn-ghost rounded-pill">
                    <i class="bi bi-globe me-2"></i>Ver global
                </a>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-stat-body text-center">
                    <div class="online-dot mx-auto mb-2"></div>
                    <small class="ui-stat-label d-block">Online Ahora</small>
                    <h2 class="ui-stat-value mb-0 text-success">{{ $onlineUsers->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-stat-body text-center">
                    <div class="stat-card-icon bg-primary bg-opacity-10 text-primary mx-auto mb-2">
                        <i class="bi bi-people fs-5"></i>
                    </div>
                    <small class="ui-stat-label d-block">Total Usuarios</small>
                    <h2 class="ui-stat-value mb-0">{{ $totalUsers }}</h2>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-stat" style="--delay:.25s">
                <div class="ui-stat-body text-center">
                    <div class="stat-card-icon bg-success bg-opacity-10 text-success mx-auto mb-2">
                        <i class="bi bi-percent fs-5"></i>
                    </div>
                    <small class="ui-stat-label d-block">% Activos</small>
                    <h2 class="ui-stat-value mb-0">
                        {{ $totalUsers > 0 ? round(($onlineUsers->count() / $totalUsers) * 100) : 0 }}%
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-stat" style="--delay:.3s">
                <div class="ui-stat-body text-center">
                    <div class="stat-card-icon bg-warning bg-opacity-10 text-warning mx-auto mb-2">
                        <i class="bi bi-clock fs-5"></i>
                    </div>
                    <small class="ui-stat-label d-block">Actualizado</small>
                    <small class="fw-bold">{{ now()->format('H:i:s') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.35s">
        <div class="ui-card-accent" style="background:linear-gradient(90deg,#8b5cf6,rgba(255,255,255,.3))"></div>
        <div class="card-body p-4">
            @if($onlineUsers->isEmpty())
                <div class="py-5 text-center">
                    <div class="mb-3" style="font-size: 3rem; opacity:.3;">🌙</div>
                    <h5 class="fw-bold text-muted">Ningún usuario online ahora mismo</h5>
                    <p class="text-muted small mb-0">Aparecerán aquí cuando naveguen en el sistema.</p>
                </div>
            @else
                <div class="d-flex flex-column gap-2">
                    @foreach($onlineUsers as $user)
                    <div class="user-row">
                        <div class="avatar-sm">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $user->name }}</div>
                            <small class="text-muted">{{ $user->email }}</small>
                        </div>
                        @if($user->instanceRole)
                            <span class="badge bg-indigo-100 text-primary rounded-pill px-3 py-1">
                                <i class="bi bi-shield-check me-1"></i>{{ $user->instanceRole->nombre }}
                            </span>
                        @endif
                        @if($user->sucursal_id)
                            <span class="badge bg-light text-secondary rounded-pill px-3 py-1">
                                <i class="bi bi-geo-alt me-1"></i>Sucursal #{{ $user->sucursal_id }}
                            </span>
                        @endif
                        <div class="text-end" style="min-width: 110px;">
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <span class="online-dot"></span>
                                <span class="small text-success fw-semibold">Online</span>
                            </div>
                            <small class="text-muted" style="font-size:.72rem;">
                                Visto {{ $user->last_seen_at?->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
</div>

<script>
setTimeout(() => location.reload(), 60000);
</script>
@endsection
