@extends('layouts.app')

@section('title', 'Mantenimientos')

@push('styles')
@include('partials.premium-ui')
<style>
    body.dark-mode .ui-page { --accent: #3b82f6; --accent-rgb: 59,130,246; --accent-hover: #2563eb; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">
    {{-- HEADER --}}
    <div class="ui-header">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h1 class="ui-header-title">Mantenimientos</h1>
                    <div class="ui-header-meta">
                        <span>Gestión de mantenimientos de equipos</span>
                        <span class="divider">|</span>
                        <span>{{ $mantenimientos->total() }} registros</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('climatizacion.mantenimientos.create') }}" class="ui-btn ui-btn-solid">
                    <i class="bi bi-plus-lg"></i> Nuevo Mantenimiento
                </a>
            </div>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="ui-card" style="--delay:.05s;">
        <div class="ui-card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="ui-label">Buscar</label>
                    <input type="text" name="search" class="ui-input" placeholder="Número, cliente o falla..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="ui-label">Tipo</label>
                    <select name="tipo" class="ui-select">
                        <option value="">Todos los tipos</option>
                        @foreach(\App\Models\Mantenimiento::TIPOS as $val => $label)
                            <option value="{{ $val }}" {{ request('tipo') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="ui-label">Estado</label>
                    <select name="estado" class="ui-select">
                        <option value="">Todos los estados</option>
                        @foreach(\App\Models\Mantenimiento::ESTADOS as $val => $label)
                            <option value="{{ $val }}" {{ request('estado') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid flex-fill">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="{{ route('climatizacion.mantenimientos.index') }}" class="ui-btn ui-btn-ghost flex-fill">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="ui-card" style="--delay:.1s;">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Técnico</th>
                            <th>Descripción</th>
                            <th class="text-end">Total</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mantenimientos as $mtto)
                        <tr>
                            <td class="fw-semibold">
                                <a href="{{ route('climatizacion.mantenimientos.show', $mtto) }}" class="text-decoration-none" style="color:var(--accent);">
                                    {{ $mtto->numero }}
                                </a>
                            </td>
                            <td>{{ $mtto->cliente?->nombre ?? '-' }}</td>
                            <td>
                                @php $tipoColor = $mtto->tipo === 'preventivo' ? 'info' : 'warning'; @endphp
                                <span class="ui-badge ui-badge-{{ $tipoColor }}">
                                    {{ \App\Models\Mantenimiento::TIPOS[$mtto->tipo] ?? $mtto->tipo }}
                                </span>
                            </td>
                            <td>{{ $mtto->tecnico?->name ?? '-' }}</td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width:200px;" title="{{ $mtto->descripcion_falla }}">
                                    {{ Str::limit($mtto->descripcion_falla, 40) ?: '-' }}
                                </span>
                            </td>
                            <td class="text-end fw-bold">RD$ {{ number_format($mtto->total ?? 0, 2) }}</td>
                            <td>
                                @php
                                    $estadoColor = match ($mtto->estado) {
                                        'pendiente' => 'neutral',
                                        'programada' => 'info',
                                        'en_curso' => 'warning',
                                        'completado' => 'success',
                                        'cancelado' => 'danger',
                                        default => 'neutral',
                                    };
                                @endphp
                                <span class="ui-badge ui-badge-{{ $estadoColor }}">
                                    {{ \App\Models\Mantenimiento::ESTADOS[$mtto->estado] ?? $mtto->estado }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('climatizacion.mantenimientos.show', $mtto) }}"
                                       class="ui-action ui-action-view" title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if (!in_array($mtto->estado, ['completado', 'cancelado']))
                                        <a href="{{ route('climatizacion.mantenimientos.edit', $mtto) }}"
                                           class="ui-action ui-action-edit" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @php
                                            $nextState = match ($mtto->estado) {
                                                'pendiente' => 'programada',
                                                'programada' => 'en_curso',
                                                default => null,
                                            };
                                        @endphp
                                        @if ($nextState)
                                            <form action="{{ route('climatizacion.mantenimientos.advance', $mtto) }}"
                                                  method="POST" class="d-inline">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="next_state" value="{{ $nextState }}">
                                                <button type="submit" class="ui-action ui-action-print" title="Avanzar a {{ \App\Models\Mantenimiento::ESTADOS[$nextState] }}">
                                                    <i class="bi bi-forward"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('climatizacion.mantenimientos.destroy', $mtto) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Eliminar este mantenimiento? Esta acción no se puede deshacer.');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="ui-action ui-action-delete" title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="ui-empty-state">
                                    <i class="bi bi-tools"></i>
                                    <p>No hay mantenimientos registrados</p>
                                    <span class="text-muted small">Crea el primer mantenimiento para comenzar</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($mantenimientos->hasPages())
        <div class="px-3 py-2 border-top" style="border-color:#f1f5f9;">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Mostrando {{ $mantenimientos->firstItem() }}-{{ $mantenimientos->lastItem() }} de {{ $mantenimientos->total() }}
                </small>
                <div>
                    {{ $mantenimientos->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
