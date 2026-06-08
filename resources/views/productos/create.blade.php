@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1" style="color: #0f172a;"><i class="bi bi-box-seam text-primary me-2"></i>Nuevo Producto</h2>
                    <p class="text-muted mb-0">Registra un nuevo producto en el inventario</p>
                </div>
                <a href="{{ route('productos.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
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

            <!-- Form Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px);">
                <div class="card-header bg-white border-bottom border-light p-4">
                    <h5 class="fw-bold mb-0 text-dark">Información del Producto</h5>
                </div>

                <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    @include('productos.form')

                    <div class="card-footer bg-light border-top border-light p-4 text-end">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg fw-bold" style="transition: all 0.3s ease;">
                            <i class="bi bi-plus-circle me-2"></i>Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .input-group-lg .form-control:focus {
        border-color: #dee2e6;
        box-shadow: none;
    }
    .input-group:focus-within {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        border-color: #86b7fe;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>

<script>
    document.getElementById('btnGenerarBarcode')?.addEventListener('click', function() {
        const input = document.getElementById('codigo_barras');
        let prefix = '200';
        let codigo = prefix;
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
        input.focus();
        input.select();
    });
</script>
@endsection
