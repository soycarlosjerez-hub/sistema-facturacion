<?php

namespace App\Http\Controllers;

use App\Models\TattooAppointment;
use App\Models\TattooArtist;
use App\Models\TattooDesign;
use App\Models\TattooPayment;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TattooAppointmentController extends Controller
{
    public function index()
    {
        $query = TattooAppointment::with('cliente', 'artista', 'user');

        if ($busqueda = request('busqueda')) {
            $query->whereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$busqueda}%"))
                ->orWhere('id', $busqueda);
        }
        if ($estado = request('estado')) {
            $query->where('estado', $estado);
        }
        if ($artistaId = request('artista_id')) {
            $query->where('artista_id', $artistaId);
        }
        if ($fecha = request('fecha')) {
            $query->whereDate('fecha_hora_inicio', $fecha);
        }

        $citas = $query->orderBy('fecha_hora_inicio', 'desc')->paginate(20);
        $artistas = TattooArtist::activos()->orderBy('nombre_completo')->get();
        $contadores = [
            'pendiente' => TattooAppointment::where('estado', 'pendiente')->count(),
            'confirmada' => TattooAppointment::where('estado', 'confirmada')->count(),
            'hoy' => TattooAppointment::hoy()->count(),
        ];

        return view('tattoo.citas.index', compact('citas', 'artistas', 'contadores'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $artistas = TattooArtist::activos()->orderBy('nombre_completo')->get();
        $disenos = TattooDesign::activos()->with('artist')->get();
        $lugares = [
            'brazo', 'antebrazo', 'muneca', 'mano', 'dedo',
            'hombro', 'espalda', 'pecho', 'abdomen', 'costillas',
            'pierna', 'muslo', 'rodilla', 'pantorrilla', 'tobillo',
            'pie', 'cuello', 'cabeza', 'oreja', 'rostro',
        ];
        return view('tattoo.citas.create', compact('clientes', 'artistas', 'disenos', 'lugares'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id'     => 'required|exists:clientes,id',
            'artista_id'     => 'nullable|exists:tattoo_artists,id',
            'diseno_id'      => 'nullable|exists:tattoo_designs,id',
            'fecha_hora_inicio' => 'required|date|after:now',
            'duracion_min'   => 'required|integer|min:15|max:600',
            'deposito_pct'   => 'required|numeric|min:0|max:100',
            'total_servicio' => 'required|numeric|min:0',
            'descuento_aplicado' => 'nullable|numeric|min:0',
            'notas_cliente'  => 'nullable|string|max:1000',
            'notas_internas' => 'nullable|string|max:1000',
            'lugar_tatuaje'  => 'nullable|string|max:60',
            'tamanio_approx' => 'nullable|string|max:50',
            'revision_previa' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $data['tenant_id'] = Auth::user()->business_instance_id ?? null;
            $data['user_id'] = Auth::id();
            $data['descuento_aplicado'] = $request->descuento_aplicado ?? 0;
            $data['revision_previa'] = $request->boolean('revision_previa', false);
            $data['deposito_monto'] = $data['total_servicio'] * ($data['deposito_pct'] / 100);
            $data['total_final'] = $data['total_servicio'] - $data['descuento_aplicado'];
            $data['estado'] = 'pendiente';

            if ($data['total_final'] < 0) {
                return back()->with('error', 'El total final no puede ser negativo.')
                    ->withInput();
            }

            $cita = TattooAppointment::create($data);
            DB::commit();

            return redirect()->route('tattoo.citas.index')
                ->with('success', "Cita #{$cita->id} creada correctamente.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la cita: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(TattooAppointment $cita)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $artistas = TattooArtist::activos()->orderBy('nombre_completo')->get();
        $disenos = TattooDesign::activos()->with('artist')->get();
        $lugares = [
            'brazo', 'antebrazo', 'muneca', 'mano', 'dedo',
            'hombro', 'espalda', 'pecho', 'abdomen', 'costillas',
            'pierna', 'muslo', 'rodilla', 'pantorrilla', 'tobillo',
            'pie', 'cuello', 'cabeza', 'oreja', 'rostro',
        ];
        return view('tattoo.citas.edit', compact('cita', 'clientes', 'artistas', 'disenos', 'lugares'));
    }

    public function update(Request $request, TattooAppointment $cita)
    {
        $data = $request->validate([
            'cliente_id'     => 'required|exists:clientes,id',
            'artista_id'     => 'nullable|exists:tattoo_artists,id',
            'diseno_id'      => 'nullable|exists:tattoo_designs,id',
            'fecha_hora_inicio' => 'required|date',
            'duracion_min'   => 'required|integer|min:15|max:600',
            'deposito_pct'   => 'required|numeric|min:0|max:100',
            'total_servicio' => 'required|numeric|min:0',
            'descuento_aplicado' => 'nullable|numeric|min:0',
            'notas_cliente'  => 'nullable|string|max:1000',
            'notas_internas' => 'nullable|string|max:1000',
            'lugar_tatuaje'  => 'nullable|string|max:60',
            'tamanio_approx' => 'nullable|string|max:50',
        ]);

        $data['descuento_aplicado'] = $request->descuento_aplicado ?? 0;
        $data['deposito_monto'] = $data['total_servicio'] * ($data['deposito_pct'] / 100);
        $data['total_final'] = $data['total_servicio'] - $data['descuento_aplicado'];

        if ($data['total_final'] < 0) {
            return back()->with('error', 'El total final no puede ser negativo.')->withInput();
        }

        $cita->update($data);

        return redirect()->route('tattoo.citas.index')
            ->with('success', "Cita #{$cita->id} actualizada.");
    }

    public function cambiarEstado(Request $request, TattooAppointment $cita)
    {
        $request->validate(['estado' => 'required|in:pendiente,confirmada,en_progreso,completada,cancelada,no_show']);

        DB::beginTransaction();
        try {
            $cita->update(['estado' => $request->estado]);
            if ($request->estado === 'completada' && !$cita->fecha_hora_fin) {
                $cita->update(['fecha_hora_fin' => now()]);
            }
            DB::commit();
            return back()->with('success', "Cita #{$cita->id} marcada como: {$request->estado}");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cambiar estado: ' . $e->getMessage());
        }
    }

    public function confirmarRevision(Request $request, TattooAppointment $cita)
    {
        $cita->update([
            'revision_completada' => true,
            'revision_fecha' => now(),
        ]);
        return back()->with('success', 'Revisión previa confirmada.');
    }

    public function pagar(Request $request, TattooAppointment $cita)
    {
        $request->validate([
            'monto'      => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,mixto',
            'tipo'       => 'required|in:deposito,saldo,parcial',
            'referencia' => 'nullable|string|max:100',
            'notas'      => 'nullable|string|max:500',
        ]);

        if ($cita->estado === 'completada' || $cita->estado === 'cancelada') {
            return back()->with('error', 'No se pueden registrar pagos en citas completadas o canceladas.');
        }

        $saldo = $cita->saldo_pendiente;
        if ($request->tipo !== 'deposito' && $request->monto > $saldo) {
            return back()->with('error', "El monto (RD$" . number_format($request->monto, 2) . ") excede el saldo pendiente (RD$" . number_format($saldo, 2) . ").");
        }

        DB::beginTransaction();
        try {
            TattooPayment::create([
                'appointment_id' => $cita->id,
                'monto'          => $request->monto,
                'metodo_pago'    => $request->metodo_pago,
                'tipo'           => $request->tipo,
                'referencia'     => $request->referencia,
                'user_id'        => Auth::id(),
                'notas'          => $request->notas,
            ]);

            if ($request->tipo === 'deposito') {
                $cita->update([
                    'deposito_pagado' => true,
                    'metodo_deposito' => $request->metodo_pago,
                    'estado' => 'confirmada',
                ]);
            }

            $totalPagado = $cita->payments()->sum('monto');
            if ($totalPagado >= $cita->total_final && $cita->estado === 'confirmada') {
                $cita->update(['estado' => 'completada', 'fecha_hora_fin' => now()]);
            }

            DB::commit();
            return back()->with('success', 'Pago registrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar pago: ' . $e->getMessage());
        }
    }
}
