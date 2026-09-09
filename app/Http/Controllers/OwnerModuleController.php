<?php

namespace App\Http\Controllers;

use App\Models\BusinessInstanceModule;
use App\Models\BusinessTypeModule;
use App\Models\InstanceRoleModule;
use App\Models\Modulo;
use App\Traits\LogsOwnerAction;
use Illuminate\Http\Request;

class OwnerModuleController extends Controller
{
    use LogsOwnerAction;

    public function __construct()
    {
        $this->middleware(['auth', 'role:owner']);
    }

    /**
     * Listar todos los módulos
     */
    public function index(): \Illuminate\View\View
    {
        $categorias = Modulo::select('categoria')->distinct()->orderBy('categoria')->pluck('categoria');
        $modulos = Modulo::orderBy('categoria')->orderBy('orden')->get();
        return view('owner.modules.index', compact('modulos', 'categorias'));
    }

    /**
     * Mostrar el formulario de creación
     */
    public function create(): \Illuminate\View\View
    {
        $categorias = Modulo::select('categoria')->distinct()->orderBy('categoria')->pluck('categoria');
        return view('owner.modules.form', compact('categorias'));
    }

    /**
     * Almacenar un nuevo módulo
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'key' => 'required|string|max:50|unique:modulos,key',
            'label' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'categoria' => 'required|string|max:50',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'boolean',
        ]);

        Modulo::create([
            'key' => $data['key'],
            'label' => $data['label'],
            'icon' => $data['icon'] ?? 'bi-circle',
            'categoria' => $data['categoria'],
            'orden' => $data['orden'] ?? 0,
            'activo' => $request->boolean('activo', true),
        ]);

        return redirect()->route('owner.modules.index')
            ->with('success', "Módulo \"{$data['label']}\" creado correctamente.");
    }

    /**
     * Mostrar el formulario de edición
     */
    public function edit($id): \Illuminate\View\View
    {
        $modulo = Modulo::findOrFail($id);
        $categorias = Modulo::select('categoria')->distinct()->orderBy('categoria')->pluck('categoria');
        return view('owner.modules.form', compact('modulo', 'categorias'));
    }

    /**
     * Actualizar un módulo
     */
    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $modulo = Modulo::findOrFail($id);

        $data = $request->validate([
            'key' => 'required|string|max:50|unique:modulos,key,' . $modulo->id,
            'label' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'categoria' => 'required|string|max:50',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'boolean',
        ]);

        $modulo->update([
            'key' => $data['key'],
            'label' => $data['label'],
            'icon' => $data['icon'] ?? 'bi-circle',
            'categoria' => $data['categoria'],
            'orden' => $data['orden'] ?? 0,
            'activo' => $request->boolean('activo', true),
        ]);

        return redirect()->route('owner.modules.index')
            ->with('success', "Módulo \"{$modulo->label}\" actualizado correctamente.");
    }

    /**
     * Eliminar un módulo
     */
    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        $modulo = Modulo::findOrFail($id);

        $typesCount = BusinessTypeModule::where('modulo_key', $modulo->key)->count();
        $instanceRolesCount = InstanceRoleModule::where('modulo_key', $modulo->key)->count();
        $instanceOverrideCount = BusinessInstanceModule::where('modulo_key', $modulo->key)->count();

        if ($typesCount > 0 || $instanceRolesCount > 0 || $instanceOverrideCount > 0) {
            return back()->with('error', "No se puede eliminar \"{$modulo->label}\" porque está en uso por {$typesCount} tipo(s) de negocio, {$instanceRolesCount} role(s) de instancia y {$instanceOverrideCount} instancia(s). Desactívelo en su lugar.");
        }

        $modulo->delete();

        return redirect()->route('owner.modules.index')
            ->with('success', "Módulo \"{$modulo->label}\" eliminado.");
    }
}
