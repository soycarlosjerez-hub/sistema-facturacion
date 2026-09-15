<?php

namespace Database\Seeders;

use App\Models\BusinessInstance;
use App\Models\InstanceRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Crea usuarios operativos en instancias activas que faltan.
 *
 * NO borra ni modifica usuarios existentes.
 * Usa firstOrCreate por email — si ya existe, lo actualiza.
 *
 * Uso: php artisan db:seed --class=ActiveInstancesStaffSeeder
 * NO se ejecuta desde DatabaseSeeder (no tocar Full/* en prod).
 */
class ActiveInstancesStaffSeeder extends Seeder
{
    /** @var array<int, array<int, array{name:string,email:string,role:string,instance_role_name:string,sucursal_id?:int}> > */
    private array $users = [];

    public function __construct()
    {
        // ─── Inst. 5 — Gato Negro (lavadero): crear gerente, contador, vendedor ───
        // Cajero y lavadores ya existen; se corrige role y instance_role_id.
        $this->users[5] = [
            [
                'name' => 'Gerente Gato Negro',
                'email' => 'gerente@gato-negro.com',
                'role' => 'gerente',
                'instance_role_name' => 'gerente',
            ],
            [
                'name' => 'Contador Gato Negro',
                'email' => 'contador@gato-negro.com',
                'role' => 'contador',
                'instance_role_name' => 'contador',
            ],
            [
                'name' => 'Vendedor Gato Negro',
                'email' => 'vendedor@gato-negro.com',
                'role' => 'vendedor',
                'instance_role_name' => 'vendedor',
            ],
            // Corregir cajero (existente: role=vendedor → role=cajero, ir#25)
            [
                'name' => 'Cajero Gato Negro',
                'email' => 'cajero@gato-negro.com',
                'role' => 'cajero',
                'instance_role_name' => 'cajero',
            ],
            // Corregir lavadores (existentes: role=vendedor → role=lavador, ir#24)
            [
                'name' => 'Lavador 1 Gato Negro',
                'email' => 'lavador1@gato-negro.com',
                'role' => 'lavador',
                'instance_role_name' => 'lavador',
            ],
            [
                'name' => 'Lavador 2 Gato Negro',
                'email' => 'lavador2@gato-negro.com',
                'role' => 'lavador',
                'instance_role_name' => 'lavador',
            ],
            [
                'name' => 'Lavador 3 Gato Negro',
                'email' => 'lavador3@gato-negro.com',
                'role' => 'lavador',
                'instance_role_name' => 'lavador',
            ],
        ];

        // ─── Inst. 6 — Tecno Plus (tecnología) ───
        $this->users[6] = [
            [
                'name' => 'Cajero Tecno Plus',
                'email' => 'cajero@tecno-plus.com',
                'role' => 'cajero',
                'instance_role_name' => 'cajero',
            ],
            [
                'name' => 'Tecnico Tecno Plus',
                'email' => 'tecnico@tecno-plus.com',
                'role' => 'tecnico',
                'instance_role_name' => 'tecnico',
            ],
            [
                'name' => 'Contador Tecno Plus',
                'email' => 'contador@tecno-plus.com',
                'role' => 'contador',
                'instance_role_name' => 'contador',
            ],
        ];

        // ─── Inst. 7 — Colmado Rodriguez (retail) ───
        $this->users[7] = [
            [
                'name' => 'Cajero Colmado Rodriguez',
                'email' => 'cajero@colmado-rodriguez.com',
                'role' => 'cajero',
                'instance_role_name' => 'cajero',
            ],
            [
                'name' => 'Vendedor Colmado Rodriguez',
                'email' => 'vendedor@colmado-rodriguez.com',
                'role' => 'vendedor',
                'instance_role_name' => 'vendedor',
            ],
            [
                'name' => 'Almacen Colmado Rodriguez',
                'email' => 'almacen@colmado-rodriguez.com',
                'role' => 'almacen',
                'instance_role_name' => 'almacen',
            ],
            [
                'name' => 'Contador Colmado Rodriguez',
                'email' => 'contador@colmado-rodriguez.com',
                'role' => 'contador',
                'instance_role_name' => 'contador',
            ],
        ];

        // ─── Inst. 8 — Armada Shop (mecanica) ───
        $this->users[8] = [
            [
                'name' => 'Cajero Armada Shop',
                'email' => 'cajero@armadas-shop.com',
                'role' => 'cajero',
                'instance_role_name' => 'cajero',
            ],
            [
                'name' => 'Vendedor Armada Shop',
                'email' => 'vendedor@armadas-shop.com',
                'role' => 'vendedor',
                'instance_role_name' => 'vendedor',
            ],
            [
                'name' => 'Mecanico Armada Shop',
                'email' => 'mecanico@armadas-shop.com',
                'role' => 'mecanico',
                'instance_role_name' => 'mecanico',
            ],
            [
                'name' => 'Almacen Armada Shop',
                'email' => 'almacen@armadas-shop.com',
                'role' => 'almacen',
                'instance_role_name' => 'almacen',
            ],
            [
                'name' => 'Contador Armada Shop',
                'email' => 'contador@armadas-shop.com',
                'role' => 'contador',
                'instance_role_name' => 'contador',
            ],
        ];

        // ─── Inst. 9 — Arte (arte_escultura) ───
        $this->users[9] = [
            [
                'name' => 'Vendedor Galeria Arte',
                'email' => 'vendedor-galeria@arte.com',
                'role' => 'vendedor-galeria',
                'instance_role_name' => 'vendedor-galeria',
            ],
            [
                'name' => 'Cajero Arte',
                'email' => 'cajero@arte.com',
                'role' => 'cajero',
                'instance_role_name' => 'cajero',
            ],
            [
                'name' => 'Contador Arte',
                'email' => 'contador@arte.com',
                'role' => 'contador',
                'instance_role_name' => 'contador',
            ],
            [
                'name' => 'Almacen Arte',
                'email' => 'almacen@arte.com',
                'role' => 'almacen',
                'instance_role_name' => 'almacen',
            ],
        ];

        // ─── Inst. 10 — MaganTech (tecnologia) ───
        $this->users[10] = [
            [
                'name' => 'Tecnico MaganTech',
                'email' => 'tecnico@magan-tech.com',
                'role' => 'tecnico',
                'instance_role_name' => 'tecnico',
            ],
            [
                'name' => 'Vendedor Tecnico MaganTech',
                'email' => 'vendedor-tecnico@magan-tech.com',
                'role' => 'vendedor-tecnico',
                'instance_role_name' => 'vendedor-tecnico',
            ],
            [
                'name' => 'Soporte N1 MaganTech',
                'email' => 'soporte-n1@magan-tech.com',
                'role' => 'soporte-n1',
                'instance_role_name' => 'soporte-n1',
            ],
        ];
    }

    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Password para todos los nuevos usuarios (misma convención que RolesAndUsersSeeder).
        $password = Hash::make('Cambiar123');

        foreach ($this->users as $instanceId => $staffList) {
            $instance = BusinessInstance::find($instanceId);
            if (! $instance) {
                $this->command->warn("Instancia #{$instanceId} no existe, saltando.");
                continue;
            }

            $this->command->info("=== Instancia #{$instanceId} ({$instance->nombre}) ===");

            foreach ($staffList as $userData) {
                $user = User::firstOrCreate(
                    ['email' => $userData['email']],
                    [
                        'name' => $userData['name'],
                        'password' => $password,
                        'role' => $userData['role'],
                        'business_type_id' => $instance->business_type_id,
                        'business_instance_id' => $instanceId,
                        'instance_role_id' => null,
                    ]
                );

                // Actualizar campos si ya existía (cambio de role, etc.)
                if ($user->wasRecentlyCreated === false) {
                    $user->role = $userData['role'];
                    $user->business_type_id = $instance->business_type_id;
                    $user->business_instance_id = $instanceId;
                    $user->save();
                }

                // Obtener instance_role_id por name
                $instanceRole = InstanceRole::where('business_instance_id', $instanceId)
                    ->where('name', $userData['instance_role_name'])
                    ->first();

                if ($instanceRole) {
                    User::where('id', $user->id)->update([
                        'instance_role_id' => $instanceRole->id,
                    ]);
                    $user->instance_role_id = $instanceRole->id;
                }

                // Asignar Spatie role
                $user->syncRoles([$userData['role']]);

                $this->command->info("  {$userData['email']} → {$userData['role']} (ir: {$userData['instance_role_name']})");
            }
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Usuarios creados correctamente.');
    }
}
