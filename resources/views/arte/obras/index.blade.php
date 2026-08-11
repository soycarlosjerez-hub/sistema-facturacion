@extends('layouts.app')
@section('title', 'Obras de Arte')
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
                    <i class="bi bi-images"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Obras de Arte</h2>
                    <p class="text-white text-opacity-75 mb-0">Catálogo de obras, esculturas y piezas de la galería</p>
                </div>
            </div>
            <a href="{{ route('arte.obras.create') }}" class="btn btn-light rounded-pill px-4 fw-bold">
                <i class="bi bi-plus-lg me-1"></i> Nueva Obra
            </a>
        </div>
    </div>

    <div class="premium-card mb-3">
        <div class="card-accent purple"></div>
        <form method="GET" action="{{ route('arte.obras.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Buscar</label>
                <input type="text" name="q" class="form-control rounded-3" value="{{ request('q') }}" placeholder="Título, técnica, artista...">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Estado</label>
                <select name="estado" class="form-select rounded-3">
                    <option value="">Todos</option>
                    @foreach(['vendida' => 'Vendida', 'disponible' => 'Disponible', 'en_exhibicion' => 'En Exhibición', 'en_consulta' => 'En Consulta'] as $k => $v)
                        <option value="{{ $k }}" {{ request('estado') == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Artista</label>
                <select name="artista" class="form-select rounded-3">
                    <option value="">Todos</option>
                    @foreach($artistas as $a)
                        <option value="{{ $a->id }}" {{ request('artista') == $a->id ? 'selected' : '' }}>{{ $a->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Colección</label>
                <select name="coleccion" class="form-select rounded-3">
                    <option value="">Todas</option>
                    @foreach($colecciones as $c)
                        <option value="{{ $c->id }}" {{ request('coleccion') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary rounded-pill w-100" type="submit"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>

    <div class="premium-card">
        <div class="card-accent purple"></div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>#</th>
                        <th>Obra</th>
                        <th>Artista</th>
                        <th>Colección</th>
                        <th>Técnica</th>
                        <th>Estado</th>
                        <th class="text-end">Precio Venta</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($obras as $obra)
                    <tr>
                        <td>{{ $obra->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($obra->imagen)
                                    <img src="{{ asset('storage/' . $obra->imagen) }}" width="36" height="36" class="rounded-2 object-fit-cover" alt="{{ $obra->titulo }}">
                                @else
                                    <div class="bg-light rounded-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;"><i class="bi bi-image text-muted"></i></div>
                                @endif
                                <span class="fw-medium">{{ $obra->titulo }}</span>
                            </div>
                        </td>
                        <td>{{ $obra->artista?->nombre ?? '—' }}</td>
                        <td>{{ $obra->coleccion?->nombre ?? '—' }}</td>
                        <td>{{ $obra->tecnica ?? '—' }}</td>
                        <td><span class="badge bg-{{ $obra->estado_badge_class }} rounded-pill">{{ $obra->estado_label }}</span></td>
                        <td class="text-end fw-bold">RD$ {{ number_format($obra->precio_venta, 2) }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('arte.obras.show', $obra) }}" class="premium-btn-edit" title="Ver"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('arte.obras.edit', $obra) }}" class="premium-btn-edit" title="Editar"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('arte.obras.destroy', $obra) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar la obra "{{ addslashes($obra->titulo) }}"?')">
                                @csrf @method('DELETE')
                                <button class="premium-btn-delete" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No hay obras registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $obras->links() }}
        </div>
    </div>
</div>
@endsection
