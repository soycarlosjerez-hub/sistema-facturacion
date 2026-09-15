<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $indexes = DB::select("SHOW INDEX FROM cajas WHERE Key_name LIKE '%codigo%'");

        foreach ($indexes as $index) {
            $keyName = strtoupper($index->Key_name);
            if ($keyName === 'CAJAS_CODIGO_UNIQUE' ||
                $keyName === 'CAJAS_TENANT_CODIGO_UNIQUE' ||
                $keyName === 'CAJAS_TENANT_CODIGO_UNIQUE_1') {
                try {
                    DB::statement('ALTER TABLE cajas DROP INDEX `'.$index->Key_name.'`');
                } catch (\Throwable $e) {
                    if (str_contains($e->getMessage(), 'needed in a foreign key constraint')) {
                        // Index is needed by FK, try to recreate composite with different name
                    } else {
                        throw $e;
                    }
                }
            }
        }

        try {
            DB::statement('ALTER TABLE cajas ADD UNIQUE INDEX cajas_tenant_codigo_unique (tenant_id, codigo)');
        } catch (\Throwable $e) {
            // Index already exists
        }
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE cajas DROP INDEX `cajas_tenant_codigo_unique`');
        } catch (\Throwable) {
            // Index doesn't exist
        }

        try {
            DB::statement('ALTER TABLE cajas ADD UNIQUE INDEX cajas_codigo_unique (codigo)');
        } catch (\Throwable) {
            // Index already exists
        }
    }
};
