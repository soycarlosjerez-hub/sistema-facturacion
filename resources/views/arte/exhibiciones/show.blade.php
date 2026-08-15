@extends('layouts.app')
@section('title', $exhibicion->nombre)
@push('styles')
@include('partials.premium-ui')
@endpush
@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-easel"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-eye me-1"></i>DETALLE
                    </span>
                    <h2 class="fw-bold mb-0 text-white">{{ $exhibicion->nombre }}</h2>
                    <p class="mb-0 opacity-75">{{ $exhibicion->rango_fechas }}</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('arte.exhibiciones.edit', $exhibicion) }}" class="ui-btn ui-btn-primary rounded-pill px-4">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <a href="{{ route('arte.exhibiciones.index') }}" class="ui-btn ui-btn-primary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent" style="background:#8b5cf6"></div>
                <h5 class="ui-card-title"><i class="bi bi-info-circle"></i>Información</h5>
                <div class="ui-card-body">
                    <div class="small text-muted mb-1">Ubicación</div>
                    <div class="fw-semibold mb-3">{{ $exhibicion->ubicacion ?? '—' }}</div>
                    <div class="small text-muted mb-1">Descripción</div>
                    <div class="fw-semibold mb-3">{{ $exhibicion->descripcion ?? '—' }}</div>
                    <div class="small text-muted mb-1">Estado</div>
                    <div><span class="badge {{ $exhibicion->activa ? 'bg-success' : 'bg-secondary' }} rounded-pill">{{ $exhibicion->activa ? 'Activa' : 'Inactiva' }}</span></div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="background:#8b5cf6"></div>
                <h5 class="ui-card-title"><i class="bi bi-images"></i>Obras en exhibición ({{ $exhibicion->obras->count() }})</h5>
                <div class="ui-card-body p-0">
                    <div class="table-responsive">
                        <table class="ui-table">
                            <thead>
                                <tr>
                                    <th class="ps-4">Obra</th>
                                    <th>Artista</th>
                                    <th>Ubicación en sala</th>
                                    <th class="text-end">Precio</th>
                                    <th class="text-end pe-4"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($exhibicion->obras as $obra)
                                <tr>
                                    <td class="ps-4 fw-semibold">{{ $obra->titulo }}</td>
                                    <td>{{ $obra->artista?->nombre ?? '—' }}</td>
                                    <td>{{ $obra->pivot->ubicacion_en_sala ?? '—' }}</td>
                                    <td class="text-end">RD$ {{ number_format($obra->precio_venta, 2) }}</td>
                                    <td class="text-end pe-4">
                                        <form action="{{ route('arte.exhibiciones.detach-obra', [$exhibicion, $obra]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Remover esta obra de la exhibición?')">
                                            @csrf
                                            <button class="ui-action ui-action-delete" title="Remover"><i class="bi bi-x-circle"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">No hay obras asignadas a esta exhibición.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="ui-card mt-3" style="--delay:.25s">
                <div class="ui-card-accent" style="background:#10b981"></div>
                <h5 class="ui-card-title"><i class="bi bi-plus-circle"></i>Agregar obra</h5>
                <div class="ui-card-body">
                    <form method="POST" action="{{ route('arte.exhibiciones.attach-obra', $exhibicion) }}" class="row g-2">
                        @csrf
                        <div class="col-md-7">
                            <select name="obra_id" class="ui-select" required>
                                <option value="">Seleccionar obra...</option>
                                @foreach($obrasDisponibles as $obra)
                                    <option value="{{ $obra->id }}">{{ $obra->titulo }} — {{ $obra->artista?->nombre ?? 'Sin artista' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="ubicacion_en_sala" class="ui-input" placeholder="Ubicación en sala">
                        </div>
                        <div class="col-md-2">
                            <button class="ui-btn ui-btn-solid rounded-pill w-100" type="submit"><i class="bi bi-plus"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection