@extends('layouts.app')

@section('title', 'Gestión de Almacenes')

@push('styles')
<style>
    .premium-header {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 1rem; padding: 2rem; color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
        position: relative; overflow: hidden;
    }
    .premium-header::after {
        content: ''; position: absolute; top: -50%; right: -20%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .filter-card {
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    .btn-icon-hover {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%; transition: background-color 0.2s;
    }
    .btn-icon-hover:hover { background-color: rgba(0,0,0,0.05); }
    .status-badge {
        padding: 0.4em 0.8em; border-radius: 2rem;
        font-weight: 500; font-size: 0.75rem; letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="premium-header d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1 d-flex align-items-center">
                <i class="bi bi-buildings me-3 fs-1 opacity-75"></i>Gestión de Almacenes
            </h2>
            <p class="mb-0 opacity-75 fs-5">Administra los almacenes y su información</p>
        </div>
        <div>
            <a href="{{ route('almacenes.create') }}" class="btn btn-light text-primary fw-bold rounded-pill px-4 py-2 shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Nuevo Almacén
            </a>
        </div>
    </div>

    <div class="filter-card p-3 mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-lg-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="buscar-local" class="form-control border-start-0 ps-0" placeholder="Buscar almacén por nombre..." autocomplete="off">
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Almacenes -->
    <div class="row g-4">
        @forelse($almacenes as $a)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-box">
                            <div class="rounded-4 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-building fs-4"></i>
                            </div>
                        </div>
                        <div class="d-flex gap-1">
                            <a href="{{ route('almacenes.edit', $a->id) }}" class="btn btn-icon-hover text-primary border-0 bg-transparent" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('almacenes.destroy', $a->id) }}" method="POST" onsubmit="return confirm('¿Borrar este almacén?')" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-icon-hover text-danger border-0 bg-transparent" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark mb-1">{{ $a->nombre }}</h5>
                    <div class="d-flex align-items-center text-muted small mb-1">
                        <i class="bi bi-geo-alt me-1"></i> {{ $a->ubicacion ?? 'Ubicación no especificada' }}
                    </div>
                    @if($a->sucursal)
                    <div class="d-flex align-items-center text-muted small mb-3">
                        <i class="bi bi-building me-1"></i> {{ $a->sucursal->nombre }}
                    </div>
                    @endif

                <div class="p-3 bg-light rounded-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.05em;">Productos</small>
                            <span class="fw-bold">{{ \App\Models\AlmacenMovimiento::where('almacen_id', $a->id)->selectRaw('COUNT(DISTINCT producto_id) as total')->value('total') }}</span>
                        </div>
                        <div class="mt-2 small text-dark fw-bold">
                            <i class="bi bi-box-seam me-1"></i> Productos con stock
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-building-x display-1 text-muted opacity-25"></i>
            <p class="text-muted mt-3">No hay almacenes configurados en el sistema.</p>
        </div>
        @endforelse
    </div>

    <!-- Paginación -->
    @if($almacenes->hasPages())
    <div class="mt-4">
        {{ $almacenes->links() }}
    </div>
    @endif
</div>

<script>
    document.getElementById('buscar-local').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const cards = document.querySelectorAll('.col-md-6.col-lg-4');

        cards.forEach(card => {
            const name = card.querySelector('h5').innerText.toLowerCase();
            const location = card.querySelector('.text-muted').innerText.toLowerCase();
            if (name.includes(query) || location.includes(query)) {
                card.classList.remove('d-none');
            } else {
                card.classList.add('d-none');
            }
        });
    });
</script>
@endsection
