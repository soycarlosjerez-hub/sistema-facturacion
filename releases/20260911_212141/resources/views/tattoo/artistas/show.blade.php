@extends('layouts.app')

@section('title', $artista->nombre_completo)

@section('content')
<div class="container-fluid px-4">
    <a href="{{ route('tattoo.artistas.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill mb-3">
        <i class="bi bi-arrow-left me-1"></i> Volver a Artistas
    </a>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body text-center p-4">
                    @if($artista->foto_perfil)
                        <img src="{{ $artista->foto_perfil }}" class="rounded-circle mb-3" style="width:120px;height:120px;object-fit:cover;" alt="">
                    @else
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:120px;height:120px;background:rgba(168,85,247,0.15);color:#a855f7;font-size:2.5rem;font-weight:700;">
                            {{ strtoupper(substr($artista->nombre_completo, 0, 1)) }}
                        </div>
                    @endif
                    <h4 class="fw-bold">{{ $artista->nombre_completo }}</h4>
                    <p class="text-muted">{{ $artista->especialidad ?: 'Sin especialidad' }}</p>
                    <span class="badge rounded-pill px-3 {{ $artista->tipo === 'empleado' ? 'bg-primary' : 'bg-warning text-dark' }}">
                        {{ $artista->tipo === 'empleado' ? 'Empleado' : 'Externo' }}
                    </span>

                    <hr class="my-3">
                    <div class="text-start small">
                        @if($artista->experiencia_anos > 0)
                            <div class="mb-2"><i class="bi bi-clock me-2 text-muted"></i>{{ $artista->experiencia_anos }} años de experiencia</div>
                        @endif
                        @if($artista->telefono)
                            <div class="mb-2"><i class="bi bi-telephone me-2 text-muted"></i>{{ $artista->telefono }}</div>
                        @endif
                        @if($artista->whatsapp)
                            <div class="mb-2"><i class="bi bi-whatsapp me-2 text-muted"></i>{{ $artista->whatsapp }}</div>
                        @endif
                        @if($artista->instagram)
                            <div class="mb-2">
                                <i class="bi bi-instagram me-2 text-muted"></i>
                                <a href="https://instagram.com/{{ $artista->instagram }}" target="_blank">@{{ $artista->instagram }}</a>
                            </div>
                        @endif
                        <div class="mb-2"><i class="bi bi-percent me-2 text-muted"></i>Comisión: <strong>{{ number_format($artista->comision_pct, 0) }}%</strong></div>
                    </div>

                    @if($artista->biografia)
                        <hr class="my-3">
                        <p class="small text-muted">{{ $artista->biografia }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart me-2"></i>Resumen</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-4">
                            <div class="border rounded-3 p-3 text-center">
                                <div class="fw-bold fs-4" style="color:#a855f7;">{{ $artista->citas_completadas }}</div>
                                <small class="text-muted">Citas Completadas</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded-3 p-3 text-center">
                                <div class="fw-bold fs-4" style="color:#10b981;">RD${{ number_format($artista->ganancia_total, 0) }}</div>
                                <small class="text-muted">Ganancias Generadas</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded-3 p-3 text-center">
                                <div class="fw-bold fs-4" style="color:#3b82f6;">{{ $artista->designs->count() }}</div>
                                <small class="text-muted">Diseños</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Últimas Citas</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    @forelse($artista->appointments as $cita)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <span class="fw-semibold">{{ $cita->cliente->nombre }}</span>
                                <small class="text-muted d-block">{{ $cita->fecha_hora_inicio->format('d/m/Y h:i A') }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge rounded-pill
                                    {{ $cita->estado === 'completada' ? 'bg-success' : '' }}
                                    {{ $cita->estado === 'cancelada' ? 'bg-secondary' : '' }}
                                    {{ in_array($cita->estado, ['pendiente','confirmada']) ? 'bg-warning text-dark' : '' }}
                                ">{{ ucfirst(str_replace('_', ' ', $cita->estado)) }}</span>
                                <small class="d-block text-muted">RD${{ number_format($cita->total_final, 0) }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3 mb-0">Sin citas registradas</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
