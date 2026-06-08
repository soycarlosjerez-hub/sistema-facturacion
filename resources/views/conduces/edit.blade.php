@extends('layouts.app')

@section('title', 'Editar ' . $conduce->numero)

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
        <a href="{{ route('conduces.show', $conduce) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>

    <form method="POST" action="{{ route('conduces.update', $conduce) }}" id="formConduce">
        @csrf
        @method('PUT')
        @include('conduces._form', ['conduce' => $conduce, 'clientes' => $clientes, 'productos' => $productos])
    </form>
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
