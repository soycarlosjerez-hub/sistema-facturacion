@extends('layouts.app')
@section('title', 'Nuevo Producto')

@push('styles')
@include('partials.premium-ui')
<style>
/* Productos form-specific styles */
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Nuevo Producto</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-plus-circle me-1"></i>
                        Registra un nuevo producto en el inventario
                    </small>
                </div>
            </div>
            <a href="{{ route('productos.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
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

    <div class="premium-card" style="animation-delay:.1s;">
        <div class="card-accent blue"></div>
        <form id="productForm" action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('productos.form')
        </form>
    </div>

    <div style="height: 80px;"></div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#4f46e5;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nuevo producto</span>
        </div>
        <button type="submit" form="productForm" class="btn-save">
            <i class="bi bi-plus-circle me-1"></i>Guardar Producto
        </button>
    </div>
</div>

<script>
    document.getElementById('btnGenerarBarcode')?.addEventListener('click', function(e) {
        e.preventDefault();
        const input = document.getElementById('codigo_barras');
        if (!input) return;
        let codigo = '200';
        for (let i = 0; i < 9; i++) { codigo += Math.floor(Math.random() * 10); }
        let suma = 0;
        for (let i = 0; i < 12; i++) {
            const digito = parseInt(codigo.charAt(i));
            suma += (i % 2 === 0) ? digito : digito * 3;
        }
        const checkDigit = (10 - (suma % 10)) % 10;
        codigo += checkDigit;
        input.value = codigo;
    });
</script>
@endsection
