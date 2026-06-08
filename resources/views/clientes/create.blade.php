@extends('layouts.app')

@section('title', 'Nuevo Cliente')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-person-plus text-primary me-2"></i>
                Nuevo Cliente
            </h2>
            <p class="text-muted mb-0">Registrar un nuevo cliente</p>
        </div>
        <div>
            <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary rounded-pill me-2">
                <i class="bi bi-x-lg me-1"></i> Cancelar
            </a>
            <button type="submit" form="form-cliente" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-save me-1"></i> Guardar
            </button>
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

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Información del Cliente
                    </h5>
                </div>
                <div class="card-body">
                    <form id="form-cliente" action="{{ route('clientes.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Nombre *</label>
                                <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">RNC / Cédula</label>
                                <input type="text" name="rnc_cedula" class="form-control" maxlength="11" id="rncInput" placeholder="RNC o Cédula" value="{{ old('rnc_cedula') }}">
                                <div id="rncFeedback" class="small mt-1"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Tipo Documento</label>
                                <select name="tipo_documento" class="form-select" id="tipoDoc">
                                    <option value="">Auto-detectar</option>
                                    <option value="rnc" {{ old('tipo_documento')=='rnc' ? 'selected' : '' }}>RNC</option>
                                    <option value="cedula" {{ old('tipo_documento')=='cedula' ? 'selected' : '' }}>Cédula</option>
                                    <option value="pasaporte" {{ old('tipo_documento')=='pasaporte' ? 'selected' : '' }}>Pasaporte</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Teléfono</label>
                                <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Dirección</label>
                                <textarea name="direccion" class="form-control" rows="3">{{ old('direccion') }}</textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-light border-top border-light p-4 text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
                        <button type="submit" form="form-cliente" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                            <i class="bi bi-save me-2"></i>Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-info-circle me-2"></i>
                        Información
                    </h5>
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
