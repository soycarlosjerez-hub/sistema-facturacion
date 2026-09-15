@extends('layouts.app')
@section('title', 'Obras de Arte')
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
                    <i class="bi bi-images"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-collection me-1"></i>CATÁLOGO
                    </span>
                    <h2 class="fw-bold mb-0 text-white">Obras de Arte</h2>
                    <p class="mb-0 opacity-75">Catálogo de obras, esculturas y piezas de la galería</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('arte.obras.create') }}" class="ui-btn ui-btn-primary rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Obra
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-card-body">
            <form method="GET" action="{{ route('arte.obras.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="ui-label" for="q">Buscar</label>
                    <input type="text" name="q" id="q" class="ui-input" value="{{ request('q') }}" placeholder="Título, técnica, artista...">
                </div>
                <div class="col-md-3">
                    <label class="ui-label" for="estado">Estado</label>
                    <select name="estado" id="estado" class="ui-select">
                        <option value="">Todos</option>
                        @foreach(['vendida' => 'Vendida', 'disponible' => 'Disponible', 'en_exhibicion' => 'En Exhibición', 'en_consulta' => 'En Consulta'] as $k => $v)
                            <option value="{{ $k }}" {{ request('estado') == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="ui-label" for="artista">Artista</label>
                    <select name="artista" id="artista" class="ui-select">
                        <option value="">Todos</option>
                        @foreach($artistas as $a)
                            <option value="{{ $a->id }}" {{ request('artista') == $a->id ? 'selected' : '' }}>{{ $a->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="ui-label" for="coleccion">Colección</label>
                    <select name="coleccion" id="coleccion" class="ui-select">
                        <option value="">Todas</option>
                        @foreach($colecciones as $c)
                            <option value="{{ $c->id }}" {{ request('coleccion') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="ui-btn ui-btn-solid rounded-pill w-100" type="submit" title="Filtrar"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.2s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Obra</th>
                            <th>Artista</th>
                            <th>Colección</th>
                            <th>Técnica</th>
                            <th>Estado</th>
                            <th class="text-end">Precio Venta</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($obras as $obra)
                        <tr>
                            <td class="ps-4">{{ $obra->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($obra->imagen)
                                        <img src="{{ asset('storage/' . $obra->imagen) }}" width="36" height="36" class="rounded-2 object-fit-cover" alt="{{ $obra->titulo }}">
                                    @else
                                        <div class="bg-light rounded-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;"><i class="bi bi-image text-muted"></i></div>
                                    @endif
                                    <span class="fw-semibold">{{ $obra->titulo }}</span>
                                </div>
                            </td>
                            <td>{{ $obra->artista?->nombre ?? '—' }}</td>
                            <td>{{ $obra->coleccion?->nombre ?? '—' }}</td>
                            <td>{{ $obra->tecnica ?? '—' }}</td>
                            <td><span class="badge bg-{{ $obra->estado_badge_class }} rounded-pill">{{ $obra->estado_label }}</span></td>
                            <td class="text-end fw-bold">RD$ {{ number_format($obra->precio_venta, 2) }}</td>
                            <td class="text-end text-nowrap pe-4">
                                <a href="{{ route('arte.obras.show', $obra) }}" class="ui-action ui-action-view" title="Ver"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('arte.obras.edit', $obra) }}" class="ui-action ui-action-edit" title="Editar"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('arte.obras.destroy', $obra) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar la obra "{{ addslashes($obra->titulo) }}"?')">
                                    @csrf @method('DELETE')
                                    <button class="ui-action ui-action-delete" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No hay obras registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="p-3">
            {{ $obras->links() }}
        </div>
    </div>
</div>
</div>
@endsection