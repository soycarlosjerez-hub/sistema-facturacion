<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sucursal;
use App\Models\BusinessType;
use Spatie\Permission\Models\Role;
use App\Services\UserBusinessService;
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
            'supervisor' => $allUsers->filter(fn($u) => $u->hasRole('supervisor'))->count(),
            'administrativo' => $allUsers->filter(fn($u) => $u->hasRole('administrativo'))->count(),
            'mesero'    => $allUsers->filter(fn($u) => $u->hasRole('mesero'))->count(),
            'cocinero'  => $allUsers->filter(fn($u) => $u->hasRole('cocinero'))->count(),
            'delivery'  => $allUsers->filter(fn($u) => $u->hasRole('delivery'))->count(),
            'bartender' => $allUsers->filter(fn($u) => $u->hasRole('bartender'))->count(),
            'lavador'   => $allUsers->filter(fn($u) => $u->hasRole('lavador'))->count(),
            'recepcionista' => $allUsers->filter(fn($u) => $u->hasRole('recepcionista'))->count(),
            'inspector' => $allUsers->filter(fn($u) => $u->hasRole('inspector'))->count(),
            'cajero'    => $allUsers->filter(fn($u) => $u->hasRole('cajero'))->count(),
            'reponedor' => $allUsers->filter(fn($u) => $u->hasRole('reponedor'))->count(),
            'despachador' => $allUsers->filter(fn($u) => $u->hasRole('despachador'))->count(),
            'vendedor-mayorista' => $allUsers->filter(fn($u) => $u->hasRole('vendedor-mayorista'))->count(),
            'consultor' => $allUsers->filter(fn($u) => $u->hasRole('consultor'))->count(),
            'facturador' => $allUsers->filter(fn($u) => $u->hasRole('facturador'))->count(),
            'sin_rol'   => $allUsers->filter(fn($u) => $u->roles->isEmpty())->count(),
        ];

        $rolesExcluidos = auth()->user()->hasRole('owner') ? [] : ['owner', 'root'];
        $roles = Role::withCount('permissions')
            ->when(!empty($rolesExcluidos), fn($q) => $q->whereNotIn('name', $rolesExcluidos))
            ->orderBy('name')
            ->get();

        return view('usuarios.index', compact('usuarios', 'stats', 'roles'));
    }

    public function create()
    {
        $rolesExcluidos = auth()->user()->hasRole('owner') ? [] : ['owner', 'root'];
        $roles = Role::with('permissions')
            ->when(!empty($rolesExcluidos), fn($q) => $q->whereNotIn('name', $rolesExcluidos))
            ->orderBy('name')
            ->get();
        $sucursales = Sucursal::orderBy('nombre')->get();
        // Business type is NOT shown in create form for simplicity
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
            // business_type_id is NOT included in creation for simplicity
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
            // business_type_id is NOT set in creation
        ]);

        $user->assignRole($request->role);
        
        // Sync roles based on business type after creation
        UserBusinessService::syncRolesForUser($user);

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
        if ($usuario->hasRole('owner') && !auth()->user()->hasRole('owner')) {
            return back()->with('error', 'No puedes editar al dueño del sistema.');
        }

        if ($usuario->id === auth()->id() && !auth()->user()->hasRole('owner')) {
            return back()->with('error', 'No puedes editarte a ti mismo.');
        }

        // Solo el owner puede asignar roles de owner/root
        $rolesExcluidos = auth()->user()->hasRole('owner')
            ? []
            : ['owner', 'root'];

        $roles = Role::with('permissions')
            ->when(!empty($rolesExcluidos), fn($q) => $q->whereNotIn('name', $rolesExcluidos))
            ->orderBy('name')
            ->get();

        $sucursales = Sucursal::orderBy('nombre')->get();
        $businessTypes = BusinessType::where('activo', true)->orderBy('orden')->get();
        $usuario->load('roles.permissions');
        return view('usuarios.edit', compact('usuario', 'roles', 'sucursales', 'businessTypes'));
    }

    public function update(Request $request, User $usuario)
    {
        if ($usuario->hasRole('owner') && !auth()->user()->hasRole('owner')) {
            return back()->with('error', 'No puedes modificar al dueño del sistema.');
        }

        if ($usuario->id === auth()->id() && !auth()->user()->hasRole('owner')) {
            return back()->with('error', 'No puedes modificarte a ti mismo.');
        }

        $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuario->id)],
            'password'           => 'nullable|string|min:6|confirmed',
            'role'               => 'required|string|exists:roles,name',
            'sucursal_id'        => 'nullable|exists:sucursales,id',
            'business_type_id'   => 'required|exists:business_types,id', // Required for editing
        ], [
            'name.required'              => 'El nombre es obligatorio.',
            'email.required'             => 'El correo es obligatorio.',
            'email.email'                => 'El correo no es válido.',
            'email.unique'               => 'Ya existe otro usuario con ese correo.',
            'password.min'               => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed'         => 'Las contraseñas no coinciden.',
            'role.required'              => 'Selecciona un rol.',
            'business_type_id.required'  => 'El tipo de negocio es obligatorio.',
        ]);

        $cambiaRol = $usuario->roles->pluck('name')->first() !== $request->role;

        $usuario->name        = $request->name;
        $usuario->email       = $request->email;
        $usuario->role        = $request->role;
        $usuario->sucursal_id = $request->sucursal_id;
        $usuario->business_type_id = $request->business_type_id;

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        if ($cambiaRol) {
            $usuario->syncRoles([$request->role]);
        }

        UserBusinessService::syncRolesForUser($usuario);

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario \"{$usuario->name}\" actualizado.");
    }

    public function destroy(User $usuario)
    {
        if ($usuario->hasRole('owner') && !auth()->user()->hasRole('owner')) {
            return back()->with('error', 'No puedes eliminar al dueño del sistema.');
        }

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
