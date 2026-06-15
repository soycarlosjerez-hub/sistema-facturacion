<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('categorias')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('categorias')->insert([
            ['nombre' => 'Entradas', 'descripcion' => 'Aperitivos y entrantes', 'activa' => true],
            ['nombre' => 'Platos Fuertes', 'descripcion' => 'Platos principales', 'activa' => true],
            ['nombre' => 'Postres', 'descripcion' => 'Postres y dulces', 'activa' => true],
            ['nombre' => 'Bebidas', 'descripcion' => 'Bebidas frías y calientes', 'activa' => true],
        ]);
    }
}
