@extends('layouts.app')

@section('title', 'Editar Almacén')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">
                <i class="bi bi-building"></i> Editar almacén
            </h3>
            <small class="text-muted">Actualizar información del almacén</small>
        </div>

        <a href="{{ route('almacenes.index') }}"
            class="btn btn-outline-secondary btn-lg">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <!-- Alerts -->
    @if($errors->any())
    <div class="alert alert-danger rounded-4">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>
                <i class="bi bi-exclamation-triangle"></i>
                {{ $error }}
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Form -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <form action="{{ route('almacenes.update', $almacen) }}"
                method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nombre" class="form-label fw-semibold">
                        Nombre del almacén
                    </label>
                    <input type="text"
                        name="nombre"
                        id="nombre"
                        class="form-control"
                        placeholder="Ej: Almacén Principal"
                        value="{{ old('nombre', $almacen->nombre) }}"
                        autofocus
                        required>
                </div>

                <div class="mb-4">
                    <label for="ubicacion" class="form-label fw-semibold">
                        Ubicación
                    </label>
                    <input type="text"
                        name="ubicacion"
                        id="ubicacion"
                        class="form-control"
                        placeholder="Ej: Santo Domingo"
                        value="{{ old('ubicacion', $almacen->ubicacion) }}">
                </div>

                @if(isset($sucursales) && $sucursales->count())
                <div class="mb-4">
                    <label for="sucursal_id" class="form-label fw-semibold">Sucursal</label>
                    <select name="sucursal_id" id="sucursal_id" class="form-select">
                        <option value="">Sin asignar</option>
                        @foreach($sucursales as $s)
                            <option value="{{ $s->id }}" {{ old('sucursal_id', $almacen->sucursal_id) == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Guardar cambios
                    </button>

                    <a href="{{ route('almacenes.index') }}"
                        class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection