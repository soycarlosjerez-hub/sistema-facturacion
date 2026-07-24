@extends('layouts.app')

@section('title', 'Tickets de Garantía')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
:root {
    --dt-accent: #06b6d4;
    --dt-accent-rgb: 6, 182, 212;
    --dt-accent-gradient: linear-gradient(135deg, #06b6d4, #0891b2);
}
.ui-page { --accent: #06b6d4; --accent-rgb: 6,182,212; --accent-hover: #0891b2; }

.vigencia-badge {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    padding: .3rem .65rem;
    border-radius: 9999px;
    font-size: .75rem;
    font-weight: 600;
    white-space: nowrap;
}
.vigencia-badge.si {
    background: rgba(34,197,94,.1);
    color: #16a34a;
    border: 1px solid rgba(34,197,94,.2);
}
.vigencia-badge.no {
    background: rgba(239,68,68,.1);
    color: #dc2626;
    border: 1px solid rgba(239,68,68,.2);
}
.dias-restantes {
    font-weight: 700;
    font-size: .9rem;
}
.dias-restantes.positivo { color: #16a34a; }
.dias-restantes.cero     { color: #d97706; }
.dias-restantes.negativo { color: #dc2626; }
.filter-row .form-select,
.filter-row .form-control {
    font-size: .85rem;
    border-radius: .65rem;
}

body.dark-mode .vigencia-badge.si {
    background: rgba(34,197,94,.15);
    border-color: rgba(34,197,94,.3);
    color: #4ade80;
}
body.dark-mode .vigencia-badge.no {
    background: rgba(239,68,68,.15);
    border-color: rgba(239,68,68,.3);
    color: #f87171;
}
body.dark-mode .dias-restantes.positivo { color: #4ade80; }
body.dark-mode .dias-restantes.cero     { color: #fbbf24; }
body.dark-mode .dias-restantes.negativo { color: #f87171; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    {{-- ============================================================
         HEADER
         ============================================================ --}}
    <div class="ui-header" style="--delay:0s;">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-ticket-perforated"></i>
                </div>
                <div>
                    <h1 class="ui-header-title">Tickets de Garantía</h1>
                    <div class="ui-header-meta">
                        <span>Gestión de garantías de equipos</span>
                        <span class="divider">·</span>
                        <span>{{ $tickets->total() }} registros</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('climatizacion.tickets-garantia.create') }}" class="ui-btn ui-btn-primary ui-btn-pill">
                    <i class="bi bi-plus-lg"></i> Nuevo Ticket
                </a>
            </div>
        </div>
    </div>

    {{-- ============================================================
         STATS ROW
         ============================================================ --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="ui-stat" style="--delay:.05s;">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Tickets Abiertos</div>
                    <div class="ui-stat-value">{{ $ticketsAbiertos }}</div>
                    <div class="ui-stat-sub">Pendientes de evaluar</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="ui-stat" style="--delay:.1s;">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Total Tickets</div>
                    <div class="ui-stat-value" style="color:#64748b;">{{ $tickets->total() }}</div>
                    <div class="ui-stat-sub">Registrados en el sistema</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="ui-stat" style="--delay:.15s;">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Clientes</div>
                    <div class="ui-stat-value" style="color:#8b5cf6;">{{ $clientes->count() }}</div>
                    <div class="ui-stat-sub">Con tickets de garantía</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="ui-stat" style="--delay:.2s;">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Distribución</div>
                    <div class="ui-stat-value" style="font-size:.85rem;color:#475569;display:flex;flex-wrap:wrap;gap:.25rem;">
                        @php
                            $allTickets = $tickets->getCollection();
                            $counts = [
                                'abierto' => $allTickets->where('estado','abierto')->count(),
                                'aprobado' => $allTickets->where('estado','aprobado')->count(),
                                'rechazado' => $allTickets->where('estado','rechazado')->count(),
                                'cerrado' => $allTickets->where('estado','cerrado')->count(),
                            ];
                        @endphp
                        <span class="ui-badge ui-badge-primary">{{ $counts['abierto'] }} abiertos</span>
                        <span class="ui-badge ui-badge-success">{{ $counts['aprobado'] }} aprob.</span>
                        <span class="ui-badge ui-badge-danger">{{ $counts['rechazado'] }} rechaz.</span>
                    </div>
                    <div class="ui-stat-sub">{{ $counts['cerrado'] }} cerrados</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         CARDS — Tabla con filtros
         ============================================================ --}}
    <div class="ui-card" style="--delay:.25s;">
        <div style="height:4px;background:linear-gradient(90deg, #06b6d4, rgba(255,255,255,.3));"></div>
        <div style="padding:1.25rem 1.75rem 0;">
            <div class="ui-card-title" style="padding:0;margin-bottom:.15rem;">
                <i class="bi bi-list-check"></i> Listado de Tickets
            </div>
            <div class="ui-card-subtitle" style="padding:0;">Filtra y administra los tickets de garantía</div>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="filter-row" style="padding:1rem 1.75rem;">
            <div class="row g-2 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small text-muted fw-semibold mb-1">Estado</label>
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Todos los estados</option>
                        @foreach(\App\Models\TicketGarantia::ESTADOS as $val => $label)
                            <option value="{{ $val }}" {{ request('estado') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small text-muted fw-semibold mb-1">Tipo Garantía</label>
                    <select name="tipo_garantia" class="form-select form-select-sm">
                        <option value="">Todos los tipos</option>
                        @foreach(\App\Models\TicketGarantia::TIPOS as $val => $label)
                            <option value="{{ $val }}" {{ request('tipo_garantia') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-4 col-md-8">
                    <label class="form-label small text-muted fw-semibold mb-1">Buscar</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Código, cliente, problema…" value="{{ request('search') }}">
                </div>
                <div class="col-lg-2 col-md-4 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid ui-btn-sm flex-fill" style="border-radius:.65rem;">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="{{ route('climatizacion.tickets-garantia.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm" style="border-radius:.65rem;">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </div>
        </form>

        {{-- Tabla --}}
        <div style="overflow-x:auto;">
            <table class="ui-table dt-table" id="garantias-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Cliente</th>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Vencimiento</th>
                        <th>Días Rest.</th>
                        <th>Vigente</th>
                        <th>Estado</th>
                        <th style="width:140px;" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $t)
                    @php
                        $vigente = $t->estaVigente();
                        $diasRest = $t->diasRestantes();
                        $diasClass = $diasRest > 30 ? 'positivo' : ($diasRest > 0 ? 'cero' : 'negativo');
                        $estadoColor = match ($t->estado) {
                            'abierto'   => 'primary',
                            'evaluando' => 'warning',
                            'aprobado'  => 'success',
                            'rechazado' => 'danger',
                            'cerrado'   => 'neutral',
                            default     => 'neutral',
                        };
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ $t->codigo }}</td>
                        <td>{{ $t->cliente?->nombre ?? '-' }}</td>
                        <td>{{ $t->producto?->nombre ?? '-' }}</td>
                        <td>
                            <span class="ui-badge ui-badge-primary">
                                {{ \App\Models\TicketGarantia::TIPOS[$t->tipo_garantia] ?? $t->tipo_garantia }}
                            </span>
                        </td>
                        <td>{{ $t->fecha_vencimiento_garantia?->format('d/m/Y') ?? '-' }}</td>
                        <td>
                            <span class="dias-restantes {{ $diasClass }}">{{ $diasRest }} día{{ $diasRest !== 1 ? 's' : '' }}</span>
                        </td>
                        <td>
                            <span class="vigencia-badge {{ $vigente ? 'si' : 'no' }}">
                                <i class="bi bi-{{ $vigente ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
                                {{ $vigente ? 'Vigente' : 'Vencida' }}
                            </span>
                        </td>
                        <td>
                            <span class="ui-badge ui-badge-{{ $estadoColor }}">
                                {{ \App\Models\TicketGarantia::ESTADOS[$t->estado] ?? $t->estado }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('climatizacion.tickets-garantia.show', $t) }}"
                                   class="ui-action ui-action-view" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($t->estado === 'abierto')
                                    <button type="button" class="ui-action"
                                            style="background:rgba(34,197,94,.1);color:#16a34a;border-color:rgba(34,197,94,.2);"
                                            title="Evaluar/Aprobar"
                                            onclick="UI._fire({
                                                title:'¿Evaluar y aprobar ticket?',
                                                text:'Se marcará como aprobado. Luego podrás completar la evaluación.',
                                                icon:'question',
                                                color:'#16a34a',
                                                confirmText:'Sí, aprobar',
                                                url:'{{ route('climatizacion.tickets-garantia.evaluar', $t) }}'
                                            })">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                    <button type="button" class="ui-action"
                                            style="background:rgba(239,68,68,.1);color:#dc2626;border-color:rgba(239,68,68,.2);"
                                            title="Rechazar"
                                            onclick="UI._fire({
                                                title:'¿Rechazar ticket?',
                                                text:'Se marcará como rechazado.',
                                                icon:'warning',
                                                color:'#dc2626',
                                                confirmText:'Sí, rechazar',
                                                url:'{{ route('climatizacion.tickets-garantia.rechazar', $t) }}'
                                            })">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                                @if(!in_array($t->estado, ['abierto']))
                                    <a href="{{ route('climatizacion.tickets-garantia.edit', $t) }}"
                                       class="ui-action ui-action-edit" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif
                                @if(!in_array($t->estado, ['abierto']))
                                    <button type="button" class="ui-action ui-action-delete" title="Eliminar"
                                            onclick="UI._fire({
                                                title:'¿Eliminar ticket?',
                                                text:'{{ $t->codigo }} - Esta acción no se puede deshacer.',
                                                icon:'error',
                                                color:'#dc2626',
                                                confirmText:'Sí, eliminar',
                                                form: document.getElementById('delete-form-{{ $t->id }}')
                                            })">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $t->id }}"
                                          action="{{ route('climatizacion.tickets-garantia.destroy', $t) }}"
                                          method="POST" class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="bi bi-inbox" style="font-size:2.5rem;display:block;margin-bottom:.75rem;color:#cbd5e1;"></i>
                            <p class="fw-semibold mb-1" style="color:#64748b;">No hay tickets de garantía</p>
                            <span style="font-size:.85rem;">Crea el primer ticket para comenzar</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
        <div style="padding:1rem 1.75rem;border-top:1px solid #f1f5f9;">
            <div class="d-flex justify-content-end">
                {{ $tickets->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection