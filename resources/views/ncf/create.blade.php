@extends('layouts.app')

@section('title', 'Nueva Secuencia NCF')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

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

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1"><i class="bi bi-file-earmark-text text-primary me-2"></i>Nuevo NCF</h2>
                    <p class="text-muted mb-0">Registra una nueva secuencia de comprobante fiscal</p>
                </div>
                <a href="{{ route('ncf.index') }}" class="btn btn-light rounded-pill"><i class="bi bi-arrow-left me-1"></i> Volver</a>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <form action="{{ route('ncf.store') }}" method="POST">
                    @csrf
                    <div class="card-header bg-light border-bottom border-light p-4">
                        <h5 class="mb-0 fw-semibold"><i class="bi bi-file-earmark-text me-2"></i>Información del NCF</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Nombre del Comprobante</label>
                                <input type="text" name="nombre" class="form-control rounded-3" placeholder="Ej: Crédito Fiscal" required>
                                <small class="text-muted">Descripción para identificar internamente.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Prefijo (3 Letras)</label>
                                <input type="text" name="prefijo" class="form-control rounded-3" maxlength="3" placeholder="B01" required onkeyup="this.value = this.value.toUpperCase()">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Desde (Número)</label>
                                <input type="number" name="desde" class="form-control rounded-3" value="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Hasta (Límite)</label>
                                <input type="number" name="hasta" class="form-control rounded-3" placeholder="1000" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Número Actual</label>
                                <input type="number" name="actual" class="form-control rounded-3" value="0" required>
                                <small class="text-muted">Último número emitido.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Fecha de Vencimiento</label>
                                <input type="date" name="fecha_vencimiento" class="form-control rounded-3" required>
                                <small class="text-muted">Fecha límite autorizada por DGII.</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top border-light p-4 text-end">
                        <a href="{{ route('ncf.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                            Guardar Secuencia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
