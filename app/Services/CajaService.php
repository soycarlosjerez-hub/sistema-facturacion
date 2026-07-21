<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\Gasto;
use App\Models\Pago;
use App\Models\SesionCaja;
use App\Models\Venta;
use Illuminate\Support\Collection;

class CajaService
{
    public function listarConStats(): array
    {
        $query = Caja::orderBy('nombre');
        if ($sucursalId = session('sucursal_id')) {
            $query->where('sucursal_id', $sucursalId);
        }
        if (request()->boolean('hide_inactive')) {
            $query->where('activo', true);
        }
        $cajas = $query->get();

        $sesionActivaUsuario = SesionCaja::with('caja', 'user')
            ->where('user_id', auth()->id())
            ->where('estado', 'abierta')
            ->latest('fecha_apertura')
            ->first();

        $stats = [
            'total'     => $cajas->count(),
            'abiertas'  => $cajas->where('estado', 'abierta')->count(),
            'cerradas'  => $cajas->where('estado', 'cerrada')->count(),
            'activas'   => $cajas->where('activo', true)->count(),
            'inactivas' => $cajas->where('activo', false)->count(),
        ];

        // Bulk load session counts and ventas historico to eliminate N+1
        $cajaIds = $cajas->pluck('id');
        $sesionesPorCaja = SesionCaja::whereIn('caja_id', $cajaIds)
            ->orderBy('fecha_apertura', 'desc')
            ->get()
            ->groupBy('caja_id');
        $ventasPorCaja = Venta::selectRaw('caja_id, SUM(total) as total')
            ->whereIn('caja_id', $cajaIds)
            ->groupBy('caja_id')
            ->pluck('total', 'caja_id');

        $cajasConStats = $cajas->map(function ($caja) use ($sesionesPorCaja, $ventasPorCaja) {
            $sesiones = $sesionesPorCaja->get($caja->id, collect());
            $caja->setRelation('sesiones', $sesiones);
            $caja->ultima_sesion = $sesiones->first();
            $caja->total_sesiones = $sesiones->count();
            $caja->ventas_historico = (float) ($ventasPorCaja[$caja->id] ?? 0);
            return $caja;
        });

        return compact('cajasConStats', 'sesionActivaUsuario', 'stats');
    }

    public function create(array $data): Caja
    {
        $data['tenant_id'] = auth()->user()->business_instance_id;
        $data['sucursal_id'] = $data['sucursal_id'] ?? session('sucursal_id');
        $data['activo'] = $data['activo'] ?? true;
        $data['estado'] = 'cerrada';
        return Caja::create($data);
    }

    public function update(Caja $caja, array $data): Caja
    {
        $data['activo'] = (bool) ($data['activo'] ?? true);
        $caja->update($data);
        return $caja;
    }

    public function delete(Caja $caja): array
    {
        $user = auth()->user();

        $isElevated = $user->hasRole('admin')
            || $user->hasRole('owner')
            || $user->hasRole('admin-business')
            || $user->hasRole('root')
            || in_array($user->role ?? '', ['admin', 'owner', 'admin-business', 'root']);

        if (!$isElevated && !$user->can('cajas.delete')) {
            abort(403, 'No tienes permiso para eliminar cajas.');
        }

        if ($caja->estado === 'abierta') {
            return ['success' => false, 'message' => 'No se puede eliminar una caja abierta. Ciérrela primero.'];
        }

        $hasData = Venta::where('caja_id', $caja->id)->exists()
                || SesionCaja::where('caja_id', $caja->id)->exists()
                || Pago::where('caja_id', $caja->id)->exists()
                || Gasto::where('caja_id', $caja->id)->exists();

        if ($hasData) {
            $caja->update(['activo' => false]);
            return ['success' => true, 'deactivated' => true, 'message' => 'La caja tiene datos asociados, se desactivó en lugar de eliminarse.'];
        }

        $caja->delete();
        return ['success' => true, 'message' => 'Caja eliminada correctamente.'];
    }

    public function abrir(Caja $caja, float $montoInicial = 0): array
    {
        if (!$caja->activo) {
            return ['success' => false, 'message' => 'Esta caja está inactiva.'];
        }

        if ($caja->estado == 'abierta') {
            $sesionOtra = $caja->sesionActiva();
            if ($sesionOtra && $sesionOtra->user_id !== auth()->id()) {
                return ['success' => false, 'message' => 'La caja ya está siendo usada por otro cajero.'];
            }
            return ['success' => false, 'message' => 'La caja ya está abierta.'];
        }

        $sesionActiva = SesionCaja::where('user_id', auth()->id())
            ->where('estado', 'abierta')->first();

        if ($sesionActiva) {
            return ['success' => false, 'message' => 'Ya tienes otra caja abierta ("' . $sesionActiva->caja->nombre . '"). Ciérrala antes de abrir una nueva.'];
        }

        SesionCaja::create([
            'tenant_id'      => auth()->user()->business_instance_id,
            'caja_id'        => $caja->id,
            'user_id'        => auth()->id(),
            'fecha_apertura' => now(),
            'monto_inicial'  => $montoInicial,
            'estado'         => 'abierta',
        ]);

        $caja->update(['estado' => 'abierta']);

        return ['success' => true, 'message' => 'Caja "' . $caja->nombre . '" abierta.', 'redirect' => route('cajas.index')];
    }

    public function resumenCierre(Caja $caja): array
    {
        $query = SesionCaja::where('caja_id', $caja->id)
            ->where('estado', 'abierta');

        if (in_array(auth()->user()->role, ['admin', 'owner'])) {
            $query->withoutGlobalScope('tenant');
        } else {
            $query->where('user_id', auth()->id());
        }

        $sesion = $query->firstOrFail();

        $pagosEfectivo = 0;
        $pagosTarjeta = 0;
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
                    match ($m) {
                        'tarjeta'       => $pagosTarjeta += $monto,
                        'transferencia' => $pagosTransferencia += $monto,
                        default         => $pagosEfectivo += $monto,
                    };
                }
            }
        }

        return [
            'caja'                => $caja,
            'sesion'              => $sesion,
            'pagosEfectivo'       => $pagosEfectivo,
            'pagosTarjeta'        => $pagosTarjeta,
            'pagosTransferencia'  => $pagosTransferencia,
            'totalEsperado'       => (float) $sesion->monto_inicial + $pagosEfectivo,
            'ventasTotales'       => $ventas->sum('total'),
        ];
    }

    public function cerrar(Caja $caja, array $data): array
    {
        $query = SesionCaja::where('caja_id', $caja->id)
            ->where('estado', 'abierta');

        if (in_array(auth()->user()->role, ['admin', 'owner'])) {
            $query->withoutGlobalScope('tenant');
        } else {
            $query->where('user_id', auth()->id());
        }

        $sesion = $query->firstOrFail();

        $montoDeclarado = (float) ($data['monto_declarado'] ?? 0);
        $cobrosEfectivo = (float) ($data['cobros_efectivo'] ?? 0);
        $cobrosTarjeta = (float) ($data['cobros_tarjeta'] ?? 0);
        $cobrosTransferencia = (float) ($data['cobros_transferencia'] ?? 0);

        // Server-side calculation — prevents client manipulation via hidden field
        $totalEsperado = $sesion->monto_inicial + $cobrosEfectivo;
        $descuadre = $montoDeclarado - $totalEsperado;

        $sesion->update([
            'fecha_cierre'         => now(),
            'ventas_efectivo'      => $cobrosEfectivo,
            'ventas_tarjeta'       => $cobrosTarjeta,
            'ventas_transferencia' => $cobrosTransferencia,
            'monto_declarado'      => $montoDeclarado,
            'descuadre'            => $descuadre,
            'estado'               => 'cerrada',
            'notas'                => $data['notas'] ?? null,
        ]);

        $caja->update(['estado' => 'cerrada']);

        return [
            'success'   => true,
            'message'   => 'Caja cerrada. Descuadre: RD$ ' . number_format($descuadre, 2),
            'descuadre' => $descuadre,
        ];
    }
}
