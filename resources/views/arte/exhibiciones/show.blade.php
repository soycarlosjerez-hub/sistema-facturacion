@extends('layouts.app')
@section('title', $exhibicion->nombre)
@push('styles')
@include('partials.premium-ui')
@endpush
@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-easel"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">{{ $exhibicion->nombre }}</h2>
                    <p class="text-white text-opacity-75 mb-0">{{ $exhibicion->rango_fechas }}</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('arte.exhibiciones.edit', $exhibicion) }}" class="btn btn-light rounded-pill px-4">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <a href="{{ route('arte.exhibiciones.index') }}" class="btn btn-outline-light rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="premium-card">
                <div class="card-accent purple"></div>
                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Información</h6>
                <div class="small text-muted mb-1">Ubicación</div>
                <div class="fw-medium mb-3">{{ $exhibicion->ubicacion ?? '—' }}</div>
                <div class="small text-muted mb-1">Descripción</div>
                <div class="fw-medium mb-3">{{ $exhibicion->descripcion ?? '—' }}</div>
                <div class="small text-muted mb-1">Estado</div>
                <div><span class="badge {{ $exhibicion->activa ? 'bg-success' : 'bg-secondary' }} rounded-pill">{{ $exhibicion->activa ? 'Activa' : 'Inactiva' }}</span></div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="premium-card">
                <div class="card-accent purple"></div>
                <h6 class="fw-bold mb-3"><i class="bi bi-images me-2"></i>Obras en exhibición ({{ $exhibicion->obras->count() }})</h6>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Obra</th>
                                <th>Artista</th>
                                <th>Ubicación en sala</th>
                                <th class="text-end">Precio</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exhibicion->obras as $obra)
                            <tr>
                                <td class="fw-medium">{{ $obra->titulo }}</td>
                                <td>{{ $obra->artista?->nombre ?? '—' }}</td>
                                <td>{{ $obra->pivot->ubicacion_en_sala ?? '—' }}</td>
                                <td class="text-end">RD$ {{ number_format($obra->precio_venta, 2) }}</td>
                                <td class="text-end">
                                    <form action="{{ route('arte.exhibiciones.detach-obra', [$exhibicion, $obra]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Remover esta obra de la exhibición?')">
                                        @csrf
                                        <button class="premium-btn-delete"><i class="bi bi-x-circle"></i></button>
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

            <div class="premium-card mt-3">
                <div class="card-accent green"></div>
                <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle me-2"></i>Agregar obra</h6>
                <form method="POST" action="{{ route('arte.exhibiciones.attach-obra', $exhibicion) }}" class="row g-2">
                    @csrf
                    <div class="col-md-7">
                        <select name="obra_id" class="form-select rounded-3" required>
                            <option value="">Seleccionar obra...</option>
                            @foreach($obrasDisponibles as $obra)
                                <option value="{{ $obra->id }}">{{ $obra->titulo }} — {{ $obra->artista?->nombre ?? 'Sin artista' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="ubicacion_en_sala" class="form-control rounded-3" placeholder="Ubicación en sala">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary rounded-pill w-100" type="submit"><i class="bi bi-plus"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
