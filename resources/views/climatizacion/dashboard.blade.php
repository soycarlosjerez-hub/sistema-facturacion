@extends('layouts.app')

@section('title', 'Climatización - Dashboard')

@section('content')
<div class="container-fluid py-3">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0"><i class="bi bi-wind me-2"></i>Climatización</h2>
            <p class="text-muted mb-0">Panel de control de mantenimiento y servicios</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-file-earmark-text text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Contratos Activos</h6>
                            <h3 class="mb-0 fw-bold">{{ \App\Models\ContratoMantenimiento::activos()->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-calendar-event text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Próximos a Vencer</h6>
                            <h3 class="mb-0 fw-bold">{{ \App\Models\ContratoMantenimiento::proximosAVencer(30)->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Órdenes Críticas</h6>
                            <h3 class="mb-0 fw-bold">{{ \App\Models\OrdenEmergencia::criticas()->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-ticket-perforated text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Tickets Abiertos</h6>
                            <h3 class="mb-0 fw-bold">{{ \App\Models\TicketGarantia::abiertos()->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <h5 class="mb-0">Accesos Rápidos</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('climatizacion.contratos.index') }}" class="text-decoration-none">
                                <div class="border rounded-3 p-3 text-center h-100 hover-shadow transition">
                                    <i class="bi bi-file-earmark-text fs-2 text-primary d-block mb-2"></i>
                                    <span class="text-muted small">Contratos</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('climatizacion.instalaciones.index') }}" class="text-decoration-none">
                                <div class="border rounded-3 p-3 text-center h-100 hover-shadow transition">
                                    <i class="bi bi-tools fs-2 text-success d-block mb-2"></i>
                                    <span class="text-muted small">Instalaciones</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('climatizacion.mantenimientos.index') }}" class="text-decoration-none">
                                <div class="border rounded-3 p-3 text-center h-100 hover-shadow transition">
                                    <i class="bi bi-wrench-adjustable fs-2 text-warning d-block mb-2"></i>
                                    <span class="text-muted small">Mantenimientos</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('climatizacion.ordenes-emergencia.index') }}" class="text-decoration-none">
                                <div class="border rounded-3 p-3 text-center h-100 hover-shadow transition">
                                    <i class="bi bi-exclamation-octagon fs-2 text-danger d-block mb-2"></i>
                                    <span class="text-muted small">Emergencias</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('climatizacion.tickets-garantia.index') }}" class="text-decoration-none">
                                <div class="border rounded-3 p-3 text-center h-100 hover-shadow transition">
                                    <i class="bi bi-ticket-perforated fs-2 text-info d-block mb-2"></i>
                                    <span class="text-muted small">Garantías</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('climatizacion.tipos-equipos.index') }}" class="text-decoration-none">
                                <div class="border rounded-3 p-3 text-center h-100 hover-shadow transition">
                                    <i class="bi bi-cpu fs-2 text-secondary d-block mb-2"></i>
                                    <span class="text-muted small">Tipos de Equipo</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    transform: translateY(-2px);
}
.transition {
    transition: all 0.2s ease-in-out;
}
</style>
@endsection
