<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('secuencias_ecf', 'tenant_id')) {
            return;
        }

        // Eliminar índice simple previo (si existe) — portable.
        if (Schema::hasIndex('secuencias_ecf', 'tipo_ecf')
            || Schema::hasIndex('secuencias_ecf', 'secuencias_ecf_tipo_ecf_unique')) {
            Schema::table('secuencias_ecf', function (Blueprint $table) {
                try {
                    $table->dropUnique(['tipo_ecf']);
                } catch (\Throwable) {
                    // Ya eliminado o con otro nombre; seguir.
                }
            });
        }

        if (! Schema::hasIndex('secuencias_ecf', 'secuencias_ecf_tenant_id_tipo_ecf_unique')) {
            Schema::table('secuencias_ecf', function (Blueprint $table) {
                $table->unique(['tenant_id', 'tipo_ecf']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('secuencias_ecf', 'secuencias_ecf_tenant_id_tipo_ecf_unique')) {
            Schema::table('secuencias_ecf', function (Blueprint $table) {
                $table->dropUnique(['tenant_id', 'tipo_ecf']);
            });
        }

        if (Schema::hasColumn('secuencias_ecf', 'tipo_ecf')
            && DB::getDriverName() === 'mysql'
            && ! Schema::hasIndex('secuencias_ecf', 'tipo_ecf')) {
            DB::statement('ALTER TABLE secuencias_ecf ADD UNIQUE (tipo_ecf)');
        }
    }
};
