@extends('layouts.app')
@section('title', 'Categorías de Mesas')
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Categorías de Mesas</h4>
        <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#categoriaModal">
            <i class="bi bi-plus-lg me-1"></i> Nueva Categoría
        </button>
    </div>

    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Color</th>
                            <th>Nombre</th>
                            <th>Icono</th>
                            <th>Orden</th>
                            <th>Mesas</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categorias as $cat)
                        <tr>
                            <td><span class="d-inline-block rounded-circle" style="width:24px;height:24px;background:{{ $cat->color }};"></span></td>
                            <td class="fw-semibold">{{ $cat->nombre }}</td>
                            <td>@if($cat->icono)<i class="bi {{ $cat->icono }}"></i>@else — @endif</td>
                            <td>{{ $cat->orden }}</td>
                            <td>{{ $cat->mesas->count() }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary rounded-pill" onclick="editarCategoria({{ $cat->id }})"><i class="bi bi-pencil"></i></button>
                                <form action="{{ route('restaurante.categorias.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta categoría? Las mesas quedarán sin categoría.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @if($categorias->isEmpty())
                        <tr><td colspan="6" class="text-center text-muted py-4">No hay categorías creadas</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal crear/editar --}}
<div class="modal fade" id="categoriaModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content rounded-4 border-0 shadow" id="categoriaForm">
            @csrf
            <input type="hidden" name="_method" value="POST" id="cat-method">
            <div class="modal-header border-0">
                <h5 class="fw-bold" id="catModalTitle">Nueva Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control rounded-3" required maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label">Color</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="color" name="color" class="form-control form-control-color rounded-3" value="#6b7280" style="width:60px;height:40px;padding:3px;">
                        <input type="text" class="form-control rounded-3" id="color-hex" value="#6b7280" maxlength="7" oninput="this.previousElementSibling.value=this.value">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Icono (clase Bootstrap Icon)</label>
                    <input type="text" name="icono" class="form-control rounded-3" placeholder="ej: bi-tree" maxlength="50">
                    <div class="form-text">Ej: <code>bi-tree</code>, <code>bi-sun</code>, <code>bi-star</code></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Orden</label>
                    <input type="number" name="orden" class="form-control rounded-3" value="0" min="0">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary rounded-pill">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
function editarCategoria(id) {
    fetch('/restaurante/categorias/' + id)
        .then(r => r.json())
        .then(cat => {
            document.getElementById('catModalTitle').textContent = 'Editar Categoría';
            document.getElementById('cat-method').value = 'PUT';
            document.getElementById('categoriaForm').action = '/restaurante/categorias/' + id;
            document.getElementById('categoriaForm').querySelector('[name=nombre]').value = cat.nombre;
            document.getElementById('categoriaForm').querySelector('[name=color]').value = cat.color;
            document.getElementById('color-hex').value = cat.color;
            document.getElementById('categoriaForm').querySelector('[name=icono]').value = cat.icono || '';
            document.getElementById('categoriaForm').querySelector('[name=orden]').value = cat.orden;
            new bootstrap.Modal(document.getElementById('categoriaModal')).show();
        });
}

document.getElementById('categoriaModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('catModalTitle').textContent = 'Nueva Categoría';
    document.getElementById('cat-method').value = 'POST';
    document.getElementById('categoriaForm').action = '{{ route("restaurante.categorias.store") }}';
    document.getElementById('categoriaForm').reset();
});
</script>
@endsection
