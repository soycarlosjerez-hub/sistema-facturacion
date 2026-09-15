<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'ordenes.view',
            'ordenes.view.own',
            'ordenes.create',
            'ordenes.update',
            'ordenes.cancel',
            'ordenes.pay',
            'ordenes.export',
            'kds.view',
            'kds.update',
            'terminales.manage',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Assign to admin
        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $admin->givePermissionTo($permissions);
        }

        // Assign to admin-business
        $adminBusiness = Role::where('name', 'admin-business')->first();
        if ($adminBusiness) {
            $adminBusiness->givePermissionTo($permissions);
        }

        // Assign limited set to gerente
        $gerente = Role::where('name', 'gerente')->first();
        if ($gerente) {
            $gerente->givePermissionTo([
                'ordenes.view',
                'ordenes.view.own',
                'ordenes.create',
                'ordenes.update',
                'ordenes.pay',
                'ordenes.export',
                'kds.view',
                'kds.update',
            ]);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = Role::whereIn('name', ['admin', 'admin-business', 'gerente'])->get();

        foreach ($roles as $role) {
            $role->revokePermissionTo([
                'ordenes.view',
                'ordenes.view.own',
                'ordenes.create',
                'ordenes.update',
                'ordenes.cancel',
                'ordenes.pay',
                'ordenes.export',
                'kds.view',
                'kds.update',
                'terminales.manage',
            ]);
        }

        Permission::whereIn('name', [
            'ordenes.view', 'ordenes.view.own', 'ordenes.create',
            'ordenes.update', 'ordenes.cancel', 'ordenes.pay',
            'ordenes.export', 'kds.view', 'kds.update',
            'terminales.manage',
        ])->delete();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
