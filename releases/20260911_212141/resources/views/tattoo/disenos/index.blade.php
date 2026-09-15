@extends('layouts.app')

@section('title', 'Catálogo de Diseños')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-images me-2"></i>Catálogo de Diseños</h2>
            <p class="text-muted mb-0">Galería de diseños y obras del estudio</p>
        </div>
        <a href="{{ route('tattoo.disenos.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Diseño
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm">{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-3">
                    <form method="GET" class="row g-2 align-items-center">
                        <div class="col-lg-4">
                            <input type="text" name="busqueda" class="form-control rounded-3" placeholder="Buscar por título..." value="{{ request('busqueda') }}">
                        </div>
                        <div class="col-lg-2">
                            <select name="estilo" class="form-select rounded-3">
                                <option value="">Todos los estilos</option>
                                @foreach($estilos as $e)
                                    <option value="{{ $e }}" {{ request('estilo') == $e ? 'selected' : '' }}>{{ $e }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <select name="artista_id" class="form-select rounded-3">
                                <option value="">Todos los artistas</option>
                                @foreach($artistas as $a)
                                    <option value="{{ $a->id }}" {{ request('artista_id') == $a->id ? 'selected' : '' }}>{{ $a->nombre_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <button class="btn btn-primary rounded-pill w-100"><i class="bi bi-funnel me-1"></i> Filtrar</button>
                        </div>
                        <div class="col-lg-2">
                            <a href="{{ route('tattoo.disenos.index') }}" class="btn btn-light rounded-pill w-100"><i class="bi bi-x-lg"></i></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @php
        $query = \App\Models\TattooDesign::with('artist');
        if ($busqueda = request('busqueda')) $query->where('titulo', 'like', "%{$busqueda}%");
        if ($estilo = request('estilo')) $query->where('estilo', $estilo);
        if ($artistaId = request('artista_id')) $query->where('artist_id', $artistaId);
        $disenos = $query->orderBy('created_at', 'desc')->get();
    @endphp

    <div class="row g-3">
        @forelse($disenos as $d)
            <div class="col-lg-3 col-md-4 col-6">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div style="position:relative;padding-top:75%;overflow:hidden;border-radius:16px 16px 0 0;">
                        @if($d->imagen_portada)
                            <img src="{{ $d->imagen_portada }}" class="position-absolute top-0 start-0 w-100 h-100" style="object-fit:cover;" alt="{{ $d->titulo }}">
                        @else
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background:linear-gradient(135deg,#2d1b69,#7c3aed);">
                                <i class="bi bi-brush" style="font-size:3rem;opacity:0.3;"></i>
                            </div>
                        @endif
                        @if($d->popular)
                            <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2 rounded-pill px-2">
                                <i class="bi bi-fire"></i> Popular
                            </span>
                        @endif
                        @if($d->estilo)
                            <span class="position-absolute bottom-0 start-0 badge bg-dark bg-opacity-75 m-2 rounded-pill">
                                {{ $d->estilo }}
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-1 text-truncate">{{ $d->titulo }}</h6>
                        @if($d->artist)
                            <small class="text-muted d-block"><i class="bi bi-person-badge me-1"></i>{{ $d->artist->nombre_completo }}</small>
                        @endif
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="fw-bold" style="color:#a855f7;">
                                RD${{ number_format($d->precio_minimo, 0) }}
                                @if($d->precio_maximo > $d->precio_minimo)
                                    - RD${{ number_format($d->precio_maximo, 0) }}
                                @endif
                            </span>
                            <div class="btn-group">
                                <a href="{{ route('tattoo.disenos.edit', $d) }}" class="btn btn-sm btn-outline-warning rounded-pill">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('tattoo.disenos.destroy', $d) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este diseño?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill ms-1"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-images display-1 text-muted opacity-25 d-block mb-3"></i>
                    <p class="text-muted">No hay diseños registrados</p>
                    <a href="{{ route('tattoo.disenos.create') }}" class="btn btn-primary rounded-pill">
                        <i class="bi bi-plus-lg me-1"></i> Primer diseño
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
