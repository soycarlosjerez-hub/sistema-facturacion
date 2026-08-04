<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MecanicaCategoriasSeeder extends Seeder
{
    public function run(): void
    {
        // Asegurar que el tipo de negocio 'mecanica' exista (BusinessTypeSeeder debe correr primero)
        $mecanicaType = BusinessType::where('slug', 'mecanica')->first();

        if (!$mecanicaType) {
            $this->command->warn("BusinessType 'mecanica' no encontrado. Correr BusinessTypeSeeder primero.");
            return;
        }

        $categorias = [
            [
                'nombre'      => 'Aceites y Lubricantes',
                'descripcion'  => 'Aceites de motor por viscosidad y lubricantes automotrices',
                'color'        => '#f59e0b',
                'icono'        => 'bi-droplet-half',
                'orden'        => 1,
                'configuracion'=> null,
            ],
            [
                'nombre'      => 'Filtros',
                'descripcion'  => 'Filtros de aceite, aire y gasolina',
                'color'        => '#6b7280',
                'icono'        => 'bi-funnel',
                'orden'        => 2,
                'configuracion'=> null,
            ],
            [
                'nombre'      => 'Servicios de Mecanica',
                'descripcion'  => 'Servicios de mantenimiento: cambio de aceite y cambio de filtro',
                'color'        => '#3b82f6',
                'icono'        => 'bi-tools',
                'orden'        => 3,
                'configuracion'=> null,
            ],
            [
                'nombre'      => 'Otros Repuestos',
                'descripcion'  => 'Otros repuestos automotrices generales',
                'color'        => '#22c55e',
                'icono'        => 'bi-grid',
                'orden'        => 4,
                'configuracion'=> null,
            ],
        ];

        foreach ($categorias as $catData) {
            // Buscar categoria existente (por nombre y tenant null) para evitar duplicados
            $category = Category::where('nombre', $catData['nombre'])
                ->whereNull('tenant_id')
                ->first();

            if (!$category) {
                $category = Category::create(array_merge($catData, [
                    'tenant_id' => null,
                    'activa'    => true,
                ]));
                $this->command->info("Categoria creada: {$category->nombre} (ID {$category->id})");
            } else {
                $this->command->info("Categoria ya existe: {$category->nombre} (ID {$category->id})");
            }

            // Vincular al BusinessType 'mecanica' via categorizables si no existe ya el link
            $exists = DB::table('categorizables')
                ->where('category_id', $category->id)
                ->where('categorizable_type', BusinessType::class)
                ->where('categorizable_id', $mecanicaType->id)
                ->exists();

            if (!$exists) {
                DB::table('categorizables')->insert([
                    'category_id'          => $category->id,
                    'categorizable_type'   => BusinessType::class,
                    'categorizable_id'     => $mecanicaType->id,
                    'configuracion'        => null,
                    'soft_delete_enabled'  => true,
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ]);
                $this->command->info("  -> vinculada a BusinessType 'mecanica'");
            }
        }

        $this->command->info('MecanicaCategoriasSeeder finalizado.');
    }
}
