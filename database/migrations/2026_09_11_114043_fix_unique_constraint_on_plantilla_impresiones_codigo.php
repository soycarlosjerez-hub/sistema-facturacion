<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar el unique global actual (si existe)
        if (Schema::hasIndex('plantilla_impresiones', 'plantilla_impresiones_codigo_unique')) {
            Schema::table('plantilla_impresiones', function (Blueprint $table) {
                $table->dropUnique('plantilla_impresiones_codigo_unique');
            });
        }

        // Crear unique compuesto (codigo, tenant_id) — portable (no SQL crudo).
        if (! Schema::hasIndex('plantilla_impresiones', 'plantilla_impresiones_codigo_tenant_unique')) {
            Schema::table('plantilla_impresiones', function (Blueprint $table) {
                $table->unique(['codigo', 'tenant_id'], 'plantilla_impresiones_codigo_tenant_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('plantilla_impresiones', 'plantilla_impresiones_codigo_tenant_unique')) {
            Schema::table('plantilla_impresiones', function (Blueprint $table) {
                $table->dropUnique('plantilla_impresiones_codigo_tenant_unique');
            });
        }

        if (! Schema::hasIndex('plantilla_impresiones', 'plantilla_impresiones_codigo_unique')) {
            Schema::table('plantilla_impresiones', function (Blueprint $table) {
                $table->unique('codigo', 'plantilla_impresiones_codigo_unique');
            });
        }
    }
};
