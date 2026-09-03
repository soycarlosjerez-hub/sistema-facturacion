<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;

class ActualizarPreciosStock extends Command
{
    protected $signature = 'productos:actualizar-precios-stoc
                            {--tenant=10 : Tenant ID}
                            {--dry-run : Solo mostrar cambios sin guardar}';

    protected $description = 'Actualizar precios y stock de productos sin precio/stock en base a precios del mercado RD';

    public function handle()
    {
        $tenantId = $this->option('tenant');
        $dryRun = $this->option('dry-run');

        $this->info('Buscando productos sin precio para tenant ' . $tenantId);

        $products = Producto::where('tenant_id', $tenantId)
            ->where(function($q) {
                $q->whereNull('precio')
                  ->orWhere('precio', 0)
                  ->orWhere('precio', '<=', 0);
            })
            ->get();

        if ($products->isEmpty()) {
            $this->info('No se encontraron productos sin precio.');
            return Command::SUCCESS;
        }

        $this->info("Se encontraron {$products->count()} productos sin precio.");
        $this->table(
            ['ID', 'Nombre', 'Marca', 'Categoría'],
            $products->map(fn($p) => [$p->id, $p->nombre, $p->marca, $p->categoria_id])->toArray()
        );

        // Precios estimados del mercado dominicano
        $priceMap = [
            345 => [
                'precio' => 13800.00,
                'stock'  => 45,
                'note'   => 'Cable UTP CAT6 1000 pies 23AWG Agiler - similar a otros CAT6 del sistema (~11k-13k), Agiler premium'
            ],
            348 => [
                'precio' => 7200.00,
                'stock'  => 30,
                'note'   => 'Cable UTP CAT5 1000 pies Click-Cam - CAT5 es más económico que CAT6 (~6k-8k)'
            ],
            371 => [
                'precio' => 185.00,
                'stock'  => 150,
                'note'   => 'Patch Cable CAT6 1ft Nexxt - patch cables cortos (~100-250 RD$)'
            ],
            380 => [
                'precio' => 320.00,
                'stock'  => 80,
                'note'   => 'Cable VGA 6 pies - cables de video (~250-400 RD$)'
            ],
            383 => [
                'precio' => 45.00,
                'stock'  => 200,
                'note'   => 'Bota/Conector RJ45 Nexxt - conectores individuales (~30-60 RD$)'
            ],
            388 => [
                'precio' => 1250.00,
                'stock'  => 60,
                'note'   => 'Caja SDD/HDD 2.5" Omega Tech Hikvision USB 3.0 - cajas externas (~800-1500 RD$)'
            ],
            406 => [
                'precio' => 62500.00,
                'stock'  => 8,
                'note'   => 'Laptop HP ProBook 4 GLI 16" - laptop empresarial (~55k-75k RD$)'
            ],
            407 => [
                'precio' => 95000.00,
                'stock'  => 5,
                'note'   => 'Laptop HP EliteBook 645 G11 - AMD Ryzen 5/7 Pro, 16GB, 512GB SSD (~85k-115k RD$)'
            ],
            425 => [
                'precio' => 125000.00,
                'stock'  => 4,
                'note'   => 'Laptop Lenovo Slim 7 14ILL10 Aura Edition - Ultra 7, 32GB, 1TB SSD (~115k-130k RD$)'
            ],
            428 => [
                'precio' => 1650.00,
                'stock'  => 25,
                'note'   => 'Bulto Laptop Klip Xtreme Emblem 15.6" mochila (~1200-2000 RD$)'
            ],
            435 => [
                'precio' => 1550.00,
                'stock'  => 20,
                'note'   => 'Bulto Laptop Klip Xtreme Toscana 15.6" azul (~1200-2000 RD$)'
            ],
        ];

        $updated = 0;
        $failed = 0;

        $this->newLine();
        $this->warn("=== ACTUALIZACIÓN DE PRECIOS Y STOCK ===");
        $this->newLine();

        foreach ($products as $product) {
            if (isset($priceMap[$product->id])) {
                $data = $priceMap[$product->id];

                if ($dryRun) {
                    $this->info("[DRY-RUN] ID {$product->id}: {$product->nombre}");
                    $this->line("  Precio: {$data['precio']} RD$ (antes: {$product->precio})");
                    $this->line("  Stock: {$data['stock']} (antes: {$product->stock})");
                    $this->line("  Nota: {$data['note']}");
                    $updated++;
                } else {
                    try {
                        $product->update([
                            'precio' => $data['precio'],
                            'stock'  => $data['stock'],
                        ]);

                        $this->info("[OK] ID {$product->id}: {$product->nombre}");
                        $this->line("  Precio: {$data['precio']} RD$ → Stock: {$data['stock']}");
                        $this->line("  Nota: {$data['note']}");
                        $updated++;
                    } catch (\Exception $e) {
                        $this->error("[FAIL] ID {$product->id}: " . $e->getMessage());
                        $failed++;
                    }
                }

                $this->newLine();
            } else {
                $this->warn("[SKIP] ID {$product->id}: No hay precio estimado disponible");
                $failed++;
            }
        }

        if ($dryRun) {
            $this->warn("\n=== MODO DRY-RUN - No se guardaron cambios ===");
        } else {
            $this->newLine();
            $this->success("Actualización completa!");
            $this->info("Actualizados: {$updated} | Fallidos: {$failed}");
        }

        return Command::SUCCESS;
    }
}
