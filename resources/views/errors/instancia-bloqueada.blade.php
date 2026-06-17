@extends('layouts.guest')
@section('title', 'Instancia Bloqueada')
@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="text-center" style="max-width: 480px;">
        <div class="mb-4">
            <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <i class="bi bi-lock-fill text-danger fs-1"></i>
            </div>
        </div>
        <h2 class="fw-bold mb-2">Instancia Bloqueada</h2>
        <p class="text-muted mb-4">
            @if(session('error'))
                {{ session('error') }}
            @else
                Esta instancia ha sido bloqueada. Comuníquese con el administrador del sistema para más información.
            @endif
        </p>
        <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-5 fw-bold">
            <i class="bi bi-box-arrow-right me-2"></i>Ir al Login
        </a>
    </div>
</div>
@endsection
