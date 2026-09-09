<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GatoNegroMenuSeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            'Artículos destacados' => [
                'Street Cat Nachos' => ['precio' => 600, 'descripcion' => '#1 de tus favoritos'],
                'Jungle Smash' => ['precio' => 605.50, 'descripcion' => '#2 de tus favoritos, antes RD$865, -30%', 'precio_compra' => 450],
                'Alitas' => ['precio' => 560, 'descripcion' => '#3 de tus favoritos, antes RD$700, -20%', 'precio_compra' => 350],
                'Quesadillas' => ['precio' => 505, 'descripcion' => null],
                'Doble Smash Burger' => ['precio' => 600, 'descripcion' => 'antes RD$750, -20%', 'precio_compra' => 420],
                'Trío de Empanadas' => ['precio' => 416, 'descripcion' => null],
                'Carne Salada 12 oz' => ['precio' => 512, 'descripcion' => null],
                'Jugo Natural de Fresa' => ['precio' => 190, 'descripcion' => null, 'tipo_producto' => 'consumible', 'linea_negocio' => 'bebidas'],
                'Jugo Natural de Chinola' => ['precio' => 190, 'descripcion' => null, 'tipo_producto' => 'consumible', 'linea_negocio' => 'bebidas'],
                'Jungle Cat Fries' => ['precio' => 635, 'descripcion' => null],
                'Tabla de Carnes' => ['precio' => 1220, 'descripcion' => null],
                'El Charro Smash' => ['precio' => 710, 'descripcion' => null],
                'Mozzarella Sticks' => ['precio' => 416, 'descripcion' => null],
                'Dip de Espinaca' => ['precio' => 505, 'descripcion' => null],
                'Trío de Quipes' => ['precio' => 416, 'descripcion' => null],
            ],
            'Ahorros exclusivos' => [
                'Doble Smash Burger' => ['precio' => 600, 'descripcion' => 'Smash clásico con spicy mayo, pepinillo, 2 carnes smash y queso americano. Papas incluidas. -20%', 'precio_compra' => 420],
                'Alitas' => ['precio' => 560, 'descripcion' => '8 Unidades de deliciosas y suaves alitas en el sabor de tu preferencia. -20%', 'precio_compra' => 350],
                'Jungle Smash' => ['precio' => 605.50, 'descripcion' => 'Smash burger en salsa de hongos, tocineta, cebolla caramelizada, 2 carnes smash y queso mozzarella. Papas incluidas. -30%', 'precio_compra' => 450],
            ],
            'Combos' => [
                'Combo Mundial' => ['precio' => 900, 'descripcion' => 'Hamburguesa Doble smash (con papas), mozzarella sticks y una coca cola.', 'precio_compra' => 600],
            ],
            'Gato Snacks' => [
                'Trío de Quipes' => ['precio' => 416, 'descripcion' => 'Deliciosos quipes rellenos de carne de res.', 'precio_compra' => 280],
            ],
            'Especial del Gato' => [],
            'Smash it' => [],
            'Bebidas' => [],
        ];

        $createdProducts = 0;
        foreach ($cats as $catName => $products) {
            $catId = DB::table('categorias')->where('tenant_id', 5)->where('nombre', $catName)->value('id');
            if (!$catId) {
                $catId = DB::table('categorias')->insertGetId([
                    'tenant_id' => 5,
                    'nombre' => $catName,
                    'activa' => 1,
                    'orden' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->command->info("Creada categoría: '$catName' (ID: $catId)");
            } else {
                $this->command->info("Categoría '$catName' ya existe (ID: $catId)");
            }
            
            foreach ($products as $prodName => $data) {
                $existing = DB::table('productos')->where('tenant_id', 5)->where('nombre', $prodName)->first();
                if ($existing) {
                    $updateData = [
                        'precio' => $data['precio'],
                        'descripcion' => $data['descripcion'] ?? null,
                        'categoria_id' => $catId,
                        'tipo_producto' => $data['tipo_producto'] ?? 'fisico',
                        'linea_negocio' => $data['linea_negocio'] ?? 'alimentos',
                        'activo' => 1,
                        'updated_at' => now(),
                    ];
                    DB::table('productos')
                        ->where('id', $existing->id)
                        ->update($updateData);
                    
                    if (isset($data['precio_compra'])) {
                        DB::table('productos')
                            ->where('id', $existing->id)
                            ->update(['precio_compra' => $data['precio_compra'], 'updated_at' => now()]);
                    }
                    $this->command->info("  => Actualizado: '$prodName' => '$catName' (RD{$data['precio']})");
                } else {
                    DB::table('productos')->insert([
                        'tenant_id' => 5,
                        'nombre' => $prodName,
                        'descripcion' => $data['descripcion'] ?? null,
                        'precio' => $data['precio'],
                        'precio_compra' => $data['precio_compra'] ?? $data['precio'] * 0.6,
                        'stock' => 0,
                        'categoria_id' => $catId,
                        'tipo_producto' => $data['tipo_producto'] ?? 'fisico',
                        'linea_negocio' => $data['linea_negocio'] ?? 'alimentos',
                        'activo' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $this->command->info("  => Creado: '$prodName' => '$catName' (RD{$data['precio']})");
                }
                $createdProducts++;
            }
        }

        $this->command->newLine();
        $this->command->info("Productos creados/actualizados: $createdProducts");
        $this->command->info('Categorías de tenant 5:');
        $catsAfter = DB::table('categorias')->where('tenant_id', 5)->get();
        foreach ($catsAfter as $c) {
            $prodCount = DB::table('productos')->where('tenant_id', 5)->where('categoria_id', $c->id)->count();
            $this->command->info('  - ' . $c->nombre . ' (' . $prodCount . ' productos)');
        }
        $this->command->info('Total productos tenant 5: ' . DB::table('productos')->where('tenant_id', 5)->count());
    }
}
