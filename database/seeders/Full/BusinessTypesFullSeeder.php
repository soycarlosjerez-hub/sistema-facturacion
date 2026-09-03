<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BusinessTypesFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `business_types` (11 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('business_types');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('business_types')->truncate();

        $rows = [
            ['id' => 1, 'slug' => 'restaurante', 'nombre' => 'Restaurante / Bar / Café', 'descripcion' => 'Negocio de comida y bebida con terminal de mesas', 'color' => 'info', 'icon' => 'bi-cup-straw', 'activo' => 1, 'orden' => 1, 'config' => null, 'created_at' => '2026-07-01 15:03:59', 'updated_at' => '2026-07-01 15:03:59'],
            ['id' => 2, 'slug' => 'retail', 'nombre' => 'Colmado / Minimarket / Retail', 'descripcion' => 'Venta al por menor de productos generales', 'color' => 'success', 'icon' => 'bi-cart-plus', 'activo' => 1, 'orden' => 2, 'config' => null, 'created_at' => '2026-07-01 15:04:00', 'updated_at' => '2026-07-01 15:04:00'],
            ['id' => 3, 'slug' => 'mayorista', 'nombre' => 'Mayorista / Distribuidor', 'descripcion' => 'Venta por mayor y distribución de productos', 'color' => 'warning', 'icon' => 'bi-truck', 'activo' => 1, 'orden' => 3, 'config' => null, 'created_at' => '2026-07-01 15:04:00', 'updated_at' => '2026-07-01 15:04:00'],
            ['id' => 4, 'slug' => 'servicios', 'nombre' => 'Servicios Profesionales', 'descripcion' => 'Prestación de servicios profesionales y consultoría', 'color' => 'primary', 'icon' => 'bi-briefcase', 'activo' => 1, 'orden' => 4, 'config' => null, 'created_at' => '2026-07-01 15:04:01', 'updated_at' => '2026-07-01 15:04:01'],
            ['id' => 5, 'slug' => 'lavadero', 'nombre' => 'Lavadero de Carro', 'descripcion' => 'Servicio de lavado y detallado de vehículos', 'color' => 'primary', 'icon' => 'bi-droplet', 'activo' => 1, 'orden' => 5, 'config' => null, 'created_at' => '2026-07-01 15:04:02', 'updated_at' => '2026-07-01 15:04:02'],
            ['id' => 6, 'slug' => 'mixto', 'nombre' => 'Mixto (Restaurante + Retail)', 'descripcion' => 'Negocio que combina restaurante y venta al por menor', 'color' => 'secondary', 'icon' => 'bi-grid', 'activo' => 1, 'orden' => 6, 'config' => null, 'created_at' => '2026-07-01 15:04:03', 'updated_at' => '2026-07-01 15:04:03'],
            ['id' => 9, 'slug' => 'climatizacion', 'nombre' => 'Climatización / HVAC', 'descripcion' => 'Servicios de climatización, aire acondicionado y mantenimiento', 'color' => 'purple', 'icon' => 'bi-wind', 'activo' => 1, 'orden' => 8, 'config' => null, 'created_at' => '2026-07-22 22:45:29', 'updated_at' => '2026-07-22 22:45:29'],
            ['id' => 10, 'slug' => 'tecnologia', 'nombre' => 'Tienda de Tecnología', 'descripcion' => 'Venta y servicios de equipos tecnológicos, hardware, software, redes e infraestructura', 'color' => 'danger', 'icon' => 'bi-phone', 'activo' => 1, 'orden' => 9, 'config' => null, 'created_at' => '2026-07-22 23:36:18', 'updated_at' => '2026-07-22 23:36:18'],
            ['id' => 11, 'slug' => 'mecanica', 'nombre' => 'Repuesto de Mecanica', 'descripcion' => 'Venta de repuestos automotrices y servicios de mecánica (cambio de aceite y filtros)', 'color' => 'warning', 'icon' => 'bi-tools', 'activo' => 1, 'orden' => 10, 'config' => null, 'created_at' => '2026-08-04 11:05:19', 'updated_at' => '2026-08-04 11:05:19'],
            ['id' => 12, 'slug' => 'arte_escultura', 'nombre' => 'Arte / Escultura / Galería', 'descripcion' => 'Galería de arte y escultura — gestión de obras, encargos, consignaciones y exhibiciones', 'color' => 'purple', 'icon' => 'bi-palette', 'activo' => 1, 'orden' => 11, 'config' => '{"facturacion_modo": "obras_arte"}', 'created_at' => '2026-08-11 15:48:39', 'updated_at' => '2026-08-14 18:51:37'],
            ['id' => 13, 'slug' => 'embutidos', 'nombre' => 'Embutidos / Charcutería', 'descripcion' => 'Venta de embutidos, carnes frías y productos de charcutería', 'color' => 'danger', 'icon' => 'bi-egg-fried', 'activo' => 1, 'orden' => 12, 'config' => null, 'created_at' => '2026-08-12 12:33:52', 'updated_at' => '2026-08-12 12:33:52'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('business_types')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
