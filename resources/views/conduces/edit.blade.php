@extends('layouts.app')

@section('title', 'Editar ' . $conduce->numero)

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .sticky-save-bar {
    background: #0f172a;
    border-top-color: #fbbf24;
}
</style>
@endpush

@section('content')
<div class="container-fluid premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <h4 class="mb-1 fw-bold">Editar Conduce</h4>
                    <p class="mb-0 opacity-75">
                        {{ $conduce->numero }} · Estado:
                        <span class="badge bg-{{ $conduce->estado_color }}">{{ $conduce->estado_label }}</span>
                    </p>
                </div>
            </div>
            <a href="{{ route('conduces.show', $conduce) }}" class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
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
                <button type="submit" form="instanceForm" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); border: none;">
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
