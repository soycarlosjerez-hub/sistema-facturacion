<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CategoriasFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `categorias` (18 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('categorias');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('categorias')->truncate();

        $rows = [
            ['id' => 1, 'tenant_id' => null, 'nombre' => 'Entradas', 'descripcion' => 'Aperitivos y entrantes', 'activa' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 2, 'tenant_id' => null, 'nombre' => 'Platos Fuertes', 'descripcion' => 'Platos principales', 'activa' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 3, 'tenant_id' => null, 'nombre' => 'Postres', 'descripcion' => 'Postres y dulces', 'activa' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 4, 'tenant_id' => null, 'nombre' => 'Bebidas', 'descripcion' => 'Bebidas frías y calientes', 'activa' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 5, 'tenant_id' => 1, 'nombre' => 'test', 'descripcion' => null, 'activa' => 1, 'created_at' => '2026-07-01 15:10:13', 'updated_at' => '2026-07-01 15:10:13'],
            ['id' => 6, 'tenant_id' => 2, 'nombre' => 'ADICIONALES', 'descripcion' => null, 'activa' => 1, 'created_at' => '2026-07-02 17:13:03', 'updated_at' => '2026-07-02 17:13:03'],
            ['id' => 7, 'tenant_id' => 2, 'nombre' => 'PLATOS DEL DIA', 'descripcion' => null, 'activa' => 1, 'created_at' => '2026-07-02 17:13:50', 'updated_at' => '2026-07-02 17:13:50'],
            ['id' => 8, 'tenant_id' => 2, 'nombre' => 'MOFONGOS', 'descripcion' => null, 'activa' => 1, 'created_at' => '2026-07-02 17:14:27', 'updated_at' => '2026-07-02 17:14:27'],
            ['id' => 9, 'tenant_id' => 2, 'nombre' => 'CALDOS', 'descripcion' => null, 'activa' => 1, 'created_at' => '2026-07-02 17:15:01', 'updated_at' => '2026-07-02 17:15:01'],
            ['id' => 10, 'tenant_id' => 2, 'nombre' => 'BEBIDAS', 'descripcion' => null, 'activa' => 1, 'created_at' => '2026-07-02 17:56:53', 'updated_at' => '2026-07-02 17:56:53'],
            ['id' => 11, 'tenant_id' => 2, 'nombre' => 'ESPECIALIDADES', 'descripcion' => null, 'activa' => 1, 'created_at' => '2026-07-02 17:57:11', 'updated_at' => '2026-07-02 17:57:11'],
            ['id' => 12, 'tenant_id' => 2, 'nombre' => 'POSTRES', 'descripcion' => null, 'activa' => 1, 'created_at' => '2026-07-02 17:57:22', 'updated_at' => '2026-07-02 17:57:22'],
            ['id' => 13, 'tenant_id' => 2, 'nombre' => 'PASTAS', 'descripcion' => null, 'activa' => 1, 'created_at' => '2026-07-02 18:03:59', 'updated_at' => '2026-07-02 18:03:59'],
            ['id' => 14, 'tenant_id' => 5, 'nombre' => 'CELULARES', 'descripcion' => 'CELULARES', 'activa' => 1, 'created_at' => '2026-07-23 12:24:43', 'updated_at' => '2026-07-23 12:24:43'],
            ['id' => 15, 'tenant_id' => 4, 'nombre' => 'aires acondicionados', 'descripcion' => 'climatizacion', 'activa' => 1, 'created_at' => '2026-07-23 22:53:48', 'updated_at' => '2026-07-23 22:53:48'],
            ['id' => 16, 'tenant_id' => 7, 'nombre' => 'Alimentos', 'descripcion' => null, 'activa' => 1, 'created_at' => '2026-08-12 14:58:01', 'updated_at' => '2026-08-12 14:58:01'],
            ['id' => 18, 'tenant_id' => 7, 'nombre' => 'Cervezas', 'descripcion' => 'Cervezas nacionales e importadas', 'activa' => 1, 'created_at' => '2026-08-12 15:41:22', 'updated_at' => '2026-08-12 15:41:22'],
            ['id' => 20, 'tenant_id' => 9, 'nombre' => 'TEST', 'descripcion' => 'TEST', 'activa' => 1, 'created_at' => '2026-08-14 17:56:02', 'updated_at' => '2026-08-14 17:56:02'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('categorias')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
