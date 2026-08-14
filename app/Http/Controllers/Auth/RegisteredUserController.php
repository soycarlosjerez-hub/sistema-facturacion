<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\NuevaInstanciaRegistrada;
use App\Models\BusinessInstance;
use App\Models\BusinessType;
use App\Models\InstanceRole;
use App\Models\PagoInstancia;
use App\Models\Plan;
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
        $plans = Plan::active();

        return view('auth.register', compact('businessTypes', 'plans'));
    }

    /**
     * Handle an incoming registration request.
     *
     * Crea el usuario administrador, su instancia de negocio, el plan, el pago
     * pendiente de implementación, el rol de instancia 'admin' con los módulos
     * del tipo de negocio y notifica a todos los owners para su asignación.
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
            'plan_id' => ['required', 'exists:plans,id'],
        ]);

        $businessType = BusinessType::where('activo', true)->find($data['business_type_id']);
        if (! $businessType) {
            return back()->withInput()->with('error', 'El tipo de negocio seleccionado no está disponible.');
        }

        $plan = Plan::active()->firstWhere('id', $data['plan_id']);
        if (! $plan) {
            return back()->withInput()->with('error', 'El plan seleccionado no está disponible.');
        }

        $instance = null;
        $user = null;

        try {
            DB::transaction(function () use ($data, $businessType, $plan, &$instance, &$user) {
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
                    'plan_id' => $plan->id,
                    'owner_user_id' => $user->id,
                    'owner_email' => $data['email'],
                    'owner_nombre' => $data['name'],
                    'costo_mensual' => $plan->precio_mensual,
                    'fecha_vencimiento' => now()->addMonth(),
                    'activo' => true,
                    'setup_completed' => false,
                    'configuracion' => [],
                ]);

                $user->update([
                    'business_instance_id' => $instance->id,
                ]);

                $primerPago = $plan->costoImplementacionEfectivo();
                PagoInstancia::create([
                    'business_instance_id' => $instance->id,
                    'plan_id' => $plan->id,
                    'monto' => $primerPago,
                    'mes_pagado' => now()->startOfMonth(),
                    'fecha_pago' => now(),
                    'metodo_pago' => 'transferencia',
                    'referencia_externa' => 'REGISTRO-AUTOSERVICIO',
                    'estado_pago' => 'pendiente',
                    'notas' => 'Registro autoservicio — pendiente de confirmación (implementación + primer mes)',
                    'registrado_por' => $user->id,
                ]);

                $adminRole = InstanceRole::create([
                    'business_instance_id' => $instance->id,
                    'name' => 'admin',
                    'guard_name' => 'instance',
                ]);

                $modulos = BusinessType::getModulosVisibles($businessType->slug);
                $planModulos = $plan->modulosPermitidos();
                if ($planModulos !== []) {
                    $modulos = array_values(array_intersect($modulos, $planModulos));
                }
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

        // Notificar a todos los owners para que asignen el owner de la nueva instancia
        try {
            foreach (User::role('owner')->cursor() as $owner) {
                Mail::to($owner->email)->send(new NuevaInstanciaRegistrada($instance, $user));
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify owners about new instance', [
                'instance_id' => $instance->id,
                'error' => $e->getMessage(),
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        session(['business_instance_id' => $instance->id]);
        session(['business_type_slug' => $businessType->slug]);

        return redirect(route('dashboard', absolute: false));
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
