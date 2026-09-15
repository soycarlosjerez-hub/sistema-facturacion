@extends('layouts.app')
@section('title', 'Nuevo Producto')

@push('styles')
@include('partials.premium-ui')

@endpush

@section('content')
<div class="ui-page" style="--accent:#6366f1;--accent-rgb:99,102,241;--accent-hover:#4f46e5;">

    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <div class="ui-header-title">Nuevo Producto</div>
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>
                        Registra un nuevo producto en el inventario
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('productos.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="ui-card" style="--delay:.1s;">
        <div class="ui-card-accent"></div>
        <form id="productForm" action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('productos.form')
        </form>
    </div>

    <div style="height: 80px;"></div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#4f46e5;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nuevo producto</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('productos.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="productForm" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-plus-circle me-1"></i>Guardar Producto
            </button>
        </div>
    </div>
</div>
@endsection
