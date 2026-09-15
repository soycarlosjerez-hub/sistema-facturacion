<div class="ui-card" style="--delay:.35s">
    <div class="ui-card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-building me-2" style="color:var(--accent,#8b5cf6)"></i>
                Instancias Recientes
            </h5>
            <a href="{{ route('owner.instances.index') }}" class="text-decoration-none small fw-bold">Ver todas →</a>
        </div>
        <div class="table-responsive">
            <table class="ui-table mb-0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Vencimiento</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($instancias->take(8) as $instance)
                    <tr>
                        <td class="fw-bold">
                            <a href="{{ route('owner.instances.show', $instance) }}" class="text-decoration-none">{{ $instance->nombre }}</a>
                        </td>
                        <td><span class="ui-badge ui-badge-{{ $instance->businessType?->color ?? 'secondary' }} rounded-pill">{{ $instance->businessType?->nombre ?? '—' }}</span></td>
                        <td>
                            @if(!$instance->activo)
                                <span class="ui-badge ui-badge-neutral rounded-pill">Inactiva</span>
                            @elseif($instance->bloqueado)
                                <span class="ui-badge ui-badge-danger rounded-pill">Bloqueada</span>
                            @elseif($instance->estaAlDia())
                                <span class="ui-badge ui-badge-success rounded-pill">Al día</span>
                            @else
                                <span class="ui-badge ui-badge-warning rounded-pill">{{ $instance->mesesAtrasados() }} mes(es)</span>
                            @endif
                        </td>
                        <td>
                            @if($instance->fecha_vencimiento)
                                {{ $instance->fecha_vencimiento->format('d/m/Y') }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('owner.instances.show', $instance) }}" class="ui-action ui-action-view" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('owner.instances.edit', $instance) }}" class="ui-action ui-action-edit" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No hay instancias registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
