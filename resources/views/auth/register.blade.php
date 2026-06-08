@extends('layouts.app')
@section('title', 'Registrarse')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-sm-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus display-3 text-primary"></i>
                        <h4 class="fw-bold mt-2">Crear cuenta</h4>
                        <p class="text-muted small">Registra un nuevo usuario</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nombre</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-person text-muted"></i></span>
                                <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus placeholder="Tu nombre">
                            </div>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-envelope text-muted"></i></span>
                                <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="correo@ejemplo.com">
                            </div>
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-lock text-muted"></i></span>
                                <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Mín. 8 caracteres">
                            </div>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirmar contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-lock-fill text-muted"></i></span>
                                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required placeholder="Repite la contraseña">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold">
                            <i class="bi bi-person-check me-1"></i> Registrarse
                        </button>

                        <div class="text-center mt-3">
                            <a href="{{ route('login') }}" class="small text-decoration-none">¿Ya tienes cuenta? Inicia sesión</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
