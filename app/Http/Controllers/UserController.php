<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sucursal;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('rol')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->rol));
        }

        if ($request->filled('buscar')) {
            $search = $request->buscar;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $usuarios = $query->orderBy('name')->paginate(12)->withQueryString();

        $allUsers = User::with('roles')->get();
        $stats = [
            'total'     => $allUsers->count(),
            'admin'     => $allUsers->filter(fn($u) => $u->hasRole('admin'))->count(),
            'gerente'   => $allUsers->filter(fn($u) => $u->hasRole('gerente'))->count(),
            'vendedor'  => $allUsers->filter(fn($u) => $u->hasRole('vendedor'))->count(),
            'almacen'   => $allUsers->filter(fn($u) => $u->hasRole('almacen'))->count(),
            'contador'  => $allUsers->filter(fn($u) => $u->hasRole('contador'))->count(),
            'sin_rol'   => $allUsers->filter(fn($u) => $u->roles->isEmpty())->count(),
        ];

        $roles = Role::withCount('permissions')->orderBy('name')->get();

        return view('usuarios.index', compact('usuarios', 'stats', 'roles'));
    }

    public function create()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        $sucursales = Sucursal::orderBy('nombre')->get();
        return view('usuarios.create', compact('roles', 'sucursales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255|unique:users,email',
            'password'    => 'required|string|min:6|confirmed',
            'role'        => 'required|string|exists:roles,name',
            'sucursal_id' => 'nullable|exists:sucursales,id',
        ], [
            'name.required'      => 'El nombre es obligatorio.',
            'email.required'     => 'El correo es obligatorio.',
            'email.email'        => 'El correo no es válido.',
            'email.unique'       => 'Ya existe un usuario con ese correo.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'role.required'      => 'Selecciona un rol.',
        ]);

        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'sucursal_id' => $request->sucursal_id,
        ]);

        $user->assignRole($request->role);
        $user->role = $request->role;
        $user->save();

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario \"{$user->name}\" creado con rol " . ucfirst($request->role) . ".");
    }

    public function show(User $usuario)
    {
        $usuario->load('roles.permissions');
        return view('usuarios.show', compact('usuario'));
    }

    public function edit(User $usuario)
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        $sucursales = Sucursal::orderBy('nombre')->get();
        $usuario->load('roles.permissions');
        return view('usuarios.edit', compact('usuario', 'roles', 'sucursales'));
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuario->id)],
            'password'    => 'nullable|string|min:6|confirmed',
            'role'        => 'required|string|exists:roles,name',
            'sucursal_id' => 'nullable|exists:sucursales,id',
        ], [
            'name.required'      => 'El nombre es obligatorio.',
            'email.required'     => 'El correo es obligatorio.',
            'email.email'        => 'El correo no es válido.',
            'email.unique'       => 'Ya existe otro usuario con ese correo.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $cambiaRol = $usuario->roles->pluck('name')->first() !== $request->role;

        $usuario->name        = $request->name;
        $usuario->email       = $request->email;
        $usuario->role        = $request->role;
        $usuario->sucursal_id = $request->sucursal_id;

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        if ($cambiaRol) {
            $usuario->syncRoles([$request->role]);
        }

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario \"{$usuario->name}\" actualizado.");
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        if ($usuario->hasRole('admin') && User::role('admin')->count() <= 1) {
            return back()->with('error', 'No puedes eliminar al único administrador del sistema.');
        }

        $nombre = $usuario->name;
        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario \"{$nombre}\" eliminado.");
    }
}
