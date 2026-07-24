@extends('layouts.app')

@section('title', 'Gestión de Almacenes')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#14b8a6;--accent-rgb:20,184,166;--accent-hover:#0d9488;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Gestión de Almacenes</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i>
                        <span>{{ $almacenes->total() }} registro(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('almacenes.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Almacén
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <div class="row g-2 align-items-end">
                <div class="col-lg-6">
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="buscar-local" class="ui-input" placeholder="Buscar almacén por nombre..." autocomplete="off">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Almacenes -->
    <div class="row g-4">
        @forelse($almacenes as $a)
        <div class="col-md-6 col-lg-4">
            <div class="ui-card h-100" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-4" style="width:50px;height:50px;background:rgba(var(--accent-rgb),0.1);color:var(--accent);">
                            <i class="bi bi-building fs-4"></i>
                        </div>
                        <div class="d-flex gap-1">
                            <a href="{{ route('almacenes.edit', $a->id) }}" class="ui-action ui-action-edit" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('almacenes.destroy', $a->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="ui-action ui-action-delete" onclick="event.preventDefault();UI.confirm.delete('{{ route('almacenes.destroy', $a->id) }}', '{{ addslashes($a->nombre) }}')" title="Eliminar">
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

                    <div class="ui-stat" style="--delay:0s">
                        <div class="ui-stat-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="ui-stat-label">Productos</div>
                                <div class="ui-stat-sub"><i class="bi bi-box-seam me-1"></i>con stock</div>
                            </div>
                            <div class="ui-stat-value fs-4">{{ \App\Models\AlmacenMovimiento::where('almacen_id', $a->id)->selectRaw('COUNT(DISTINCT producto_id) as total')->value('total') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="ui-empty-state">
                <i class="bi bi-building-x"></i>
                <p>No hay almacenes configurados en el sistema.</p>
            </div>
        </div>
        @endforelse
    </div>
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
