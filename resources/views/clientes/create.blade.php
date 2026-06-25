@extends('layouts.app')

@section('title', 'Nuevo Cliente')

@push('styles')
@include('partials.premium-ui')
<style>
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
                    <i class="bi bi-person-plus"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Nuevo Cliente</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-plus-circle me-1"></i>
                        Registrar un nuevo cliente en el directorio
                    </small>
                </div>
            </div>
            <a href="{{ route('clientes.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
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

    <form id="clienteForm" action="{{ route('clientes.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="premium-card" style="animation-delay:.1s;">
                    <div class="card-accent green"></div>
                    <div class="premium-card-title">
                        <i class="bi bi-info-circle icon-green"></i>
                        Información del Cliente
                    </div>
                    <div class="premium-card-subtitle">Completa los datos del nuevo cliente</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                                @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">RNC / Cédula</label>
                                <input type="text" name="rnc_cedula" class="form-control @error('rnc_cedula') is-invalid @enderror" maxlength="11" id="rncInput" placeholder="RNC o Cédula" value="{{ old('rnc_cedula') }}">
                                <div id="rncFeedback" class="small mt-1"></div>
                                @error('rnc_cedula') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tipo Documento</label>
                                <select name="tipo_documento" class="form-select @error('tipo_documento') is-invalid @enderror" id="tipoDoc">
                                    <option value="">Auto-detectar</option>
                                    <option value="rnc" {{ old('tipo_documento')=='rnc' ? 'selected' : '' }}>RNC</option>
                                    <option value="cedula" {{ old('tipo_documento')=='cedula' ? 'selected' : '' }}>Cédula</option>
                                    <option value="pasaporte" {{ old('tipo_documento')=='pasaporte' ? 'selected' : '' }}>Pasaporte</option>
                                    <option value="ninguno" {{ old('tipo_documento')=='ninguno' ? 'selected' : '' }}>Ninguno</option>
                                </select>
                                @error('tipo_documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}">
                                @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Dirección</label>
                                <textarea name="direccion" class="form-control @error('direccion') is-invalid @enderror" rows="3">{{ old('direccion') }}</textarea>
                                @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="premium-card" style="animation-delay:.15s;">
                    <div class="card-accent green"></div>
                    <div class="premium-card-title">
                        <i class="bi bi-info-circle icon-green"></i>
                        Información
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Los campos marcados con * son obligatorios.
                        </p>
                        <hr>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-credit-card me-1"></i>
                            El RNC o Cédula se usa para facturación electrónica (e-CF).
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div style="height: 80px;"></div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-end align-items-center">
        <a href="{{ route('clientes.index') }}" class="btn-cancel me-2">Cancelar</a>
        <button type="submit" form="clienteForm" class="btn-save">
            <i class="bi bi-check-lg me-2"></i>Guardar Cliente
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
const rncInput = document.getElementById('rncInput');
const rncFeedback = document.getElementById('rncFeedback');
const tipoDoc = document.getElementById('tipoDoc');

function validarRNC() {
    const rnc = rncInput.value.replace(/[^0-9]/g, '');
    rncInput.value = rnc;
    const tipo = tipoDoc.value || 'auto';

    if (rnc.length < 9) {
        rncFeedback.innerHTML = '<span class="text-muted">Mínimo 9 dígitos</span>';
        return;
    }

    fetch('{{ route("ecf.validar-rnc") }}?rnc=' + encodeURIComponent(rnc) + '&tipo=' + tipo)
        .then(r => r.json())
        .then(data => {
            if (data.valido) {
                rncFeedback.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>' + data.mensaje + '</span>';
                if (!tipoDoc.value) {
                    const opcion = document.querySelector('#tipoDoc option[value="' + data.tipo_inferido + '"]');
                    if (opcion) { tipoDoc.value = data.tipo_inferido; }
                }
            } else {
                rncFeedback.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>' + data.mensaje + '</span>';
            }
        })
        .catch(() => {});
}

rncInput.addEventListener('input', validarRNC);
tipoDoc.addEventListener('change', validarRNC);
</script>
@endpush
