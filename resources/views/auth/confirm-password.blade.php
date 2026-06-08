@extends('layouts.app')
@section('title', 'Confirmar contraseña')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-sm-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-exclamation display-3 text-warning"></i>
                        <h4 class="fw-bold mt-2">Área segura</h4>
                        <p class="text-muted small">Confirma tu contraseña para continuar.</p>
                    </div>

                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-lock text-muted"></i></span>
                                <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="••••••••">
                            </div>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold">
                            <i class="bi bi-check-lg me-1"></i> Confirmar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
