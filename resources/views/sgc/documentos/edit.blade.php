@extends('layouts.app')

@section('title', 'Editar Documento SGC')

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
                    <i class="bi bi-file-earmark-edit"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar {{ $documento->codigo }}</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.documentos.show', $documento) }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Editando documento SGC: {{ $documento->titulo }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="{{ route('sgc.documentos.update', $documento) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-custom">Código</label>
                        <input type="text" name="codigo" class="form-control form-control-custom" value="{{ old('codigo', $documento->codigo) }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="form-control form-control-custom" value="{{ old('titulo', $documento->titulo) }}" required>
                        @error('titulo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Descripción</label>
                        <textarea name="descripcion" class="form-control form-control-custom" rows="3">{{ old('descripcion', $documento->descripcion) }}</textarea>
                        @error('descripcion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Categoría</label>
                        <select name="categoria" class="form-select form-select-custom">
                            <option value="">Seleccionar...</option>
                            @foreach($categorias as $key => $label)
                            <option value="{{ $key }}" {{ old('categoria', $documento->categoria) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('categoria')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-custom">Formato</label>
                        <input type="text" name="formato" class="form-control form-control-custom" value="{{ old('formato', $documento->formato) }}" placeholder="PDF">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Versión</label>
                        <input type="text" name="version" class="form-control form-control-custom" value="{{ old('version', $documento->version) }}" placeholder="1.0">
                        @error('version')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Fecha Emisión</label>
                        <input type="date" name="fecha_emision" class="form-control form-control-custom" value="{{ old('fecha_emision', $documento->fecha_emision?->format('Y-m-d')) }}">
                        @error('fecha_emision')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Fecha Revisión</label>
                        <input type="date" name="fecha_revision" class="form-control form-control-custom" value="{{ old('fecha_revision', $documento->fecha_revision?->format('Y-m-d')) }}">
                        @error('fecha_revision')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Fecha Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="form-control form-control-custom" value="{{ old('fecha_vencimiento', $documento->fecha_vencimiento?->format('Y-m-d')) }}">
                        @error('fecha_vencimiento')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Estado</label>
                        <select name="estado" class="form-select form-select-custom">
                            <option value="borrador" {{ old('estado', $documento->estado) == 'borrador' ? 'selected' : '' }}>Borrador</option>
                            <option value="revision" {{ old('estado', $documento->estado) == 'revision' ? 'selected' : '' }}>En Revisión</option>
                            <option value="aprobado" {{ old('estado', $documento->estado) == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                            <option value="vigente" {{ old('estado', $documento->estado) == 'vigente' ? 'selected' : '' }}>Vigente</option>
                            <option value="obsoleto" {{ old('estado', $documento->estado) == 'obsoleto' ? 'selected' : '' }}>Obsoleto</option>
                            <option value="archivado" {{ old('estado', $documento->estado) == 'archivado' ? 'selected' : '' }}>Archivado</option>
                        </select>
                        @error('estado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Proveedor</label>
                        <select name="proveedor_id" class="form-select form-select-custom">
                            <option value="">Sin proveedor</option>
                            @foreach($proveedores as $prov)
                            <option value="{{ $prov->id }}" {{ old('proveedor_id', $documento->proveedor_id) == $prov->id ? 'selected' : '' }}>{{ $prov->nombre }}</option>
                            @endforeach
                        </select>
                        @error('proveedor_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-5">
                        <label class="form-label-custom">Reemplazar Archivo</label>
                        <input type="file" name="archivo" class="form-control form-control-custom" accept=".pdf,.doc,.docx,.xls,.xlsx">
                        @error('archivo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        @if($documento->archivo_path)
                        <div class="mt-1 small text-muted">
                            Archivo actual: {{ $documento->archivo_original_name ?? 'Archivo' }}
                            @if($documento->archivo_size_bytes)
                            ({{ number_format($documento->archivo_size_bytes / 1024, 1) }} KB)
                            @endif
                        </div>
                        @endif
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Observaciones</label>
                        <textarea name="observaciones" class="form-control form-control-custom" rows="2">{{ old('observaciones') }}</textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Actualizar
                    </button>
                    <a href="{{ route('sgc.documentos.show', $documento) }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
