@extends('layouts.app')
@section('title', 'Reservaciones')
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-calendar-check me-2"></i>Reservaciones</h4>
        <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#reservaModal">
            <i class="bi bi-plus-lg me-1"></i> Nueva Reservación
        </button>
    </div>

    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mesa</th>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Personas</th>
                            <th>Fecha / Hora</th>
                            <th>Estado</th>
                            <th>Notas</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservaciones as $r)
                        <tr>
                            <td class="fw-semibold">{{ $r->mesa->nombre ?? 'Mesa '.$r->mesa->numero }}</td>
                            <td>{{ $r->cliente_nombre }}</td>
                            <td>{{ $r->cliente_telefono ?? '—' }}</td>
                            <td>{{ $r->personas }}</td>
                            <td>{{ $r->fecha_hora->format('d/m/Y h:i A') }}</td>
                            <td>
                                <span class="badge rounded-pill
                                    {{ $r->estado === 'pendiente' ? 'bg-warning text-dark' : '' }}
                                    {{ $r->estado === 'confirmada' ? 'bg-success' : '' }}
                                    {{ $r->estado === 'cancelada' ? 'bg-secondary' : '' }}
                                    {{ $r->estado === 'cumplida' ? 'bg-info' : '' }}
                                ">{{ ucfirst($r->estado) }}</span>
                            </td>
                            <td><small class="text-muted">{{ Str::limit($r->notas, 30) ?: '—' }}</small></td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
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
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('restaurante.reservaciones.destroy', $r) }}" method="POST" onsubmit="return confirm('¿Eliminar esta reservación?')">
                                                @csrf @method('DELETE')
                                                <button class="dropdown-item small text-danger"><i class="bi bi-trash me-2"></i>Eliminar</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @if($reservaciones->isEmpty())
                        <tr><td colspan="8" class="text-center text-muted py-4">No hay reservaciones</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $reservaciones->links() }}</div>
</div>

{{-- Modal nueva reservación --}}
<div class="modal fade" id="reservaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header border-0">
                <h5 class="fw-bold">Nueva Reservación</h5>
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
@endsection
