<div class="col-xl col-md-6 col-6">
    <div class="ui-stat">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-stat-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="ui-stat-label">Usuarios Activos (24h)</div>
                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2 py-1">
                    {{ $usuariosActivos24h > 0 ? round(($usuariosActivos24h / max($totalUsuarios, 1)) * 100) : 0 }}%
                </span>
            </div>
            <div class="ui-stat-value" style="font-size:1.45rem;color:#8b5cf6;">{{ $usuariosActivos24h }}</div>
            <div class="ui-stat-sub">De {{ $totalUsuarios }} total</div>
        </div>
    </div>
</div>
