@extends('layouts.app')

@section('title', 'Imprimir Documento')

@push('styles')
@include('partials.premium-ui')
<style>
    body.dark-mode .premium-header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-printer"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1">Imprimir Documento</h3>
                    <p class="mb-0 opacity-75">{{ ucfirst($tipo) }} #{{ $id }}</p>
                </div>
            </div>
            <button type="button" class="btn btn-light rounded-pill px-3" onclick="history.back()">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </button>
        </div>
    </div>

    <div class="premium-card">
        <div class="card-accent blue"></div>
        <div class="card-body p-4">
            <form id="printForm">
                @include('impresoras._print_dialog', ['impresoras' => $impresoras, 'tipo' => $tipo, 'id' => $id, 'impresoraPorDefecto' => $impresoraPorDefecto])

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-printer me-1"></i>Imprimir
                    </button>
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" onclick="history.back()">Cancelar</button>
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