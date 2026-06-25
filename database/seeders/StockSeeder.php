<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Almacen;
use App\Models\AlmacenMovimiento;
use App\Models\User;

class StockSeeder extends Seeder
{
    public function run()
    {
        $admin = User::first();
        $tenantId = $admin?->business_instance_id;

        $almacen = Almacen::where('nombre', 'PRINCIPAL')->first();
        if (!$almacen) {
            $almacen = Almacen::create([
                'tenant_id' => $tenantId,
                'nombre' => 'PRINCIPAL',
                'ubicacion' => 'Sede Central',
            ]);
        } elseif (!$almacen->tenant_id && $tenantId) {
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
