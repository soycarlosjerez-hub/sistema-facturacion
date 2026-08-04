<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\SesionCaja;
use App\Models\Sucursal;
use App\Services\CajaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
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

    public function show(Caja $caja)
    {
        $sesionesActivas = $caja->sesiones()->where('estado', 'abierta')->get();
        $stats = null;
        if ($sesionesActivas->isNotEmpty()) {
            try {
                $stats = $this->cajaService->resumenCierre($sesionesActivas->first());
            } catch (\Exception $e) {
                $stats = null;
            }
        }
        return view('cajas.show', compact('caja', 'sesionesActivas', 'stats'));
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

        if (!empty($result['sesion'])) {
            Event::dispatch(new \App\Events\ShiftOpened($result['sesion']));
        }

        return redirect()->to($result['redirect'] ?? route('cajas.index'))
            ->with('success', $result['message']);
    }

    public function resumenCierre(Caja $caja, ?SesionCaja $sesion = null)
    {
        return view('cajas.cierre', $this->cajaService->resumenCierre($sesion));
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

        $sesion = SesionCaja::where('caja_id', $caja->id)
            ->where('estado', 'abierta')
            ->when(!in_array(auth()->user()->role, ['admin', 'owner']), function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->firstOrFail();

        $result = $this->cajaService->cerrar($sesion, $request->all());

        Event::dispatch(new \App\Events\ShiftClosed($sesion));

        return redirect()->route('cajas.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function cerrarPorSesion(Request $request, SesionCaja $sesion)
    {
        $request->validate([
            'monto_declarado'      => 'required|numeric|min:0',
            'cobros_efectivo'      => 'required|numeric|min:0',
            'cobros_tarjeta'       => 'required|numeric|min:0',
            'cobros_transferencia' => 'required|numeric|min:0',
            'total_esperado'       => 'required|numeric',
            'notas'                => 'nullable|string|max:500',
        ]);

        // Check permissions: admin can close any, regular users only their own
        if (!in_array(auth()->user()->role, ['admin', 'owner', 'admin-business', 'root'])) {
            if ($sesion->user_id !== auth()->id()) {
                abort(403, 'No puedes cerrar una sesión que no es tuya.');
            }
        }

        if ($sesion->estado !== 'abierta') {
            return back()->with('error', 'Esta sesión ya está cerrada.');
        }

        $result = $this->cajaService->cerrar($sesion, $request->all());

        Event::dispatch(new \App\Events\ShiftClosed($sesion));

        return redirect()->route('cajas.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
