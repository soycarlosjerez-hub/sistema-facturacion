@extends('layouts.app')

@section('title', 'Gestión de Almacenes')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-buildings text-primary me-2"></i>
                Centro de Almacenamiento
            </h2>
            <p class="text-muted mb-0">Controla tus depósitos y ubicaciones de mercancía</p>
        </div>
        <div>
            <div class="d-inline-block me-3" style="width: 250px;">
                <div class="input-group input-group-merge border rounded-pill bg-white px-3 py-1">
                    <span class="input-group-text bg-transparent border-0 p-0 me-2"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="search-almacen" class="form-control border-0 shadow-none bg-transparent p-0" placeholder="Buscar almacén...">
                </div>
            </div>
            <a href="{{ route('almacenes.create') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Nuevo Almacén
            </a>
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

                    <div class="d-flex gap-2 mb-2">
                        <a href="{{ route('almacenes.edit', $a->id) }}" class="btn btn-sm btn-outline-primary rounded-pill flex-fill">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                        <form action="{{ route('almacenes.destroy', $a->id) }}" method="POST" class="flex-fill" onsubmit="return confirm('¿Borrar este almacén?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill w-100"><i class="bi bi-trash me-1"></i> Eliminar</button>
                        </form>
                    </div>

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
    document.getElementById('search-almacen').addEventListener('input', function() {
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
