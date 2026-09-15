@extends('layouts.app')

@section('title', 'Instalaciones')

@push('styles')
@include('partials.premium-ui')
<style>
.ui-page { --accent: #06b6d4; --accent-rgb: 6,182,212; --accent-hover: #0891b2; }
.filter-row .form-select,
.filter-row .form-control { font-size: .85rem; border-radius: .65rem; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    <div class="ui-header" style="--delay:0s;">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h1 class="ui-header-title">Instalaciones</h1>
                    <div class="ui-header-meta">
                        <span>Gestión de instalaciones de equipos de climatización</span>
                        <span class="divider">·</span>
                        <span>{{ $instalaciones->total() }} registro(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('climatizacion.instalaciones.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Instalación
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s;">
        <div style="height:4px;background:linear-gradient(90deg, #06b6d4, rgba(255,255,255,.3));"></div>
        <div style="padding:1.25rem 1.75rem 0;">
            <div class="ui-card-title" style="padding:0;margin-bottom:.15rem;">
                <i class="bi bi-list-check"></i> Listado de Instalaciones
            </div>
            <div class="ui-card-subtitle" style="padding:0;">Filtra y administra las instalaciones</div>
        </div>

        <form method="GET" class="filter-row" style="padding:1rem 1.75rem;">
            <div class="row g-2 align-items-end">
                <div class="col-lg-4">
                    <label class="form-label small text-muted fw-semibold mb-1">Buscar</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Número, cliente o dirección..." value="{{ request('search') }}">
                </div>
                <div class="col-lg-2">
                    <label class="form-label small text-muted fw-semibold mb-1">Estado</label>
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach (\App\Models\Instalacion::ESTADOS as $key => $label)
                            <option value="{{ $key }}" {{ request('estado') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label small text-muted fw-semibold mb-1">Tipo Inmueble</label>
                    <select name="tipo_inmueble" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        @foreach (\App\Models\Instalacion::TIPOS_INMUEBLE as $key => $label)
                            <option value="{{ $key }}" {{ request('tipo_inmueble') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid ui-btn-sm flex-fill" style="border-radius:.65rem;">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="{{ route('climatizacion.instalaciones.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm" style="border-radius:.65rem;">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </div>
        </form>

        <div style="overflow-x:auto;">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Dirección</th>
                        <th>Tipo Inmueble</th>
                        <th>Instalador</th>
                        <th>Programada</th>
                        <th>Estado</th>
                        <th class="text-end">Total</th>
                        <th style="width:140px;" class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($instalaciones as $inst)
                    @php
                        $badgeColor = match ($inst->estado) {
                            'pendiente' => 'neutral',
                            'programada' => 'info',
                            'en_progreso' => 'warning',
                            'completada' => 'success',
                            'cancelada' => 'danger',
                            default => 'neutral',
                        };
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ $inst->numero }}</td>
                        <td>{{ $inst->cliente?->nombre ?? '-' }}</td>
                        <td class="text-truncate" style="max-width:180px;">{{ $inst->direccion_instalacion ?? '-' }}</td>
                        <td>{{ \App\Models\Instalacion::TIPOS_INMUEBLE[$inst->tipo_inmueble] ?? $inst->tipo_inmueble }}</td>
                        <td>{{ $inst->instalador?->name ?? '-' }}</td>
                        <td>{{ $inst->programada_para ? $inst->programada_para->format('d/m/Y H:i') : '-' }}</td>
                        <td>
                            <span class="ui-badge ui-badge-{{ $badgeColor }}">
                                {{ \App\Models\Instalacion::ESTADOS[$inst->estado] ?? $inst->estado }}
                            </span>
                        </td>
                        <td class="text-end fw-semibold">{{ number_format($inst->total ?? 0, 2) }}</td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('climatizacion.instalaciones.show', $inst) }}" class="ui-action ui-action-view" title="Ver"><i class="bi bi-eye"></i></a>
                                @if (!in_array($inst->estado, ['completada', 'cancelada']))
                                    <a href="{{ route('climatizacion.instalaciones.edit', $inst) }}" class="ui-action ui-action-edit" title="Editar"><i class="bi bi-pencil"></i></a>
                                    @php
                                        $nextState = match ($inst->estado) {
                                            'pendiente' => 'programada',
                                            'programada' => 'en_progreso',
                                            default => null,
                                        };
                                    @endphp
                                    @if ($nextState)
                                        <form action="{{ route('climatizacion.instalaciones.advance', $inst) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="next_state" value="{{ $nextState }}">
                                            <button type="submit" class="ui-action" style="background:rgba(6,182,212,.1);color:#06b6d4;border-color:rgba(6,182,212,.2);" title="Avanzar a {{ \App\Models\Instalacion::ESTADOS[$nextState] }}"><i class="bi bi-forward"></i></button>
                                        </form>
                                    @endif
                                    <button type="button" class="ui-action ui-action-delete" title="Eliminar"
                                            onclick="UI._fire({title:'¿Eliminar instalación?',text:'{{ $inst->numero }}',icon:'error',color:'#dc2626',confirmText:'Sí, eliminar',form:document.getElementById('del-inst-{{ $inst->id }}')})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form id="del-inst-{{ $inst->id }}" action="{{ route('climatizacion.instalaciones.destroy', $inst) }}" method="POST" class="d-none">
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
                            <p class="fw-semibold mb-1" style="color:#64748b;">No hay instalaciones</p>
                            <span style="font-size:.85rem;">Crea la primera instalación para comenzar</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($instalaciones->hasPages())
        <div style="padding:1rem 1.75rem;border-top:1px solid #f1f5f9;">
            <div class="d-flex justify-content-end">
                {{ $instalaciones->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
