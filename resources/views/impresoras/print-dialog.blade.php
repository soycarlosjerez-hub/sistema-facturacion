@extends('layouts.app')

@section('title', 'Imprimir Documento')

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <h3 class="fw-bold mb-0"><i class="bi bi-printer text-primary me-2"></i>Imprimir Documento</h3>
        <p class="text-muted mb-0">{{ ucfirst($tipo) }} #{{ $id }}</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form id="printForm">
                @include('impresoras._print_dialog', ['impresoras' => $impresoras, 'tipo' => $tipo, 'id' => $id, 'impresoraPorDefecto' => $impresoraPorDefecto])

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-printer me-1"></i>Imprimir
                    </button>
                    <button type="button" class="btn btn-light rounded-pill px-4" onclick="history.back()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('printForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Imprimiendo...';

    fetch('{{ route("impresoras.print-direct") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(new FormData(form))),
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-printer me-1"></i>Imprimir';
        if (data.success) {
            window.location.href = data.redirect || '{{ url()->previous() }}';
        } else {
            alert('Error: ' + data.mensaje);
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-printer me-1"></i>Imprimir';
        alert('Error de conexión');
    });
});
</script>
@endpush
@endsection
