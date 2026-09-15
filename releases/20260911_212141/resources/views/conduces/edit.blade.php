@extends('layouts.app')

@section('title', 'Editar ' . $conduce->numero)

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .ui-sticky-bar {
    background: #0f172a;
    border-top-color: #fbbf24;
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar Conduce</h4>
                    <div class="ui-header-meta">
                        {{ $conduce->numero }} · Estado:
                        <span class="badge bg-{{ $conduce->estado_color }}">{{ $conduce->estado_label }}</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('conduces.show', $conduce) }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('conduces.update', $conduce) }}" id="instanceForm">
        @csrf
        @method('PUT')
        @include('conduces._form', ['conduce' => $conduce, 'clientes' => $clientes, 'productos' => $productos])
    </form>

    <div class="ui-sticky-bar">
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted small d-none d-md-inline">
                <i class="bi bi-info-circle me-1"></i> Editando conduce: {{ $conduce->numero }}
            </span>
            <div class="d-flex gap-2 ms-auto">
                <a href="{{ route('conduces.show', $conduce) }}" class="ui-btn ui-btn-ghost rounded-pill px-4">Cancelar</a>
                <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid rounded-pill px-5 fw-bold shadow-sm">
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