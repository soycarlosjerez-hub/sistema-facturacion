<?php

namespace App\Http\Controllers;

use App\Models\CuentaBancaria;
use App\Services\CuentaBancariaService;
use Illuminate\Http\Request;

class CuentaBancariaController extends Controller
{
    public function __construct(
        protected CuentaBancariaService $cuentaBancariaService
    ) {}

    public function index(Request $request)
    {
        return view('cuentas-bancarias.index', $this->cuentaBancariaService->list($request->all()));
    }

    public function create()
    {
        return view('cuentas-bancarias.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:255',
            'banco'         => 'nullable|string|max:255',
            'tipo_cuenta'   => 'nullable|string|in:ahorros,corriente',
            'numero_cuenta' => 'nullable|string|max:50',
            'moneda'        => 'nullable|string|in:RD,USD,EUR',
            'titular'       => 'nullable|string|max:255',
            'cedula_ruc'    => 'nullable|string|max:20',
            'saldo_inicial' => 'nullable|numeric|min:0',
            'activo' => 'nullable|boolean',
        ]);

        $this->cuentaBancariaService->create($data);

        return redirect()->route('cuentas-bancarias.index')->with('success', 'Cuenta bancaria creada correctamente.');
    }

    public function show(CuentaBancaria $cuentasBancarium)
    {
        return view('cuentas-bancarias.show', compact('cuentasBancarium'));
    }

    public function edit(CuentaBancaria $cuentasBancarium)
    {
        return view('cuentas-bancarias.edit', compact('cuentasBancarium'));
    }

    public function update(Request $request, CuentaBancaria $cuentasBancarium)
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:255',
            'banco'         => 'nullable|string|max:255',
            'tipo_cuenta'   => 'nullable|string|in:ahorros,corriente',
            'numero_cuenta' => 'nullable|string|max:50',
            'moneda'        => 'nullable|string|in:RD,USD,EUR',
            'titular'       => 'nullable|string|max:255',
            'cedula_ruc'    => 'nullable|string|max:20',
            'saldo_inicial' => 'nullable|numeric|min:0',
            'activo'        => 'boolean',
        ]);

        $this->cuentaBancariaService->update($cuentasBancarium, $data);

        return redirect()->route('cuentas-bancarias.index')->with('success', 'Cuenta bancaria actualizada correctamente.');
    }

    public function destroy(CuentaBancaria $cuentasBancarium)
    {
        $result = $this->cuentaBancariaService->delete($cuentasBancarium);
        return redirect()->route('cuentas-bancarias.index')->with('success', $result['message']);
    }
}
