<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Sucursal;
use App\Services\CajaService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CajaController extends Controller
{
    public function __construct(
        protected CajaService $cajaService
    ) {}

    public function index()
    {
        return view('cajas.index', $this->cajaService->listarConStats());
    }

    public function create()
    {
        $caja = new Caja();
        $sucursales = Sucursal::orderBy('nombre')->get();

        $lastCode = Caja::max('codigo');
        $nextCode = 'C01';
        if ($lastCode) {
            $num = intval(substr($lastCode, 1)) + 1;
            $nextCode = 'C' . str_pad($num, 2, '0', STR_PAD_LEFT);
        }

        return view('cajas.create', compact('caja', 'sucursales', 'nextCode'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'codigo'      => [
                'nullable', 'string', 'max:20',
                Rule::unique('cajas', 'codigo')->where('tenant_id', auth()->user()->business_instance_id),
            ],
            'ubicacion'   => 'nullable|string|max:100',
            'activo'      => 'boolean',
            'sucursal_id' => 'nullable|exists:sucursales,id',
        ], [
            'nombre.required' => 'El nombre de la caja es obligatorio.',
            'codigo.unique'   => 'Este código ya está en uso.',
        ]);

        $this->cajaService->create($data);

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
            'codigo'      => [
                'nullable', 'string', 'max:20',
                Rule::unique('cajas', 'codigo')->ignore($caja->id)->where('tenant_id', auth()->user()->business_instance_id),
            ],
            'ubicacion'   => 'nullable|string|max:100',
            'activo'      => 'boolean',
            'sucursal_id' => 'nullable|exists:sucursales,id',
        ]);

        $this->cajaService->update($caja, $data);

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
        $result = $this->cajaService->delete($caja);

        return redirect()->route('cajas.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function abrir(Request $request, Caja $caja)
    {
        $result = $this->cajaService->abrir($caja, (float) $request->input('monto_inicial', 0));

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->to($result['redirect'] ?? route('cajas.index'))
            ->with('success', $result['message']);
    }

    public function resumenCierre(Caja $caja)
    {
        return view('cajas.cierre', $this->cajaService->resumenCierre($caja));
    }

    public function cerrar(Request $request, Caja $caja)
    {
        $request->validate([
            'monto_declarado'      => 'required|numeric|min:0',
            'cobros_efectivo'      => 'required|numeric|min:0',
            'cobros_tarjeta'       => 'required|numeric|min:0',
            'cobros_transferencia' => 'required|numeric|min:0',
            'total_esperado'       => 'required|numeric',
            'notas'                => 'nullable|string|max:500',
        ]);

        $result = $this->cajaService->cerrar($caja, $request->all());

        return redirect()->route('cajas.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
