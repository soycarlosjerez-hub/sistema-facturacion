<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::withCount(['permissions', 'users']);

        if ($request->filled('buscar')) {
            $s = $request->buscar;
            $query->where('name', 'like', "%{$s}%");
        }

        $roles = $query->orderBy('name')->get();

        $stats = [
            'total'       => $roles->count(),
            'permisos'    => Permission::count(),
            'usuarios'    => \App\Models\User::count(),
            'con_usuarios'=> $roles->filter(fn($r) => $r->users_count > 0)->count(),
        ];

        $modulos = Permission::all()
            ->groupBy(fn($p) => explode('.', $p->name)[0])
            ->sortKeys();

        return view('roles.index', compact('roles', 'stats', 'modulos'));
    }

    public function matrix()
    {
        $roles = Role::orderBy('name')->get();
        $permisos = Permission::orderBy('name')->get();
        $modulos = $permisos->groupBy(fn($p) => explode('.', $p->name)[0])->sortKeys();

        $matrix = [];
        foreach ($roles as $rol) {
            $matrix[$rol->name] = $rol->permissions->pluck('name')->flip();
        }

        return view('roles.matrix', compact('roles', 'permisos', 'modulos', 'matrix'));
    }

    public function create()
    {
        $modulos = Permission::orderBy('name')->get()
            ->groupBy(fn($p) => explode('.', $p->name)[0])
            ->sortKeys();
        return view('roles.create', compact('modulos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:50|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique'   => 'Ya existe un rol con este nombre.',
        ]);

        $rol = Role::create(['name' => strtolower($request->name), 'guard_name' => 'web']);

        if ($request->filled('permissions')) {
            $rol->syncPermissions($request->permissions);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')
            ->with('success', "Rol \"{$rol->name}\" creado con {$rol->permissions->count()} permisos.");
    }

    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        $permisosGrouped = $role->permissions->groupBy(fn($p) => explode('.', $p->name)[0])->sortKeys();
        $permisosAll = Permission::orderBy('name')->get();
        $modulos = $permisosAll->groupBy(fn($p) => explode('.', $p->name)[0])->sortKeys();
        $users = $role->users()->orderBy('name')->get();
        $isProtected = in_array($role->name, ['admin', 'vendedor', 'gerente', 'almacen', 'contador']);

        return view('roles.show', compact('role', 'permisosGrouped', 'modulos', 'users', 'isProtected'));
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        $modulos = Permission::orderBy('name')->get()
            ->groupBy(fn($p) => explode('.', $p->name)[0])
            ->sortKeys();
        $permisosAsignados = $role->permissions->pluck('name')->toArray();
        $isProtected = in_array($role->name, ['admin']);

        return view('roles.edit', compact('role', 'modulos', 'permisosAsignados', 'isProtected'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:50', Rule::unique('roles', 'name')->ignore($role->id)],
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique'   => 'Ya existe un rol con este nombre.',
        ]);

        $rolNuevo = strtolower($request->name);
        $cambiaNombre = $rolNuevo !== $role->name;
        $esAdmin = $role->name === 'admin';

        if ($cambiaNombre) {
            \App\Models\User::where('role', $role->name)->update(['role' => $rolNuevo]);
            $role->name = $rolNuevo;
            $role->save();
        }

        if (!$esAdmin && $request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } elseif (!$request->has('permissions') && !$esAdmin) {
            $role->syncPermissions([]);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')
            ->with('success', "Rol \"{$role->name}\" actualizado correctamente.");
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            return back()->with('error', 'No se puede eliminar el rol de Administrador del sistema.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', "No se puede eliminar el rol \"{$role->name}\" porque tiene {$role->users()->count()} usuario(s) asignado(s).");
        }

        $nombre = $role->name;
        $role->delete();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')
            ->with('success', "Rol \"{$nombre}\" eliminado correctamente.");
    }
}
