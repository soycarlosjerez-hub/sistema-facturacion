<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar el unique global actual
        Schema::table('plantilla_impresiones', function (Blueprint $table) {
            $table->dropUnique('plantilla_impresiones_codigo_unique');
        });

        // Crear unique compuesto (codigo, tenant_id)
        DB::statement(
            'ALTER TABLE plantilla_impresiones ADD UNIQUE INDEX plantilla_impresiones_codigo_tenant_unique (codigo, tenant_id)'
        );
    }

    public function down(): void
    {
        DB::statement(
            'ALTER TABLE plantilla_impresiones DROP INDEX plantilla_impresiones_codigo_tenant_unique'
        );

        Schema::table('plantilla_impresiones', function (Blueprint $table) {
            $table->unique('codigo', 'plantilla_impresiones_codigo_unique');
        });
    }
};
