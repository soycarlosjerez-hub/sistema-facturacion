@extends('layouts.app')
@section('title', 'Citas / Turnos')
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-calendar-event text-primary me-2"></i>Citas / Turnos</h2>
            <p class="text-muted mb-0">Programación de servicios</p>
        </div>
        <div class="d-flex gap-2">
            <form method="GET" class="d-flex gap-2">
                <input type="date" name="fecha" class="form-control rounded-3" value="{{ $fecha }}">
                <button class="btn btn-primary rounded-pill px-3">Ver</button>
            </form>
            <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#citaModal">
                <i class="bi bi-plus-lg me-1"></i> Nueva Cita
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white rounded-top-4 border-0 pt-3 px-4">
            <h6 class="fw-bold mb-0">Citas del {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light small">
                        <tr><th>Hora</th><th>Cliente</th><th>Vehículo</th><th>Servicio</th><th>Estado</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse($citas as $c)
                        <tr>
                            <td class="fw-bold">{{ $c->fecha_hora->format('h:i A') }}</td>
                            <td>{{ $c->cliente?->nombre ?? '—' }}</td>
                            <td>{{ $c->vehiculo?->nombre_completo ?? '—' }}</td>
                            <td>{{ $c->servicio ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $c->estado === 'pendiente' ? 'bg-warning text-dark' : ($c->estado === 'confirmada' ? 'bg-info' : ($c->estado === 'completada' ? 'bg-success' : 'bg-secondary')) }} rounded-pill">
                                    {{ $c->estado }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill dropdown-toggle" data-bs-toggle="dropdown">Acción</button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0">
                                        <li>
                                            <form action="{{ route('lavadero.citas.update', $c) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="estado" value="confirmada">
                                                <button class="dropdown-item small"><i class="bi bi-check-circle me-2 text-info"></i>Confirmar</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('lavadero.citas.update', $c) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="estado" value="completada">
                                                <button class="dropdown-item small"><i class="bi bi-check-lg me-2 text-success"></i>Completar</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('lavadero.citas.update', $c) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="estado" value="cancelada">
                                                <button class="dropdown-item small"><i class="bi bi-x-circle me-2 text-danger"></i>Cancelar</button>
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('lavadero.citas.destroy', $c) }}" method="POST" onsubmit="return confirm('¿Eliminar cita?')">
                                                @csrf @method('DELETE')
                                                <button class="dropdown-item small text-danger"><i class="bi bi-trash me-2"></i>Eliminar</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">Sin citas para esta fecha</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Nueva Cita --}}
<div class="modal fade" id="citaModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('lavadero.citas.store') }}" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header border-0">
                <h6 class="fw-bold"><i class="bi bi-plus-circle me-2"></i>Nueva Cita</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Cliente</label>
                    <select name="cliente_id" class="form-select rounded-3" required>
                        <option value="">Seleccionar cliente</option>
                        @foreach(\App\Models\Cliente::orderBy('nombre')->get() as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }} {{ $cliente->telefono ? '· ' . $cliente->telefono : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Vehículo (opcional)</label>
                    <select name="vehiculo_id" class="form-select rounded-3">
                        <option value="">Sin vehículo</option>
                        @foreach(\App\Models\Vehiculo::with('cliente')->orderBy('placa')->get() as $v)
                        <option value="{{ $v->id }}">{{ $v->nombre_completo }} ({{ $v->cliente?->nombre }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Fecha y Hora</label>
                    <input type="datetime-local" name="fecha_hora" class="form-control rounded-3" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Servicio</label>
                    <input type="text" name="servicio" class="form-control rounded-3" placeholder="Ej: Lavado completo">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Notas</label>
                    <textarea name="notas" class="form-control rounded-3" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
