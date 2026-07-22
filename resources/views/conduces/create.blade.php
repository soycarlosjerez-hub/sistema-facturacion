@extends('layouts.app')
@section('title', 'Nuevo Conduce')

@push('styles')
@include('partials.premium-ui')
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
                <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index:2;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="premium-avatar-circle">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1 text-white">Nuevo Conduce</h4>
                            <small class="text-white opacity-75">
                                <i class="bi bi-plus-circle me-1"></i>
                                Nota de entrega de productos al cliente
                            </small>
                        </div>
                    </div>
                    <a href="{{ route('conduces.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
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

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#8b5cf6;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nuevo conduce</span>
        </div>
        <div>
            <a href="{{ route('conduces.index') }}" class="btn-cancel me-2">Cancelar</a>
            <button type="submit" form="formConduce" class="btn-save">
                <i class="bi bi-check-lg me-2"></i>Guardar Conduce
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
