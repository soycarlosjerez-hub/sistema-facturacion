@extends('layouts.app')

@section('title', 'Editar ' . $conduce->numero)

@push('styles')
<style>
    .sticky-save-bar {
        position: fixed;
        bottom: 0;
        left: var(--sidebar-width, 280px);
        right: 0;
        background: #fff;
        border-top: 2px solid #f59e0b;
        padding: 0.75rem 1.5rem;
        z-index: 1050;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
    }
    .sticky-save-bar .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    body.dark-mode .sticky-save-bar {
        background: #0f172a;
        border-top-color: #fbbf24;
    }
    @media (max-width: 991.98px) {
        .sticky-save-bar { left: 0; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="bi bi-pencil-square me-2 text-warning"></i>Editar Conduce
            </h4>
            <p class="text-muted small mb-0">
                {{ $conduce->numero }} · Estado:
                <span class="badge bg-{{ $conduce->estado_color }}">{{ $conduce->estado_label }}</span>
            </p>
        </div>
        <a href="{{ route('conduces.show', $conduce) }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>

    <form method="POST" action="{{ route('conduces.update', $conduce) }}" id="instanceForm">
        @csrf
        @method('PUT')
        @include('conduces._form', ['conduce' => $conduce, 'clientes' => $clientes, 'productos' => $productos])
    </form>

    <div class="sticky-save-bar">
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted small d-none d-md-inline">
                <i class="bi bi-info-circle me-1"></i> Editando conduce: {{ $conduce->numero }}
            </span>
            <div class="d-flex gap-2 ms-auto">
                <a href="{{ route('conduces.show', $conduce) }}" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
                <button type="submit" form="instanceForm" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                    <i class="bi bi-save me-2"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    window.conduceData = {
        id: {{ $conduce->id }},
        items: @json($conduce->items),
        descuentos: @json($conduce->descuentos ?? [])
    };
});
</script>
<script src="{{ asset('js/conduces.js') }}"></script>
@endpush
