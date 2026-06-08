@extends('layouts.app')
@section('title', 'Recuperar contraseña')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-sm-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-key display-3 text-warning"></i>
                        <h4 class="fw-bold mt-2">¿Olvidaste tu contraseña?</h4>
                        <p class="text-muted small">Ingresa tu correo y te enviaremos un enlace para restablecerla.</p>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success border-0 rounded-3 shadow-sm mb-4">
                            <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-envelope text-muted"></i></span>
                                <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="correo@ejemplo.com">
                            </div>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold">
                            <i class="bi bi-send me-1"></i> Enviar enlace
                        </button>

                        <div class="text-center mt-3">
                            <a href="{{ route('login') }}" class="small text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i> Volver al inicio de sesión
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
