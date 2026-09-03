<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WizardStepsFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `wizard_steps` (14 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('wizard_steps');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('wizard_steps')->truncate();

        $rows = [
            ['id' => 1, 'key' => 'parametros', 'module_key' => 'configuracion-general', 'label' => 'Parámetros del Sistema', 'icon' => 'bi-gear', 'required' => 1, 'skipable' => 0, 'entity_class' => 'App\\Models\\SystemSetting', 'orden' => 5, 'created_at' => '2026-07-01 15:04:09', 'updated_at' => '2026-07-01 15:04:09'],
            ['id' => 2, 'key' => 'sucursal', 'module_key' => 'sucursales', 'label' => 'Sucursal', 'icon' => 'bi-building', 'required' => 1, 'skipable' => 0, 'entity_class' => 'App\\Models\\Sucursal', 'orden' => 10, 'created_at' => '2026-07-01 15:04:09', 'updated_at' => '2026-07-01 15:04:09'],
            ['id' => 3, 'key' => 'caja', 'module_key' => 'cajas', 'label' => 'Caja', 'icon' => 'bi-cash-stack', 'required' => 1, 'skipable' => 0, 'entity_class' => 'App\\Models\\Caja', 'orden' => 20, 'created_at' => '2026-07-01 15:04:09', 'updated_at' => '2026-07-01 15:04:09'],
            ['id' => 4, 'key' => 'almacen', 'module_key' => 'almacenes', 'label' => 'Almacén', 'icon' => 'bi-buildings', 'required' => 1, 'skipable' => 0, 'entity_class' => 'App\\Models\\Almacen', 'orden' => 30, 'created_at' => '2026-07-01 15:04:10', 'updated_at' => '2026-07-01 15:04:10'],
            ['id' => 5, 'key' => 'categoria-producto', 'module_key' => 'inventario', 'label' => 'Categoría de Productos', 'icon' => 'bi-tags', 'required' => 0, 'skipable' => 0, 'entity_class' => 'App\\Models\\Categoria', 'orden' => 35, 'created_at' => '2026-07-01 15:04:10', 'updated_at' => '2026-07-01 15:04:10'],
            ['id' => 6, 'key' => 'producto', 'module_key' => 'inventario', 'label' => 'Productos', 'icon' => 'bi-box-seam', 'required' => 1, 'skipable' => 0, 'entity_class' => 'App\\Models\\Producto', 'orden' => 40, 'created_at' => '2026-07-01 15:04:10', 'updated_at' => '2026-07-01 15:04:10'],
            ['id' => 7, 'key' => 'proveedor', 'module_key' => 'proveedores', 'label' => 'Proveedores', 'icon' => 'bi-truck', 'required' => 0, 'skipable' => 0, 'entity_class' => 'App\\Models\\Proveedor', 'orden' => 45, 'created_at' => '2026-07-01 15:04:10', 'updated_at' => '2026-07-01 15:04:10'],
            ['id' => 8, 'key' => 'cliente', 'module_key' => 'clientes', 'label' => 'Clientes', 'icon' => 'bi-people', 'required' => 0, 'skipable' => 0, 'entity_class' => 'App\\Models\\Cliente', 'orden' => 48, 'created_at' => '2026-07-01 15:04:10', 'updated_at' => '2026-07-01 15:04:10'],
            ['id' => 9, 'key' => 'ncf', 'module_key' => 'ncf', 'label' => 'Secuencias NCF', 'icon' => 'bi-receipt-cutoff', 'required' => 0, 'skipable' => 0, 'entity_class' => 'App\\Models\\NcfSequence', 'orden' => 50, 'created_at' => '2026-07-01 15:04:10', 'updated_at' => '2026-07-01 15:04:10'],
            ['id' => 10, 'key' => 'ubicacion-mesa', 'module_key' => 'restaurante', 'label' => 'Ubicación de Mesas', 'icon' => 'bi-geo-alt', 'required' => 1, 'skipable' => 0, 'entity_class' => 'App\\Models\\MesaUbicacion', 'orden' => 60, 'created_at' => '2026-07-01 15:04:10', 'updated_at' => '2026-07-01 15:04:10'],
            ['id' => 11, 'key' => 'categoria-mesa', 'module_key' => 'restaurante', 'label' => 'Categoría de Mesa', 'icon' => 'bi-tags', 'required' => 1, 'skipable' => 0, 'entity_class' => 'App\\Models\\MesaCategoria', 'orden' => 70, 'created_at' => '2026-07-01 15:04:10', 'updated_at' => '2026-07-01 15:04:10'],
            ['id' => 12, 'key' => 'mesa', 'module_key' => 'restaurante', 'label' => 'Mesas', 'icon' => 'bi-grid-3x3-gap', 'required' => 0, 'skipable' => 0, 'entity_class' => 'App\\Models\\Mesa', 'orden' => 80, 'created_at' => '2026-07-01 15:04:10', 'updated_at' => '2026-07-01 15:04:10'],
            ['id' => 13, 'key' => 'servicio-lavado', 'module_key' => 'lavadero', 'label' => 'Servicio de Lavado', 'icon' => 'bi-card-checklist', 'required' => 1, 'skipable' => 0, 'entity_class' => 'App\\Models\\Producto', 'orden' => 90, 'created_at' => '2026-07-01 15:04:10', 'updated_at' => '2026-07-01 15:04:10'],
            ['id' => 14, 'key' => 'lavador', 'module_key' => 'lavadero', 'label' => 'Lavadores', 'icon' => 'bi-people', 'required' => 0, 'skipable' => 0, 'entity_class' => 'App\\Models\\Lavador', 'orden' => 100, 'created_at' => '2026-07-01 15:04:10', 'updated_at' => '2026-07-01 15:04:10'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('wizard_steps')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
