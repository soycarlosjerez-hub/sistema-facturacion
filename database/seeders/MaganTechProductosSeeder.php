<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaganTechProductosSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar productos anteriores de maganTech (los del intento fallido sin tenant_id)
        DB::table('productos')->whereNull('tenant_id')->delete();

        $instanceId = DB::table('business_instances')->where('slug', 'magan-tech')->value('id');
        if (!$instanceId) {
            $this->command->error('Instancia magan-tech no encontrada.');
            return;
        }

        $catIds = DB::table('categorias')->where('tenant_id', $instanceId)->pluck('id', 'nombre')->toArray();
        $jsonPath = database_path('seeders/MaganTechProductos.json');
        $data = json_decode(file_get_contents($jsonPath), true);

        $productos = [];
        foreach ($data as $item) {
            $cat = $item[0];
            $brand = $item[1];
            $name = $item[2];
            $price = $item[3];
            $warranty = $item[4];
            $desc = isset($item[5]) ? $item[5] : '';

            // Truncate name to 190 chars (varchar 191 - 1 safety)
            $name = mb_substr($name, 0, 190);
            if ($desc) {
                $desc = strip_tags(html_entity_decode($desc, ENT_QUOTES, 'UTF-8'));
                $desc = mb_substr($desc, 0, 1000);
            }

            $productos[] = [
                'tenant_id' => null,
                'nombre' => trim($name),
                'descripcion' => $desc ?: null,
                'precio' => round($price, 2),
                'precio_compra' => round($price * 0.6, 2),
                'unidad_medida' => 'Unidad',
                'itbis_porcentaje' => 18.00,
                'stock' => $price <= 0 ? 100 : 0,
                'ventas_count' => 0,
                'stock_minimo' => 5,
                'activo' => 1,
                'categoria_id' => $catIds[$cat] ?? 0,
                'especializacion' => 'accesorio',
                'vendible_imei' => 0,
                'requiere_imei' => 0,
                'marca' => $brand,
                'tipo_servicio' => 'producto',
                'tipo_producto' => 'fisico',
                'linea_negocio' => 'todos',
                'garantia_dias' => $warranty,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $chunkSize = 100;
        $inserted = 0;
        foreach (array_chunk($productos, $chunkSize) as $chunk) {
            DB::table('productos')->insert($chunk);
            $inserted += count($chunk);
        }

        $this->command->info("Productos insertados: {$inserted} para maganTech (ID: {$instanceId})");
    }
}
