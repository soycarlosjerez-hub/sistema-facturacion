<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DownloadProductImages extends Command
{
    protected $signature = 'images:download-products {--force : Overwrite existing images}';
    protected $description = 'Download real food images for restaurant products';

    protected $imageMap = [
        1 => [
            'nombre' => 'Yaroa Mixta',
            'url' => 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=400&h=300&fit=crop',
            'filename' => '1-yaroa-mixta.jpg',
        ],
        2 => [
            'nombre' => 'Tostones con Salami',
            'url' => 'https://images.unsplash.com/photo-1618160702438-9b02ab6515c9?w=400&h=300&fit=crop',
            'filename' => '2-tostones-con-salami.jpg',
        ],
        3 => [
            'nombre' => 'Ceviche de Pescado',
            'url' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=400&h=300&fit=crop',
            'filename' => '3-ceviche-de-pescado.jpg',
        ],
        4 => [
            'nombre' => 'Pastelitos (3 unidades)',
            'url' => 'https://images.unsplash.com/photo-1599487488170-d11ec9c172f0?w=400&h=300&fit=crop',
            'filename' => '4-pastelitos.jpg',
        ],
        5 => [
            'nombre' => 'La Bandera Dominicana',
            'url' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400&h=300&fit=crop',
            'filename' => '5-la-bandera-dominicana.jpg',
        ],
        6 => [
            'nombre' => 'Mangú con Salami y Queso',
            'url' => 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=400&h=300&fit=crop',
            'filename' => '6-mangu-con-salami-queso.jpg',
        ],
        7 => [
            'nombre' => 'Sancocho de Siete Carnes',
            'url' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=400&h=300&fit=crop',
            'filename' => '7-sancocho-siete-carnes.jpg',
        ],
        8 => [
            'nombre' => 'Pescado Frito Entero',
            'url' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=400&h=300&fit=crop',
            'filename' => '8-pescado-frito-entero.jpg',
        ],
        9 => [
            'nombre' => 'Chivo Guisado',
            'url' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=400&h=300&fit=crop',
            'filename' => '9-chivo-guisado.jpg',
        ],
        10 => [
            'nombre' => 'Pernil con Arroz',
            'url' => 'https://images.unsplash.com/photo-1529193591184-b1d58069ecdd?w=400&h=300&fit=crop',
            'filename' => '10-pernil-con-arroz.jpg',
        ],
        11 => [
            'nombre' => 'Pollo Guisado',
            'url' => 'https://images.unsplash.com/photo-1603105037880-880cd4edfb0d?w=400&h=300&fit=crop',
            'filename' => '11-pollo-guisado.jpg',
        ],
        12 => [
            'nombre' => 'Habichuelas con Dulce',
            'url' => 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=400&h=300&fit=crop',
            'filename' => '12-habichuelas-con-dulce.jpg',
        ],
        13 => [
            'nombre' => 'Flan de Caramelo',
            'url' => 'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=400&h=300&fit=crop',
            'filename' => '13-flan-de-caramelo.jpg',
        ],
        14 => [
            'nombre' => 'Arroz con Leche',
            'url' => 'https://images.unsplash.com/photo-1541783245831-57d6fb0926d3?w=400&h=300&fit=crop',
            'filename' => '14-arroz-con-leche.jpg',
        ],
        15 => [
            'nombre' => 'Tres Leches',
            'url' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400&h=300&fit=crop',
            'filename' => '15-tres-leches.jpg',
        ],
        16 => [
            'nombre' => 'Jugo de Chinola Natural',
            'url' => 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=400&h=300&fit=crop',
            'filename' => '16-jugo-chinola.jpg',
        ],
        17 => [
            'nombre' => 'Morir Soñando',
            'url' => 'https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=400&h=300&fit=crop',
            'filename' => '17-morir-sonando.jpg',
        ],
        18 => [
            'nombre' => 'Café Santo Domingo',
            'url' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&h=300&fit=crop',
            'filename' => '18-cafe-santo-domingo.jpg',
        ],
        19 => [
            'nombre' => 'Cerveza Presidente',
            'url' => 'https://images.unsplash.com/photo-1608270586620-248524c67de9?w=400&h=300&fit=crop',
            'filename' => '19-cerveza-presidente.jpg',
        ],
        20 => [
            'nombre' => 'Agua Botellón',
            'url' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=400&h=300&fit=crop',
            'filename' => '20-agua-botellon.jpg',
        ],
    ];

    public function handle()
    {
        $force = $this->option('force');
        $disk = Storage::disk('public');
        $dir = 'productos';

        if (!$disk->exists($dir)) {
            $disk->makeDirectory($dir);
        }

        $this->info('Descargando imágenes reales para 20 productos...');
        $bar = $this->output->createProgressBar(count($this->imageMap));
        $bar->start();

        $downloaded = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($this->imageMap as $id => $data) {
            $path = "{$dir}/{$data['filename']}";

            if ($disk->exists($path) && !$force) {
                $skipped++;
                $bar->advance();
                continue;
            }

            try {
                $response = Http::timeout(30)->withOptions(['verify' => false])->get($data['url']);
                
                if ($response->successful()) {
                    $disk->put($path, $response->body());
                    $downloaded++;
                } else {
                    $this->warn("\nFalló {$data['nombre']}: HTTP {$response->status()}");
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->warn("\nError {$data['nombre']}: {$e->getMessage()}");
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Completado: {$downloaded} descargadas, {$skipped} omitidas, {$failed} fallidas");

        if ($downloaded > 0) {
            $this->info('Ejecuta el seeder para actualizar la BD: php artisan db:seed --class=ProductosSeeder');
        }

        return Command::SUCCESS;
    }
}