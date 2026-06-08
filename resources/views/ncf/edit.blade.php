@extends('layouts.app')

@section('title', 'Editar Secuencia NCF')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('ncf.index') }}" class="btn btn-light rounded-circle me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h3 class="fw-bold mb-0">Editar Secuencia: {{ $ncf->prefijo }}</h3>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('ncf.update', $ncf) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Nombre del Comprobante</label>
                                <input type="text" name="nombre" class="form-control rounded-3" value="{{ $ncf->nombre }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Prefijo</label>
                                <input type="text" name="prefijo" class="form-control rounded-3" maxlength="3" value="{{ $ncf->prefijo }}" required onkeyup="this.value = this.value.toUpperCase()">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Desde</label>
                                <input type="number" name="desde" class="form-control rounded-3" value="{{ $ncf->desde }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Hasta</label>
                                <input type="number" name="hasta" class="form-control rounded-3" value="{{ $ncf->hasta }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Número Actual</label>
                                <input type="number" name="actual" class="form-control rounded-3" value="{{ $ncf->actual }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Fecha de Vencimiento</label>
                                <input type="date" name="fecha_vencimiento" class="form-control rounded-3" value="{{ $ncf->fecha_vencimiento }}" required>
                            </div>

                            <div class="col-12 mt-4">
                                <hr class="opacity-10">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('ncf.index') }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                                        Actualizar Secuencia
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
