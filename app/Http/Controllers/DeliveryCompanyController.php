<?php

namespace App\Http\Controllers;

use App\Models\DeliveryCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryCompanyController extends Controller
{
    public function index()
    {
        $companies = DeliveryCompany::orderBy('nombre')->paginate(10);
        return view('delivery-companies.index', compact('companies'));
    }

    public function create()
    {
        return view('delivery-companies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'              => 'required|string|max:100',
            'nombre_corto'        => 'required|string|max:30|unique:delivery_companies,nombre_corto',
            'comision_porcentaje' => 'required|numeric|min:0|max:100',
            'activo'              => 'boolean',
        ]);

        $data['activo'] = $request->boolean('activo', true);
        $data['tenant_id'] = Auth::user()->business_instance_id;

        DeliveryCompany::create($data);

        return redirect()->route('delivery-companies.index')
            ->with('success', 'Empresa de delivery creada correctamente.');
    }

    public function edit(DeliveryCompany $deliveryCompany)
    {
        return view('delivery-companies.edit', ['company' => $deliveryCompany]);
    }

    public function update(Request $request, DeliveryCompany $deliveryCompany)
    {
        $data = $request->validate([
            'nombre'              => 'required|string|max:100',
            'nombre_corto'        => 'required|string|max:30|unique:delivery_companies,nombre_corto,' . $deliveryCompany->id,
            'comision_porcentaje' => 'required|numeric|min:0|max:100',
            'activo'              => 'boolean',
        ]);

        $data['activo'] = $request->boolean('activo', true);

        $deliveryCompany->update($data);

        return redirect()->route('delivery-companies.index')
            ->with('success', 'Empresa de delivery actualizada correctamente.');
    }

    public function destroy(DeliveryCompany $deliveryCompany)
    {
        $deliveryCompany->delete();
        return redirect()->route('delivery-companies.index')
            ->with('success', 'Empresa de delivery eliminada.');
    }

    public function listarActivas()
    {
        return response()->json(
            DeliveryCompany::activos()->orderBy('nombre')->get(['id', 'nombre', 'nombre_corto', 'comision_porcentaje'])
        );
    }
}
