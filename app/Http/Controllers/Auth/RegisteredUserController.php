<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\NuevaSolicitudInstancia;
use App\Models\BusinessInstance;
use App\Models\BusinessType;
use App\Models\InstanceRole;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $businessTypes = BusinessType::where('activo', true)->orderBy('nombre')->get();

        return view('auth.register', compact('businessTypes'));
    }

    /**
     * Handle an incoming registration request.
     *
     * Crea el usuario administrador, su instancia de negocio en estado pendiente
     * de aprobación y notifica a todos los owners para su validación.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'negocio_nombre' => ['required', 'string', 'max:255'],
            'business_type_id' => ['required', 'exists:business_types,id'],
            'rnc' => ['required', 'string', 'max:20', 'unique:business_instances,rnc'],
            'telefono' => ['required', 'string', 'max:50'],
            'direccion' => ['required', 'string', 'max:500'],
        ]);

        $businessType = BusinessType::where('activo', true)->find($data['business_type_id']);
        if (! $businessType) {
            return back()->withInput()->with('error', 'El tipo de negocio seleccionado no está disponible.');
        }

        $instance = null;
        $user = null;

        try {
            DB::transaction(function () use ($data, $businessType, &$instance, &$user) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'role' => 'admin-business',
                    'business_type_id' => $businessType->id,
                ]);
                $user->assignRole('admin-business');

                $instance = BusinessInstance::create([
                    'nombre' => $data['negocio_nombre'],
                    'slug' => $this->uniqueSlug($data['negocio_nombre']),
                    'rnc' => $data['rnc'],
                    'email' => $data['email'],
                    'telefono' => $data['telefono'],
                    'direccion' => $data['direccion'],
                    'business_type_id' => $businessType->id,
                    'owner_user_id' => $user->id,
                    'owner_email' => $data['email'],
                    'owner_nombre' => $data['name'],
                    'activo' => true,
                    'setup_completed' => false,
                    'aprobado' => false,
                    'configuracion' => [],
                ]);

                $user->update([
                    'business_instance_id' => $instance->id,
                ]);

                $adminRole = InstanceRole::create([
                    'business_instance_id' => $instance->id,
                    'name' => 'admin',
                    'guard_name' => 'instance',
                ]);

                $modulos = BusinessType::getModulosVisibles($businessType->slug);
                $adminRole->syncModules($modulos);

                $user->update([
                    'instance_role_id' => $adminRole->id,
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Error al registrar instancia por autoservicio', [
                'email' => $data['email'],
                'negocio' => $data['negocio_nombre'],
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'No se pudo completar el registro. Intente nuevamente.');
        }

        // Notificar a todos los owners/root para que revisen la solicitud
        try {
            foreach (User::role(['owner', 'root'])->cursor() as $owner) {
                Mail::to($owner->email)->send(new NuevaSolicitudInstancia($instance, $user));
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify owners about new instance request', [
                'instance_id' => $instance->id,
                'error' => $e->getMessage(),
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        session(['business_instance_id' => $instance->id]);
        session(['business_type_slug' => $businessType->slug]);

        return redirect()->route('solicitud.pendiente');
    }

    /**
     * Genera un slug único para la instancia.
     */
    protected function uniqueSlug(string $nombre): string
    {
        $base = Str::slug($nombre) ?: 'negocio';
        $slug = $base;
        $i = 2;
        while (BusinessInstance::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
