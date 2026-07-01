@extends('layouts.app')

@section('title', 'Plantilla de Gastos')

@push('styles')
@include('partials.premium-ui')
<style>
.plantas-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(16,185,129,.04);
    margin: 0;
}
.plantas-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.plantas-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
.plantas-table tbody tr:last-child td { border-bottom: none; }
.plantas-table tbody tr { transition: background .15s; }
.plantas-table tbody tr:hover { background: rgba(16,185,129,.03); }
body.dark-mode .plantas-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .plantas-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
.status-dot {
    width: 8px; height: 8px; border-radius: 50%; display: inline-block;
}
.status-dot.active { background: #10b981; }
.status-dot.inactive { background: #94a3b8; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-file-earmark-richtext"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Plantilla de Gastos</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-bookmark me-1"></i>
                        Gestiona plantillas para registrar gastos recurrentes rápidamente
                        <span class="mx-2">·</span>
                        <i class="bi bi-list-ul me-1"></i>
                        {{ $plantillas->total() }} registro(s)
                    </small>
                </div>
            </div>
            <div>
                @can('plantilla-gastos.create')
                <a href="{{ route('plantilla-gastos.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Plantilla
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="premium-card mb-4" style="animation-delay:.15s;">
        <div class="card-accent green"></div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('plantilla-gastos.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, descripción o comprobante..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <select name="categoria" class="form-select">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $key => $label)
                            <option value="{{ $key }}" {{ request('categoria') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <select name="activo" class="form-select">
                        <option value="">Estado</option>
                        <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activa</option>
                        <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactiva</option>
                    </select>
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('plantilla-gastos.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card" style="animation-delay:.2s;">
        <div class="card-accent green"></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table plantas-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Nombre</th>
                            <th>Categoría</th>
                            <th>Método Pago</th>
                            <th>Comprobante</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plantillas as $plantilla)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-semibold">{{ $plantilla->nombre }}</span>
                                    @if($plantilla->descripcion)
                                        <br><small class="text-muted">{{ Str::limit($plantilla->descripcion, 60) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($plantilla->categoria)
                                        <span class="badge rounded-pill" style="background:rgba(16,185,129,.1);color:#059669;font-weight:600;">{{ $categorias[$plantilla->categoria] ?? $plantilla->categoria }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($plantilla->metodo_pago)
                                        <span class="text-muted small">{{ ucfirst(str_replace('_', ' ', $plantilla->metodo_pago)) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($plantilla->comprobante)
                                        <span class="badge rounded-pill" style="background:rgba(99,102,241,.1);color:#4f46e5;font-weight:600;">{{ $plantilla->comprobante }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="status-dot {{ $plantilla->activo ? 'active' : 'inactive' }}"></span>
                                    <small class="ms-1 text-muted">{{ $plantilla->activo ? 'Activa' : 'Inactiva' }}</small>
                                </td>
                                <td class="text-end pe-4">
                                    @can('plantilla-gastos.edit')
                                    <a href="{{ route('plantilla-gastos.edit', $plantilla) }}" class="premium-btn-edit" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @if($plantilla->activo)
                                        <form action="{{ route('plantilla-gastos.desactivar', $plantilla) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="premium-btn-warning ms-1" title="Desactivar">
                                                <i class="bi bi-pause-circle"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('plantilla-gastos.activar', $plantilla) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="premium-btn-success ms-1" title="Activar">
                                                <i class="bi bi-play-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @can('plantilla-gastos.delete')
                                    <button type="button" class="premium-btn-delete ms-1" 
                                            onclick="confirmDelete('{{ route('plantilla-gastos.destroy', $plantilla) }}', '{{ addslashes($plantilla->nombre) }}')"
                                            title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1" style="color:#cbd5e1;"></i>
                                    <p class="mt-2 mb-0 fw-semibold">No hay plantillas registradas</p>
                                    @can('plantilla-gastos.create')
                                    <a href="{{ route('plantilla-gastos.create') }}" class="btn btn-primary rounded-pill mt-2">Crear primera plantilla</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($plantillas->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $plantillas->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(url, name) {
    Swal.fire({
        title: '¿Eliminar plantilla?',
        text: `Se eliminará: "${name}"`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.innerHTML = '@csrf @method("DELETE")';
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
