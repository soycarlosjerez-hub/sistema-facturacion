<div class="ui-card h-100" style="--delay:.25s">
    <div class="ui-card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-calendar-event me-2" style="color:#f59e0b"></i>
                Próximos Vencimientos
            </h5>
            <a href="{{ route('owner.instances.index') }}" class="text-decoration-none small fw-bold">Ver todos</a>
        </div>

        @forelse($proximosVencimientos as $instance)
            <div class="d-flex align-items-center justify-content-between mb-2 pb-2 {{ $loop->last ? '' : 'border-bottom border-light' }}">
                <div style="min-width:0;">
                    <a href="{{ route('owner.instances.show', $instance) }}" class="fw-bold text-decoration-none small d-block text-truncate" style="max-width:180px;">
                        {{ $instance->nombre }}
                    </a>
                    <small class="text-muted" style="font-size:.7rem;">{{ $instance->businessType?->nombre ?? 'Sin tipo' }}</small>
                </div>
                <div class="text-end flex-shrink-0">
                    <div class="ui-badge ui-badge-warning rounded-pill" style="font-size:.65rem;">
                        {{ $instance->fecha_vencimiento->diffForHumans() }}
                    </div>
                    <div class="text-muted" style="font-size:.65rem;">{{ $instance->fecha_vencimiento->format('d/m/Y') }}</div>
                </div>
            </div>
        @empty
            <div class="ui-empty-state">
                <i class="bi bi-calendar-check" style="color:#10b981;"></i>
                <p>Todo al día</p>
                <small class="text-muted">No hay vencimientos próximos</small>
            </div>
        @endforelse
    </div>
</div>
