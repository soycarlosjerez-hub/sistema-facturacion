@extends('layouts.app')

@section('title', $catSub->exists ? 'Editar Subcategoría' : 'Nueva Subcategoría')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">
    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-folder-symlink"></i></div>
                <div>
                    <h4 class="ui-header-title">{{ $catSub->exists ? 'Editar: ' . $catSub->nombre : 'Nueva Subcategoría' }}</h4>
                    <div class="ui-header-meta"><i class="bi bi-tags me-1"></i><span>Organización de categorías</span></div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('category-subcategories.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="ui-card">
        <div class="ui-card-accent"></div>
        <div class="card-body p-4">
            <form action="{{ $catSub->exists ? route('category-subcategories.update', $catSub) : route('category-subcategories.store') }}" method="POST">
                @csrf @if($catSub->exists) @method('PUT') @endif

                <div class="mb-3">
                    <label class="ui-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="ui-input" value="{{ old('nombre', $catSub->nombre) }}" required>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="ui-label">Categoría Padre</label>
                        <select name="category_id" class="ui-select">
                            <option value="">Seleccionar...</option>
                            @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $catSub->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Subcategoría Padre (opcional)</label>
                        <select name="parent_id" class="ui-select">
                            <option value="">Ninguna (es categoría raíz)</option>
                            @foreach($subcategorias as $sub)
                            <option value="{{ $sub->id }}" {{ old('parent_id', $catSub->parent_id) == $sub->id ? 'selected' : '' }}>{{ $sub->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <label class="ui-label">Orden</label>
                        <input type="number" name="orden" class="ui-input" value="{{ old('orden', $catSub->orden) }}" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label">Tipo de Negocio</label>
                        <select name="business_type_id" class="ui-select">
                            <option value="">Todos</option>
                            @foreach($businessTypes as $bt)
                            <option value="{{ $bt->id }}" {{ old('business_type_id', $catSub->business_type_id) == $bt->id ? 'selected' : '' }}>{{ $bt->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(139,92,246,0.05);">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="activa" value="1" id="chk-activa" {{ old('activa', $catSub->activa) ? 'checked' : '' }} role="switch" style="width:3em;height:1.5em;">
                            <label class="form-check-label fw-semibold ms-2" for="chk-activa">Categoría Activa</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <a href="{{ route('category-subcategories.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
        <button type="submit" form="" class="ui-btn ui-btn-solid rounded-pill px-5">
            <i class="bi bi-check-lg me-2"></i>{{ $catSub->exists ? 'Actualizar' : 'Crear' }}
        </button>
    </div>
</div>

@endsection
