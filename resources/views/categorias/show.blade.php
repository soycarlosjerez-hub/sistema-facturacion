@extends('layouts.app')
@section('title', $categoria->nombre)
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-tag text-primary me-2"></i>{{ $categoria->nombre }}</h2>
            <p class="text-muted mb-0">{{ $categoria->productos->count() }} producto(s)</p>
        </div>
        <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left me-1"></i> Volver</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted text-uppercase d-block">Descripción</small>
                    <p>{{ $categoria->descripcion ?? 'Sin descripción' }}</p>
                </div>
                <div class="col-md-3">
                    <small class="text-muted text-uppercase d-block">Estado</small>
                    @if($categoria->activa)
                        <span class="badge bg-success rounded-pill">Activa</span>
                    @else
                        <span class="badge bg-secondary rounded-pill">Inactiva</span>
                    @endif
                </div>
                <div class="col-md-3">
                    <small class="text-muted text-uppercase d-block">Productos</small>
                    <span class="fw-bold fs-4">{{ $categoria->productos->count() }}</span>
                </div>
            </div>

            <hr>
            <h5 class="fw-bold mb-3">Productos en esta categoría</h5>
            @if($categoria->productos->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-muted small text-uppercase">
                                <th class="ps-3">Producto</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categoria->productos as $p)
                                <tr>
                                    <td class="ps-3">{{ $p->nombre }}</td>
                                    <td class="text-end">RD$ {{ number_format($p->precio, 2) }}</td>
                                    <td class="text-end">{{ $p->stock }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">No hay productos en esta categoría.</p>
            @endif
        </div>
    </div>
</div>
@endsection