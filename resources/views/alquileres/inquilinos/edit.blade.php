@extends('layouts.app')
@section('title', "Editar Inquilino")

@push('styles')
@include('partials.premium-ui')
<style>
    .form-floating-modern { position: relative; margin-bottom: 1rem; }
    .form-floating-modern .form-icon { position: absolute; top: 50%; left: 14px; transform: translateY(-50%); color: #94a3b8; z-index: 5; font-size: 1.1rem; pointer-events: none; }
    .form-floating-modern .form-control { padding-left: 42px; height: 50px; border-radius: 12px; border: 1.5px solid #e2e8f0; }
    .form-floating-modern .form-control:focus { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,.12); }
    .form-floating-modern .form-label-float { position: absolute; top: 50%; left: 42px; transform: translateY(-50%); color: #94a3b8; transition: all .2s; pointer-events: none; background: transparent; padding: 0 4px; }
    .form-floating-modern .form-control:focus + .form-label-float,
    .form-floating-modern .form-control:not(:placeholder-shown) + .form-label-float { top: -10px; left: 36px; font-size: .75rem; color: #10b981; background: #fff; }
    textarea.form-control { height: auto !important; padding-top: 14px !important; }
    textarea.form-control + .form-label-float { top: 14px !important; }
    textarea.form-control:focus + .form-label-float,
    textarea.form-control:not(:placeholder-shown) + .form-label-float { top: -10px !important; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">
    <div class="premium-header mb-4" style="background: linear-gradient(135deg, #10b981, #06b6d4);">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="d-flex align-items-center gap-3 position-relative" style="z-index:2;">
            <div class="premium-avatar-circle"><i class="bi bi-pencil-square"></i></div>
            <div><h4 class="fw-bold mb-1 text-white">{{ $inquilino->nombre }}</h4><small class="text-white opacity-75">Editando inquilino</small></div>
        </div>
    </div>

    <form action="{{ route('alquileres.inquilinos.update', $inquilino) }}" method="POST">
        @csrf @method('PUT')
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="premium-card">
                    <div class="card-accent green"></div>
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4"><h6 class="fw-bold mb-0"><i class="bi bi-person-badge text-primary me-2"></i>Datos del Inquilino</h6></div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="bi bi-person form-icon"></i>
                                    <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $inquilino->nombre) }}" placeholder=" " required>
                                    <label class="form-label-float" for="nombre">Nombre completo *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="bi bi-credit-card form-icon"></i>
                                    <input type="text" name="cedula" id="cedula" class="form-control" value="{{ old('cedula', $inquilino->cedula) }}" placeholder=" ">
                                    <label class="form-label-float" for="cedula">C�dula</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="bi bi-telephone form-icon"></i>
                                    <input type="text" name="telefono" id="telefono" class="form-control" value="{{ old('telefono', $inquilino->telefono) }}" placeholder=" ">
                                    <label class="form-label-float" for="telefono">Tel�fono</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating-modern">
                                    <i class="bi bi-envelope form-icon"></i>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $inquilino->email) }}" placeholder=" ">
                                    <label class="form-label-float" for="email">Email</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="bi bi-geo-alt form-icon"></i>
                                    <input type="text" name="direccion" id="direccion" class="form-control" value="{{ old('direccion', $inquilino->direccion) }}" placeholder=" ">
                                    <label class="form-label-float" for="direccion">Direcci�n</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating-modern">
                                    <i class="bi bi-sticky form-icon" style="top:14px;"></i>
                                    <textarea name="notas" id="notas" class="form-control" placeholder=" " rows="3">{{ old('notas', $inquilino->notas) }}</textarea>
                                    <label class="form-label-float" for="notas">Notas</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" style="background:linear-gradient(135deg,#10b981,#059669);border:0;"><i class="bi bi-save me-1"></i>Guardar Cambios</button>
            <a href="{{ route('alquileres.inquilinos.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
        </div>
    </form>
</div>
@endsection
