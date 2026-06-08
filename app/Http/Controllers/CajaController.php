<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caja;
use App\Models\SesionCaja;
use App\Models\Venta;
use App\Models\Pago;
use App\Models\Sucursal;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    public function index()
    {
        $query = Caja::orderBy('nombre');
        if ($sucursalId = session('sucursal_id')) {
            $query->where('sucursal_id', $sucursalId);
        }
        $cajas = $query->get();
        // Si no hay cajas, crear la principal por defecto (solo si no existe globalmente)
        if ($cajas->isEmpty()) {
            $caja = Caja::firstOrCreate(
                ['codigo' => 'C01'],
                ['nombre' => 'Caja Principal', 'estado' => 'cerrada', 'activo' => true]
            );
            $cajas = collect([$caja]);
        }

        $sesionActivaUsuario = SesionCaja::with('caja', 'user')
            ->where('user_id', auth()->id())
            ->where('estado', 'abierta')
            ->latest('fecha_apertura')
            ->first();

        // Estadísticas para el header
        $stats = [
            'total'      => $cajas->count(),
            'abiertas'   => $cajas->where('estado', 'abierta')->count(),
            'cerradas'   => $cajas->where('estado', 'cerrada')->count(),
            'activas'    => $cajas->where('activo', true)->count(),
            'inactivas'  => $cajas->where('activo', false)->count(),
        ];

        // Por cada caja: última sesión y total vendido
        $cajasConStats = $cajas->map(function ($caja) {
            $ultimaSesion = SesionCaja::where('caja_id', $caja->id)
                ->latest('fecha_apertura')
                ->first();
            $caja->ultima_sesion = $ultimaSesion;
            $caja->total_sesiones = SesionCaja::where('caja_id', $caja->id)->count();
            $caja->ventas_historico = \App\Models\Venta::where('caja_id', $caja->id)->sum('total');
            return $caja;
        });

        return view('cajas.index', compact('cajasConStats', 'sesionActivaUsuario', 'stats'));
    }

    public function create()
    {
        $caja = new Caja();
        $sucursales = Sucursal::orderBy('nombre')->get();
        return view('cajas.create', compact('caja', 'sucursales'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'codigo'      => 'nullable|string|max:20|unique:cajas,codigo',
            'ubicacion'   => 'nullable|string|max:100',
            'activo'      => 'boolean',
            'sucursal_id' => 'nullable|exists:sucursales,id',
        ], [
            'nombre.required' => 'El nombre de la caja es obligatorio.',
            'codigo.unique'   => 'Este código ya está en uso.',
        ]);

        $data['activo'] = $data['activo'] ?? true;
        $data['estado'] = 'cerrada';

        Caja::create($data);

        return redirect()->route('cajas.index')
            ->with('success', 'Caja creada correctamente.');
    }

    public function edit(Caja $caja)
    {
        $sucursales = Sucursal::orderBy('nombre')->get();
        return view('cajas.edit', compact('caja', 'sucursales'));
    }

    public function update(Request $request, Caja $caja)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'codigo'      => 'nullable|string|max:20|unique:cajas,codigo,' . $caja->id,
            'ubicacion'   => 'nullable|string|max:100',
            'activo'      => 'boolean',
            'sucursal_id' => 'nullable|exists:sucursales,id',
        ]);

        $data['activo'] = $request->boolean('activo');
        $caja->update($data);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => 'Caja actualizada correctamente.',
                'caja' => $caja->fresh(),
            ]);
        }

        return redirect()->route('cajas.index')
            ->with('success', 'Caja actualizada correctamente.');
    }

    public function destroy(Caja $caja)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->can('cajas.delete')) {
            abort(403, 'Solo administradores pueden eliminar cajas.');
        }

        if ($caja->estado === 'abierta') {
            return back()->with('error', 'No se puede eliminar una caja abierta. Ciérrela primero.');
        }

        if (\App\Models\Venta::where('caja_id', $caja->id)->exists()) {
            $caja->update(['activo' => false]);
            return redirect()->route('cajas.index')
                ->with('success', 'La caja tiene ventas asociadas, se desactivó en lugar de eliminarse.');
        }

        $caja->delete();
        return redirect()->route('cajas.index')
            ->with('success', 'Caja eliminada correctamente.');
    }

    public function abrir(Request $request, Caja $caja)
    {
        if (! $caja->activo) {
            return back()->with('error', 'Esta caja está inactiva.');
        }

        if ($caja->estado == 'abierta') {
            $sesionOtra = $caja->sesionActiva();
            if ($sesionOtra && $sesionOtra->user_id !== auth()->id()) {
                return back()->with('error', 'La caja ya está siendo usada por otro cajero.');
            }
            return back()->with('error', 'La caja ya está abierta.');
        }

        $sesionActiva = SesionCaja::where('user_id', auth()->id())
            ->where('estado', 'abierta')
            ->first();

        if ($sesionActiva) {
            return back()->with('error', 'Ya tienes otra caja abierta ("' . $sesionActiva->caja->nombre . '"). Ciérrala antes de abrir una nueva.');
        }

        $montoInicial = (float) $request->input('monto_inicial', 0);

        $sesion = SesionCaja::create([
            'caja_id'        => $caja->id,
            'user_id'        => auth()->id(),
            'fecha_apertura' => now(),
            'monto_inicial'  => $montoInicial,
            'estado'         => 'abierta',
        ]);

        $caja->update(['estado' => 'abierta']);

        return redirect()->route('ventas.create')
            ->with('success', 'Caja "' . $caja->nombre . '" abierta. ¡Bienvenido al POS!');
    }

    public function resumenCierre(Caja $caja)
    {
        $sesion = SesionCaja::where('caja_id', $caja->id)
            ->where('estado', 'abierta')
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $pagosEfectivo    = 0;
        $pagosTarjeta     = 0;
        $pagosTransferencia = 0;

        $ventas = Venta::with('pagos')->where('sesion_caja_id', $sesion->id)->get();

        foreach ($ventas as $venta) {
            $metodos = $venta->pagos;
            if ($metodos->isEmpty()) {
                $pagosEfectivo += (float) $venta->total;
            } else {
                foreach ($metodos as $pago) {
                    $m = $pago->metodo_pago ?? 'efectivo';
                    $monto = (float) $pago->monto;
                    if ($m === 'tarjeta')        $pagosTarjeta       += $monto;
                    elseif ($m === 'transferencia') $pagosTransferencia += $monto;
                    else                          $pagosEfectivo      += $monto;
                }
            }
        }

        $totalEsperado = (float) $sesion->monto_inicial + $pagosEfectivo;
        $ventasTotales = $ventas->sum('total');

        return view('cajas.cierre', compact(
            'caja', 'sesion',
            'pagosEfectivo', 'pagosTarjeta', 'pagosTransferencia',
            'totalEsperado', 'ventasTotales'
        ));
    }

    public function cerrar(Request $request, Caja $caja)
    {
        $sesion = SesionCaja::where('caja_id', $caja->id)
            ->where('estado', 'abierta')
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $montoDeclarado = (float) $request->input('monto_declarado', 0);
        $cobrosEfectivo    = (float) $request->input('cobros_efectivo', 0);
        $cobrosTarjeta     = (float) $request->input('cobros_tarjeta', 0);
        $cobrosTransferencia = (float) $request->input('cobros_transferencia', 0);
        $totalEsperado     = (float) $request->input('total_esperado', 0);
        $descuadre         = $montoDeclarado - $totalEsperado;

        $sesion->update([
            'fecha_cierre'         => now(),
            'ventas_efectivo'      => $cobrosEfectivo,
            'ventas_tarjeta'       => $cobrosTarjeta,
            'ventas_transferencia' => $cobrosTransferencia,
            'monto_declarado'      => $montoDeclarado,
            'descuadre'            => $descuadre,
            'estado'               => 'cerrada',
            'notas'                => $request->input('notas'),
        ]);

        $caja->update(['estado' => 'cerrada']);

        return redirect()->route('cajas.index')
            ->with('success', 'Caja cerrada. Descuadre: RD$ ' . number_format($descuadre, 2));
    }

}
