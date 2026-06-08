@extends('layouts.app')
@section('title', 'Categorías')
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-tags text-primary me-2"></i>Categorías</h2>
            <p class="text-muted mb-0">Clasifica tus productos por categorías</p>
        </div>
        <a href="{{ route('categorias.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-1"></i> Nueva Categoría
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;">
                        <th class="ps-4 py-3">Nombre</th>
                        <th>Descripción</th>
                        <th class="text-center">Productos</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categorias as $c)
                        <tr>
                            <td class="ps-4"><span class="fw-semibold">{{ $c->nombre }}</span></td>
                            <td><small class="text-muted">{{ $c->descripcion ?? '—' }}</small></td>
                            <td class="text-center">{{ $c->productos_count }}</td>
                            <td class="text-center">
                                @if($c->activa)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill">Activa</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">Inactiva</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('categorias.edit', $c) }}" class="btn btn-sm btn-outline-primary rounded-pill"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('categorias.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta categoría?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1"></i><p class="mt-2 mb-0">No hay categorías</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($categorias->hasPages())
        <div class="mt-3">{{ $categorias->links() }}</div>
    @endif
</div>
@endsection