<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CopyProductsToTenant extends Command
{
    protected $signature = 'products:copy-tenant {--from= : ID de la instancia origen} {--to= : ID de la instancia destino}';
    protected $description = 'Copiar productos y categorías de una instancia a otra';

    public function handle(): int
    {
        $from = $this->option('from');
        $to = $this->option('to');

        if (!$from || !$to) {
            $this->error('Debes especificar ambas instancias: --from=<id> --to=<id>');
            return 1;
        }

        if ($from == $to) {
            $this->error('Las instancias origen y destino no pueden ser iguales.');
            return 1;
        }

        $fromExists = DB::table('business_instances')->where('id', $from)->exists();
        $toExists = DB::table('business_instances')->where('id', $to)->exists();

        if (!$fromExists) {
            $this->error("La instancia origen #{$from} no existe.");
            return 1;
        }
        if (!$toExists) {
            $this->error("La instancia destino #{$to} no existe.");
            return 1;
        }

        $categoryIdMap = $this->copyCategorias($from, $to);
        $this->copyProductos($from, $to, $categoryIdMap);

        return 0;
    }

    private function copyCategorias(int $from, int $to): array
    {
        $categorias = DB::table('categorias')
            ->where('tenant_id', $from)
            ->get();

        $map = [];

        foreach ($categorias as $cat) {
            $existing = DB::table('categorias')
                ->where('tenant_id', $to)
                ->where('nombre', $cat->nombre)
                ->first();

            if ($existing) {
                $map[$cat->id] = $existing->id;
                continue;
            }

            $newId = DB::table('categorias')->insertGetId([
                'nombre' => $cat->nombre,
                'descripcion' => $cat->descripcion,
                'activa' => $cat->activa,
                'tenant_id' => $to,
                'created_at' => $cat->created_at,
                'updated_at' => $cat->updated_at,
            ]);

            $map[$cat->id] = $newId;
        }

        $this->info("Categorías: " . count($map) . " procesadas (" . count($categorias) . " originales).");
        return $map;
    }

    private function copyProductos(int $from, int $to, array $categoryIdMap): void
    {
        $productos = DB::table('productos')
            ->where('tenant_id', $from)
            ->get();

        $copied = 0;
        $skipped = 0;

        foreach ($productos as $prod) {
            $existing = DB::table('productos')
                ->where('tenant_id', $to)
                ->where('nombre', $prod->nombre)
                ->first();

            if ($existing) {
                $skipped++;
                continue;
            }

            $newCategoryId = $categoryIdMap[$prod->categoria_id] ?? $prod->categoria_id;

            DB::table('productos')->insert([
                'categoria_id' => $newCategoryId,
                'category_subcategory_id' => $prod->category_subcategory_id,
                'nombre' => $prod->nombre,
                'codigo_barras' => $prod->codigo_barras,
                'descripcion' => $prod->descripcion,
                'marca' => $prod->marca,
                'modelo' => $prod->modelo,
                'capacidad_toneladas' => $prod->capacidad_toneladas,
                'capacidad_btu' => $prod->capacidad_btu,
                'tipo_equipo' => $prod->tipo_equipo,
                'eficiencia_seer' => $prod->eficiencia_seer,
                'gas_refrigerante' => $prod->gas_refrigerante,
                'voltaje' => $prod->voltaje,
                'peso_kg' => $prod->peso_kg,
                'dimensiones' => $prod->dimensiones,
                'categoria_clima' => $prod->categoria_clima,
                'precio' => $prod->precio,
                'precio_compra' => $prod->precio_compra,
                'unidad_medida' => $prod->unidad_medida,
                'itbis_porcentaje' => $prod->itbis_porcentaje,
                'stock' => $prod->stock,
                'stock_minimo' => $prod->stock_minimo,
                'activo' => $prod->activo,
                'incluir_kds' => $prod->incluir_kds,
                'imagen' => $prod->imagen,
                'tenant_id' => $to,
                'tipo_producto' => $prod->tipo_producto,
                'linea_negocio' => $prod->linea_negocio,
                'requiere_serial' => $prod->requiere_serial,
                'categoria_tecnica' => $prod->categoria_tecnica,
                'garantia_dias' => $prod->garantia_dias,
                'es_licencia' => $prod->es_licencia,
                'tipo_licencia' => $prod->tipo_licencia,
                'licencia_max_usuarios' => $prod->licencia_max_usuarios,
                'requires_setup' => $prod->requires_setup,
                'marca_tecnologica_id' => $prod->marca_tecnologica_id,
                'created_at' => $prod->created_at,
                'updated_at' => $prod->updated_at,
            ]);

            $copied++;
        }

        $this->info("Productos: {$copied} copiados, {$skipped} omitidos (ya existían).");
    }
}
