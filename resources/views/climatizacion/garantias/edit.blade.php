@extends('layouts.app')

@section('title', 'Editar ' . $ticket->codigo)

@push('styles')
@include('partials.premium-ui')
<style>
.ui-page { --accent: #06b6d4; --accent-rgb: 6,182,212; --accent-hover: #0891b2; }

body.dark-mode .ui-card-title { color: #f1f5f9; }
body.dark-mode .ui-card-subtitle { color: #94a3b8; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    {{-- ============================================================
         HEADER
         ============================================================ --}}
    <div class="ui-header" style="--delay:0s;">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-ticket-perforated"></i>
                </div>
                <div>
                    <h1 class="ui-header-title">Editar: {{ $ticket->codigo }}</h1>
                    <div class="ui-header-meta">
                        <span>Modificar ticket de garantía</span>
                        <span class="divider">·</span>
                        <a href="{{ route('climatizacion.garantias.show', $ticket) }}" style="color:rgba(255,255,255,.8);text-decoration:none;">
                            <i class="bi bi-eye"></i> Ver detalle
                        </a>
                        <span class="divider">·</span>
                        <a href="{{ route('climatizacion.garantias.index') }}" style="color:rgba(255,255,255,.8);text-decoration:none;">
                            <i class="bi bi-arrow-left"></i> Volver al listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         FORM CARD
         ============================================================ --}}
    <div class="ui-card" style="--delay:.1s;max-width:900px;margin:0 auto;">
        <div style="height:4px;background:linear-gradient(90deg, #06b6d4, rgba(255,255,255,.3));"></div>
        <div style="padding:1.25rem 1.75rem 0;">
            <div class="ui-card-title" style="padding:0;margin-bottom:.15rem;">
                <i class="bi bi-pencil-square"></i> Editar Ticket #{{ $ticket->codigo }}
            </div>
            <div class="ui-card-subtitle" style="padding:0;">Modifica los campos necesarios y guarda los cambios</div>
        </div>

        <div class="ui-card-body">
            <form action="{{ route('climatizacion.garantias.update', $ticket) }}" method="POST">
                @csrf @method('PUT')

                {{-- Cliente y Producto --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="ui-label" for="cliente_id">Cliente <span class="text-danger">*</span></label>
                        <select name="cliente_id" id="cliente_id"
                                class="ui-select @error('cliente_id') is-invalid @enderror" required>
                            <option value="">Seleccionar cliente</option>
                            @foreach($clientes as $c)
                                <option value="{{ $c->id }}" {{ old('cliente_id', $ticket->cliente_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->nombre }} {{ $c->identificacion ? ' - '.$c->identificacion : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('cliente_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label" for="producto_id">Producto</label>
                        <select name="producto_id" id="producto_id"
                                class="ui-select @error('producto_id') is-invalid @enderror">
                            <option value="">Sin producto específico</option>
                            @foreach($productos as $p)
                                <option value="{{ $p->id }}" {{ old('producto_id', $ticket->producto_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->nombre }} {{ $p->codigo ? '('.$p->codigo.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('producto_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Tipo Garantía e Instalación --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="ui-label" for="tipo_garantia">Tipo de Garantía <span class="text-danger">*</span></label>
                        <select name="tipo_garantia" id="tipo_garantia"
                                class="ui-select @error('tipo_garantia') is-invalid @enderror" required>
                            <option value="">Seleccionar tipo</option>
                            @foreach(\App\Models\TicketGarantia::TIPOS as $val => $label)
                                <option value="{{ $val }}" {{ old('tipo_garantia', $ticket->tipo_garantia) === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_garantia') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="estado">Estado <span class="text-danger">*</span></label>
                        <select name="estado" id="estado"
                                class="ui-select @error('estado') is-invalid @enderror" required>
                            @foreach(\App\Models\TicketGarantia::ESTADOS as $val => $label)
                                <option value="{{ $val }}" {{ old('estado', $ticket->estado) === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('estado') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="instalacion_id">Instalación</label>
                        <select name="instalacion_id" id="instalacion_id"
                                class="ui-select @error('instalacion_id') is-invalid @enderror">
                            <option value="">Sin instalación</option>
                            @foreach(\App\Models\Instalacion::where('estado', 'completada')->get() as $inst)
                                <option value="{{ $inst->id }}" {{ old('instalacion_id', $ticket->instalacion_id) == $inst->id ? 'selected' : '' }}>
                                    #{{ $inst->id }} - {{ $inst->cliente?->nombre ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        @error('instalacion_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Fechas (readonly en edición) --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="ui-label">Fecha de Compra</label>
                        <input type="date" name="fecha_compra" id="fecha_compra"
                               class="ui-input @error('fecha_compra') is-invalid @enderror"
                               value="{{ old('fecha_compra', $ticket->fecha_compra?->format('Y-m-d')) }}">
                        @error('fecha_compra') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Fecha Vencimiento Garantía</label>
                        <input type="date" name="fecha_vencimiento_garantia" id="fecha_vencimiento_garantia"
                               class="ui-input @error('fecha_vencimiento_garantia') is-invalid @enderror"
                               value="{{ old('fecha_vencimiento_garantia', $ticket->fecha_vencimiento_garantia?->format('Y-m-d')) }}">
                        @error('fecha_vencimiento_garantia') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Descripción --}}
                <div class="mb-3">
                    <label class="ui-label" for="descripcion_problema">Descripción del Problema <span class="text-danger">*</span></label>
                    <textarea name="descripcion_problema" id="descripcion_problema" rows="4"
                              class="ui-textarea @error('descripcion_problema') is-invalid @enderror"
                              placeholder="Describe detalladamente el problema reportado…">{{ old('descripcion_problema', $ticket->descripcion_problema) }}</textarea>
                    @error('descripcion_problema') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                {{-- Evaluación y Acción (solo si ya fue evaluado) --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="ui-label" for="resultado_evaluacion">Resultado de Evaluación</label>
                        <textarea name="resultado_evaluacion" id="resultado_evaluacion" rows="3"
                                  class="ui-textarea @error('resultado_evaluacion') is-invalid @enderror"
                                  placeholder="Resultado de la evaluación técnica…">{{ old('resultado_evaluacion', $ticket->resultado_evaluacion) }}</textarea>
                        @error('resultado_evaluacion') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="accion">Acción</label>
                        <select name="accion" id="accion"
                                class="ui-select @error('accion') is-invalid @enderror">
                            <option value="">Sin acción</option>
                            <option value="reparar" {{ old('accion', $ticket->accion) === 'reparar' ? 'selected' : '' }}>Reparar</option>
                            <option value="reemplazar" {{ old('accion', $ticket->accion) === 'reemplazar' ? 'selected' : '' }}>Reemplazar</option>
                            <option value="devolver" {{ old('accion', $ticket->accion) === 'devolver' ? 'selected' : '' }}>Devolver</option>
                        </select>
                        @error('accion') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Técnico Asignado + Fecha Cierre --}}
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <label class="ui-label" for="tecnico_asignado_id">Técnico Asignado</label>
                        <select name="tecnico_asignado_id" id="tecnico_asignado_id"
                                class="ui-select @error('tecnico_asignado_id') is-invalid @enderror">
                            <option value="">Sin asignar</option>
                            @foreach(\App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'tecnico'))->orWhere('id', $ticket->tecnico_asignado_id)->orderBy('name')->get() as $u)
                                <option value="{{ $u->id }}" {{ old('tecnico_asignado_id', $ticket->tecnico_asignado_id) == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('tecnico_asignado_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label" for="cerrado_en">Fecha de Cierre</label>
                        <input type="datetime-local" name="cerrado_en" id="cerrado_en"
                               class="ui-input @error('cerrado_en') is-invalid @enderror"
                               value="{{ old('cerrado_en', $ticket->cerrado_en?->format('Y-m-d\TH:i')) }}">
                        <div class="form-text text-muted small">Solo si el ticket está cerrado</div>
                        @error('cerrado_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Sticky Bar --}}
                <div class="ui-sticky-bar" style="position:sticky;bottom:0;left:0;right:0;background:rgba(255,255,255,.85);backdrop-filter:blur(20px);border-top:2px solid #06b6d4;padding:.7rem 1.5rem;z-index:1050;box-shadow:0 -4px 20px rgba(0,0,0,.08);margin:0 -1.75rem -1.5rem;border-radius:0 0 var(--radius-2xl) var(--radius-2xl);">
                    <div class="ui-sticky-bar-inner">
                        <a href="{{ route('climatizacion.garantias.show', $ticket) }}" class="ui-btn ui-btn-ghost">
                            <i class="bi bi-x-lg"></i> Cancelar
                        </a>
                        <button type="submit" class="ui-btn ui-btn-solid">
                            <i class="bi bi-check-lg"></i> Actualizar Ticket
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection