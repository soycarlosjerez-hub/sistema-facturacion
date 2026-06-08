<?php

namespace App\Http\Controllers;

use App\Models\Modulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModuloController extends Controller
{
    public function index()
    {
        $modulos = Modulo::orderBy('categoria')->orderBy('orden')->get()->groupBy('categoria');
        return view('modulos.index', compact('modulos'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:50|regex:/^[a-z0-9\-]+$/|unique:modulos,key',
            'label' => 'required|string|max:100',
            'icon' => 'required|string|max:50',
            'categoria' => 'required|string|max:50',
            'sidebar_route' => 'nullable|string|max:100',
            'sidebar_is_route' => 'nullable|string|max:100',
            'sidebar_exact_route' => 'nullable|string|max:100',
            'sidebar_permission' => 'nullable|string|max:100',
            'orden' => 'integer|min:0',
        ], [
            'key.required' => 'La clave es obligatoria.',
            'key.unique' => 'Ya existe un módulo con esa clave.',
            'key.regex' => 'La clave solo puede contener minúsculas, números y guiones.',
            'label.required' => 'El nombre es obligatorio.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Modulo::create($request->only([
            'key', 'label', 'icon', 'categoria',
            'sidebar_route', 'sidebar_is_route', 'sidebar_exact_route', 'sidebar_permission',
            'orden',
        ]));

        return redirect()->route('modulos.index')->with('success', 'Módulo creado correctamente.');
    }

    public function update(Request $request, Modulo $modulo)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:100',
            'icon' => 'required|string|max:50',
            'categoria' => 'required|string|max:50',
            'sidebar_route' => 'nullable|string|max:100',
            'sidebar_is_route' => 'nullable|string|max:100',
            'sidebar_exact_route' => 'nullable|string|max:100',
            'sidebar_permission' => 'nullable|string|max:100',
            'orden' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $modulo->update($request->only([
            'label', 'icon', 'categoria',
            'sidebar_route', 'sidebar_is_route', 'sidebar_exact_route', 'sidebar_permission',
            'orden',
        ]));

        return redirect()->route('modulos.index')->with('success', 'Módulo actualizado correctamente.');
    }

    public function destroy(Modulo $modulo)
    {
        $modulo->delete();
        return redirect()->route('modulos.index')->with('success', 'Módulo eliminado correctamente.');
    }

    public function toggle(Modulo $modulo)
    {
        $modulo->update(['activo' => !$modulo->activo]);
        return response()->json(['success' => true, 'activo' => $modulo->activo]);
    }
}
