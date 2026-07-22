@extends('layouts.app')

@section('title', 'Empresas de Delivery')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Empresas de Delivery</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i>
                        <span>{{ $companies->total() }} registro(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('delivery-companies.create')
                <a href="{{ route('delivery-companies.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Empresa
                </a>
                @endcan
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="table-responsive">
            <table class="ui-table table-hover align-middle mb-0">
                <thead>
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
                        <td><span class="ui-badge ui-badge-neutral">{{ $company->nombre_corto }}</span></td>
                        <td>{{ $company->comision_formateada }}</td>
                        <td>
                            @if($company->activo)
                                <span class="ui-badge ui-badge-success">Activo</span>
                            @else
                                <span class="ui-badge ui-badge-neutral">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('delivery-companies.edit', $company) }}" class="ui-action ui-action-edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('delivery-companies.destroy', $company) }}" method="POST" class="d-inline" onsubmit="return UI.confirm.delete('¿Eliminar esta empresa? Las ventas asociadas no se verán afectadas.')">
                                @csrf @method('DELETE')
                                <button class="ui-action ui-action-delete" type="submit">
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