<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar tabla antes de sembrar
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('productos')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('productos')->insert([
            [
                'nombre' => 'Arroz Selecto (Libra)',
                'codigo_barras' => '7460123456001',
                'descripcion' => 'Arroz blanco de primera calidad',
                'precio' => 35.00,
                'precio_compra' => 28.00,
                'unidad_medida' => 'Libra',
                'itbis_porcentaje' => 0.00,
                'stock' => 500,
                'imagen' => 'img/productos/arroz.png',
            ],
            [
                'nombre' => 'Habichuelas Rojas (Libra)',
                'codigo_barras' => '7460123456002',
                'descripcion' => 'Habichuelas rojas secas',
                'precio' => 75.00,
                'precio_compra' => 60.00,
                'unidad_medida' => 'Libra',
                'itbis_porcentaje' => 0.00,
                'stock' => 200,
                'imagen' => null,
            ],
            [
                'nombre' => 'Salami Super Especial (Libra)',
                'codigo_barras' => '7460123456003',
                'descripcion' => 'Salami de alta calidad',
                'precio' => 135.00,
                'precio_compra' => 110.00,
                'unidad_medida' => 'Libra',
                'itbis_porcentaje' => 18.00,
                'stock' => 50,
                'imagen' => 'img/productos/salami.png',
            ],
            [
                'nombre' => 'Aceite Vegetal (16oz)',
                'codigo_barras' => '7460123456004',
                'descripcion' => 'Aceite para cocinar',
                'precio' => 95.00,
                'precio_compra' => 75.00,
                'unidad_medida' => 'Unidad',
                'itbis_porcentaje' => 0.00,
                'stock' => 100,
                'imagen' => null,
            ],
            [
                'nombre' => 'Leche Listamilk (Litro)',
                'codigo_barras' => '7460123456005',
                'descripcion' => 'Leche entera UHT',
                'precio' => 90.00,
                'precio_compra' => 78.00,
                'unidad_medida' => 'Unidad',
                'itbis_porcentaje' => 0.00,
                'stock' => 60,
                'imagen' => 'img/productos/leche.png',
            ],
            [
                'nombre' => 'Café Santo Domingo (Sobre)',
                'codigo_barras' => '7460123456006',
                'descripcion' => 'Café molido tradicional',
                'precio' => 25.00,
                'precio_compra' => 18.00,
                'unidad_medida' => 'Unidad',
                'itbis_porcentaje' => 0.00,
                'stock' => 200,
                'imagen' => 'img/productos/cafe.png',
            ],
            [
                'nombre' => 'Azúcar Crema (Libra)',
                'codigo_barras' => '7460123456007',
                'descripcion' => 'Azúcar crema nacional',
                'precio' => 30.00,
                'precio_compra' => 24.00,
                'unidad_medida' => 'Libra',
                'itbis_porcentaje' => 0.00,
                'stock' => 300,
                'imagen' => null,
            ],
            [
                'nombre' => 'Huevos (Unidad)',
                'codigo_barras' => '7460123456008',
                'descripcion' => 'Huevo fresco de granja',
                'precio' => 8.00,
                'precio_compra' => 6.00,
                'unidad_medida' => 'Unidad',
                'itbis_porcentaje' => 0.00,
                'stock' => 360,
                'imagen' => 'img/productos/huevos.png',
            ],
            [
                'nombre' => 'Pan de Agua (Unidad)',
                'codigo_barras' => '7460123456009',
                'descripcion' => 'Pan fresco del día',
                'precio' => 7.00,
                'precio_compra' => 5.00,
                'unidad_medida' => 'Unidad',
                'itbis_porcentaje' => 0.00,
                'stock' => 100,
                'imagen' => null,
            ],
            [
                'nombre' => 'Espaguetis (Paquete)',
                'codigo_barras' => '7460123456010',
                'descripcion' => 'Pasta larga',
                'precio' => 45.00,
                'precio_compra' => 35.00,
                'unidad_medida' => 'Unidad',
                'itbis_porcentaje' => 18.00,
                'stock' => 120,
                'imagen' => null,
            ],
            [
                'nombre' => 'Cerveza Presidente (Pequeña)',
                'codigo_barras' => '7460123456014',
                'descripcion' => 'Cerveza nacional',
                'precio' => 160.00,
                'precio_compra' => 135.00,
                'unidad_medida' => 'Unidad',
                'itbis_porcentaje' => 18.00,
                'stock' => 48,
                'imagen' => 'img/productos/cerveza.png',
            ],
            [
                'nombre' => 'Jabón de Cuaba',
                'codigo_barras' => '7460123456015',
                'descripcion' => 'Jabón para lavar platos/ropa',
                'precio' => 25.00,
                'precio_compra' => 18.00,
                'unidad_medida' => 'Unidad',
                'itbis_porcentaje' => 18.00,
                'stock' => 200,
                'imagen' => null,
            ],
            [
                'nombre' => 'Ron Añejo (Chata)',
                'codigo_barras' => '7460123456017',
                'descripcion' => 'Ron dominicano',
                'precio' => 250.00,
                'precio_compra' => 190.00,
                'unidad_medida' => 'Unidad',
                'itbis_porcentaje' => 18.00,
                'stock' => 30,
                'imagen' => null,
            ],
            [
                'nombre' => 'Chocolate Embajador (Unidad)',
                'codigo_barras' => '7460123456020',
                'descripcion' => 'Chocolate de taza',
                'precio' => 15.00,
                'precio_compra' => 10.00,
                'unidad_medida' => 'Unidad',
                'itbis_porcentaje' => 18.00,
                'stock' => 200,
                'imagen' => null,
            ],
            [
                'nombre' => 'Sopita Maggi (Unidad)',
                'codigo_barras' => '7460123456021',
                'descripcion' => 'Caldo de pollo en cubo',
                'precio' => 7.00,
                'precio_compra' => 5.00,
                'unidad_medida' => 'Unidad',
                'itbis_porcentaje' => 18.00,
                'stock' => 500,
                'imagen' => null,
            ],
        ]);
    }
}
