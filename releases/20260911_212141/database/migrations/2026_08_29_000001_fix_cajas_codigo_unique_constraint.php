<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check what indexes exist
        $indexes = DB::select("SHOW INDEX FROM cajas WHERE Key_name LIKE '%codigo%'");

        // Drop any unique index on codigo column
        foreach ($indexes as $index) {
            if (strtoupper($index->Key_name) === 'CAJAS_CODIGO_UNIQUE' ||
                strtoupper($index->Key_name) === 'CAJAS_TENANT_CODIGO_UNIQUE') {
                DB::statement('ALTER TABLE cajas DROP INDEX `'.$index->Key_name.'`');
            }
        }

        // Create composite unique index
        DB::statement('ALTER TABLE cajas ADD UNIQUE INDEX cajas_tenant_codigo_unique (tenant_id, codigo)');
    }

    public function down(): void
    {
        // Drop composite index
        DB::statement('ALTER TABLE cajas DROP INDEX `cajas_tenant_codigo_unique`');

        // Recreate original single column unique
        DB::statement('ALTER TABLE cajas ADD UNIQUE INDEX cajas_codigo_unique (codigo)');
    }
};
