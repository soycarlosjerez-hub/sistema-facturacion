@extends('layouts.app')

@section('title', 'Empresas de Delivery')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Empresas de Delivery</h2>
                    <p class="text-white text-opacity-75 mb-0">Configuración de plataformas de delivery y sus comisiones</p>
                </div>
            </div>
            @can('delivery-companies.create')
            <a href="{{ route('delivery-companies.create') }}" class="btn btn-light rounded-pill px-4 fw-bold">
                <i class="bi bi-plus-lg me-1"></i> Nueva Empresa
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="premium-card">
        <div class="card-accent green"></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nombre</th>
                        <th>Código</th>
                        <th>Comisión</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                    <tr>
                        <td class="ps-4 fw-semibold">{{ $company->nombre }}</td>
                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $company->nombre_corto }}</span></td>
                        <td>{{ $company->comision_formateada }}</td>
                        <td>
                            @if($company->activo)
                                <span class="badge bg-success bg-opacity-10 text-success">Activo</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('delivery-companies.edit', $company) }}" class="premium-btn-edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('delivery-companies.destroy', $company) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta empresa? Las ventas asociadas no se verán afectadas.')">
                                @csrf @method('DELETE')
                                <button class="premium-btn-delete" type="submit">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-truck fs-1 d-block mb-2"></i>
                            No hay empresas de delivery registradas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-light border-0 p-3">
            {{ $companies->links() }}
        </div>
    </div>
</div>
@endsection
