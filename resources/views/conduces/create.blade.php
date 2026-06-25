@extends('layouts.app')
@section('title', 'Nuevo Conduce')

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
<div class="container-fluid py-4 premium-page">
    <div class="row justify-content-center">
        <div class="col-lg-12">

            {{-- Header --}}
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
                            <h2 class="fw-bold mb-1">Nuevo Conduce</h2>
                            <p class="mb-0 opacity-75">Nota de entrega de productos al cliente</p>
                        </div>
                    </div>
                    <a href="{{ route('conduces.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                        <i class="bi bi-arrow-left me-2"></i>Volver
                    </a>
                </div>
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
            <div class="premium-card">
                <div class="card-accent purple"></div>
                <div class="card-body">
                    <h5 class="premium-card-title"><i class="bi bi-truck icon-purple"></i>Detalles del Conduce</h5>
                </div>

                <form method="POST" action="{{ route('conduces.store') }}" id="formConduce">
                    @csrf
                    <div class="card-body pt-0">
                        @include('conduces._form', ['conduce' => null, 'clientes' => $clientes, 'productos' => $productos])
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<div class="sticky-save-bar">
    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted small d-none d-md-inline">
            <i class="bi bi-info-circle me-1"></i> Creando nuevo conduce
        </span>
        <div class="d-flex gap-2 ms-auto">
            <a href="{{ route('conduces.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
            <button type="submit" form="formConduce" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); border: none;">
                <i class="bi bi-save me-2"></i>Guardar Conduce
            </button>
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
