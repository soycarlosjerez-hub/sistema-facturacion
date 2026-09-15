<?php

namespace Database\Seeders;

use App\Models\Almacen;
use App\Models\AlmacenMovimiento;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    public function run()
    {
        $admin = User::first();
        $tenantId = $admin?->business_instance_id;

        $almacen = Almacen::where('nombre', 'PRINCIPAL')->first();
        if (! $almacen) {
            $almacen = Almacen::create([
                'tenant_id' => $tenantId,
                'nombre' => 'PRINCIPAL',
                'ubicacion' => 'Sede Central',
            ]);
        } elseif (! $almacen->tenant_id && $tenantId) {
            $almacen->update(['tenant_id' => $tenantId]);
        }

        $productos = Producto::all();

        foreach ($productos as $producto) {
            AlmacenMovimiento::create([
                'tenant_id' => $tenantId,
                'producto_id' => $producto->id,
                'almacen_id' => $almacen->id,
                'tipo' => 'entrada',
                'cantidad' => 100,
                'nota' => 'Inventario Inicial',
                'user_id' => $admin->id ?? 1,
            ]);

            // Actualizar el campo stock en la tabla productos (si existe)
            $producto->update(['stock' => 100]);
        }
    }
}
