@extends('layouts.app')

@section('title', 'Gastos')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Gastos</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-receipt me-1"></i>
                        Registro de gastos operativos
                        <span class="divider">·</span>
                        <i class="bi bi-list-ul me-1"></i>
                        <span>{{ $gastos->total() }} registro(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('gastos.create')
                <a href="{{ route('gastos.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Gasto
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.05s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Total Gastos</div>
                    <div class="ui-stat-value">RD$ {{ number_format($totalGastos, 2) }}</div>
                    <div class="ui-stat-sub">{{ $gastos->total() }} registro(s)</div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="ui-stat-label me-1" style="text-transform:none;letter-spacing:0;">Categorías:</span>
                        <a href="{{ route('gastos.index') }}" class="ui-badge {{ !request('categoria') ? 'ui-badge-success' : 'ui-badge-neutral' }}">Todas</a>
                        @foreach($categorias as $key => $label)
                            <a href="{{ route('gastos.index', array_merge(request()->all(), ['categoria' => $key, 'page' => null])) }}"
                               class="ui-badge {{ request('categoria') === $key ? 'ui-badge-success' : 'ui-badge-neutral' }}">
                                {{ $label }}
                                @if(isset($totalPorCategoria[$key]))
                                    <span class="ms-1 opacity-75">(RD${{ number_format($totalPorCategoria[$key], 0) }})</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.15s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form method="GET" action="{{ route('gastos.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="ui-input" placeholder="Buscar por descripción o comprobante..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <input type="date" name="desde" class="ui-input" value="{{ request('desde') }}" placeholder="Desde">
                </div>
                <div class="col-lg-2">
                    <input type="date" name="hasta" class="ui-input" value="{{ request('hasta') }}" placeholder="Hasta">
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('gastos.index') }}" class="ui-btn ui-btn-ghost"><i class="bi bi-x-lg"></i></a>
                </div>
                <div class="col-lg-2 text-end">
                    <span class="fw-bold text-muted small">Filtrado: RD$ {{ number_format($totalGastos, 2) }}</span>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.2s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Descripción</th>
                            <th>Categoría</th>
                            <th>Proveedor</th>
                            <th>Monto</th>
                            <th>Método Pago</th>
                            <th>Registrado por</th>
                            <th>Fecha</th>
                            <th class="text-center">Comprobante</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gastos as $gasto)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-semibold">{{ $gasto->descripcion }}</span>
                                    @if($gasto->notas)
                                        <br><small class="text-muted">{{ Str::limit($gasto->notas, 60) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($gasto->categoria)
                                        <span class="ui-badge ui-badge-success">{{ $categorias[$gasto->categoria] ?? $gasto->categoria }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $gasto->proveedor?->nombre ?? '—' }}</span>
                                </td>
                                <td class="fw-bold" style="color:#059669;">RD$ {{ number_format($gasto->monto, 2) }}</td>
                                <td>
                                    @if($gasto->metodo_pago)
                                        <span class="text-muted small">{{ ucfirst($gasto->metodo_pago) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $gasto->user?->name ?? '—' }}</small></td>
                                <td><small>{{ $gasto->fecha_gasto->format('d/m/Y') }}</small></td>
                                <td class="text-center">
                                    @if($gasto->comprobante)
                                        <span class="ui-badge ui-badge-info">{{ $gasto->comprobante }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @can('gastos.edit')
                                    <a href="{{ route('gastos.edit', $gasto) }}" class="ui-action ui-action-edit" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('gastos.delete')
                                    <button type="button" class="ui-action ui-action-delete"
                                            onclick="UI.confirm.delete('{{ route('gastos.destroy', $gasto) }}', '{{ addslashes($gasto->descripcion) }}')"
                                            title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="ui-empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>No hay gastos registrados</p>
                                        @can('gastos.create')
                                        <a href="{{ route('gastos.create') }}" class="ui-btn ui-btn-solid ui-btn-sm mt-2 rounded-pill">Registrar primer gasto</a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($gastos->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $gastos->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
@endpush
