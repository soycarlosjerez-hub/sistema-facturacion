@extends('layouts.app')
@section('title', 'Usuarios Online')

@push('styles')
@include('partials.premium-ui')
<style>
.online-dot {
    width: 10px; height: 10px; border-radius: 50%;
    background: #22c55e;
    box-shadow: 0 0 0 3px rgba(34,197,94,.25);
    animation: pulse-green 2s infinite;
    display: inline-block; flex-shrink: 0;
}
@keyframes pulse-green {
    0%, 100% { box-shadow: 0 0 0 3px rgba(34,197,94,.25); }
    50%       { box-shadow: 0 0 0 6px rgba(34,197,94,.1); }
}
.user-card {
    background: white;
    border-radius: 14px;
    padding: 14px 18px;
    border: 1px solid rgba(0,0,0,.07);
    transition: box-shadow .2s, transform .2s;
}
.user-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.08); transform: translateY(-2px); }
.avatar-sm {
    width: 40px; height: 40px; border-radius: 50%;
    background: linear-gradient(135deg,#6366f1,#8b5cf6);
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; color: white; font-size: .9rem; flex-shrink: 0;
}
.instance-section-title {
    font-weight: 700; font-size: .85rem; letter-spacing: .05em;
    text-transform: uppercase; color: #64748b;
    padding: 10px 0 6px;
    border-bottom: 2px solid #f1f5f9;
    margin-bottom: 12px;
}
.refresh-badge {
    background: rgba(99,102,241,.1); color: #6366f1;
    border-radius: 20px; padding: 4px 12px;
    font-size: .78rem; font-weight: 600;
}
</style>
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
                    <i class="bi bi-wifi"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Usuarios Online</h2>
                    <p class="mb-0 opacity-75">Usuarios activos en los &uacute;ltimos 5 minutos en todas las instancias.</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <span class="refresh-badge">
                    <i class="bi bi-arrow-clockwise me-1"></i>Se actualiza con cada visita
                </span>
                <a href="{{ route('owner.dashboard') }}" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Dashboard
                </a>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="ui-stat h-100" style="--delay:.1s">
                <div class="card-body p-3 text-center">
                    <div class="online-dot mx-auto mb-2"></div>
                    <small class="ui-stat-label d-block">Online Ahora</small>
                    <h2 class="ui-stat-value mb-0 text-success">{{ $onlineUsers->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-stat h-100" style="--delay:.15s">
                <div class="card-body p-3 text-center">
                    <small class="ui-stat-label d-block">Instancias Activas</small>
                    <h2 class="ui-stat-value mb-0">{{ $byInstance->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-stat h-100" style="--delay:.2s">
                <div class="card-body p-3 text-center">
                    <small class="ui-stat-label d-block">Ventana</small>
                    <h2 class="ui-stat-value mb-0">5 min</h2>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-stat h-100" style="--delay:.25s">
                <div class="card-body p-3 text-center">
                    <small class="ui-stat-label d-block">&Uacute;ltima actualizaci&oacute;n</small>
                    <small class="fw-bold">{{ now()->format('H:i:s') }}</small>
                </div>
            </div>
        </div>
    </div>

    @if($onlineUsers->isEmpty())
        <div class="ui-card" style="--delay:.3s">
            <div class="card-body p-5 text-center">
                <div class="mb-3" style="font-size: 3rem; opacity: .3;">🌙</div>
                <h5 class="fw-bold text-muted">Ning&uacute;n usuario online en este momento</h5>
                <p class="text-muted small mb-0">Los usuarios aparecer&aacute;n aqu&iacute; cuando naveguen en el sistema.</p>
            </div>
        </div>
    @else
        {{-- Por instancia --}}
        @foreach($byInstance as $instanceId => $users)
            @php $inst = $instancias[$instanceId] ?? null; @endphp
            <div class="ui-card mb-4" style="--delay:.{{ min(5, $loop->iteration + 3) }}s">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="online-dot"></div>
                            <div>
                                <h6 class="fw-bold mb-0">{{ $inst?->nombre ?? 'Instancia #'.$instanceId }}</h6>
                                <small class="text-muted">{{ $users->count() }} usuario(s) online
                                    @if($inst) · {{ $totalByInstance[$instanceId] ?? 0 }} totales @endif
                                </small>
                            </div>
                        </div>
                        @if($inst)
                        <a href="{{ route('owner.instances.online', $inst) }}" class="ui-btn ui-btn-ghost btn-sm rounded-pill px-3">
                            <i class="bi bi-eye me-1"></i>Ver instancia
                        </a>
                        @endif
                    </div>

                    <div class="row g-2">
                        @foreach($users as $user)
                        <div class="col-md-6 col-xl-4">
                            <div class="user-card d-flex align-items-center gap-3">
                                <div class="avatar-sm">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-truncate">{{ $user->name }}</div>
                                    <small class="text-muted text-truncate d-block">{{ $user->email }}</small>
                                    @if($user->instanceRole)
                                        <span class="ui-badge ui-badge-primary rounded-pill px-2 py-0 small">
                                            {{ $user->instanceRole->nombre }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <div class="online-dot mb-1"></div>
                                    <small class="text-muted d-block" style="font-size:.7rem;">
                                        {{ $user->last_seen_at?->diffForHumans(null, true) }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @endif

</div>
</div>

<script>
// Auto-refresh cada 60 segundos
setTimeout(() => location.reload(), 60000);
</script>
@endsection
