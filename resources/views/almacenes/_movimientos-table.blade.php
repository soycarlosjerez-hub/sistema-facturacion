<table class="table table-hover align-middle mb-0">
    <thead class="table-light">
        <tr>
            <th class="ps-4">Fecha y Hora</th>
            <th>Producto</th>
            <th>Almacén</th>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Nota / Concepto</th>
            <th class="text-end pe-4">Registrado por</th>
        </tr>
    </thead>
    <tbody>
        @forelse($movimientos as $m)
        <tr>
            <td class="ps-4">
                <div class="small fw-bold text-dark">{{ $m->created_at->format('d/m/Y') }}</div>
                <div class="text-muted small" style="font-size: 0.7rem;">{{ $m->created_at->format('h:i A') }}</div>
            </td>
            <td>
                <div class="fw-bold text-dark small">{{ $m->producto?->nombre ?? '—' }}</div>
                <small class="text-muted" style="font-size: 0.7rem;">ID: {{ $m->producto?->id ?? '—' }}</small>
            </td>
            <td>
                <span class="badge bg-light text-dark border-0 p-0 fw-bold">{{ $m->almacen?->nombre ?? '—' }}</span>
            </td>
            <td>
                @if($m->tipo === 'entrada')
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                        <i class="bi bi-arrow-down-left me-1"></i> Entrada
                    </span>
                @else
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">
                        <i class="bi bi-arrow-up-right me-1"></i> Salida
                    </span>
                @endif
            </td>
            <td>
                <div class="fw-bold {{ $m->tipo === 'entrada' ? 'text-success' : 'text-danger' }}">
                    {{ $m->tipo === 'entrada' ? '+' : '-' }} {{ $m->cantidad }}
                </div>
            </td>
            <td>
                <small class="text-muted">{{ $m->nota ?? 'Sin observaciones' }}</small>
            </td>
            <td class="text-end pe-4">
                <div class="small fw-bold text-dark">{{ $m->user->name ?? 'Sistema' }}</div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center py-5">
                <i class="bi bi-arrow-left-right display-1 text-muted opacity-25"></i>
                <p class="text-muted mt-3">No hay movimientos registrados.</p>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
