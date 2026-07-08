<?php

namespace App\Services;

use App\Models\CuentaBancaria;
use Illuminate\Support\Facades\Auth;

class CuentaBancariaService
{
    public function list(array $filters = []): array
    {
        $buscar = $filters['buscar'] ?? null;
        $incluirInactivos = ($filters['incluir_inactivos'] ?? '') === '1';

        $query = CuentaBancaria::query();
        if (!$incluirInactivos) {
            $query->activo();
        }
        $query->when($buscar, fn($q) => $q->where(function ($sub) use ($buscar) {
            $sub->where('nombre', 'like', "%{$buscar}%")
                ->orWhere('banco', 'like', "%{$buscar}%")
                ->orWhere('numero_cuenta', 'like', "%{$buscar}%")
                ->orWhere('titular', 'like', "%{$buscar}%");
        }))->latest();

        $cuentas = $query->paginate(10)->appends($filters);
        $totalCuentas = CuentaBancaria::count();
        $totalActivas = CuentaBancaria::activo()->count();

        return compact('cuentas', 'totalCuentas', 'totalActivas');
    }

    public function create(array $data): CuentaBancaria
    {
        $data['saldo_actual'] = $data['saldo_inicial'] ?? 0;
        $data['activo'] = $data['activo'] ?? false;
        $data['tenant_id'] = Auth::user()->business_instance_id ?? null;
        return CuentaBancaria::create($data);
    }

    public function update(CuentaBancaria $cuenta, array $data): CuentaBancaria
    {
        $data['activo'] = $data['activo'] ?? false;
        $cuenta->update($data);
        return $cuenta;
    }

    public function delete(CuentaBancaria $cuenta): array
    {
        $cuenta->delete();
        return ['success' => true, 'message' => 'Cuenta bancaria eliminada correctamente.'];
    }
}
