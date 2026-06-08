@extends('layouts.app')
@section('title', 'Nuevo movimiento')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1"><i class="bi bi-arrow-left-right text-primary me-2"></i>Nuevo Movimiento</h2>
                    <p class="text-muted mb-0">Registra una entrada o salida de inventario</p>
                </div>
                <a href="{{ route('almacenes.movimientos') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>

            {{-- Error general --}}
            @error('error')
                <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ $message }}
                </div>
            @enderror

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

            {{-- Form --}}
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom border-light p-4">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i>Detalles del movimiento</h5>
                </div>

                <form action="{{ route('almacenes.movimientos.store') }}" method="POST">
                    @csrf
                    <div class="card-body p-4">

                        {{-- Producto --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Producto</label>
                            <select name="producto_id" class="form-select @error('producto_id') is-invalid @enderror" required>
                                <option value="">Seleccione un producto</option>
                                @foreach($productos as $producto)
                                    <option value="{{ $producto->id }}" @selected(old('producto_id') == $producto->id)>
                                        {{ $producto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('producto_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Almacén --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Almacén</label>
                            <select name="almacen_id" class="form-select @error('almacen_id') is-invalid @enderror" required>
                                <option value="">Seleccione un almacén</option>
                                @foreach($almacenes as $a)
                                    <option value="{{ $a->id }}" @selected(old('almacen_id') == $a->id)>
                                        {{ $a->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('almacen_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tipo y Cantidad --}}
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Tipo de movimiento</label>
                                    <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                        <option value="entrada" @selected(old('tipo') == 'entrada')>
                                            <i class="bi bi-arrow-down-left"></i> Entrada
                                        </option>
                                        <option value="salida" @selected(old('tipo') == 'salida')>
                                            <i class="bi bi-arrow-up-right"></i> Salida
                                        </option>
                                    </select>
                                    @error('tipo')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Cantidad</label>
                                    <input type="number" name="cantidad" class="form-control @error('cantidad') is-invalid @enderror" min="1" value="{{ old('cantidad') }}" required placeholder="0">
                                    @error('cantidad')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Nota --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nota / Motivo</label>
                            <input type="text" name="nota" class="form-control @error('nota') is-invalid @enderror" value="{{ old('nota') }}" placeholder="Ej: Ajuste de inventario, compra a proveedor, etc.">
                            @error('nota')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="card-footer bg-light border-top border-light p-4 text-end">
                        <a href="{{ route('almacenes.movimientos') }}" class="btn btn-light rounded-pill px-4 fw-semibold me-2">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold">
                            <i class="bi bi-check-lg me-2"></i>Guardar Movimiento
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
