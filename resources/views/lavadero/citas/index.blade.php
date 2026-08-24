@extends('layouts.app')
@section('title', 'Citas / Turnos')
@push('styles')
@include('partials.premium-ui')
@endpush
@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-calendar-event-fill"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Citas / Turnos</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-calendar3 me-1"></i>
                        <span>Programación de servicios</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <form method="GET" class="d-flex gap-2">
                    <input type="date" name="fecha" class="ui-input" value="{{ $fecha }}">
                    <button class="ui-btn ui-btn-ghost"><i class="bi bi-search me-1"></i> Ver</button>
                </form>
                <button class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#citaModal">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Cita
                </button>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <div class="ui-card-title"><i class="bi bi-calendar-day me-2"></i>Citas del {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</div>
            <div class="table-responsive">
                <table class="ui-table mb-0">
                    <thead>
                        <tr><th>Hora</th><th>Cliente</th><th>Vehículo</th><th>Servicio</th><th>Estado</th><th class="text-end">Acciones</th></tr>
                    </thead>
                    <tbody>
                        @forelse($citas as $c)
                        <tr>
                            <td class="fw-bold">{{ $c->fecha_hora->format('h:i A') }}</td>
                            <td>{{ $c->cliente?->nombre ?? '—' }}</td>
                            <td>{{ $c->vehiculo?->nombre_completo ?? '—' }}</td>
                            <td>{{ $c->servicio ?? '—' }}</td>
                            <td>
                                <span class="ui-badge {{ $c->estado === 'pendiente' ? 'ui-badge-warning' : ($c->estado === 'confirmada' ? 'ui-badge-info' : ($c->estado === 'completada' ? 'ui-badge-success' : 'ui-badge-neutral')) }}">
                                    {{ $c->estado }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill dropdown-toggle" data-bs-toggle="dropdown">Acción</button>
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
                                            <form id="del-cita-{{ $c->id }}" action="{{ route('lavadero.citas.destroy', $c) }}" method="POST">@csrf @method('DELETE')</form>
                                            <button type="button" class="dropdown-item small text-danger" onclick="UI.confirm.deleteWithForm('del-cita-{{ $c->id }}', '{{ addslashes($c->cliente?->nombre ?? 'cita') }}')">
                                                <i class="bi bi-trash me-2"></i>Eliminar
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="ui-empty-state">
                                    <i class="bi bi-calendar-x"></i>
                                    <p>Sin citas para esta fecha</p>
                                </div>
                            </td>
                        </tr>
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
                    <label class="ui-label">Cliente</label>
                    <select name="cliente_id" class="ui-select" required>
                        <option value="">Seleccionar cliente</option>
                        @foreach(\App\Models\Cliente::orderBy('nombre')->get() as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }} {{ $cliente->telefono ? '· ' . $cliente->telefono : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="ui-label">Vehículo (opcional)</label>
                    <select name="vehiculo_id" class="ui-select">
                        <option value="">Sin vehículo</option>
                        @foreach(\App\Models\Vehiculo::with('cliente')->orderBy('placa')->get() as $v)
                        <option value="{{ $v->id }}">{{ $v->nombre_completo }} ({{ $v->cliente?->nombre }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="ui-label">Fecha y Hora</label>
                    <input type="datetime-local" name="fecha_hora" class="ui-input" required>
                </div>
                <div class="mb-3">
                    <label class="ui-label">Servicio</label>
                    <input type="text" name="servicio" class="ui-input" placeholder="Ej: Lavado completo">
                </div>
                <div class="mb-3">
                    <label class="ui-label">Notas</label>
                    <textarea name="notas" class="ui-textarea" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-4">Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
