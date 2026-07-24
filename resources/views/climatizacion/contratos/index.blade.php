@extends('layouts.app')
@section('title', 'Contratos de Mantenimiento')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
.ui-page { --accent: #06b6d4; --accent-rgb: 6,182,212; --accent-hover: #0891b2; --dt-accent: #06b6d4; --dt-accent-rgb: 6,182,212; --dt-accent-gradient: linear-gradient(135deg,#06b6d4,#0ea5e9); }
body.dark-mode .ui-page { --accent: #22d3ee; --accent-rgb: 34,211,238; --accent-hover: #06b6d4; }
body.dark-mode .ui-stat-value { color: #22d3ee; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    {{-- Header --}}
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Contratos de Mantenimiento</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-wind me-1"></i>Climatización
                        <span class="mx-2">·</span>
                        <i class="bi bi-calendar3 me-1"></i>{{ now()->format('d/m/Y') }}
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('climatizacion.contratos.create') }}" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg"></i> Nuevo Contrato
                </a>
            </div>
        </div>
    </div>

    {{-- Stats + Filtros --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="ui-stat" style="--delay:.05s">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Próximos a Vencer</div>
                    <div class="ui-stat-value">{{ $proximosVencer }}</div>
                    <div class="ui-stat-sub">Contratos activos por vencer en 30 días</div>
                </div>
            </div>
        </div>
        <div class="col-xl-9 col-lg-8 col-md-6">
            <div class="ui-card" style="--delay:.1s">
                <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
                <div class="ui-card-body">
                    <form method="GET" action="{{ route('climatizacion.contratos.index') }}" class="row g-2 align-items-end">
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <label class="ui-label small">Estado</label>
                            <select name="estado" class="ui-select form-select form-select-sm">
                                <option value="">Todos</option>
                                @foreach(\App\Models\ContratoMantenimiento::ESTADOS as $val => $label)
                                    <option value="{{ $val }}" {{ request('estado') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <label class="ui-label small">Periodicidad</label>
                            <select name="periodicidad" class="ui-select form-select form-select-sm">
                                <option value="">Todas</option>
                                @foreach(\App\Models\ContratoMantenimiento::PERIODICIDADES as $val => $label)
                                    <option value="{{ $val }}" {{ request('periodicidad') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-8">
                            <label class="ui-label small">Buscar</label>
                            <input type="search" name="search" class="ui-input form-control form-control-sm" placeholder="Código o cliente..." value="{{ request('search') }}">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-4 d-flex gap-1">
                            <button type="submit" class="ui-btn ui-btn-solid ui-btn-sm flex-fill">
                                <i class="bi bi-funnel"></i> Filtrar
                            </button>
                            @if(request()->anyFilled(['estado','periodicidad','search']))
                                <a href="{{ route('climatizacion.contratos.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla Server-side --}}
    <div class="ui-card" style="--delay:.15s">
        <div class="card-accent" style="height:4px;background:linear-gradient(90deg,var(--accent,#06b6d4),rgba(255,255,255,.3));"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table dt-table mb-0" id="contratos-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Cliente</th>
                            <th>Periodicidad</th>
                            <th>Vigencia Desde</th>
                            <th>Vigencia Hasta</th>
                            <th>Valor Mensual</th>
                            <th>Visitas</th>
                            <th>Estado</th>
                            <th style="width:140px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contratos as $c)
                            @php
                                $proximosVencerItem = $c->estaActivo() && $c->vigencia_hasta <= now()->addDays(30);
                                $badgeColor = match($c->estado) {
                                    'borrador' => 'neutral',
                                    'activo' => 'success',
                                    'vencido' => 'danger',
                                    'cancelado' => 'neutral',
                                    default => 'neutral',
                                };
                                if ($proximosVencerItem) $badgeColor = 'warning';
                            @endphp
                            <tr>
                                <td><strong>{{ $c->codigo }}</strong></td>
                                <td>{{ $c->cliente?->nombre ?? '-' }}</td>
                                <td>{{ \App\Models\ContratoMantenimiento::PERIODICIDADES[$c->tipo_periodicidad] ?? $c->tipo_periodicidad }}</td>
                                <td>{{ $c->vigencia_desde?->format('d/m/Y') ?? '-' }}</td>
                                <td>
                                    {{ $c->vigencia_hasta?->format('d/m/Y') ?? '-' }}
                                    @if($proximosVencerItem)
                                        <i class="bi bi-exclamation-triangle-fill text-warning ms-1" title="Próximo a vencer"></i>
                                    @endif
                                </td>
                                <td class="fw-semibold">RD$ {{ number_format($c->valor_mensual ?? 0, 2) }}</td>
                                <td>
                                    @if($c->incluye_visitas)
                                        <span class="ui-badge ui-badge-primary">
                                            {{ $c->visitas_realizadas ?? 0 }}/{{ $c->num_visitas_anuales ?? 0 }}
                                        </span>
                                    @else
                                        <span class="ui-badge ui-badge-neutral">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($proximosVencerItem)
                                        <span class="ui-badge ui-badge-warning">
                                            <i class="bi bi-exclamation-circle"></i>
                                            {{ \App\Models\ContratoMantenimiento::ESTADOS[$c->estado] ?? $c->estado }}
                                        </span>
                                    @else
                                        <span class="ui-badge ui-badge-{{ $badgeColor }}">
                                            {{ \App\Models\ContratoMantenimiento::ESTADOS[$c->estado] ?? $c->estado }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('climatizacion.contratos.show', $c) }}" class="ui-action ui-action-view" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($c->estado !== 'cancelado')
                                            <a href="{{ route('climatizacion.contratos.edit', $c) }}" class="ui-action ui-action-edit" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                        @if($c->estado === 'borrador')
                                            <form action="{{ route('climatizacion.contratos.activar', $c) }}" method="POST" class="d-inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="ui-action" style="background:rgba(34,197,94,.1);color:#16a34a;border-color:rgba(34,197,94,.2);" title="Activar"
                                                        onclick="return confirm('¿Activar este contrato?')">
                                                    <i class="bi bi-play-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if(in_array($c->estado, ['activo','borrador']))
                                            <form action="{{ route('climatizacion.contratos.cancelar', $c) }}" method="POST" class="d-inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="ui-action ui-action-delete" title="Cancelar"
                                                        onclick="return confirm('¿Cancelar este contrato?')">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if(!in_array($c->estado, ['activo','cancelado']))
                                            <form action="{{ route('climatizacion.contratos.destroy', $c) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="ui-action ui-action-delete" title="Eliminar"
                                                        onclick="return confirm('¿Eliminar este contrato?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    No hay contratos registrados.
                                    <a href="{{ route('climatizacion.contratos.create') }}" class="d-block mt-1">Crear el primer contrato</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Paginación --}}
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small">
            Mostrando {{ $contratos->firstItem() ?? 0 }} - {{ $contratos->lastItem() ?? 0 }} de {{ $contratos->total() }} contratos
        </div>
        <div>
            {{ $contratos->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Badge color mapping for responsive child rows
    window.renderEstadoContrato = function(estado, proximo) {
        const labels = @json(\App\Models\ContratoMantenimiento::ESTADOS);
        const label = labels[estado] || estado;
        if (proximo) {
            return '<span class="ui-badge ui-badge-warning"><i class="bi bi-exclamation-circle"></i> ' + label + '</span>';
        }
        const map = { borrador: 'neutral', activo: 'success', vencido: 'danger', cancelado: 'neutral' };
        const cls = map[estado] || 'neutral';
        return '<span class="ui-badge ui-badge-' + cls + '">' + label + '</span>';
    };
});
</script>
@endpush
@endsection
