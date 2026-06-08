@extends('layouts.app')
@section('title', 'Mi Perfil')
@section('content')
<div class="container-fluid px-4 py-3">
    <h4 class="fw-bold mb-4"><i class="bi bi-person-circle me-2"></i>Mi Perfil</h4>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 rounded-top-4 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Información del perfil</h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 rounded-top-4 py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-lock me-2 text-warning"></i>Cambiar contraseña</h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-header bg-white border-0 rounded-top-4 py-3">
                    <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Eliminar cuenta</h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
