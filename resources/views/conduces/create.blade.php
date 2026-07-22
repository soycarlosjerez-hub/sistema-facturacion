@extends('layouts.app')
@section('title', 'Nuevo Conduce')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="row justify-content-center">
        <div class="col-lg-12">

            {{-- Header --}}
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
                            <h4 class="ui-header-title">Nuevo Conduce</h4>
                            <div class="ui-header-meta">
                                <i class="bi bi-plus-circle me-1"></i>
                                <span>Nota de entrega de productos al cliente</span>
                            </div>
                        </div>
                    </div>
                    <div class="ui-header-actions">
                        <a href="{{ route('conduces.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                            <i class="bi bi-arrow-left me-1"></i>Volver
                        </a>
                    </div>
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
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title"><i class="bi bi-truck"></i>Detalles del Conduce</h5>
                </div>

                <form method="POST" action="{{ route('conduces.store') }}" id="formConduce">
                    @csrf
                    <div class="ui-card-body pt-0">
                        @include('conduces._form', ['conduce' => null, 'clientes' => $clientes, 'productos' => $productos])
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<div class="ui-sticky-bar">
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
