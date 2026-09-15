<div class="col-xl col-md-6 col-6">
    <div class="ui-stat">
        <div class="ui-card-accent" style="background:#3b82f6"></div>
        <div class="ui-stat-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="ui-stat-label">Aprobaciones</div>
                <span class="badge {{ $pendingApprovals > 0 ? 'bg-warning' : 'bg-success' }} bg-opacity-10 text-{{ $pendingApprovals > 0 ? 'warning' : 'success' }} rounded-pill px-2 py-1">
                    {{ $pendingApprovals > 0 ? 'Pendiente' : 'Al día' }}
                </span>
            </div>
            <div class="ui-stat-value" style="font-size:1.45rem;color:#3b82f6;">{{ $pendingApprovals }}</div>
            <div class="ui-stat-sub">Solicitudes sin aprobar</div>
        </div>
    </div>
</div>
