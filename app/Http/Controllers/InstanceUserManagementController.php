<?php

namespace App\Http\Controllers;

use App\Mail\UserCreatedNotification;
use App\Models\BusinessInstance;
use App\Models\InstanceRole;
use App\Models\User;
use App\Services\PlanLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InstanceUserManagementController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->business_instance_id) {
            return redirect()->route('dashboard')->with('error', 'No tienes una instancia asignada.');
        }

        $instance = BusinessInstance::where('id', $user->business_instance_id)->first();

        $query = User::where('business_instance_id', $user->business_instance_id)
            ->with('instanceRole', 'sucursal');

        if ($request->filled('buscar')) {
            $search = $request->buscar;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('instance_role')) {
            $query->where('instance_role_id', $request->instance_role);
        }

        $usuarios = $query->orderBy('name')->paginate(12)->withQueryString();

        $allUsers = User::where('business_instance_id', $user->business_instance_id)->get();
        $stats = [
            'total' => $allUsers->count(),
            'sin_rol' => $allUsers->filter(fn($u) => $u->instance_role_id === null)->count(),
        ];

        foreach ($allUsers as $u) {
            if ($u->instance_role_id && ! isset($stats["rol_{$u->instance_role_id}_count"])) {
                $stats["rol_{$u->instance_role_id}_count"] = 0;
            }
        }
        foreach ($allUsers as $u) {
            if ($u->instance_role_id) {
                $stats["rol_{$u->instance_role_id}_count"]++;
            }
        }

        $instanceRoles = InstanceRole::where('business_instance_id', $user->business_instance_id)
            ->orderBy('name')
            ->get();

        return view('instance.users.index', compact('usuarios', 'stats', 'instanceRoles', 'instance'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        if (! $user->business_instance_id) {
            return redirect()->route('dashboard')->with('error', 'No tienes una instancia asignada.');
        }

        $instance = BusinessInstance::where('id', $user->business_instance_id)->first();

        $instanceRoles = InstanceRole::where('business_instance_id', $user->business_instance_id)
            ->where('name', '!=', 'admin')
            ->orderBy('name')
            ->get();

        return view('instance.users.create', compact('instance', 'instanceRoles'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (! $user->business_instance_id) {
            return redirect()->route('dashboard')->with('error', 'No tienes una instancia asignada.');
        }

        $instance = BusinessInstance::where('id', $user->business_instance_id)->first();

        $limitCheck = app(PlanLimitService::class)->verificar($instance, 'usuario');
        if (! $limitCheck['ok']) {
            return back()->withInput()->with('error', $limitCheck['mensaje']);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:12|confirmed',
            'instance_role_id' => 'required|exists:instance_roles,id',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo no es válido.',
            'email.unique' => 'Ya existe un usuario con ese correo.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 12 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'instance_role_id.required' => 'Debes asignar un rol de instancia al usuario.',
        ]);

        $instanceRoleId = $data['instance_role_id'];
        $instanceRoleExists = InstanceRole::where('id', $instanceRoleId)
            ->where('business_instance_id', $user->business_instance_id)
            ->exists();
        if (! $instanceRoleExists) {
            return back()->withInput()->with('error', 'El rol seleccionado no existe en esta instancia.');
        }

        $businessType = $instance?->businessType;

        $newUser = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => null,
            'business_type_id' => $businessType?->id,
            'business_instance_id' => $user->business_instance_id,
            'instance_role_id' => $instanceRoleId,
            'sucursal_id' => null,
        ]);

        $token = Str::random(60);
        DB::table('password_reset_tokens')->insert([
            'email' => $newUser->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        try {
            Mail::to($newUser->email)->send(new UserCreatedNotification($newUser, $token));
        } catch (\Exception $e) {
            Log::warning('Failed to send welcome email', [
                'user_id' => $newUser->id,
                'email' => $newUser->email,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('instance.users.index', $user->business_instance_id)
            ->with('success', "Usuario \"{$newUser->name}\" creado correctamente.");
    }

    public function show(Request $request, $instanceId, User $user)
    {
        if ($user->business_instance_id !== $request->route('instanceId')) {
            return back()->with('error', 'No tienes permiso para ver este usuario.');
        }

        $instance = BusinessInstance::where('id', $instanceId)->first();

        return view('instance.users.show', compact('user', 'instance'));
    }

    public function edit(Request $request, $instanceId, User $user)
    {
        if ($user->business_instance_id !== $request->route('instanceId')) {
            return back()->with('error', 'No tienes permiso para editar este usuario.');
        }

        $instance = BusinessInstance::where('id', $instanceId)->first();

        $instanceRoles = InstanceRole::where('business_instance_id', $user->business_instance_id)
            ->where('name', '!=', 'admin')
            ->orderBy('name')
            ->get();

        return view('instance.users.edit', compact('user', 'instance', 'instanceRoles'));
    }

    public function update(Request $request, $instanceId, User $user)
    {
        if ($user->business_instance_id !== $request->route('instanceId')) {
            return back()->with('error', 'No tienes permiso para editar este usuario.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:12|confirmed',
            'instance_role_id' => 'nullable|exists:instance_roles,id',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo no es válido.',
            'email.unique' => 'Ya existe otro usuario con ese correo.',
            'password.min' => 'La contraseña debe tener al menos 12 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $instanceRoleId = $data['instance_role_id'] ?? null;
        if ($instanceRoleId) {
            $instanceRoleExists = InstanceRole::where('id', $instanceRoleId)
                ->where('business_instance_id', $user->business_instance_id)
                ->exists();
            if (! $instanceRoleExists) {
                return back()->withInput()->with('error', 'El rol seleccionado no existe en esta instancia.');
            }
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->instance_role_id = $instanceRoleId;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            try {
                $token = Str::random(60);
                DB::table('password_reset_tokens')->where('email', $user->email)->delete();
                DB::table('password_reset_tokens')->insert([
                    'email' => $user->email,
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]);
                Mail::to($user->email)->send(new UserCreatedNotification($user, $token));
            } catch (\Exception $e) {
                Log::warning('Failed to send password reset email', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $user->save();

        return redirect()->route('instance.users.index', $user->business_instance_id)
            ->with('success', "Usuario \"{$user->name}\" actualizado correctamente.");
    }

    public function destroy(Request $request, $instanceId, User $user)
    {
        if ($user->business_instance_id !== $instanceId) {
            return back()->with('error', 'No tienes permiso para eliminar este usuario.');
        }

        $totalAdminBusiness = User::where('business_instance_id', $instanceId)
            ->where('role', 'admin-business')
            ->count();

        if ($user->role === 'admin-business' && $totalAdminBusiness <= 1) {
            return back()->with('error', 'No puedes eliminar al único administrador de la instancia.');
        }

        $nombre = $user->name;
        $user->delete();

        return redirect()->route('instance.users.index', $instanceId)
            ->with('success', "Usuario \"{$nombre}\" eliminado correctamente.");
    }
}
