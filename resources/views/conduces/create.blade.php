@extends('layouts.app')
@section('title', 'Nuevo Conduce')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-12">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1"><i class="bi bi-truck text-primary me-2"></i>Nuevo Conduce</h2>
                    <p class="text-muted mb-0">Nota de entrega de productos al cliente</p>
                </div>
                <a href="{{ route('conduces.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>

            {{-- Session error --}}
            @if (session('error'))
                <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form Card --}}
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom border-light p-4">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-truck me-2 text-primary"></i>Detalles del Conduce</h5>
                </div>

                <form method="POST" action="{{ route('conduces.store') }}" id="formConduce">
                    @csrf
                    <div class="card-body p-4">
                        @include('conduces._form', ['conduce' => null, 'clientes' => $clientes, 'productos' => $productos])
                    </div>

                    <div class="card-footer bg-light border-top border-light p-4 text-end">
                        <a href="{{ route('conduces.index') }}" class="btn btn-light rounded-pill px-4 fw-semibold me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold">
                            <i class="bi bi-check-lg me-2"></i>Guardar Conduce
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    window.conduceData = { id: null, items: [], descuentos: [] };
});
</script>
<script src="{{ asset('js/conduces.js') }}"></script>
@endpush
