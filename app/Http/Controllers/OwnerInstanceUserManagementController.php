<?php

namespace App\Http\Controllers;

use App\Mail\UserCreatedNotification;
use App\Models\BusinessInstance;
use App\Models\InstanceRole;
use App\Models\Modulo;
use App\Models\User;
use App\Services\PlanLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class OwnerInstanceUserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:owner']);
    }

    private function logOwnerAction(string $action, string $description, ?array $oldValues = null, ?array $newValues = null, ?Model $model = null): void
    {
        try {
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'model_type' => $model ? get_class($model) : null,
                'model_id' => $model?->id,
                'description' => $description,
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'tenant_id' => null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to log owner action: ' . $e->getMessage());
        }
    }

    // ─── Usuarios de Instancia ──────────────────────────────────────

    public function instanceUserCreate($id): \Illuminate\View\View
    {
        $instance = BusinessInstance::with('businessType')->findOrFail($id);
        $instanceRoles = InstanceRole::where('business_instance_id', $instance->id)->orderBy('name')->get();
        return view('owner.instances.users.create', compact('instance', 'instanceRoles'));
    }

    public function instanceUserStore(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $instance = BusinessInstance::with('businessType')->findOrFail($id);

        $limitCheck = app(PlanLimitService::class)->verificar($instance, 'usuario');
        if (! $limitCheck['ok']) {
            return back()->withInput()->with('error', $limitCheck['mensaje']);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:12|confirmed',
            'instance_role_id' => 'nullable|exists:instance_roles,id',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin-business',
            'business_type_id' => $instance->businessType?->id,
            'business_instance_id' => $instance->id,
            'instance_role_id' => $data['instance_role_id'] ?? null,
            'sucursal_id' => null,
        ]);

        $user->assignRole('admin-business');

        // Do NOT send plaintext password. Create a reset token so the user can set their own password.
        $token = Str::random(60);
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        try {
            Mail::to($user->email)->send(new UserCreatedNotification($user, $token));
        } catch (\Exception $e) {
            Log::warning('Failed to send welcome email, token stored', [
                'user_id' => $user->id,
                'email' => $user->email,
                'instance_id' => $instance->id,
                'error' => $e->getMessage(),
            ]);
        }

        Log::info("Usuario {$user->name} creado, se envió enlace de bienvenida", [
            'user_id' => $user->id,
            'instance_id' => $instance->id,
        ]);

        $this->logOwnerAction('USER_CREATE', "Usuario '{$user->name}' creado para instancia '{$instance->nombre}'. Se envió enlace de bienvenida para establecer contraseña.", null, ['user_id' => $user->id], $instance);

        return redirect()->route('owner.instances.show', $instance)
            ->with('success', "Usuario {$user->name} creado correctamente para {$instance->nombre}.");
    }

    public function instanceUserEdit($id, $userId): \Illuminate\View\View
    {
        $instance = BusinessInstance::findOrFail($id);
        $user = User::where('business_instance_id', $instance->id)->findOrFail($userId);
        $instanceRoles = InstanceRole::where('business_instance_id', $instance->id)->orderBy('name')->get();

        return view('owner.instances.users.edit', compact('instance', 'user', 'instanceRoles'));
    }

    public function instanceUserUpdate(Request $request, $id, $userId): \Illuminate\Http\RedirectResponse
    {
        $instance = BusinessInstance::findOrFail($id);
        $user = User::where('business_instance_id', $instance->id)->findOrFail($userId);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:12|confirmed',
            'instance_role_id' => 'nullable|exists:instance_roles,id',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->instance_role_id = $data['instance_role_id'] ?? null;

        $passwordChanged = false;
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
            $passwordChanged = true;
        }

        $user->save();

        if ($passwordChanged) {
            // Do NOT email the plaintext password. Instead, suggest the user reset via the forgot-password flow.
            try {
                // Create a password reset token so the user can set a new password themselves
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
                    'instance_id' => $instance->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('owner.instances.show', $instance)
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function instanceUserDestroy($id, $userId): \Illuminate\Http\RedirectResponse
    {
        $instance = BusinessInstance::findOrFail($id);
        $user = User::where('business_instance_id', $instance->id)->findOrFail($userId);

        if ($user->hasRole('owner')) {
            return redirect()->route('owner.instances.show', $instance)
                ->with('error', 'No puedes eliminar al dueño del sistema desde aquí.');
        }

        $name = $user->name;
        $this->logOwnerAction('USER_DELETE', "Usuario '{$name}' eliminado de instancia '{$instance->nombre}'", ['user_id' => $user->id, 'email' => $user->email], null, $instance);
        \App\Models\AuditLog::where('user_id', $user->id)->delete();
        $user->delete();

        return redirect()->route('owner.instances.show', $instance)
            ->with('success', "Usuario {$name} eliminado de {$instance->nombre}.");
    }

    // ─── Instance Roles CRUD ────────────────────────────────────────

    public function instanceRoles($id): \Illuminate\View\View
    {
        $instance = BusinessInstance::findOrFail($id);
        $roles = InstanceRole::where('business_instance_id', $instance->id)
            ->withCount('users')
            ->orderBy('name')
            ->get();
        return view('owner.instances.roles.index', compact('instance', 'roles'));
    }

    public function instanceRolesCreate($id): \Illuminate\View\View
    {
        $instance = BusinessInstance::findOrFail($id);
        $modulos = Modulo::allActive()->groupBy('categoria');
        $totalModulos = Modulo::allActive()->count();
        return view('owner.instances.roles.create', compact('instance', 'modulos', 'totalModulos'));
    }

    public function instanceRolesStore(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $instance = BusinessInstance::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'modulos' => 'nullable|array',
            'modulos.*' => 'string|exists:modulos,key',
        ]);

        $existing = InstanceRole::where('business_instance_id', $instance->id)
            ->where('name', $data['name'])->exists();
        if ($existing) {
            return back()->withInput()->with('error', 'Ya existe un rol con ese nombre en esta instancia.');
        }

        $role = InstanceRole::create([
            'business_instance_id' => $instance->id,
            'name' => $data['name'],
        ]);

        if (!empty($data['modulos'])) {
            $role->syncModules($data['modulos']);
        }

        $this->logOwnerAction('ROLE_CREATE', "Rol '{$role->name}' creado para instancia '{$instance->nombre}'", null, ['role_id' => $role->id], $instance);

        return redirect()->route('owner.instances.roles', $instance)
            ->with('success', "Rol '{$role->name}' creado correctamente.");
    }

    public function instanceRolesEdit($id, $roleId): \Illuminate\View\View
    {
        $instance = BusinessInstance::findOrFail($id);
        $role = InstanceRole::where('business_instance_id', $instance->id)
            ->with('modules')->findOrFail($roleId);
        $modulos = Modulo::allActive()->groupBy('categoria');

        // Extraer módulos de contabilidad y crear categoría propia
        $modsContabilidad = ['ncf','ecf','secuencias-ecf','certificados-digitales','libros-ventas','libros-compras','reportes-retenciones','reportes-fiscales','reportes-resumen','formulario-14-14'];
        $contabilidadMods = collect();
        foreach ($modsContabilidad as $key) {
            $modulos->each(function ($items) use ($key, &$contabilidadMods) {
                $found = $items->firstWhere('key', $key);
                if ($found) {
                    $contabilidadMods->push($found);
                }
            });
        }
        // Remover de categorías originales
        $modulos = $modulos->map(function ($items) use ($modsContabilidad) {
            return $items->reject(fn($m) => in_array($m->key, $modsContabilidad));
        })->filter(fn($items) => $items->isNotEmpty());
        if ($contabilidadMods->isNotEmpty()) {
            $modulos->put('contabilidad', $contabilidadMods);
        }

        $totalModulos = Modulo::allActive()->count();
        $selectedModulos = $role->modules->where('is_visible', true)->pluck('modulo_key')->toArray();

        // Sort categories by defined priority order, with contabilidad always appearing early
        $priorityMap = [
            'core'           =>  0,
            'operaciones'    =>  1,
            'clientes'       =>  2,
            'organizacion'   =>  3,
            'restaurante'    =>  5,
            'alquileres'     =>  6,
            'tattoo'         =>  7,
            'climatizacion'  =>  8,
            'tecnologia'     =>  9,
            'arte'           => 10,
            'contabilidad'   => 11,
            'delivery'       => 12,
            'reportes'       => 13,
            'sistema'        => 14,
            'configuracion'  => 15,
        ];

        $sorted = $modulos->sort(function ($itemsA, $itemsB) use ($priorityMap) {
            $catA = $itemsA->first()?->categoria ?? 'z';
            $catB = $itemsB->first()?->categoria ?? 'z';
            $prioA = $priorityMap[$catA] ?? 20;
            $prioB = $priorityMap[$catB] ?? 20;
            return $prioA <=> $prioB;
        });

        $modulos = $sorted;

        return view('owner.instances.roles.edit', compact('instance', 'role', 'modulos', 'totalModulos', 'selectedModulos'));
    }

    public function instanceRolesUpdate(Request $request, $id, $roleId): \Illuminate\Http\RedirectResponse
    {
        $instance = BusinessInstance::findOrFail($id);
        $role = InstanceRole::where('business_instance_id', $instance->id)
            ->findOrFail($roleId);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'modulos' => 'nullable|array',
            'modulos.*' => 'string|exists:modulos,key',
        ]);

        $existing = InstanceRole::where('business_instance_id', $instance->id)
            ->where('name', $data['name'])
            ->where('id', '!=', $role->id)->exists();
        if ($existing) {
            return back()->withInput()->with('error', 'Ya existe otro rol con ese nombre en esta instancia.');
        }

        $role->update(['name' => $data['name']]);

        if (!empty($data['modulos'])) {
            $role->syncModules($data['modulos']);
        } else {
            $role->modules()->delete();
        }

        return redirect()->route('owner.instances.roles', $instance)
            ->with('success', "Rol '{$role->name}' actualizado correctamente.");
    }

    public function instanceRolesDestroy($id, $roleId): \Illuminate\Http\RedirectResponse
    {
        $instance = BusinessInstance::findOrFail($id);
        $role = InstanceRole::where('business_instance_id', $instance->id)
            ->findOrFail($roleId);

        if ($role->users()->count() > 0) {
            return back()->with('error', 'No puedes eliminar un rol que tiene usuarios asignados.');
        }

        $name = $role->name;
        $this->logOwnerAction('ROLE_DELETE', "Rol '{$name}' eliminado de instancia '{$instance->nombre}'", ['role_id' => $role->id], null, $instance);
        $role->delete();

        return redirect()->route('owner.instances.roles', $instance)
            ->with('success', "Rol '{$name}' eliminado correctamente.");
    }
}
