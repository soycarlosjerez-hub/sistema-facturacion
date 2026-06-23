@extends('layouts.app')

@section('title', 'Reservaciones')

@push('styles')
<style>
    .premium-header {
        background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
        border-radius: 1rem;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
        position: relative;
        overflow: hidden;
    }
    .premium-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .filter-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }
    .status-badge {
        padding: 0.4em 0.8em;
        border-radius: 2rem;
        font-weight: 500;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    .btn-icon-hover {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
    }
    .btn-icon-hover:hover { transform: scale(1.15); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="premium-header">
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white bg-opacity-20 rounded-2 p-2 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bi bi-calendar-check fs-2 text-white"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Reservaciones</h2>
                    <p class="text-white text-opacity-75 mb-0">Gestión de reservaciones de mesas</p>
                </div>
            </div>
            <button class="btn btn-light rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#reservaModal">
                <i class="bi bi-plus-lg me-1"></i> Nueva Reservación
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible rounded-4 border-0 shadow-sm fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible rounded-4 border-0 shadow-sm fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-3 bg-light bg-opacity-50">
            <form method="GET" action="{{ route('restaurante.reservaciones.index') }}" id="filtros-form" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <div class="input-group input-group-merge border-0 shadow-none">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="busqueda" class="form-control border-0 bg-white"
                               placeholder="Buscar por cliente o mesa..." value="{{ request('busqueda') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <select name="estado" class="form-select border-0 shadow-none bg-white">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="confirmada" {{ request('estado') == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                        <option value="cumplida" {{ request('estado') == 'cumplida' ? 'selected' : '' }}>Cumplida</option>
                        <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>
                <div class="col-lg-4 d-flex gap-2">
                    <button class="btn btn-primary rounded-pill px-3 flex-grow-1"><i class="bi bi-funnel"></i> Filtrar</button>
                    <a href="{{ route('restaurante.reservaciones.index') }}" class="btn btn-light rounded-pill px-3"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted text-uppercase small">
                        <th class="ps-4">Mesa</th>
                        <th>Cliente</th>
                        <th>Teléfono</th>
                        <th>Personas</th>
                        <th>Fecha / Hora</th>
                        <th>Estado</th>
                        <th>Notas</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservaciones as $r)
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold">{{ $r->mesa->nombre ?? 'Mesa '.$r->mesa->numero }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $r->cliente_nombre }}</div>
                            @if($r->cliente_email)
                                <small class="text-muted">{{ $r->cliente_email }}</small>
                            @endif
                        </td>
                        <td>{{ $r->cliente_telefono ?? '—' }}</td>
                        <td>{{ $r->personas }}</td>
                        <td>{{ $r->fecha_hora->format('d/m/Y h:i A') }}</td>
                        <td>
                            <span class="badge rounded-pill px-3
                                {{ $r->estado === 'pendiente' ? 'bg-warning text-dark' : '' }}
                                {{ $r->estado === 'confirmada' ? 'bg-success' : '' }}
                                {{ $r->estado === 'cancelada' ? 'bg-secondary' : '' }}
                                {{ $r->estado === 'cumplida' ? 'bg-info' : '' }}
                            ">{{ ucfirst($r->estado) }}</span>
                        </td>
                        <td><small class="text-muted">{{ Str::limit($r->notas, 30) ?: '—' }}</small></td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a class="btn btn-sm btn-outline-primary rounded-pill me-1" href="#" title="Ver detalles"
                                   onclick="editarReservacion({{ $r->id }}, {{ $r->mesa_id }}, @js($r->cliente_nombre), @js($r->cliente_telefono), @js($r->cliente_email), {{ $r->personas }}, @js($r->fecha_hora->format('Y-m-d\TH:i')), @js($r->notas)); return false;">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a class="btn btn-sm btn-outline-warning rounded-pill me-1" href="#" title="Editar"
                                   onclick="editarReservacion({{ $r->id }}, {{ $r->mesa_id }}, @js($r->cliente_nombre), @js($r->cliente_telefono), @js($r->cliente_email), {{ $r->personas }}, @js($r->fecha_hora->format('Y-m-d\TH:i')), @js($r->notas)); return false;">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <div class="dropdown d-inline me-1">
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill dropdown-toggle" data-bs-toggle="dropdown" title="Cambiar estado">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0">
                                        <li>
                                            <form action="{{ route('restaurante.reservaciones.estado', $r) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="estado" value="confirmada">
                                                <button class="dropdown-item small"><i class="bi bi-check-circle text-success me-2"></i>Confirmar</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('restaurante.reservaciones.estado', $r) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="estado" value="cumplida">
                                                <button class="dropdown-item small"><i class="bi bi-check-all text-info me-2"></i>Marcar cumplida</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('restaurante.reservaciones.estado', $r) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="estado" value="cancelada">
                                                <button class="dropdown-item small"><i class="bi bi-x-circle text-danger me-2"></i>Cancelar</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                                <form action="{{ route('restaurante.reservaciones.destroy', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar la reservación de ' + @js($r->cliente_nombre) + '? Esta acción no se puede deshacer.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-calendar-x display-1 text-muted opacity-25"></i>
                            <p class="text-muted mt-3">No se encontraron reservaciones.</p>
                            <button class="btn btn-primary rounded-pill mt-2" data-bs-toggle="modal" data-bs-target="#reservaModal">
                                <i class="bi bi-plus-lg me-1"></i> Crear primera reservación
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $reservaciones->links() }}
    </div>
</div>

{{-- Modal nueva reservación --}}
<div class="modal fade" id="reservaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('restaurante.reservaciones.store') }}" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header border-0">
                <h5 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nueva Reservación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Mesa <span class="text-danger">*</span></label>
                    <select name="mesa_id" class="form-select rounded-3" required>
                        <option value="">Seleccionar mesa</option>
                        @foreach($mesas as $m)
                            <option value="{{ $m->id }}">{{ $m->nombre ?? 'Mesa '.$m->numero }} (Cap. {{ $m->capacidad }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nombre del cliente <span class="text-danger">*</span></label>
                    <input type="text" name="cliente_nombre" class="form-control rounded-3" required maxlength="200">
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Teléfono</label>
                        <input type="text" name="cliente_telefono" class="form-control rounded-3" maxlength="30">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="cliente_email" class="form-control rounded-3" maxlength="200">
                    </div>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Personas <span class="text-danger">*</span></label>
                        <input type="number" name="personas" class="form-control rounded-3" value="2" min="1" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Fecha y Hora <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="fecha_hora" class="form-control rounded-3" required>
                    </div>
                </div>
                <div class="mb-3 mt-2">
                    <label class="form-label small fw-bold">Notas</label>
                    <textarea name="notas" class="form-control rounded-3" rows="2" maxlength="500"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary rounded-pill">Crear Reservación</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal editar / ver reservación --}}
<div class="modal fade" id="editarReservaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content rounded-4 border-0 shadow" id="form-editar-reserva">
            @csrf
            @method('PUT')
            <div class="modal-header border-0">
                <h5 class="fw-bold"><i class="bi bi-pencil me-2"></i>Editar Reservación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Mesa <span class="text-danger">*</span></label>
                    <select name="mesa_id" id="edit-mesa-id" class="form-select rounded-3" required>
                        <option value="">Seleccionar mesa</option>
                        @foreach($mesas as $m)
                            <option value="{{ $m->id }}">{{ $m->nombre ?? 'Mesa '.$m->numero }} (Cap. {{ $m->capacidad }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nombre del cliente <span class="text-danger">*</span></label>
                    <input type="text" name="cliente_nombre" id="edit-cliente-nombre" class="form-control rounded-3" required maxlength="200">
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Teléfono</label>
                        <input type="text" name="cliente_telefono" id="edit-cliente-telefono" class="form-control rounded-3" maxlength="30">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="cliente_email" id="edit-cliente-email" class="form-control rounded-3" maxlength="200">
                    </div>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Personas <span class="text-danger">*</span></label>
                        <input type="number" name="personas" id="edit-personas" class="form-control rounded-3" min="1" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Fecha y Hora <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="fecha_hora" id="edit-fecha-hora" class="form-control rounded-3" required>
                    </div>
                </div>
                <div class="mb-3 mt-2">
                    <label class="form-label small fw-bold">Notas</label>
                    <textarea name="notas" id="edit-notas" class="form-control rounded-3" rows="2" maxlength="500"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary rounded-pill">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<style>
.table > :not(caption) > * > * { padding: 0.85rem 0.5rem; }
.table thead th { font-weight: 700; letter-spacing: 0.03em; border-bottom: 1px solid #e2e8f0; }
</style>
@endsection

@push('scripts')
<script>
function editarReservacion(id, mesaId, clienteNombre, clienteTelefono, clienteEmail, personas, fechaHora, notas) {
    var form = document.getElementById('form-editar-reserva');
    form.action = '/restaurante/reservaciones/' + id;
    document.getElementById('edit-mesa-id').value = mesaId;
    document.getElementById('edit-cliente-nombre').value = clienteNombre || '';
    document.getElementById('edit-cliente-telefono').value = clienteTelefono || '';
    document.getElementById('edit-cliente-email').value = clienteEmail || '';
    document.getElementById('edit-personas').value = personas;
    document.getElementById('edit-fecha-hora').value = fechaHora || '';
    document.getElementById('edit-notas').value = notas || '';
    new bootstrap.Modal(document.getElementById('editarReservaModal')).show();
}
</script>
@endpush