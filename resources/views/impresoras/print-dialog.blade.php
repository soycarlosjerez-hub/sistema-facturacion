@extends('layouts.app')

@section('title', 'Imprimir Documento')

@push('styles')
@include('partials.premium-ui')
<style>
    body.dark-mode .ui-header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-printer"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Imprimir Documento</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-printer me-1"></i>
                        <span>{{ ucfirst($tipo) }} #{{ $id }}</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <button type="button" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" onclick="history.back()">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </button>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-4">
            <form id="printForm">
                @include('impresoras._print_dialog', ['impresoras' => $impresoras, 'tipo' => $tipo, 'id' => $id, 'impresoraPorDefecto' => $impresoraPorDefecto])

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-4">
                        <i class="bi bi-printer me-1"></i>Imprimir
                    </button>
                    <button type="button" class="ui-btn ui-btn-ghost rounded-pill px-4" onclick="history.back()">Cancelar</button>
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