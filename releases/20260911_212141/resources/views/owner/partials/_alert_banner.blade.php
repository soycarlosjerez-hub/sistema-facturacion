@if($bloqueadas > 0 || $erroresCriticos24h > 0 || $pendingApprovals > 0)
<div class="ui-card mb-4" style="border-left:4px solid {{ $bloqueadas > 0 ? '#ef4444' : ($erroresCriticos24h > 0 ? '#dc2626' : '#f59e0b') }};animation-delay:.05s">
    <div class="card-body py-3 px-4">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <i class="bi bi-exclamation-triangle-fill {{ $bloqueadas > 0 ? 'text-danger' : ($erroresCriticos24h > 0 ? 'text-danger' : 'text-warning') }} fs-5 flex-shrink-0"></i>
            <div class="flex-grow-1 fw-medium" style="min-width:200px;">
                @if($bloqueadas > 0)
                    {{ $bloqueadas }} instancia(s) <strong>bloqueada(s)</strong>
                    @if($erroresCriticos24h > 0 || $pendingApprovals > 0)<br>@endif
                @endif
                @if($erroresCriticos24h > 0)
                    {{ $erroresCriticos24h }} error(es) <strong>crítico(s)</strong> (24h)
                    @if($pendingApprovals > 0)<br>@endif
                @endif
                @if($pendingApprovals > 0)
                    {{ $pendingApprovals }} solicitud(ones) <strong>pendiente(s)</strong> de aprobación
                @endif
            </div>
            <div class="d-flex flex-wrap gap-2 flex-shrink-0">
                @if($bloqueadas > 0)
                    <a href="{{ route('owner.instances.index') }}" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                        <i class="bi bi-lock-fill me-1"></i>Ver Bloqueadas
                    </a>
                @endif
                @if($erroresCriticos24h > 0)
                    <a href="{{ route('owner.errors.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                        <i class="bi bi-bug-fill me-1"></i>Errores
                    </a>
                @endif
                @if($pendingApprovals > 0)
                    <a href="{{ route('owner.solicitudes.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                        <i class="bi bi-check2-circle me-1"></i>Aprobar
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
