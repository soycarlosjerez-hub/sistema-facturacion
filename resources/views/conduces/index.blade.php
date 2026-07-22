@extends('layouts.app')

@section('title', 'Conduces (Notas de Entrega)')

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .card-footer { background: rgba(15,23,42,.8); border-color: #334155; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    {{-- Encabezado --}}
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Conduces</h4>
                    <div class="ui-header-meta">Notas de entrega y transporte de mercancía</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('conduces.create')
                <a href="{{ route('conduces.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i>Nuevo Conduce
                </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- Estadísticas --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-stat-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="ui-stat-label">Total</div>
                            <div class="ui-stat-value">{{ $stats['total'] }}</div>
                        </div>
                        <i class="bi bi-files fs-1 text-secondary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-stat-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="ui-stat-label">En tránsito</div>
                            <div class="ui-stat-value text-info">{{ $stats['en_transito'] }}</div>
                        </div>
                        <i class="bi bi-truck fs-1 text-info opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-stat-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="ui-stat-label">Entregados hoy</div>
                            <div class="ui-stat-value text-success">{{ $stats['entregados_hoy'] }}</div>
                        </div>
                        <i class="bi bi-check-circle-fill fs-1 text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="ui-stat" style="--delay:.25s">
                <div class="ui-stat-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="ui-stat-label">Vencidos</div>
                            <div class="ui-stat-value text-danger">{{ $stats['vencidos'] }}</div>
                        </div>
                        <i class="bi bi-exclamation-triangle-fill fs-1 text-danger opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="ui-card mb-3" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form method="GET" action="{{ route('conduces.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="buscar" class="ui-label small">Buscar</label>
                    <input type="text" name="buscar" id="buscar" class="ui-input"
                           placeholder="Número, cliente, transportista..."
                           value="{{ request('buscar') }}">
                </div>
                <div class="col-md-2">
                    <label for="estado" class="ui-label small">Estado</label>
                    <select name="estado" id="estado" class="ui-select">
                        <option value="todos">Todos</option>
                        @foreach(\App\Models\Conduce::ESTADOS as $key => $estado)
                            <option value="{{ $key }}" {{ request('estado') == $key ? 'selected' : '' }}>
                                {{ $estado['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="cliente_id" class="ui-label small">Cliente</label>
                    <select name="cliente_id" id="cliente_id" class="ui-select">
                        <option value="">Todos</option>
                        @foreach($clientes as $cli)
                            <option value="{{ $cli->id }}" {{ request('cliente_id') == $cli->id ? 'selected' : '' }}>
                                {{ $cli->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="desde" class="ui-label small">Desde</label>
                    <input type="date" name="desde" id="desde" class="ui-input" value="{{ request('desde') }}">
                </div>
                <div class="col-md-2">
                    <label for="hasta" class="ui-label small">Hasta</label>
                    <input type="date" name="hasta" id="hasta" class="ui-input" value="{{ request('hasta') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="ui-btn ui-btn-solid w-100" aria-label="Filtrar">
                        <i class="bi bi-funnel"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="ui-card" style="--delay:.2s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table table-hover align-middle mb-0" role="table" aria-label="Lista de conduces">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Transportista</th>
                            <th class="text-center">Items</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($conduces as $conduce)
                            <tr>
                                <td>
                                    <a href="{{ route('conduces.show', $conduce) }}" class="fw-bold text-decoration-none">
                                        {{ $conduce->numero }}
                                    </a>
                                    @if($conduce->esta_vencido)
                                        <i class="bi bi-exclamation-circle-fill text-danger ms-1"
                                           title="Vencido" aria-label="Vencido"></i>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $conduce->fecha->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <div>{{ $conduce->cliente?->nombre ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $conduce->cliente?->rnc_cedula }}</small>
                                </td>
                                <td>
                                    @if($conduce->transportista)
                                        <div>{{ $conduce->transportista }}</div>
                                        @if($conduce->chofer)
                                            <small class="text-muted">{{ $conduce->chofer }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $conduce->total_items }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $conduce->estado_color }}">
                                        <i class="bi bi-{{ $conduce->estado_icon }} me-1" aria-hidden="true"></i>
                                        {{ $conduce->estado_label }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('conduces.show', $conduce) }}"
                                       class="btn btn-sm btn-outline-primary rounded-pill"
                                       aria-label="Ver conduce {{ $conduce->numero }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($conduce->puede_entregarse)
                                    @can('conduces.edit')
                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEntregar{{ $conduce->id }}"
                                            aria-label="Marcar como entregado">
                                        <i class="bi bi-check2"></i>
                                    </button>
                                    @endcan
                                    @endif
                                    <a href="{{ route('conduces.ticket', $conduce) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-secondary rounded-pill"
                                       aria-label="Imprimir conduce">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                    No se encontraron conduces
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($conduces->hasPages())
        <div class="card-footer bg-white">
            {{ $conduces->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modales de entrega (uno por conduce) --}}
@foreach($conduces as $conduce)
@if($conduce->puede_entregarse)
<div class="modal fade" id="modalEntregar{{ $conduce->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('conduces.entregar', $conduce) }}">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-check-circle me-2"></i>Marcar como Entregado
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Conduce: <strong>{{ $conduce->numero }}</strong></p>

                    @foreach($conduce->items as $item)
                    <div class="mb-2 row align-items-center">
                        <div class="col-7">
                            <label class="form-label small mb-0">{{ $item->nombre }}</label>
                            <small class="text-muted d-block">Enviado: {{ $item->cantidad }} {{ $item->unidad }}</small>
                        </div>
                        <div class="col-5">
                            <input type="number" step="0.01" min="0"
                                   class="form-control form-control-sm"
                                   name="items_recibidos[{{ $item->id }}]"
                                   value="{{ $item->cantidad }}"
                                   placeholder="Recibido"
                                   aria-label="Cantidad recibida de {{ $item->nombre }}">
                        </div>
                    </div>
                    @endforeach

                    <hr>

                    <div class="mb-3">
                        <label for="recibido_por{{ $conduce->id }}" class="form-label">Recibido por <span class="required-indicator">*</span></label>
                        <input type="text" id="recibido_por{{ $conduce->id }}"
                               name="recibido_por" class="form-control" required
                               placeholder="Nombre completo">
                    </div>
                    <div class="mb-3">
                        <label for="recibido_cedula{{ $conduce->id }}" class="form-label">Cédula</label>
                        <input type="text" id="recibido_cedula{{ $conduce->id }}"
                               name="recibido_cedula" class="form-control"
                               placeholder="000-0000000-0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check2 me-1"></i>Confirmar Entrega
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
@endsection

    {{-- Estadísticas --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="premium-stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="stat-label">Total</small>
                            <div class="stat-value">{{ $stats['total'] }}</div>
                        </div>
                        <i class="bi bi-files fs-1 text-secondary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="premium-stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="stat-label">En tránsito</small>
                            <div class="stat-value text-info">{{ $stats['en_transito'] }}</div>
                        </div>
                        <i class="bi bi-truck fs-1 text-info opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="premium-stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="stat-label">Entregados hoy</small>
                            <div class="stat-value text-success">{{ $stats['entregados_hoy'] }}</div>
                        </div>
                        <i class="bi bi-check-circle-fill fs-1 text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="premium-stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="stat-label">Vencidos</small>
                            <div class="stat-value text-danger">{{ $stats['vencidos'] }}</div>
                        </div>
                        <i class="bi bi-exclamation-triangle-fill fs-1 text-danger opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="premium-card mb-3">
        <div class="card-accent purple"></div>
        <div class="card-body">
            <form method="GET" action="{{ route('conduces.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="buscar" class="form-label small">Buscar</label>
                    <input type="text" name="buscar" id="buscar" class="form-control"
                           placeholder="Número, cliente, transportista..."
                           value="{{ request('buscar') }}">
                </div>
                <div class="col-md-2">
                    <label for="estado" class="form-label small">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="todos">Todos</option>
                        @foreach(\App\Models\Conduce::ESTADOS as $key => $estado)
                            <option value="{{ $key }}" {{ request('estado') == $key ? 'selected' : '' }}>
                                {{ $estado['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="cliente_id" class="form-label small">Cliente</label>
                    <select name="cliente_id" id="cliente_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($clientes as $cli)
                            <option value="{{ $cli->id }}" {{ request('cliente_id') == $cli->id ? 'selected' : '' }}>
                                {{ $cli->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="desde" class="form-label small">Desde</label>
                    <input type="date" name="desde" id="desde" class="form-control" value="{{ request('desde') }}">
                </div>
                <div class="col-md-2">
                    <label for="hasta" class="form-label small">Hasta</label>
                    <input type="date" name="hasta" id="hasta" class="form-control" value="{{ request('hasta') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100" aria-label="Filtrar">
                        <i class="bi bi-funnel"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="premium-card">
        <div class="card-accent purple"></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" role="table" aria-label="Lista de conduces">
                    <thead class="table-light">
                        <tr>
                            <th>Número</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Transportista</th>
                            <th class="text-center">Items</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($conduces as $conduce)
                            <tr>
                                <td>
                                    <a href="{{ route('conduces.show', $conduce) }}" class="fw-bold text-decoration-none">
                                        {{ $conduce->numero }}
                                    </a>
                                    @if($conduce->esta_vencido)
                                        <i class="bi bi-exclamation-circle-fill text-danger ms-1"
                                           title="Vencido" aria-label="Vencido"></i>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $conduce->fecha->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <div>{{ $conduce->cliente?->nombre ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $conduce->cliente?->rnc_cedula }}</small>
                                </td>
                                <td>
                                    @if($conduce->transportista)
                                        <div>{{ $conduce->transportista }}</div>
                                        @if($conduce->chofer)
                                            <small class="text-muted">{{ $conduce->chofer }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $conduce->total_items }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $conduce->estado_color }}">
                                        <i class="bi bi-{{ $conduce->estado_icon }} me-1" aria-hidden="true"></i>
                                        {{ $conduce->estado_label }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('conduces.show', $conduce) }}"
                                       class="btn btn-sm btn-outline-primary rounded-pill"
                                       aria-label="Ver conduce {{ $conduce->numero }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($conduce->puede_entregarse)
                                    @can('conduces.edit')
                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEntregar{{ $conduce->id }}"
                                            aria-label="Marcar como entregado">
                                        <i class="bi bi-check2"></i>
                                    </button>
                                    @endcan
                                    @endif
                                    <a href="{{ route('conduces.ticket', $conduce) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-secondary rounded-pill"
                                       aria-label="Imprimir conduce">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                    No se encontraron conduces
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($conduces->hasPages())
        <div class="card-footer bg-white">
            {{ $conduces->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modales de entrega (uno por conduce) --}}
@foreach($conduces as $conduce)
@if($conduce->puede_entregarse)
<div class="modal fade" id="modalEntregar{{ $conduce->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('conduces.entregar', $conduce) }}">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-check-circle me-2"></i>Marcar como Entregado
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Conduce: <strong>{{ $conduce->numero }}</strong></p>

                    @foreach($conduce->items as $item)
                    <div class="mb-2 row align-items-center">
                        <div class="col-7">
                            <label class="form-label small mb-0">{{ $item->nombre }}</label>
                            <small class="text-muted d-block">Enviado: {{ $item->cantidad }} {{ $item->unidad }}</small>
                        </div>
                        <div class="col-5">
                            <input type="number" step="0.01" min="0"
                                   class="form-control form-control-sm"
                                   name="items_recibidos[{{ $item->id }}]"
                                   value="{{ $item->cantidad }}"
                                   placeholder="Recibido"
                                   aria-label="Cantidad recibida de {{ $item->nombre }}">
                        </div>
                    </div>
                    @endforeach

                    <hr>

                    <div class="mb-3">
                        <label for="recibido_por{{ $conduce->id }}" class="form-label">Recibido por <span class="required-indicator">*</span></label>
                        <input type="text" id="recibido_por{{ $conduce->id }}"
                               name="recibido_por" class="form-control" required
                               placeholder="Nombre completo">
                    </div>
                    <div class="mb-3">
                        <label for="recibido_cedula{{ $conduce->id }}" class="form-label">Cédula</label>
                        <input type="text" id="recibido_cedula{{ $conduce->id }}"
                               name="recibido_cedula" class="form-control"
                               placeholder="000-0000000-0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check2 me-1"></i>Confirmar Entrega
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
@endsection
