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
        $almacen = Almacen::where('nombre', 'PRINCIPAL')->first();
        if (!$almacen) {
            $almacen = Almacen::create([
                'nombre' => 'PRINCIPAL',
                'ubicacion' => 'Sede Central',
            ]);
        }

        $admin = User::first();
        $productos = Producto::all();

        foreach ($productos as $producto) {
            // Agregar 100 unidades de cada producto como inventario inicial
            AlmacenMovimiento::create([
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
