<?php

namespace Database\Seeders\Full;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackupsFullSeeder extends Seeder
{
    /**
     * Datos extraídos de solo_inserts.sql — tabla `backups` (2 filas).
     */
    public function run(): void
    {
        $exists = Schema::hasTable('backups');
        if (!$exists) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('backups')->truncate();

        $rows = [
            ['id' => 2, 'filename' => 'backup_facturacion_db_20260804_004745.sql', 'filepath' => 'app/backups/backup_facturacion_db_20260804_004745.sql', 'size_bytes' => 4557035, 'type' => 'manual', 'status' => 'completado', 'notes' => 'Backup manual desde la interfaz', 'user_id' => 8, 'created_at' => '2026-08-03 22:47:48', 'updated_at' => '2026-08-03 22:47:48'],
            ['id' => 3, 'filename' => 'backup_facturacion_db_20260818_134521.sql', 'filepath' => 'app/backups/backup_facturacion_db_20260818_134521.sql', 'size_bytes' => 7989418, 'type' => 'manual', 'status' => 'completado', 'notes' => 'Backup manual desde la interfaz', 'user_id' => 23, 'created_at' => '2026-08-18 11:45:25', 'updated_at' => '2026-08-18 11:45:25'],
        ];

        foreach (array_chunk($rows, 250) as $chunk) {
            DB::table('backups')->insert($chunk);
        }

        Schema::enableForeignKeyConstraints();
    }
}
