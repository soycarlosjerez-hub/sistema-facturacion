<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSucursalRequest;
use App\Http\Requests\UpdateSucursalRequest;
use App\Models\Sucursal;
use App\Services\SucursalService;

class SucursalController extends Controller
{
    protected SucursalService $sucursalService;

    public function __construct(SucursalService $sucursalService)
    {
        $this->sucursalService = $sucursalService;
    }

    public function index()
    {
        $sucursales = $this->sucursalService->list(request()->only(['search']));

        return view('sucursales.index', compact('sucursales'));
    }

    public function create()
    {
        return view('sucursales.create');
    }

    public function store(StoreSucursalRequest $request)
    {
        $this->sucursalService->create($request->validated());

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal creada exitosamente.');
    }

    public function show(Sucursal $sucursal)
    {
        $sucursal->loadCount(['almacenes', 'cajas', 'usuarios', 'ventas', 'compras']);

        $stats = $this->sucursalService->getStats($sucursal);
        $activity = $this->sucursalService->getRecentActivity($sucursal);

        return view('sucursales.show', compact('sucursal', 'stats', 'activity'));
    }

    public function edit(Sucursal $sucursal)
    {
        return view('sucursales.edit', compact('sucursal'));
    }

    public function update(UpdateSucursalRequest $request, Sucursal $sucursal)
    {
        $this->sucursalService->update($sucursal, $request->validated());

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal actualizada exitosamente.');
    }

    public function destroy(Sucursal $sucursal)
    {
        $result = $this->sucursalService->delete($sucursal);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('sucursales.index')
            ->with('success', $result['message']);
    }
}
