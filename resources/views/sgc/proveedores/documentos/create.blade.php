@extends('layouts.app')

@section('title', 'Cargar Documento Proveedor')

@push('styles')
@include('partials.premium-ui')
<style>
.form-label-custom { font-size: .85rem; font-weight: 600; color: #64748b; margin-bottom: .25rem; }
.form-control-custom:focus, .form-select-custom:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 .2rem rgba(99,102,241,.2);
}
</style>
@endpush

@section('content')
<div class="ui-page">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-building-add"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Cargar Documento - {{ $proveedor->nombre }}</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.documentos-proveedor', $proveedor) }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Cargar un nuevo documento para el proveedor
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="{{ route('sgc.documentos-proveedor.store', $proveedor) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    @if($documentosSgc)
                    <div class="col-md-6">
                        <label class="form-label-custom">Documento SGC asociado <span class="text-danger">*</span></label>
                        <select name="documento_sgc_id" class="form-select form-select-custom" required>
                            <option value="">Seleccionar documento SGC...</option>
                            @foreach($documentosSgc as $doc)
                            <option value="{{ $doc->id }}" {{ old('documento_sgc_id') == $doc->id ? 'selected' : '' }}>
                                {{ $doc->codigo }} - {{ Str::limit($doc->titulo, 40) }}
                            </option>
                            @endforeach
                        </select>
                        @error('documento_sgc_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    @endif
                    <div class="col-md-6">
                        <label class="form-label-custom">Descripción del documento</label>
                        <input type="text" name="descripcionDocumento" class="form-control form-control-custom" value="{{ old('descripcionDocumento') }}">
                        @error('descripcionDocumento')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Fecha Carga <span class="text-danger">*</span></label>
                        <input type="date" name="fechaCarga" class="form-control form-control-custom" value="{{ old('fechaCarga', date('Y-m-d')) }}" required>
                        @error('fechaCarga')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Fecha Vencimiento</label>
                        <input type="date" name="fechaVencimiento" class="form-control form-control-custom" value="{{ old('fechaVencimiento') }}">
                        @error('fechaVencimiento')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Estado</label>
                        <select name="estado" class="form-select form-select-custom">
                            <option value="por_cargar" {{ old('estado')=='por_cargar' ? 'selected' : '' }}>Por Cargar</option>
                            <option value="vigente" {{ old('estado')=='vigente' ? 'selected' : '' }}>Vigente</option>
                            <option value="pendiente" {{ old('estado')=='pendiente' ? 'selected' : '' }}>Pendiente</option>
                        </select>
                        @error('estado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label-custom">Archivo <span class="text-danger">*</span></label>
                        <input type="file" name="archivo" class="form-control form-control-custom" accept=".pdf,.doc,.docx,.xls,.xlsx" required>
                        @error('archivo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Guardar
                    </button>
                    <a href="{{ route('sgc.documentos-proveedor', $proveedor) }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
