@extends('layouts.app')

@section('title', $tipo->nombre)

@section('content')
<div class="container-fluid py-3">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0"><i class="bi bi-cpu me-2"></i>{{ $tipo->nombre }}</h2>
            <p class="text-muted mb-0">Detalles del tipo de equipo</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-muted small">Nombre</label>
                    <p class="fw-medium">{{ $tipo->nombre }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small">Slug</label>
                    <p><code>{{ $tipo->slug }}</code></p>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small">Categoría</label>
                    <p>{{ ucfirst($tipo->categoria) }}</p>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small">Icono</label>
                    <p>@if($tipo->icono)<i class="bi {{ $tipo->icono }}"></i> {{ $tipo->icono }}@else-@endif</p>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small">Orden</label>
                    <p>{{ $tipo->orden }}</p>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small">Estado</label>
                    <p>
                        @if($tipo->activo)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </p>
                </div>
                <div class="col-12">
                    <label class="form-label text-muted small">Creado</label>
                    <p>{{ $tipo->created_at ? $tipo->created_at->format('d/m/Y h:i A') : '-' }}</p>
                </div>
                <div class="col-12">
                    <label class="form-label text-muted small">Actualizado</label>
                    <p>{{ $tipo->updated_at ? $tipo->updated_at->format('d/m/Y h:i A') : '-' }}</p>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white border-0">
            <a href="{{ route('climatizacion.tipos-equipos.edit', $tipo) }}" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>Editar</a>
            <a href="{{ route('climatizacion.tipos-equipos.index') }}" class="btn btn-outline-secondary ms-2">Volver</a>
        </div>
    </div>
</div>
@endsection
