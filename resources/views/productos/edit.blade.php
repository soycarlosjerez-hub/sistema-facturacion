@extends('layouts.app')
@section('title', 'Editar Producto')

@push('styles')
<style>
    .premium-header {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        border-radius: 1rem;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
        position: relative;
        overflow: hidden;
    }
    .premium-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .sticky-save-bar {
        position: fixed;
        bottom: 0;
        left: var(--sidebar-width, 280px);
        right: 0;
        background: #fff;
        border-top: 2px solid #4f46e5;
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
        border-top-color: #38bdf8;
    }
    @media (max-width: 991.98px) {
        .sticky-save-bar { left: 0; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="premium-header">
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white bg-opacity-20 rounded-2 p-2 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bi bi-pencil-square fs-2 text-white"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Editar Producto</h2>
                    <p class="text-white text-opacity-75 mb-0">Actualiza la información del producto en el inventario</p>
                </div>
            </div>
            <a href="{{ route('productos.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="alert border-0 rounded-4 shadow-sm mb-4" style="background: rgba(79, 70, 229, 0.05); border-left: 4px solid #4f46e5 !important;">
        <div class="d-flex align-items-center">
            <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; color: #4f46e5; background: rgba(79, 70, 229, 0.1);">
                <i class="bi bi-info-circle fs-5"></i>
            </div>
            <div>
                <span class="text-muted">Estás editando el producto:</span>
                <strong class="text-dark d-block" style="font-size: 1.1rem;">{{ $producto->nombre }}</strong>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-5" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px);">
        <form id="productForm" action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @include('productos.form')
        </form>
    </div>
</div>

<div id="stickySaveBar" class="sticky-save-bar">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle text-primary"></i>
            <span class="fw-semibold d-none d-sm-inline">Editando: {{ $producto->nombre }}</span>
        </div>
        <button type="submit" form="productForm" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-cloud-arrow-up me-1"></i>Guardar Cambios
        </button>
    </div>
</div>

<script>
    document.getElementById('btnGenerarBarcode')?.addEventListener('click', function(e) {
        e.preventDefault();
        if (document.getElementById('codigo_barras').value && !confirm('¿Reemplazar el código de barras actual?')) {
            return;
        }
        const input = document.getElementById('codigo_barras');
        let codigo = '200';
        for (let i = 0; i < 9; i++) {
            codigo += Math.floor(Math.random() * 10);
        }
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
