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

        $isElevated = in_array(auth()->user()->role, ['admin', 'owner', 'admin-business', 'root'])
            || auth()->user()->hasAnyRole(['admin', 'owner', 'admin-business', 'root']);

        $sesionesActivasUsuario = SesionCaja::with('caja', 'user')
            ->where('estado', 'abierta');

        if (!$isElevated) {
            $sesionesActivasUsuario->where('user_id', auth()->id());
        }

        $sesionesActivasUsuario = $sesionesActivasUsuario->latest('fecha_apertura')->get();

        $stats = [
            'total'         => $cajas->count(),
            'abiertas'      => $cajas->where('estado', 'abierta')->count(),
            'cerradas'      => $cajas->where('estado', 'cerrada')->count(),
            'activas'       => $cajas->where('activo', true)->count(),
            'inactivas'     => $cajas->where('activo', false)->count(),
            'sesiones_activas_usuario' => $sesionesActivasUsuario->count(),
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

        return compact('cajasConStats', 'sesionesActivasUsuario', 'stats');
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

        // Regla: máximo 1 sesión abierta por caja (sin importar el usuario)
        $sesionMismaCaja = SesionCaja::where('caja_id', $caja->id)
            ->where('estado', 'abierta')
            ->first();

        if ($sesionMismaCaja) {
            return ['success' => false, 'message' => 'La caja "' . $caja->nombre . '" ya está abierta.'];
        }

        // Regla: un usuario no-elevado solo puede tener 1 caja abierta a la vez
        $isElevated = in_array(auth()->user()->role, ['admin', 'owner', 'admin-business', 'root'])
            || auth()->user()->hasAnyRole(['admin', 'owner', 'admin-business', 'root']);

        if (!$isElevated) {
            $otraSesion = SesionCaja::with('caja')
                ->where('user_id', auth()->id())
                ->where('estado', 'abierta')
                ->first();

            if ($otraSesion) {
                return ['success' => false, 'message' => 'Ya tienes otra caja abierta ("' . $otraSesion->caja->nombre . '"). Ciérrala antes de abrir una nueva.'];
            }
        }

        $sesion = SesionCaja::create([
            'tenant_id'      => auth()->user()->business_instance_id,
            'caja_id'        => $caja->id,
            'user_id'        => auth()->id(),
            'fecha_apertura' => now(),
            'monto_inicial'  => $montoInicial,
            'estado'         => 'abierta',
        ]);

        $caja->update(['estado' => 'abierta']);

        return [
            'success'  => true,
            'message'  => 'Caja "' . $caja->nombre . '" abierta.',
            'redirect' => route('cajas.index'),
            'sesion'   => $sesion->load('caja'),
        ];
    }

    public function resumenCierre(?SesionCaja $sesion = null): array
    {
        if (!$sesion) {
            $query = SesionCaja::where('estado', 'abierta');

            if (in_array(auth()->user()->role, ['admin', 'owner'])) {
                $query->withoutGlobalScope('tenant');
            } else {
                $query->where('user_id', auth()->id());
            }

            $sesion = $query->firstOrFail();
        }

        $caja = $sesion->caja;

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

    public function cerrar(SesionCaja $sesion, array $data): array
    {
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

        // Check if this was the last open session for this caja
        $otraSesionAbierta = SesionCaja::where('caja_id', $sesion->caja_id)
            ->where('estado', 'abierta')
            ->exists();

        if (!$otraSesionAbierta) {
            $sesion->caja->update(['estado' => 'cerrada']);
        }

        return [
            'success'   => true,
            'message'   => 'Caja cerrada. Descuadre: RD$ ' . number_format($descuadre, 2),
            'descuadre' => $descuadre,
        ];
    }
}
